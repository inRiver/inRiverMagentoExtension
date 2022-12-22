<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Test\Unit\Model\Data;

use Inriver\Adapter\Model\Data\AppVersions;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;
use PHPUnit\Framework\TestCase;

class AppVersionsTest extends TestCase
{
    public const MAGENTO_VERSION = '2.3.4';
    public const MAGENTO_EDITION = 'Enterprise';
    public const MODULE_VERSION = '0.1.0';

    /** @var \Inriver\Adapter\Model\Data\AppVersions */
    private $model;

    /** @var \Magento\Framework\App\ProductMetadataInterface|\Inriver\Adapter\Test\Unit\Model\Data\MockObject */
    private $productMetadataMock;

    /** @var \Magento\Framework\Module\ModuleListInterface|\Inriver\Adapter\Test\Unit\Model\Data\MockObject */
    private $moduleListMock;

    public function testGetMagentoVersion(): void
    {
        $this->productMetadataMock->expects($this->once())->method('getVersion')->willReturn(self::MAGENTO_VERSION);
        $this->assertEquals(self::MAGENTO_VERSION, $this->model->getMagentoVersion());
    }

    public function testGetMagentoEdition(): void
    {
        $this->productMetadataMock->expects($this->once())->method('getEdition')->willReturn(self::MAGENTO_EDITION);
        $this->assertEquals(self::MAGENTO_EDITION, $this->model->getMagentoEdition());
    }

    public function testGetAdapterVersion(): void
    {
        /** @noinspection PhpParamsInspection */
        $this->moduleListMock
            ->expects($this->once())
            ->method('getOne')
            ->with(AppVersions::MODULE_NAME)
            ->willReturn(['setup_version' => self::MODULE_VERSION]);

        $this->assertEquals(self::MODULE_VERSION, $this->model->getAdapterVersion());
    }

    protected function setUp(): void
    {
        $this->productMetadataMock = $this->createMock(ProductMetadataInterface::class);
        $this->moduleListMock = $this->createMock(ModuleListInterface::class);

        $this->model = new AppVersions(
            $this->productMetadataMock,
            $this->moduleListMock
        );
    }
}
