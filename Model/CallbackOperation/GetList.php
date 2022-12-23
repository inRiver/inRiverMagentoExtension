<?php

/**
 * @author InRiver <inriveradapters@inriver.com>
 * @copyright Copyright (c) InRiver (https://www.inriver.com/)
 * @link https://www.inriver.com/
 */

declare(strict_types=1);

namespace Inriver\Adapter\Model\CallbackOperation;

use Inriver\Adapter\Model\ResourceModel\CallbackOperation\CollectionFactory as CallbackOperationCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;

/**
 * Class GetList
 * For retrieving lists of CallbackOperation model entities based on a given search criteria.
 */
class GetList
{
    /** @var \Inriver\Adapter\Model\CallbackOperation\SearchResultsInterfaceCallbackOperationCollectionFactory */
    private $callbackOperationCollectionFactory;

    /** @var \Magento\Framework\Api\SearchResultsInterfaceFactory */
    private $searchResultsFactory;

    /** @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface */
    private $collectionProcessor;

    //phpcs:ignoreFile Generic.Files.LineLength.Too.Long
    /**
     * @param \Inriver\Adapter\Model\ResourceModel\CallbackOperation\CollectionFactory $callbackOperationCollectionFactory
     * @param \Magento\Framework\Api\SearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        CallbackOperationCollectionFactory $callbackOperationCollectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->callbackOperationCollectionFactory = $callbackOperationCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * Returns the list of CallbackOperation for the specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria): SearchResultsInterface
    {
        /** @var \Magento\Framework\Api\SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        /** @var \Inriver\Adapter\Model\ResourceModel\CallbackOperation\Collection $collection */
        $collection = $this->callbackOperationCollectionFactory->create();
        $this->collectionProcessor->process($criteria, $collection);

        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }
}
