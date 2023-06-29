<?php

namespace Fovoso\ImportProducts\Model;

/**
 * Class ReviewUpdater
 * @package Fovoso\ImportProducts\Model
 */
class ReviewUpdater
{
    /**
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @var \Magento\Review\Model\RatingFactory
     */
    protected $ratingFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $connection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * ReviewUpdater constructor.
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param \Magento\Review\Model\RatingFactory $ratingFactory
     * @param \Magento\Framework\App\ResourceConnection $connection
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Framework\App\ResourceConnection $connection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->reviewFactory = $reviewFactory;
        $this->ratingFactory = $ratingFactory;
        $this->connection = $connection;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $dataComment
     */
    public function createReview($dataComment)
    {
        foreach ($dataComment as $commentData) {
            $isUpdate = $commentData['is_update'];
            $productId = $this->getProductBySku($commentData['sku']);
            $reviewData = $commentData['comments'];
            if ($isUpdate) {
                if ($this->scopeConfig->isSetFlag('import_product/comment/replace_comment')) {
                    $this->connection->getConnection()->delete(
                        $this->connection->getTableName('review'),
                        ['entity_pk_value = ?' => $productId]
                    );
                } else {
                    return;
                }
            }
            $stores = $this->storeManager->getStores();
            $storeIds = [];
            foreach ($stores as $store) {
                $storeIds[] = $store->getId();
            }
            if (is_array($reviewData) && count($reviewData) > 0) {
                foreach ($reviewData as $reviewDatum) {
                    $reviewFinalData['ratings'][1] = $reviewDatum['rating'];
                    $reviewFinalData['nickname'] = $reviewDatum['nickName'];
                    $reviewFinalData['title'] = $reviewDatum['title'];
                    $reviewFinalData['detail'] = $reviewDatum['detail'];
                    $review = $this->reviewFactory->create()->setData($reviewFinalData);
                    $review->unsetData('review_id');
                    $review->setEntityId($review->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE))
                        ->setEntityPkValue($productId)
                        ->setStatusId(\Magento\Review\Model\Review::STATUS_APPROVED)
                        ->setStoreId(0)
                        ->setStores($storeIds)
                        ->save();

                    foreach ($reviewFinalData['ratings'] as $ratingId => $optionId) {
                        $this->ratingFactory->create()
                            ->setRatingId($ratingId)
                            ->setReviewId($review->getId())
                            ->addOptionVote($optionId, $productId);
                    }

                    $review->aggregate();
                    $reviewId = $review->getId();
                    if (!empty($reviewDatum['images'])) {
                        foreach ($reviewDatum['images'] as $imagePath) {
                            $this->connection->getConnection()->insert(
                                $this->connection->getTableName('rltsquare_productreviewimages_reviewmedia'),
                                [
                                    'review_id' => $reviewId,
                                    'media_url' => $imagePath
                                ]
                            );
                        }
                    }
                    if (isset($reviewDatum['created_at'])) {
                        $createdAt = date("Y-m-d H:i:s", strtotime($reviewDatum['created_at']));
                        $this->connection->getConnection()->update(
                            $this->connection->getTableName('review'),
                            [
                                'created_at' => $createdAt
                            ],
                            ['review_id = ?' => $reviewId]
                        );
                    }
                }
            }
        }
    }

    /**
     * @param $sku
     * @return string
     */
    public function getProductBySku($sku)
    {
        $select = $this->connection->getConnection()
            ->select()->from(
                $this->connection->getTableName('catalog_product_entity'),
                'entity_id'
            )->where('sku = ?', $sku);
        return $this->connection->getConnection()->fetchOne($select);
    }
}
