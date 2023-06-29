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

namespace Mageplaza\Affiliate\Block;

use Magento\Framework\View\Element\Html\Link;
use Magento\Framework\View\Element\Html\Links;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Affiliate\Helper\Data;

/**
 * Class Navigation
 * @package Mageplaza\Affiliate\Block
 */
class Navigation extends Links
{
    /**
     * @var Data
     */
    protected $_helper;

    /**
     * Navigation constructor.
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
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Get links
     *
     * @return Link[]
     */
    public function getLinks()
    {
        $links = $this->_layout->getChildBlocks($this->getNameInLayout());

        $isGuest = true;
        $account = $this->_helper->getCurrentAffiliate();
        if ($account && $account->getId() && $account->isActive()) {
            $isGuest = false;
        }

        foreach ($links as $key => $block) {
            if (($isGuest && ($block->getActive() == 'login')) || (!$isGuest && ($block->getActive() == 'guess'))) {
                unset($links[$key]);
            }
        }

        usort($links, function ($a, $b) {
            return $a->getSortOrder() - $b->getSortOrder();
        });

        return $links;
    }
}
