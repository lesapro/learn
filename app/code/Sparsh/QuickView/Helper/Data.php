<?php
/**
 * Class Data
 *
 * PHP version 7
 *
 * @category Sparsh
 * @package  Sparsh_QuickView
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\QuickView\Helper;

/**
 * Class Data
 *
 * @category Sparsh
 * @package  Sparsh_QuickView
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Return config value
     *
     * @param string $configPath configPath
     *
     * @return mixed
     */
    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
