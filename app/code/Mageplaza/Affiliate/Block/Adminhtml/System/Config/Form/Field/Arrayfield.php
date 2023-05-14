<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Affiliate
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Affiliate\Block\Adminhtml\System\Config\Form\Field;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field as FormField;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Mageplaza\Affiliate\Helper\Payment as HelperData;
use Zend_Serializer_Exception;

/**
 * Class Arrayfield
 * @package Mageplaza\Affiliate\Block\Adminhtml\System\Config\Form\Field
 */
class Arrayfield extends FormField
{
    /**
     * @var
     */
    protected $element;

    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * Arrayfield constructor.
     *
     * @param Context $context
     * @param HelperData $paymentHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $paymentHelper,
        array $data = []
    ) {
        $this->helper = $paymentHelper;

        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->setTemplate('Mageplaza_Affiliate::system/config/array.phtml');
        parent::_construct();
    }

    /**
     * Return element html
     *
     * @param AbstractElement $element
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->element = $element;

        return $this->_toHtml();
    }

    /**
     * @return mixed
     */
    public function getHtmlid()
    {
        return $this->element->getHtmlId();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->element->getName();
    }

    /**
     * @return array
     * @throws Zend_Serializer_Exception
     */
    public function getArrayRows()
    {
        $arrayRows = [];

        if ($this->helper->getAllMethods()) {
            foreach ($this->helper->getAllMethods() as $key => $config) {
                $arrayRows[$key] = __($config['label']);
            }
        }

        return $arrayRows;
    }

    /**
     * @return array|mixed
     */
    public function getConfigData()
    {
        try {
            $config = $this->helper->getPaymentMethod();

            return $this->helper->unserialize($config);
        } catch (Exception $e) {
            return [];
        }
    }
}
