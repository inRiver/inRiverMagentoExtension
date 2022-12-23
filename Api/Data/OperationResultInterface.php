<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Api\Data;

/**
 * Interface OperationResultInterface
 */
interface OperationResultInterface
{
    /**
     * Get error code
     *
     * @return string|null
     */
    public function getErrorCode(): ?string;

    /**
     * Set error code
     *
     * @param string $errorCode
     *
     * @return \Inriver\Adapter\Api\Data\OperationResultInterface
     */
    public function setErrorCode(string $errorCode): OperationResultInterface;

    /**
     * Get column name
     *
     * @return string|null
     */
    public function getColumnName(): ?string;

    /**
     * Set column name
     *
     * @param string|null $columnName
     *
     * @return \Inriver\Adapter\Api\Data\OperationResultInterface
     */
    public function setColumnName(?string $columnName): OperationResultInterface;

    /**
     * Get error message
     *
     * @return string
     */
    public function getErrorMessage(): ?string;

    /**
     * Set error message
     *
     * @param string $errorMessage
     *
     * @return \Inriver\Adapter\Api\Data\OperationResultInterface
     */
    public function setErrorMessage(string $errorMessage): OperationResultInterface;

    /**
     * Get row numbers
     *
     * @return int[]
     */
    public function getRowNumbers(): array;

    /**
     * Set row numbers
     *
     * @param int[] $rowNumbers
     *
     * @return \Inriver\Adapter\Api\Data\OperationResultInterface
     */
    public function setRowNumbers(array $rowNumbers): OperationResultInterface;

    /**
     * Add row number
     *
     * @param int $rowNumber
     *
     * @return \Inriver\Adapter\Api\Data\OperationResultInterface
     */
    public function addRowNumber(int $rowNumber): OperationResultInterface;
}
