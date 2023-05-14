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
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\MaxCDN\Observer;

use TIG\MaxCDN\Model\Api;
use Magento\Framework\Event\ObserverInterface;

class PurgeAll extends Api implements ObserverInterface {

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return array
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $purgedZones = $this->purgeAllZones();

        if(!$purgedZones) {
            return;
        }

        foreach ($purgedZones as $zone => $errorCode) {
            $messages[$zone] = $this->generateMessage($zone, $errorCode);
        }

        return $messages;
    }

    /**
     * @return array
     */
    public function purgeAllZones() {
        if (!$this->maxCdnFactory->getIsEnabled()) {
            return;
        };

        $api = $this->getConnection();

        if ($api->alias == null || $api->key == null || $api->secret == null) {
            $this->getMessageManager()->addNoticeMessage(__('You haven\'t entered all required information to connect to the MaxCDN API.'));

            return;
        }

        $zones = json_decode($api->get('/zones.json'))->data->zones;

        foreach ($zones as $zone) {
            $status[$zone->id] = $this->purgeZone($zone);
        }

        return $status;
    }

    /**
     * @param $zone
     *
     * @return int|\Exception
     */
    public function purgeZone($zone) {
        if (!$this->maxCdnFactory->getIsEnabled()) {
            return;
        };

        $api = $this->getConnection();

        $zoneId = $zone->id;

        try {
            $status = $api->delete('/zones/pull.json/' . $zoneId . '/cache');
        } catch (\Exception $e) {
            $status = $e;
        }

        return $status;
    }

    /**
     * @param $zone
     * @param $errorCode
     *
     * @return \Magento\Framework\Message\ManagerInterface
     */
    public function generateMessage($zone, $errorCode) {
        $code = (int)json_decode($errorCode)->code;

        if ($code !== 200) {
            return $this->getMessageManager()->addErrorMessage(__('MaxCDN Pull Zone [ID: ' . $zone . '] not purged. Error code:') . ' ' . $code);
        }

        return $this->getMessageManager()->addSuccessMessage(__('MaxCDN Pull Zone [ID: ' . $zone . '] purged successfully.'));
    }
}
