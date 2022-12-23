<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Data;

use Inriver\Adapter\Api\Data\OperationResultInterface;

class OperationResult implements OperationResultInterface
{
    /** @var string */
    private $errorCode = '';

    /** @var string */
    private $columnName = '';

    /** @var string */
    private $errorMessage;

    /** @var int[] */
    private $rowNumbers = [];

    /**
     * Get error code
     *
     * @return string|null
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * Set error code
     *
     * @param string $errorCode
     *
     * @return \Inriver\Adapter\Api\Data\OperationResultInterface
     */
    public function setErrorCode(string $errorCode): OperationResultInterface
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * Get column name
     *
     * @return string|null
     */
    public function getColumnName(): ?string
    {
        return $this->columnName;
    }

    /**
     * Set column name
     *
     * @param string|null $columnName
     *
     * @return \Inriver\Adapter\Api\Data\OperationResultInterface
     */
    public function setColumnName(?string $columnName): OperationResultInterface
    {
        $this->columnName = $columnName;

        return $this;
    }

    /**
     * Get error message
     *
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * Set error message
     *
     * @param string $errorMessage
     *
     * @return \Inriver\Adapter\Api\Data\OperationResultInterface
     */
    public function setErrorMessage(string $errorMessage): OperationResultInterface
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    /**
     * Get row numbers
     *
     * @return int[]
     */
    public function getRowNumbers(): array
    {
        return $this->rowNumbers;
    }

    /**
     * Set row numbers
     *
     * @param int[] $rowNumbers
     *
     * @return \Inriver\Adapter\Api\Data\OperationResultInterface
     */
    public function setRowNumbers(array $rowNumbers): OperationResultInterface
    {
        $this->rowNumbers = $rowNumbers;

        return $this;
    }

    /**
     * Add row number
     *
     * @param int $rowNumber
     *
     * @return \Inriver\Adapter\Api\Data\OperationResultInterface
     */
    public function addRowNumber(int $rowNumber): OperationResultInterface
    {
        $this->rowNumbers[] = $rowNumber;

        return $this;
    }
}
