<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api\Data;

/**
 * Interface AppVersionsInterface
 */
interface ValidationMessageInterface
{
    /**
     * @param string $value
     * @return void
     */
    public function setMessage(string $value);

    /**
     * Validation message
     *
     * @return string
     */
    public function getMessage(): string;


    /**
     * @param bool $value
     * @return void
     */
    public function setIsValid(bool $value);

    /**
     * return validation status
     *
     * @return bool
     */
    public function getIsValid(): bool;
}
