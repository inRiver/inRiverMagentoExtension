<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api\Data;

interface ErrorCodeInterface
{
    /**
     * Get code
     *
     * @return int
     */
    public function getCode(): int;

    /**
     * Set code
     *
     * @param int $code
     *
     * @return \Inriver\Adapter\Api\Data\ErrorCodeInterface
     */
    public function setCode(int $code): ErrorCodeInterface;

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Set description
     *
     * @param string $description
     *
     * @return \Inriver\Adapter\Api\Data\ErrorCodeInterface
     */
    public function setDescription(string $description): ErrorCodeInterface;
}
