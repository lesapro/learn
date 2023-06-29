<?php
namespace TBH\BuyNow\Plugin;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Cart
{
    /**
     * @var UrlInterface
     */
    protected $_url;
    /**
     * @var Http
     */
    protected $request;
    /**
     * @var
     */
    protected $helperdata;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;


    /**
     * @param UrlInterface $url
     * @param Http $request
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        UrlInterface $url,
        Http $request,
        StoreManagerInterface $storeManager
    ) {
        $this->_url = $url;
        $this->request = $request;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $subject
     * @param $productInfo
     * @param $requestInfo
     * @return array
     * @throws NoSuchEntityException
     */
    public function beforeAddProduct($subject, $productInfo, $requestInfo = null)
    {
        $cartrtnurl =   $this->storeManager->getStore()->getBaseUrl()."checkout/";

        if($cartrtnurl != '' && isset($cartrtnurl) && $this->request->getParam('is-buy-now'))
        {
            $accUrl = $this->_url->getUrl($cartrtnurl);

            $this->request->setParam('return_url', $accUrl);
        }
        return [$productInfo, $requestInfo];
    }
}
