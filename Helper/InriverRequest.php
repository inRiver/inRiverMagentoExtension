<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Helper;

use Inriver\Adapter\Api\CallbackRepositoryInterface;
use Inriver\Adapter\Api\Data\CallbackInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Rest\Request;

/**
 * Class InriverRequest InriverRequest
 */
class InriverRequest
{
    public const CALLBACK_HEADER = 'x-inriver-callback';

    /** @var \Inriver\Adapter\Api\CallbackRepositoryInterface */
    private $callbackRepository;

    /** @var \Inriver\Adapter\Api\Data\CallbackInterfaceFactory */
    private $callbackFactory;

    /** @var \Magento\Framework\Webapi\Rest\Request */
    private $request;

    /**
     * @param \Inriver\Adapter\Api\CallbackRepositoryInterface $callbackRepository
     * @param \Inriver\Adapter\Api\Data\CallbackInterfaceFactory $callbackFactory
     * @param \Magento\Framework\Webapi\Rest\Request $request
     */
    public function __construct(
        CallbackRepositoryInterface $callbackRepository,
        CallbackInterfaceFactory $callbackFactory,
        Request $request
    ) {
        $this->callbackRepository = $callbackRepository;
        $this->callbackFactory = $callbackFactory;
        $this->request = $request;
    }

    /**
     * @param string $bulkUuid
     * @param int $operationsCount
     * @param string $topic
     *
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function captureCallBackUrlFromInriver(string $bulkUuid, int $operationsCount, string $topic): void
    {
        $callbackUrl = $this->request->getHeader(self::CALLBACK_HEADER);

        if ($callbackUrl && $bulkUuid !== '') {
            try {
                $callback = $this->callbackRepository->getByBulkUuid($bulkUuid);
            } catch (NoSuchEntityException $ex) {
                $callback = $this->callbackFactory->create();
                $callback->setBulkUuid($bulkUuid);
                $callback->setCallbackUrl($callbackUrl);
                $callback->setTopic($topic);
            }

            $callback->setNumberOfOperations($operationsCount);
            $this->callbackRepository->save($callback);
        }
    }
}
