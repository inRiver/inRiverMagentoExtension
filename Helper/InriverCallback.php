<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Helper;

use Exception;
use GuzzleHttp\Psr7\Response;
use Inriver\Adapter\Api\CallbackOperationRepositoryInterface;
use Inriver\Adapter\Api\CallbackRepositoryInterface;
use Inriver\Adapter\Api\Data\CallbackInterface;
use Inriver\Adapter\Logger\Logger;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Bulk\OperationInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LogLevel;

use function count;
use function in_array;
use function is_array;
use function is_string;
use function str_replace;
use function strpos;

class InriverCallback
{
    public const CALLBACK_HEADER = 'x-inriver-callback';
    public const RUNTIME_ID_HEADER = 'x-inriver-runtime-id';

    private const RESPONSE_HEADER_INRIVER_APIKEY = 'X-inRiver-APIKey';
    private const INRIVER_API_KEY = 'inriver/import/inriver_api_key';
    private const OPERATION_REPORT = 'operations_report';
    private const MAGENTO_CATEGORY_TOPIC = 'categoryrepositoryinterface';

    /** @var \Inriver\Adapter\Api\CallbackRepositoryInterface */
    private $callbackRepository;

    /** @var \Inriver\Adapter\Api\CallbackOperationRepositoryInterface */
    private $callbackOperationRepository;

    /** @var \Inriver\Adapter\Helper\HttpRequest */
    private $httpRequest;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    private $scopeConfig;

    /** @var \Magento\Framework\Serialize\Serializer\Json */
    private $json;

    /** @var \Inriver\Adapter\Logger\Logger */
    private $logger;

    /**
     * @param \Inriver\Adapter\Api\CallbackRepositoryInterface $callbackRepository
     * @param \Inriver\Adapter\Api\CallbackOperationRepositoryInterface $callbackOperationRepository
     * @param \Inriver\Adapter\Helper\HttpRequest $httpRequest
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Inriver\Adapter\Logger\Logger $logger
     */
    public function __construct(
        CallbackRepositoryInterface $callbackRepository,
        CallbackOperationRepositoryInterface $callbackOperationRepository,
        HttpRequest $httpRequest,
        Json $json,
        ScopeConfigInterface $scopeConfig,
        Logger $logger
    ) {
        $this->callbackRepository = $callbackRepository;
        $this->callbackOperationRepository = $callbackOperationRepository;
        $this->httpRequest = $httpRequest;
        $this->json = $json;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    /**
     * @param int $operationId
     * @param int $operationKey
     * @param string $bulkUuid
     * @param int $status
     * @param int|null $errorCode
     * @param string|null $message
     * @param null $resultData
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function createCallbackOperationAfterAsyncOperations(
        int $operationId,
        int $operationKey,
        string $bulkUuid,
        int $status,
        ?int $errorCode,
        ?string $message = null,
        $resultData = null
    ): void {
        try {
            //No operation are created for call that don't have the callback header
            $callbackOperation = $this->callbackOperationRepository->getByOperationId($operationId);
            $callback = $this->callbackRepository->getByBulkUuid($bulkUuid);
        } catch (NoSuchEntityException $e) {
            // No callback to handle
            return;
        }

        $callbackId = $callback->getCallBackId();

        if ($callbackId !== null) {
            $messageArray = [];

            $isNotComplete = in_array(
                $status,
                [
                    OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED,
                    OperationInterface::STATUS_TYPE_RETRIABLY_FAILED,
                    OperationInterface::STATUS_TYPE_REJECTED,
                    OperationInterface::STATUS_TYPE_OPEN,
                ],
                true
            );

            $empty = true;

            if ($errorCode || $isNotComplete) {
                $empty = false;
                $messageArray['error_code'] = $errorCode ?? ErrorCodesDirectory::GENERAL_ERROR;
                $messageArray['messages'] = $message;
                $callbackOperation->setErrorCount(1);
            } else {
                $messageArray['error_code'] = null;
                $messageArray['messages'] = '';
            }

            if (
                $errorCode ||
                $isNotComplete ||
                strpos($callback->getTopic(), self::MAGENTO_CATEGORY_TOPIC) === false
            ) {
                try {
                    $data = $this->json->unserialize($resultData ?? '[]');

                    if ($data !== null && is_array($data) && count($data) > 0) {
                        $empty = false;
                        $messageArray['additional_messages'] = $data;
                    } else {
                        $messageArray['additional_messages'] = [];
                    }
                } catch (Exception $e) {
                    $empty = false;
                    $messageArray['additional_messages'] = [
                        'Message' => 'An error occured while deserializing $resultData, see Magento log for more information',
                        'Exception' => $e->getMessage()
                    ];
                    $this->logger->log(
                        LogLevel::ERROR,
                        'An error occured while deserializing $resultData: ' .
                        $e->getMessage()
                    );
                }
            } else {
                $messageArray['additional_messages'] = [];
            }

            if (!$empty) {
                $callbackOperation->setMessages($this->json->serialize($messageArray));
            }

            $callbackOperation->setCallbackId($callbackId);
            $callbackOperation->setOperationKey($operationKey);

            $this->callbackOperationRepository->save($callbackOperation);
        }
    }

    /**
     * @param string $bulkUuid
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function returnResponseToInriverAfterAsyncOperations(string $bulkUuid): void
    {
        try {
            $this->logger->info("This bulk had an operation done $bulkUuid");
            $apiKey = $this->getApiKey();

            if ($apiKey !== null && $apiKey !== '') {
                try {
                    $callback = $this->callbackRepository->getByBulkUuid($bulkUuid);
                } catch (NoSuchEntityException $e) {
                    // No callback to handle
                    return;
                }

                $callbackId = $callback->getCallBackId();

                if ($callbackId !== null) {
                    $callbackOperations = $this->callbackOperationRepository->getListByCallbackId($callbackId)->getItems();
                    $countOperation = count($callbackOperations);
                    $totalOperation = $callback->getNumberOfOperations();
                    $this->logger->info("Operation Count $countOperation vs $totalOperation");
                    if ($countOperation === $totalOperation) {
                        $response = $this->sendResponse(
                            $apiKey,
                            $callback->getCallBackUrl(),
                            $this->createResultMessage($callbackOperations, $callback)
                        );

                        if ($response->getStatusCode() === 200) {
                            $callback->setInriverNotified(true);
                            $this->callbackRepository->save($callback);
                        } else {
                            $this->logger->log(
                                LogLevel::ERROR,
                                'An error occured while doing the inRiver Callback: ' .
                                'Http Error Code: ' . $response->getStatusCode() .
                                ' Error Message: ' . $response->getReasonPhrase()
                            );
                        }
                    }
                }
            } else {
                $this->logger->log(
                    LogLevel::ERROR,
                    'An error occured while doing inRiver Callback: ' .
                    'Your inRiver API key is missing in your configuration, see documentation for more information'
                );
            }
        } catch (\Exception $ex) {
            $this->logger->error('An error occured while doing inRiver Callback: ' . $ex->getMessage());
        }
    }

    /**
     * @param string $apiKey
     * @param string $url
     * @param string $message
     *
     * @return \GuzzleHttp\Psr7\Response
     */
    public function sendResponse(string $apiKey, string $url, string $message): Response
    {
        $params = [
            'headers' => [
                self::RESPONSE_HEADER_INRIVER_APIKEY => $apiKey,
                'Content-Type' => 'application/json',
            ],
        ];
        $params['json'] =
            [['fieldTypeId' => 'RuntimeResult', 'value' => $message]];

        return $this->httpRequest->sendRequest(
            $url,
            $params
        );
    }

    /**
     * @param \Inriver\Adapter\Api\Data\CallbackOperationInterface[] $callbackOperations
     * @param \Inriver\Adapter\Api\Data\CallbackInterface $callback
     *
     * @return string
     */
    private function createResultMessage(array $callbackOperations, CallbackInterface $callback): string
    {
        $rowsWithError = 0;
        $messages = [];

        $reportItems = [];

        foreach ($callbackOperations as $callbackOperation) {
            if ($callbackOperation->getErrorCount() > 0) {
                $rowsWithError++;
            }

            $operationMessages = $callbackOperation->getMessages();

            if (is_string($operationMessages) && $operationMessages !== '') {
                //php json encode remove index if it begin with 0 even if it's string, the space force it
                $key = $callbackOperation->getOperationKey() ? (string)$callbackOperation->getOperationKey() : " 0";
                $reportItems[$key] = $this->json->unserialize($operationMessages);
            }
        }

        $messages['total_operations'] = $callback->getNumberOfOperations();
        $messages['failed_operations'] = $rowsWithError;
        $messages[self::OPERATION_REPORT] = $reportItems;

        $messageEncoded = $this->json->serialize($messages);

        if ($reportItems === []) {
            $messageEncoded = str_replace(
                self::OPERATION_REPORT . '":[]',
                self::OPERATION_REPORT . '":{}',
                $messageEncoded
            );
        }

        return $messageEncoded;
    }

    public function getApiKey(): string
    {
        return $this->scopeConfig->getValue(self::INRIVER_API_KEY);
    }
}
