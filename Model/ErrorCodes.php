<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model;

use Inriver\Adapter\Api\Data\ErrorCodeInterfaceFactory;
use Inriver\Adapter\Api\ErrorCodesInterface;
use Inriver\Adapter\Helper\ErrorCodesDirectory;

class ErrorCodes implements ErrorCodesInterface
{
    /** @var \Inriver\Adapter\Helper\ErrorCodesDirectory */
    protected $errorCodesDirectory;

    /** @var \Inriver\Adapter\Api\Data\ErrorCodeInterfaceFactory */
    protected $errorCodeFactory;

    /**
     * @param \Inriver\Adapter\Helper\ErrorCodesDirectory $errorCodesDirectory
     * @param \Inriver\Adapter\Api\Data\ErrorCodeInterfaceFactory $errorCodeFactory
     */
    public function __construct(
        ErrorCodesDirectory $errorCodesDirectory,
        ErrorCodeInterfaceFactory $errorCodeFactory
    ) {
        $this->errorCodesDirectory = $errorCodesDirectory;
        $this->errorCodeFactory = $errorCodeFactory;
    }

    /**
     * Return error codes
     *
     * @return \Inriver\Adapter\Api\Data\ErrorCodeInterface[]
     */
    public function get(): array
    {
        $errors = [];

        foreach ($this->errorCodesDirectory->getErrorDescriptions() as $key => $description) {
            $errors[] = $this->errorCodeFactory->create()
                ->setCode($key)
                ->setDescription($description);
        }

        return $errors;
    }
}
