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

namespace Mageplaza\Affiliate\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Mageplaza\Affiliate\Model\GroupFactory;

/**
 * Class Group
 * @package Mageplaza\Affiliate\Model\Config\Source
 */
class Group implements ArrayInterface
{
    /**
     * @var GroupFactory
     */
    protected $_groupFactory;

    /**
     * @var
     */
    protected $_options;

    /**
     * Group constructor.
     *
     * @param GroupFactory $groupFactory
     */
    public function __construct(GroupFactory $groupFactory)
    {
        $this->_groupFactory = $groupFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $this->_options = [];
        $groupModel = $this->_groupFactory->create();
        $groupCollection = $groupModel->getCollection();
        foreach ($groupCollection as $item) {
            $data['value'] = $item->getId();
            $data['label'] = $item->getName();
            $this->_options[] = $data;
        }

        return $this->_options;
    }
}
