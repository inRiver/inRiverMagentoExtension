<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Plugin;

use Inriver\Adapter\Helper\InriverRequest;
use Inriver\Adapter\Model\CallbackOperationRepository;
use Inriver\Adapter\Model\Data\CallbackOperationFactory;
use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\WebapiAsync\Model\OperationRepository;

use function filter_var;

/**
 * Class OperationRepositoryPlugin OperationRepositoryPlugin
 */
class OperationRepositoryPlugin
{
    /** @var \Magento\Framework\Webapi\Rest\Request */
    protected $request;

    /** @var \Inriver\Adapter\Model\CallbackOperationRepository */
    protected $callbackOperationRepository;

    /** @var \Inriver\Adapter\Model\Data\CallbackOperationFactory */
    protected $callbackOperationFactory;

    /**
     * @param \Magento\Framework\Webapi\Rest\Request $request
     * @param \Inriver\Adapter\Model\CallbackOperationRepository $callbackOperationRepository
     * @param \Inriver\Adapter\Model\Data\CallbackOperationFactory $callbackOperationFactory
     */
    public function __construct(
        Request $request,
        CallbackOperationRepository $callbackOperationRepository,
        CallbackOperationFactory $callbackOperationFactory
    ) {
        $this->request = $request;
        $this->callbackOperationRepository = $callbackOperationRepository;
        $this->callbackOperationFactory = $callbackOperationFactory;
    }

    /**
     * @param \Magento\WebapiAsync\Model\OperationRepository $subject
     * @param $result
     *
     * @return \Magento\AsynchronousOperations\Api\Data\OperationInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     *
     * @noinspection PhpUnusedParameterInspection
     */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function afterCreate(OperationRepository $subject, OperationInterface $result): OperationInterface
    {
        if ($result instanceof OperationInterface && $this->callbackUrlProvided()) {
            $operation = $this->callbackOperationFactory->create();
            $operation->setOperationId($result->getId());
            $this->callbackOperationRepository->save($operation);
        }

        return $result;
    }

    /**
     * Check if a valid callback url was provided
     *
     * @return bool
     */
    private function callbackUrlProvided(): bool
    {
        $callbackUrl = $this->request->getHeader(InriverRequest::CALLBACK_HEADER);

        return filter_var($callbackUrl, FILTER_VALIDATE_URL) !== false;
    }
}
