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

namespace Mageplaza\Affiliate\Block\Html;

use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Affiliate\Helper\Data;

/**
 * Class Link
 * @package Mageplaza\Affiliate\Block\Html
 */
class Link extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Link constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        $type = $this->getType();
        if (strpos($this->helper->showAffiliateLinkOn(), $type) !== false) {
            return parent::_toHtml();
        }

        return '';
    }
}
