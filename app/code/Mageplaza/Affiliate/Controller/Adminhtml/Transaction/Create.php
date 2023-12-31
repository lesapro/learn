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

namespace Mageplaza\Affiliate\Controller\Adminhtml\Transaction;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\Affiliate\Controller\Adminhtml\Transaction;

/**
 * Class Create
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Transaction
 */
class Create extends Transaction
{
    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $this->_forward('view');
    }
}
