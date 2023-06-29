<?php
namespace Magento\Tax\Model\Config;

/**
 * Interceptor class for @see \Magento\Tax\Model\Config
 */
class Interceptor extends \Magento\Tax\Model\Config implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->___init();
        parent::__construct($scopeConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function priceIncludesTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'priceIncludesTax');
        return $pluginInfo ? $this->___callPlugins('priceIncludesTax', func_get_args(), $pluginInfo) : parent::priceIncludesTax($store);
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceIncludesTax($value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setPriceIncludesTax');
        return $pluginInfo ? $this->___callPlugins('setPriceIncludesTax', func_get_args(), $pluginInfo) : parent::setPriceIncludesTax($value);
    }

    /**
     * {@inheritdoc}
     */
    public function applyTaxAfterDiscount($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'applyTaxAfterDiscount');
        return $pluginInfo ? $this->___callPlugins('applyTaxAfterDiscount', func_get_args(), $pluginInfo) : parent::applyTaxAfterDiscount($store);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceDisplayType($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getPriceDisplayType');
        return $pluginInfo ? $this->___callPlugins('getPriceDisplayType', func_get_args(), $pluginInfo) : parent::getPriceDisplayType($store);
    }

    /**
     * {@inheritdoc}
     */
    public function discountTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'discountTax');
        return $pluginInfo ? $this->___callPlugins('discountTax', func_get_args(), $pluginInfo) : parent::discountTax($store);
    }

    /**
     * {@inheritdoc}
     */
    public function getCalculationSequence($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCalculationSequence');
        return $pluginInfo ? $this->___callPlugins('getCalculationSequence', func_get_args(), $pluginInfo) : parent::getCalculationSequence($store);
    }

    /**
     * {@inheritdoc}
     */
    public function setNeedUseShippingExcludeTax($flag)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setNeedUseShippingExcludeTax');
        return $pluginInfo ? $this->___callPlugins('setNeedUseShippingExcludeTax', func_get_args(), $pluginInfo) : parent::setNeedUseShippingExcludeTax($flag);
    }

    /**
     * {@inheritdoc}
     */
    public function getNeedUseShippingExcludeTax()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getNeedUseShippingExcludeTax');
        return $pluginInfo ? $this->___callPlugins('getNeedUseShippingExcludeTax', func_get_args(), $pluginInfo) : parent::getNeedUseShippingExcludeTax();
    }

    /**
     * {@inheritdoc}
     */
    public function getAlgorithm($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAlgorithm');
        return $pluginInfo ? $this->___callPlugins('getAlgorithm', func_get_args(), $pluginInfo) : parent::getAlgorithm($store);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingTaxClass($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getShippingTaxClass');
        return $pluginInfo ? $this->___callPlugins('getShippingTaxClass', func_get_args(), $pluginInfo) : parent::getShippingTaxClass($store);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingPriceDisplayType($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getShippingPriceDisplayType');
        return $pluginInfo ? $this->___callPlugins('getShippingPriceDisplayType', func_get_args(), $pluginInfo) : parent::getShippingPriceDisplayType($store);
    }

    /**
     * {@inheritdoc}
     */
    public function shippingPriceIncludesTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'shippingPriceIncludesTax');
        return $pluginInfo ? $this->___callPlugins('shippingPriceIncludesTax', func_get_args(), $pluginInfo) : parent::shippingPriceIncludesTax($store);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingPriceIncludeTax($flag)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setShippingPriceIncludeTax');
        return $pluginInfo ? $this->___callPlugins('setShippingPriceIncludeTax', func_get_args(), $pluginInfo) : parent::setShippingPriceIncludeTax($flag);
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartPricesInclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartPricesInclTax');
        return $pluginInfo ? $this->___callPlugins('displayCartPricesInclTax', func_get_args(), $pluginInfo) : parent::displayCartPricesInclTax($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartPricesExclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartPricesExclTax');
        return $pluginInfo ? $this->___callPlugins('displayCartPricesExclTax', func_get_args(), $pluginInfo) : parent::displayCartPricesExclTax($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartPricesBoth($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartPricesBoth');
        return $pluginInfo ? $this->___callPlugins('displayCartPricesBoth', func_get_args(), $pluginInfo) : parent::displayCartPricesBoth($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartSubtotalInclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartSubtotalInclTax');
        return $pluginInfo ? $this->___callPlugins('displayCartSubtotalInclTax', func_get_args(), $pluginInfo) : parent::displayCartSubtotalInclTax($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartSubtotalExclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartSubtotalExclTax');
        return $pluginInfo ? $this->___callPlugins('displayCartSubtotalExclTax', func_get_args(), $pluginInfo) : parent::displayCartSubtotalExclTax($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartSubtotalBoth($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartSubtotalBoth');
        return $pluginInfo ? $this->___callPlugins('displayCartSubtotalBoth', func_get_args(), $pluginInfo) : parent::displayCartSubtotalBoth($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartShippingInclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartShippingInclTax');
        return $pluginInfo ? $this->___callPlugins('displayCartShippingInclTax', func_get_args(), $pluginInfo) : parent::displayCartShippingInclTax($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartShippingExclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartShippingExclTax');
        return $pluginInfo ? $this->___callPlugins('displayCartShippingExclTax', func_get_args(), $pluginInfo) : parent::displayCartShippingExclTax($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartShippingBoth($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartShippingBoth');
        return $pluginInfo ? $this->___callPlugins('displayCartShippingBoth', func_get_args(), $pluginInfo) : parent::displayCartShippingBoth($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartDiscountInclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartDiscountInclTax');
        return $pluginInfo ? $this->___callPlugins('displayCartDiscountInclTax', func_get_args(), $pluginInfo) : parent::displayCartDiscountInclTax($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartDiscountExclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartDiscountExclTax');
        return $pluginInfo ? $this->___callPlugins('displayCartDiscountExclTax', func_get_args(), $pluginInfo) : parent::displayCartDiscountExclTax($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartDiscountBoth($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartDiscountBoth');
        return $pluginInfo ? $this->___callPlugins('displayCartDiscountBoth', func_get_args(), $pluginInfo) : parent::displayCartDiscountBoth($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartTaxWithGrandTotal($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartTaxWithGrandTotal');
        return $pluginInfo ? $this->___callPlugins('displayCartTaxWithGrandTotal', func_get_args(), $pluginInfo) : parent::displayCartTaxWithGrandTotal($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartFullSummary($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartFullSummary');
        return $pluginInfo ? $this->___callPlugins('displayCartFullSummary', func_get_args(), $pluginInfo) : parent::displayCartFullSummary($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displayCartZeroTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displayCartZeroTax');
        return $pluginInfo ? $this->___callPlugins('displayCartZeroTax', func_get_args(), $pluginInfo) : parent::displayCartZeroTax($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesPricesInclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesPricesInclTax');
        return $pluginInfo ? $this->___callPlugins('displaySalesPricesInclTax', func_get_args(), $pluginInfo) : parent::displaySalesPricesInclTax($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesPricesExclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesPricesExclTax');
        return $pluginInfo ? $this->___callPlugins('displaySalesPricesExclTax', func_get_args(), $pluginInfo) : parent::displaySalesPricesExclTax($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesPricesBoth($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesPricesBoth');
        return $pluginInfo ? $this->___callPlugins('displaySalesPricesBoth', func_get_args(), $pluginInfo) : parent::displaySalesPricesBoth($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesSubtotalInclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesSubtotalInclTax');
        return $pluginInfo ? $this->___callPlugins('displaySalesSubtotalInclTax', func_get_args(), $pluginInfo) : parent::displaySalesSubtotalInclTax($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesSubtotalExclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesSubtotalExclTax');
        return $pluginInfo ? $this->___callPlugins('displaySalesSubtotalExclTax', func_get_args(), $pluginInfo) : parent::displaySalesSubtotalExclTax($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesSubtotalBoth($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesSubtotalBoth');
        return $pluginInfo ? $this->___callPlugins('displaySalesSubtotalBoth', func_get_args(), $pluginInfo) : parent::displaySalesSubtotalBoth($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesShippingInclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesShippingInclTax');
        return $pluginInfo ? $this->___callPlugins('displaySalesShippingInclTax', func_get_args(), $pluginInfo) : parent::displaySalesShippingInclTax($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesShippingExclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesShippingExclTax');
        return $pluginInfo ? $this->___callPlugins('displaySalesShippingExclTax', func_get_args(), $pluginInfo) : parent::displaySalesShippingExclTax($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesShippingBoth($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesShippingBoth');
        return $pluginInfo ? $this->___callPlugins('displaySalesShippingBoth', func_get_args(), $pluginInfo) : parent::displaySalesShippingBoth($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesDiscountInclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesDiscountInclTax');
        return $pluginInfo ? $this->___callPlugins('displaySalesDiscountInclTax', func_get_args(), $pluginInfo) : parent::displaySalesDiscountInclTax($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesDiscountExclTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesDiscountExclTax');
        return $pluginInfo ? $this->___callPlugins('displaySalesDiscountExclTax', func_get_args(), $pluginInfo) : parent::displaySalesDiscountExclTax($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesDiscountBoth($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesDiscountBoth');
        return $pluginInfo ? $this->___callPlugins('displaySalesDiscountBoth', func_get_args(), $pluginInfo) : parent::displaySalesDiscountBoth($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesTaxWithGrandTotal($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesTaxWithGrandTotal');
        return $pluginInfo ? $this->___callPlugins('displaySalesTaxWithGrandTotal', func_get_args(), $pluginInfo) : parent::displaySalesTaxWithGrandTotal($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesFullSummary($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesFullSummary');
        return $pluginInfo ? $this->___callPlugins('displaySalesFullSummary', func_get_args(), $pluginInfo) : parent::displaySalesFullSummary($store);
    }

    /**
     * {@inheritdoc}
     */
    public function displaySalesZeroTax($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'displaySalesZeroTax');
        return $pluginInfo ? $this->___callPlugins('displaySalesZeroTax', func_get_args(), $pluginInfo) : parent::displaySalesZeroTax($store);
    }

    /**
     * {@inheritdoc}
     */
    public function crossBorderTradeEnabled($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'crossBorderTradeEnabled');
        return $pluginInfo ? $this->___callPlugins('crossBorderTradeEnabled', func_get_args(), $pluginInfo) : parent::crossBorderTradeEnabled($store);
    }

    /**
     * {@inheritdoc}
     */
    public function isWrongApplyDiscountSettingIgnored($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isWrongApplyDiscountSettingIgnored');
        return $pluginInfo ? $this->___callPlugins('isWrongApplyDiscountSettingIgnored', func_get_args(), $pluginInfo) : parent::isWrongApplyDiscountSettingIgnored($store);
    }

    /**
     * {@inheritdoc}
     */
    public function isWrongDisplaySettingsIgnored($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isWrongDisplaySettingsIgnored');
        return $pluginInfo ? $this->___callPlugins('isWrongDisplaySettingsIgnored', func_get_args(), $pluginInfo) : parent::isWrongDisplaySettingsIgnored($store);
    }

    /**
     * {@inheritdoc}
     */
    public function isWrongDiscountSettingsIgnored($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isWrongDiscountSettingsIgnored');
        return $pluginInfo ? $this->___callPlugins('isWrongDiscountSettingsIgnored', func_get_args(), $pluginInfo) : parent::isWrongDiscountSettingsIgnored($store);
    }

    /**
     * {@inheritdoc}
     */
    public function getInfoUrl($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getInfoUrl');
        return $pluginInfo ? $this->___callPlugins('getInfoUrl', func_get_args(), $pluginInfo) : parent::getInfoUrl($store);
    }

    /**
     * {@inheritdoc}
     */
    public function needPriceConversion($store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'needPriceConversion');
        return $pluginInfo ? $this->___callPlugins('needPriceConversion', func_get_args(), $pluginInfo) : parent::needPriceConversion($store);
    }
}
