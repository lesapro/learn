<?php

namespace Algolia\AlgoliaSearch\Model;

use Algolia\AlgoliaSearch\Api\QueueArchiveRepositoryInterface;
use Algolia\AlgoliaSearch\Api\Data\QueueArchiveInterface;
use Algolia\AlgoliaSearch\Model\ResourceModel\QueueArchive as QueueArchiveResource;
use Algolia\AlgoliaSearch\Model\ResourceModel\QueueArchive\CollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

class QueueArchiveRepository implements QueueArchiveRepositoryInterface
{
    /**
     * @var QueueArchiveResource
     */
    private $resource;

    /**
     * @var QueueArchiveFactory
     */
    private $queueArchiveFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var SearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    public function __construct(
        QueueArchiveResource          $resource,
        QueueArchiveFactory           $queueArchiveFactory,
        CollectionFactory             $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory
    )
    {
        $this->resource = $resource;
        $this->queueArchiveFactory = $queueArchiveFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * Save queue archive
     *
     * @param QueueArchiveInterface $queueArchive
     * @return QueueArchiveInterface
     * @throws CouldNotSaveException
     */
    public function save(QueueArchiveInterface $queueArchive)
    {
        try {
            $this->resource->save($queueArchive);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $queueArchive;
    }

    /**
     * Retrieve queue archive by id
     *
     * @param int $id
     * @return QueueArchiveInterface
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        $queueArchive = $this->queueArchiveFactory->create();
        $this->resource->load($queueArchive, $id);
        if (!$queueArchive->getId()) {
            throw new NoSuchEntityException(__('The queue archive with the "%1" ID doesn\'t exist.', $id));
        }
        return $queueArchive;
    }

    /**
     * Retrieve queue archives matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * Delete queue archive
     *
     * @param QueueArchiveInterface $queueArchive
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(QueueArchiveInterface $queueArchive)
    {
        try {
            $this->resource->delete($queueArchive);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }
        return true;
    }

    /**
     * Delete queue archive by ID
     *
     * @param int $id
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }

}
