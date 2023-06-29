<?php
/**
 * GoogleShoppingFeed Admin Product Profiler Block.
 * @category  Webkul
 * @package   Webkul_GoogleShoppingFeed
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Block\Adminhtml\FeedMap;

use Magento\Framework\Exception\LocalizedException;

class Profiler extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Webkul\GoogleShoppingFeed\Model\GoogleFeedMapFactory
     */
    private $googleFeedMap;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Webkul\GoogleShoppingFeed\Model\GoogleFeedMapFactory $googleFeedMap
     * @param array $data = []
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Webkul\GoogleShoppingFeed\Model\GoogleFeedMapFactory $googleFeedMap,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->googleFeedMap = $googleFeedMap;
    }

    /**
     * For get total imported product count.
     *
     * @return int
     */
    public function getImportedProduct()
    {
        try {
            $mappedPro = $this->googleFeedMap->create()->getCollection()->getColumnValues('mage_pro_id');
            $mappedPro = empty($mappedPro) ? [0] : $mappedPro;
            $searchCriteria = $this->searchCriteriaBuilder->addFilter('visibility', 1, 'neq')
                                      //->addFilter('type_id', ['grouped', 'bundle'], 'nin')
                                      ->addFilter('entity_id', $mappedPro, 'nin')->create();
            $productsList = $this->productRepository->getList($searchCriteria)->getItems();
            return count($productsList);
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * Get CreateProUrl
     *
     * @return string
     */
    public function getCreateProUrl()
    {
        return $this->getUrl('googleshoppingfeed/products/export');
    }
}
