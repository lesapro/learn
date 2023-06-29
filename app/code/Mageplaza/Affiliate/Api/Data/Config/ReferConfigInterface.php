<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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

namespace Mageplaza\Affiliate\Api\Data\Config;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ReferConfigInterface
 * @api
 */
interface ReferConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENABLE = 'enable';

    const ACCOUNT_SHARING = 'account_sharing';

    const DEFAULT_LINK = 'default_link';

    const SHARING_CONTENT = 'sharing_content';

    /**
     * @return bool
     */
    public function getEnable();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setEnable($value);

    /**
     * @return string
     */
    public function getAccountSharing();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setAccountSharing($value);

    /**
     * @return string
     */
    public function getDefaultLink();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDefaultLink($value);

    /**
     * @return \Mageplaza\Affiliate\Api\Data\Config\ReferSharingContentInterface
     */
    public function getSharingContent();

    /**
     * @param \Mageplaza\Affiliate\Api\Data\Config\ReferSharingContentInterface $referSharingContent
     *
     * @return \Mageplaza\Affiliate\Api\Data\Config\ReferSharingContentInterface
     */
    public function setSharingContent(\Mageplaza\Affiliate\Api\Data\Config\ReferSharingContentInterface $referSharingContent);
}
