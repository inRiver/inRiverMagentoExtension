<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api\Data;

use Magento\Framework\Phrase;

/**
 * Interface OperationErrorInterface
 */
interface OperationErrorInterface
{
    /**
     * Get error code
     *
     * @return int
     */
    public function getCode(): int;

    /**
     * Get error description
     *
     * @return string
     *
     * @noinspection PhpDocSignatureInspection
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function getDescription(): Phrase;

    /**
     * Get error as string
     *
     * @return string
     */
    public function __toString(): string;
}
