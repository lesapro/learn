<?php

namespace Fovoso\Shipping\Model;

use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Psr\Log\LoggerInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Fovoso\Shipping\Model\ResourceModel\ShippingFree\CollectionFactory;
use Magento\Directory\Model\CountryFactory;

/**
 * Class Carrier
 * @package Fovoso\Shipping\Model
 */
abstract class Carrier extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * {@inheritdoc}
     */
    protected $_isFixed = true;

    /**
     * @var null|array
     */
    protected $_rateRecord = null;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var CountryFactory
     */
    protected $countryFactory;

    /**
     * Constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param CollectionFactory $collectionFactory
     * @param CountryFactory $countryFactory
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        CollectionFactory $collectionFactory,
        CountryFactory $countryFactory,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->collectionFactory = $collectionFactory;
        $this->countryFactory = $countryFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }
    /**
     * {@inheritdoc}
     */
    public function collectRates(RateRequest $request)
    {
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();
        $rate = $this->getRate($request);

        if ($rate->getId()) {
            $subTotal = $request->getData('base_subtotal_with_discount_incl_tax');
            $freeShippingAmount = $this->_getConfig('free_ship');
            if ($subTotal > $freeShippingAmount) {
                $rate->setCosts(0);
            }
            $method = $this->_createShippingMethod($rate);
        } else {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Error $error */
            $method = $this->_rateErrorFactory->create([
                'data' => [
                    'carrier' => $this->_code,
                    'carrier_title' => $this->getConfigData('title'),
                    'error_message' => $this->getConfigData('specificerrmsg'),
                ]
            ]);
        }

        $result->append($method);

        return $result;
    }

    /**
     * @param $countryCode
     * @return string
     */
    public function getCountryName($countryCode)
    {
        $country = $this->countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }

    /**
     * @param RateRequest $request
     * @return array|null
     */
    public function getRate(RateRequest $request)
    {
        if ($this->_rateRecord == null) {
            $collection = $this->collectionFactory->create();
            $countryCode = $request->getDestCountryId();
            $countryName = $this->getCountryName($countryCode);
            $shippingMethod = $this->getShippingMethodName();
            $shippingMethod = $collection->addFieldToFilter('country', $countryName)
                ->addFieldToFilter('shipping_method', $shippingMethod)
                ->getFirstItem();
            $this->_rateRecord = $shippingMethod;
        }

        return $this->_rateRecord;
    }

    /**
     * @return string
     */
    public function getShippingMethodName()
    {
        if ($this->_code == Standard::CODE) {
            return 'Standard Shipping';
        }

        return 'Express Shipping';
    }

    /**
     * Get the method object based on the shipping price and cost
     *
     * @param $rate
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method
     */
    private function _createShippingMethod($rate)
    {
        /** @var  \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('name'));
        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('title'));
        $method->setData('method_description', $rate->getShippingTime());

        $method->setPrice($rate->getCosts());
        $method->setCost($rate->getCosts());

        return $method;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('title')];
    }

    /**
     * Get system config
     *
     * @param $field
     * @return false|string
     */
    private function _getConfig($field)
    {
        return $this->getConfigData($field);
    }
}
