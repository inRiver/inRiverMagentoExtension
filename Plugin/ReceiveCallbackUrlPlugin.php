<?php

/** @noinspection MessDetectorValidationInspection */

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Plugin;

use Inriver\Adapter\Helper\InriverRequest;
use Magento\AsynchronousOperations\Model\BulkManagement;

use function count;

/**
 * Class ReceiveCallbackUrlPlugin
 * Plugin to capture the URL sent in the bulk action so we can reply to inriver
 */
class ReceiveCallbackUrlPlugin
{
    /** @var \Inriver\Adapter\Helper\InriverRequest */
    private $inriverRequest;

    /**
     * @param \Inriver\Adapter\Helper\InriverRequest $inriverRequest
     */
    public function __construct(
        InriverRequest $inriverRequest
    ) {
        $this->inriverRequest = $inriverRequest;
    }

    /**
     * @param \Magento\AsynchronousOperations\Model\BulkManagement $subject
     * @param bool $result
     * @param string $bulkUuid
     * @param \Magento\Setup\Module\Di\App\Task\OperationInterface[] $operations
     * @param string $description
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function afterScheduleBulk(
        BulkManagement $subject, // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
        bool $result,
        string $bulkUuid,
        array $operations,
        string $description
    ): bool {
        $this->inriverRequest->captureCallBackUrlFromInriver($bulkUuid, count($operations), $description);

        return $result;
    }
}
