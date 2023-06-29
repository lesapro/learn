<?php
namespace Smartwave\Filterproducts\Block\Home;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;
class LatestList extends \Magento\Catalog\Block\Product\ListProduct
{
    protected $_collection;
    protected $categoryRepository;
    protected $_resource;
    protected $cache;
    protected $cacheKey;
    protected $cacheLifetime;
    protected $storeManager;
    protected $serializer;
    public function __construct(\Magento\Catalog\Block\Product\Context $context, \Magento\Framework\Data\Helper\PostHelper $postDataHelper, \Magento\Catalog\Model\Layer\Resolver $layerResolver, CategoryRepositoryInterface $categoryRepository, \Magento\Framework\Url\Helper\Data $urlHelper, \Magento\Catalog\Model\ResourceModel\Product\Collection $collection, \Magento\Framework\App\ResourceConnection $resource, CacheInterface $cache, StoreManagerInterface $storeManager, SerializerInterface $serializer, array $data = [])
    {
        $this->categoryRepository = $categoryRepository;
        $this->_collection = $collection;
        $this->_resource = $resource;
        $this->cache = $cache;
        $this->cacheKey = 'latest_products_cache_store_' . $storeManager->getStore()
            ->getId();
        $this->cacheLifetime = 3600; // Cache lifetime in seconds (1 hour)
        $this->storeManager = $storeManager;
        $this->serializer = $serializer;

        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    }

    protected function _getProductCollection()
    {
        return $this->getProducts();
    }

    public function getProducts()
    {
        if ($cachedData = $this->getCacheData())
        {
            return $cachedData;
        }

        $count = $this->getProductCount();
        $category_id = $this->getData("category_id");
        $collection = clone $this->_collection;
        $collection->clear()
            ->getSelect()
            ->reset(\Magento\Framework\DB\Select::WHERE)
            ->reset(\Magento\Framework\DB\Select::ORDER)
            ->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)
            ->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET)
            ->reset(\Magento\Framework\DB\Select::GROUP);

        if (!$category_id)
        {
            $category_id = $this
                ->storeManager
                ->getStore()
                ->getRootCategoryId();
        }

        $category = $this
            ->categoryRepository
            ->get($category_id);
        if (isset($category) && $category)
        {
            $collection->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('small_image')
                ->addAttributeToSelect('thumbnail')
                ->addAttributeToSelect($this
                ->_catalogConfig
                ->getProductAttributes())
                ->addUrlRewrite()
                ->addCategoryFilter($category)->addAttributeToSort('created_at', 'desc');
        }
        else
        {
            $collection->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('small_image')
                ->addAttributeToSelect('thumbnail')
                ->addAttributeToSelect($this
                ->_catalogConfig
                ->getProductAttributes())
                ->addUrlRewrite()
                ->addAttributeToSort('created_at', 'desc');
        }

        $collection->getSelect()
            ->order('created_at', 'desc')
            ->limit($count);
        $this->saveCacheData($collection);

        return $collection;
    }
    public function getImages()
    {
        if ($cachedData = $this->getCacheData('images'))
        {
            return $cachedData;
        }

        $images = [];
        foreach ($collection as $product)
        {
            $images[$product->getId() ] = $product->getImage();
        }
        $this->saveCacheData($images);

        return $images;
    }

    protected function getCacheData()
    {
        $serializedData = $this
            ->cache
            ->load($this->cacheKey);
        if ($serializedData)
        {
            return $this
                ->serializer
                ->unserialize($serializedData);
        }
        return false;
    }

    protected function saveCacheData($data)
    {
        $serializedData = $this
            ->serializer
            ->serialize($data);
        $this
            ->cache
            ->save($serializedData, $this->cacheKey, [], $this->cacheLifetime);
    }

}
