<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data;

use Inriver\Adapter\Api\Data\AppVersionsInterface;
use Inriver\Adapter\Api\Data\ValidationMessageInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;

/**
 * Class AppVersions
 * Class for service that returns the version of the app
 */
class ValidationMessage implements ValidationMessageInterface
{
    /** @var string */
    private $message;

    /** @var bool */
    private $isValid;

    public function setMessage(string $value)
    {
        $this->message = $value;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setIsValid(bool $value)
    {
        $this->isValid = $value;
    }

    public function getIsValid(): bool
    {
        return $this->isValid;
    }
}
