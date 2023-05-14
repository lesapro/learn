<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/bAy-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\MaxCDN\Controller\Adminhtml\Cache;

use TIG\MaxCDN\Observer\PurgeAll;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Purge extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Backend::cache';

    /** @var PurgeAll $purge */
    private $purge;

    public function __construct(
        PurgeAll $purge,
        Action\Context $context
    )
    {
        $this->purge = $purge;
        parent::__construct(
            $context
        );
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('adminhtml/*/index');

        $purgedZones = $this->purge->purgeAllZones();

        /**
         * If extension is disabled, no zones are returned. So return to previous screen immediately.
         */
        if (!$purgedZones) {
            return $resultRedirect;
        }

        foreach ($purgedZones as $zone => $errorCode) {
            $this->purge->generateMessage($zone, $errorCode);
        }

        return $resultRedirect;
    }
}
