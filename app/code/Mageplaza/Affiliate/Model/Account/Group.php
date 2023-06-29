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

namespace Mageplaza\Affiliate\Model\Account;

use Magento\Framework\Option\ArrayInterface;
use Mageplaza\Affiliate\Model\GroupFactory;

/**
 * Class Group
 * @package Mageplaza\Affiliate\Model\Account
 */
class Group implements ArrayInterface
{
    /**
     * @type GroupFactory
     */
    protected $group;

    /**
     * Group constructor.
     *
     * @param GroupFactory $groupFactory
     */
    public function __construct(GroupFactory $groupFactory)
    {
        $this->group = $groupFactory;
    }

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $group = $this->getGroupCollection();
        $options = [];
        foreach ($group as $item) {
            $options[] = [
                'value' => $item->getId(),
                'label' => $item->getName()
            ];
        }

        return $options;
    }

    /**
     * @return mixed
     */
    public function getGroupCollection()
    {
        $groupModel = $this->group->create();

        return $groupModel->getCollection();
    }
}
