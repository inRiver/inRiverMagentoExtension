<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data;

use Inriver\Adapter\Api\Data\ErrorCodeInterface;

/**
 * Class ErrorCode Error Code
 */
class ErrorCode implements ErrorCodeInterface
{
    /** @var int */
    private $code;

    /** @var string */
    private $description;

    /**
     * Get code
     *
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param int $code
     *
     * @return \Inriver\Adapter\Api\Data\ErrorCodeInterface
     */
    public function setCode(int $code): ErrorCodeInterface
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return \Inriver\Adapter\Api\Data\ErrorCodeInterface
     */
    public function setDescription(string $description): ErrorCodeInterface
    {
        $this->description = $description;

        return $this;
    }
}
