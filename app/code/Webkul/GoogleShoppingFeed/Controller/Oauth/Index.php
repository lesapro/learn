<?php

/**
 * Webkul GoogleShoppingFeed Oauth Index Controller
 * @category  Webkul
 * @package   Webkul_GoogleShoppingFeed
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\GoogleShoppingFeed\Controller\Oauth;

use Magento\Backend\Model\Session as CoreSession;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Backend\Model\UrlInterface;
use Webkul\GoogleShoppingFeed\Helper\Data as HelperData;
use Webkul\GoogleShoppingFeed\Logger\Logger;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    public $configWriter;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $dateTime;

    /**
     * @var \Webkul\GoogleShoppingFeed\Helper\Data
     */
    private $helperData;

    /**
     * @var \Webkul\GoogleShoppingFeed\Logger\Logger
     */
    private $logger;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param UrlInterface $urlBuilder
     * @param \Magento\Config\Model\ResourceModel\Config $configWriter
     * @param EncryptorInterface $encryptor
     * @param TimezoneInterface $dateTime
     * @param HelperData $helperData
     * @param Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        UrlInterface $urlBuilder,
        \Magento\Config\Model\ResourceModel\Config $configWriter,
        EncryptorInterface $encryptor,
        TimezoneInterface $dateTime,
        HelperData $helperData,
        Logger $logger
    ) {
        $this->resultFactory = $context->getResultFactory();
        $this->urlBuilder = $urlBuilder;
        $this->configWriter = $configWriter;
        $this->encryptor = $encryptor;
        $this->dateTime = $dateTime;
        $this->helperData = $helperData;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Execute
     */
    public function execute()
    {
        try {
            $data = $this->getRequest()->getParams();
            $this->logger->addError('google account validation - code85');
            if (isset($data['code'])) {
                $this->logger->addError('google account validation - code87');
                /** create google client */
                $config = $this->helperData->getConfigDetails();
                $client = new \Google_Client();
                $client->setApplicationName('Google Shopping Feed');
                $client->setClientId($config['oauth_consumer_key']);
                $client->setClientSecret($config['oauth_consumer_secret']);
                $redirectUrl = $this->urlBuilder->getBaseUrl().'googleshoppingfeed/oauth/index/';
                $client->setRedirectUri($redirectUrl);
                $this->logger->addError('google account validation - Google_Client');
                $accessToken = $client->authenticate($data['code']);
                if (!isset($accessToken['error_description'])) {
                    $this->logger->addError(json_encode($accessToken));
                    $gmtCurrectTime = $this->dateTime->convertConfigTimeToUtc($this->dateTime->date());
                    $accessTokenExpireOn = date(
                        'Y-m-d H:i:s',
                        strtotime('+'.($accessToken['expires_in']-10).' seconds', strtotime($gmtCurrectTime))
                    );
                    $this->logger->addError('google account validation - Google_Client'.json_encode($accessToken));
                    $path = 'googleshoppingfeed/general_settings/';
                    $this->configWriter->saveConfig(
                        $path.'oauth2_access_token',
                        $this->encryptor->encrypt($accessToken['access_token'])
                    );
                    $this->logger->addError('google account validation - access_token:'.$accessToken['access_token']);
                    $this->logger->addError('google account validation - refresh_token:'.$accessToken['refresh_token']);
                    $this->configWriter->saveConfig(
                        $path.'oauth2_refresh_token',
                        $this->encryptor->encrypt($accessToken['refresh_token'])
                    );
                    $this->logger->addError('google account validation - accessTokenExpireOn:'.$accessTokenExpireOn);
                    $this->configWriter->saveConfig($path.'oauth2_access_token_expire_on', $accessTokenExpireOn);
                    /** get merchant id*/
                    $serviceShoppingContent =  new \Google_Service_ShoppingContent($client);
                    $authInfo = $serviceShoppingContent->accounts->authinfo();
                    $authInfo = $authInfo->getAccountIdentifiers();
                    $authInfo = json_decode(json_encode($authInfo), true);
                    if (isset($authInfo[0]['merchantId'])) {
                        $this->configWriter->saveConfig($path.'merchant_id', $authInfo[0]['merchantId']);
                    }
                    $this->helperData->cleanConfigCache();
                    $closeWin = "<script>alert('"
                                .__('Google merchant center account successfully authorized.')
                                ."');window.close();</script>";
                    return $this->returnScript($closeWin);
                } else {
                    $this->logger->addError(json_encode($accessToken));
                    $closeWin = "<script>alert('".__($accessToken['error_description'])."');window.close();</script>";
                    return $this->returnScript($closeWin);
                }
            } else {
                $closeWin = "<script>alert('"
                            .__('Google merchant center account did not authorize.')
                            ."');window.close();</script>";
                return $this->returnScript($closeWin);
            }
        } catch (\Exception $e) {
            $this->logger->addError('google account validation - '.$e->getMessage());
            $closeWin = "<script>alert('".$e->getMessage()."');window.close();</script>";
            return $this->returnScript($closeWin);
        }
    }

    /**
     * Return Script
     *
     * @param string $script
     * @return $resultPage
     */
    private function returnScript($script)
    {
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_RAW);
        $resultPage->setHeader('Content-Type', 'text/html')->setContents($script);
        return $resultPage;
    }

    /**
     * Check product import permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_GoogleShoppingFeed::user_authenticate');
    }
}
