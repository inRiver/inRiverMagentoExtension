<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data;

use Exception;
use Inriver\Adapter\Api\Data\ImportInterface;
use Inriver\Adapter\Api\Data\OperationResultInterfaceFactory;
use Inriver\Adapter\Helper\Import as InriverImportHelper;
use Inriver\Adapter\Logger\Logger;
use InvalidArgumentException;
use Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Event\Manager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Filesystem\Io\File;
use Magento\ImportExport\Model\Import as MagentoImport;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\Import\Source\CsvFactory;
use Magento\ImportExport\Model\ImportFactory;
use Psr\Log\LogLevel;

use function array_key_exists;
use function base64_encode;
use function count;
use function in_array;

/**
 * Class Import
 * New Import class specific for inriver csv. This will have different behavior depending to accommodate lack of
 * information in inriver
 */
class Import implements ImportInterface
{
    protected const ERROR_LOG_PREFIX = 'InRiver Import';
    public const ARCHIVES_FOLDER = 'archives';
    public const SUCCESS_FOLDER = 'success';
    public const ERRORS_FOLDER = 'errors';

    /** @var \Inriver\Adapter\Logger\Logger */
    private $logger;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    private $scopeConfig;

    /** @var \Magento\ImportExport\Model\Import\Source\CsvFactory */
    private $csvSourceFactory;

    /** @var \Magento\Framework\Filesystem\Io\File */
    private $ioFile;

    /** @var \Magento\Framework\Filesystem\Directory\ReadFactory */
    private $readFactory;

    /** @var \Magento\ImportExport\Model\ImportFactory */
    private $importModelFactory;

    /** @var \Inriver\Adapter\Model\Data\Import */
    private $importModel;

    /** @var \Magento\Framework\Event\Manager */
    private $eventManager;

    /** @var \Inriver\Adapter\Api\Data\OperationResultInterfaceFactory */
    private $operationResultFactory;

    /** @var string */
    private $managedWebsites;

    /** @var \Magento\Framework\Filesystem\Directory\WriteInterface */
    protected $directory;

    /**
     * @param \Magento\ImportExport\Model\ImportFactory $importModelFactory
     * @param \Inriver\Adapter\Logger\Logger $logger
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory
     * @param \Magento\ImportExport\Model\Import\Source\CsvFactory $csvSourceFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem\Io\File $ioFile
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \Inriver\Adapter\Api\Data\OperationResultInterfaceFactory $operationResultFactory
     * @param \Magento\Framework\Filesystem $fs
     */
    public function __construct(
        ImportFactory $importModelFactory,
        Logger $logger,
        ReadFactory $readFactory,
        CsvFactory $csvSourceFactory,
        ScopeConfigInterface $scopeConfig,
        File $ioFile,
        Manager $eventManager,
        OperationResultInterfaceFactory $operationResultFactory,
        Filesystem $fs
    ) {
        $this->importModelFactory = $importModelFactory;
        $this->logger = $logger;
        $this->readFactory = $readFactory;
        $this->csvSourceFactory = $csvSourceFactory;
        $this->scopeConfig = $scopeConfig;
        $this->ioFile = $ioFile;
        $this->eventManager = $eventManager;
        $this->operationResultFactory = $operationResultFactory;
        $this->directory = $fs->getDirectoryWrite(DirectoryList::VAR_DIR);
    }

    /**
     * @param string $filename
     *
     * @return bool
     */
    public function execute(string $filename): bool
    {
        $this->log('Started', LogLevel::INFO);

        if (!$this->validateFile($filename)) {
            $this->log('File validation failed', LogLevel::ERROR);
            $this->moveFileAfterImport($filename, self::ERRORS_FOLDER);
            $this->eventManager->dispatch('inriver_treatment_import_validation_failure',
                ['import' => $this, 'filename' => $filename]);
            return false;
        }
        $this->log("after validate");
        $result = false;

        try {
            $importModel = $this->getImportModel();
            $result = $importModel->importSource();

            if ($result) {
                $errors = $this->getErrors();

                if (count($errors) > 0) {
                    $this->log('The import completed with errors. ' . $this->getFormattedLogTrace(), LogLevel::INFO);

                    foreach ($errors as $error) {
                        $this->log($error->getErrorMessage() . ' - '
                            . $error->getErrorDescription(), LogLevel::ERROR);
                    }

                    $this->moveFileAfterImport($filename, self::ERRORS_FOLDER);
                } else {
                    $this->log('The import was successful. ' . $this->getFormattedLogTrace());
                    $this->moveFileAfterImport($filename, self::SUCCESS_FOLDER);
                    $this->eventManager->dispatch('inriver_treatment_after_import_success',
                        ['import' => $this, 'filename' => $filename]);
                }

                $importModel->invalidateIndex();
            } else {
                $this->log('Import failed', LogLevel::ERROR);

                $errors = $this->getErrors();

                foreach ($errors as $error) {
                    $this->log($error->getErrorMessage() . ' - ' .
                        $error->getErrorDescription(), LogLevel::ERROR);
                }

                $this->moveFileAfterImport($filename, self::ERRORS_FOLDER);
                $this->eventManager->dispatch('inriver_treatment_after_import_failure',
                    ['import' => $this, 'filename' => $filename]);
            }
        } catch (InvalidArgumentException $e) {
            $errors = $this->getFormattedLogTrace();

            if (!$errors) {
                $errors = $e->getMessage();
            }
            $this->moveFileAfterImport($filename, self::ERRORS_FOLDER);

            $this->eventManager->dispatch('inriver_treatment_import_exception',
                ['import' => $this, 'filename' => $filename, 'exception' => $e]);
            $this->log('Invalid source. ' . $errors, LogLevel::ERROR);
        } catch (LocalizedException $e) {
            $this->log('Import failed', LogLevel::ERROR);
            $errors = $this->getFormattedLogTrace();

            if (!$errors) {
                $errors = $e->getMessage();
            } else {
                $errors .= "\n" . $e->getMessage();
            }
            $this->moveFileAfterImport($filename, self::ERRORS_FOLDER);

            $this->eventManager->dispatch('inriver_treatment_after_import_failure',
                ['import' => $this, 'filename' => $filename, 'exception' => $e]);
            $this->log($errors, LogLevel::ERROR);
        } finally {
            $this->log('Finished', LogLevel::INFO);
        }

        return $result;
    }

    protected function moveFileAfterImport(string $fileName, string $state)
    {
        $DS = DIRECTORY_SEPARATOR;
        $aPath = self::ARCHIVES_FOLDER . $DS . $state;

        $pathinfo = $this->ioFile->getPathInfo($fileName);
        $absoluteNewPath = $pathinfo['dirname'] . $DS . $aPath . $DS . $pathinfo['basename'];

        try {
            $this->directory->renameFile($fileName, $absoluteNewPath);
        } catch (\Exception $e) {
            $this->logger->log(LogLevel::ERROR, 'Error while moving processed file : ' . $e->getMessage());
        }
    }

    /**
     * Get errors
     *
     * @return \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getErrors(): array
    {
        return $this->getImportModel()->getErrorAggregator()->getAllErrors();
    }

    /**
     * Get errors as array
     *
     * @return \Inriver\Adapter\Api\Data\OperationResultInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getErrorsAsArray(): array
    {
        $errors = [];
        $isDisableImport = $this->getImportModel()->isImportTypeDisable();

        foreach ($this->getErrors() as $error) {
            if (!$isDisableImport || $error->getErrorCode() !== RowValidatorInterface::ERROR_INVALID_TYPE) {
                $key = base64_encode($error->getErrorCode() . $error->getColumnName() . $error->getErrorMessage());

                if (array_key_exists($key, $errors)) {
                    /** @var \Inriver\Adapter\Api\Data\OperationResultInterface $element */
                    $element = $errors[$key];
                    $errors[$key] = $element->addRowNumber($error->getRowNumber() + 1);
                } else {
                    $errors[$key] = $this->operationResultFactory->create()
                        ->setErrorCode($error->getErrorCode())
                        ->setColumnName($error->getColumnName())
                        ->setErrorMessage($error->getErrorMessage())
                        ->setRowNumbers([$error->getRowNumber() + 1]);
                }
            }
        }

        return $errors;
    }

    /**
     * Get formatted log trace
     *
     * @return string
     */
    public function getFormattedLogTrace(): string
    {
        return $this->getImportModel()->getFormatedLogTrace();
    }

    /**
     * Get import model
     *
     * @return \Magento\ImportExport\Model\Import
     */
    protected function getImportModel(): MagentoImport
    {
        if ($this->importModel === null) {
            $this->importModel = $this->importModelFactory->create()->setData(
                [
                    'entity' => InriverImportHelper::INRIVER_ENTITY,
                    'is_inriver' => true,
                    MagentoImport::FIELD_NAME_VALIDATION_STRATEGY =>
                        ProcessingErrorAggregatorInterface::VALIDATION_STRATEGY_SKIP_ERRORS,
                    MagentoImport::FIELD_NAME_ALLOWED_ERROR_COUNT =>
                        $this->scopeConfig->getValue(self::XML_INRIVER_MAX_ALLOWED_ERROR),
                    'managed_websites' => $this->getManagedWebsites()
                ]
            );

            $this->setImportBehavior();
        }

        return $this->importModel;
    }

    /**
     * Set import behavior
     *
     * @return void
     */
    protected function setImportBehavior(): void
    {
        $behavior = $this->scopeConfig->getValue(self::XML_INRIVER_IMPORT_PATH_BEHAVIOR);

        if ($behavior === null) {
            $behavior = MagentoImport::BEHAVIOR_APPEND;
        }

        if (
            in_array($behavior, [
            MagentoImport::BEHAVIOR_APPEND,
            MagentoImport::BEHAVIOR_ADD_UPDATE,
            MagentoImport::BEHAVIOR_REPLACE,
            MagentoImport::BEHAVIOR_DELETE,
            ], true)
        ) {
            $this->getImportModel()->setData('behavior', $behavior);
        }
    }

    /**
     * Log import message
     *
     * @param string $message
     * @param string $logLevel
     *
     * @return void
     */
    protected function log(string $message, string $logLevel = LogLevel::INFO): void
    {
        $this->logger->log($logLevel, $message);
    }

    /**
     * Validate CSV file
     *
     * @param string $filePath
     *
     * @return bool
     */
    private function validateFile(string $filePath): bool
    {
        if (!$this->ioFile->fileExists($filePath)) {
            $this->log('File not found.', LogLevel::ERROR);

            return false;
        }

        $pathInfo = $this->ioFile->getPathInfo($filePath);

        try {
            return $this->getImportModel()->validateSource($this->csvSourceFactory->create(
                [
                    'file' => $pathInfo['basename'],
                    'directory' => $this->readFactory->create($pathInfo['dirname']),
                ]
            ));
        } catch (LocalizedException $e) {
            $this->log('File validation failed: ' . $e->getMessage(), LogLevel::ERROR);

            return false;
        } catch (Exception $e) {
            $this->log('File validation failed: ' . $e->getMessage(), LogLevel::ERROR);

            return false;
        }
    }

    /**
     * Set the Managed websites by the adapter
     *
     * @param string $managedWebsites
     */
    public function setManagedWebsites(string $managedWebsites)
    {
        $this->managedWebsites = $managedWebsites;
    }

    /**
     * returns the Managed websites by the adapter
     *
     * @return string
     */
    public function getManagedWebsites(): string
    {
        return $this->managedWebsites;
    }

}
