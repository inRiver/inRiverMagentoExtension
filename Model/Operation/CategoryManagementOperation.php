<?php

/**
 * @author InRiver <iif-magento@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\Operation;

use Inriver\Adapter\Api\CategoryManagementInterface;
use Inriver\Adapter\Logger\Logger;
use Magento\Catalog\Model\CategoryRepository;

use function __;

/**
 * Class ProductCategoriesOperation ProductCategoriesOperation
 */
class CategoryManagementOperation implements CategoryManagementInterface
{
    /** @var \Magento\Catalog\Model\CategoryRepository */
    private $categoryRepository;

    /** @var \Inriver\Adapter\Logger\Logger  */
    private $logger;

    /**
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     * @param \Inriver\Adapter\Logger\Logger $logger
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        Logger $logger
    ) {
        $this->logger = $logger;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * This is a copy of Magento\Catalog\Model\CategoryManagement::move() with the handling of exception modified
     * If you update magento, you have to validate that this function hasn't changed
     *
     * @param int $categoryId
     * @param int $parentId
     * @param int $afterId
     * @return bool
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function move($categoryId, $parentId, $afterId = null): bool
    {
        $model = $this->categoryRepository->get($categoryId);
        $parentCategory = $this->categoryRepository->get($parentId);

        if ($parentCategory->hasChildren()) {
            $parentChildren = $parentCategory->getChildren();
            $categoryIds = explode(',', $parentChildren);
            $lastId = array_pop($categoryIds);
            $afterId = ($afterId === null || $afterId > $lastId) ? $lastId : $afterId;
        }
        $parentPath = $parentCategory->getPath();
        $path = $model->getPath();
        if ($path && strpos($parentPath, $path) === 0) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Operation do not allow to move a parent category to any of children category')
            );
        }
        try {
            $model->move($parentId, $afterId);
        } catch (\Exception $e) {
            $message =  __('Could not move category: %1', $e->getMessage());
            $this->logger->addError($message);
            throw new \Magento\Framework\Exception\LocalizedException(__('Could not move category: %1', $e->getMessage()), $e);
        }
        return true;
    }
}
