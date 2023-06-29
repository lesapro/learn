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

namespace Mageplaza\Affiliate\Model\Config\Source\Cms;

use Magento\Cms\Model\PageFactory;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class Page
 * @package Mageplaza\Affiliate\Model\Config\Source\Cms
 */
class Page implements ArrayInterface
{
    /**
     * @var PageFactory
     */
    protected $_cms;

    /**
     * @var
     */
    protected $_options;

    /**
     * Page constructor.
     *
     * @param PageFactory $pageFactory
     */
    public function __construct(PageFactory $pageFactory)
    {
        $this->_cms = $pageFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $this->_options = [];
        $existingIdentifiers = [];
        $cmsPage = $this->_cms->create();
        $cmsPageCollection = $cmsPage->getCollection();
        foreach ($cmsPageCollection as $item) {
            $identifier = $item->getData('identifier');

            $data['value'] = $identifier;
            $data['label'] = $item->getData('title');

            if (in_array($identifier, $existingIdentifiers)) {
                $data['value'] .= '|' . $item->getData('page_id');
            } else {
                $existingIdentifiers[] = $identifier;
            }

            $this->_options[] = $data;
        }

        return $this->_options;
    }
}
