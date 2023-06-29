<?php
/**
 * Copyright © MagePal LLC. All rights reserved.
 * See COPYING.txt for license details.
 * http://www.magepal.com | support@magepal.com
 */

namespace MagePal\CustomShippingRate\Model;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Helper\Carrier as ShippingCarrierHelper;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use MagePal\CustomShippingRate\Helper\Data;
use Psr\Log\LoggerInterface;

class Carrier extends AbstractCarrier implements CarrierInterface
{
    /**
     * Code of the carrier
     *
     * @var string
     */
    const CODE = 'customshippingrate';

    /**
     * Code of the carrier
     *
     * @var string
     */
    protected $_code = self::CODE;

    /**
     *
     * @var MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * Carrier helper
     *
     * @var ShippingCarrierHelper
     */
    protected $_carrierHelper;

    /**
     * @var CollectionFactory
     */
    protected $_rateFactory;

    /**
     * @var State
     */
    protected $_state;

    /**
     * @var Data
     */
    protected $_customShippingRateHelper;
    private \Fovoso\ImportProducts\Model\ShippingFeeUpdater $shippingFeeUpdater;
    private \Magento\Catalog\Api\ProductRepositoryInterface $productRepository;
    private \Magento\Framework\Serialize\Serializer\Json $serializer;
    private \Magento\Framework\App\RequestInterface $request;
    private CheckoutSession $checkoutSession;
    private \Magento\Directory\Helper\Data $config;
    private \MagePal\CustomShippingRate\Model\CountryCookie $countryCookie;
    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateFactory
     * @param ShippingCarrierHelper $carrierHelper
     * @param MethodFactory $rateMethodFactory
     * @param State $state
     * @param Data $customShippingRateHelper
     * @param \Fovoso\ImportProducts\Model\ShippingFeeUpdater $shippingFeeUpdater
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param CheckoutSession $checkoutSession
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface                            $scopeConfig,
        ErrorFactory                                    $rateErrorFactory,
        LoggerInterface                                 $logger,
        ResultFactory                                   $rateFactory,
        ShippingCarrierHelper                           $carrierHelper,
        MethodFactory                                   $rateMethodFactory,
        State                                           $state,
        Data                                            $customShippingRateHelper,
        \Fovoso\ImportProducts\Model\ShippingFeeUpdater $shippingFeeUpdater,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\RequestInterface         $request,
        \Magento\Framework\Serialize\Serializer\Json    $serializer,
        CheckoutSession                                 $checkoutSession,
        \Magento\Directory\Helper\Data                  $config,
        \MagePal\CustomShippingRate\Model\CountryCookie $countryCookie,
        array                                           $data = []
    )
    {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->_scopeConfig = $scopeConfig;
        $this->_rateErrorFactory = $rateErrorFactory;
        $this->_logger = $logger;
        $this->_rateFactory = $rateFactory;
        $this->_carrierHelper = $carrierHelper;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_state = $state;
        $this->_customShippingRateHelper = $customShippingRateHelper;
        $this->productRepository = $productRepository;
        $this->shippingFeeUpdater = $shippingFeeUpdater;
        $this->request = $request;
        $this->serializer = $serializer;
        $this->checkoutSession = $checkoutSession;
        $this->config = $config;
        $this->countryCookie = $countryCookie;

    }

    /**
     * Collect and get rates
     *
     * @param RateRequest $request
     * @return Collection|Result
     * @throws LocalizedException
     */
    public function collectRates(RateRequest $request)
    {
        $result = $this->_rateFactory->create();

        if (!$this->getConfigFlag('active') || (!$this->isAdmin() && $this->hideShippingMethodOnFrontend())) {
            return $result;
        }
        $shops = [];
        if (isset($request['all_items']) && count($request['all_items']) > 0) {
            $items = $request['all_items'];
            $countryCode = $this->getCountrySelected();
            $country = $this->getCountryNameByCode($countryCode);
            foreach ($items as $item) {
                $sku = $item['product']['sku'];
                $product = $this->productRepository->get($sku);
                if (!empty($product->getShop()) && empty($item['parent_item_id'])) {
                    if (!isset($shops[$product->getShop()])) {
                        $list = $this->shippingFeeUpdater->getListShippingBySKU($product->getSku(), $country);
                        $shops[$product->getShop()] = $list;
                    }
                }
            }
        }


        foreach ($shops as $key => $shop) {
            if(isset($shop['items'][0])){
                foreach ($shop['items'][0]['time_shipping'] as $shippingType) {
                
                    $rate = $this->_rateMethodFactory->create();
                    if (isset($shippingType['cost']))
                    {
                        $price = $shippingType['cost'];
                    } else{
                        $price = 0;
                    }
                    // $price = $shippingType['cost'];
                    // if ($this->isFreeShip($shippingType)) {
                    //     $price = 0;
                    // }
                   
                    $rate->setCarrier($this->_code);
                    if (isset($shippingType['time']))
                    {
                        $rate->setCarrierTitle($shippingType['time']);
                    } 
                    if (isset($shippingType['method']))
                    {
                        $rate->setMethod($shippingType['method'] . '_' . $key);
                        $rate->setMethodTitle($shippingType['method']);
                    } 
                   
                    $rate->setCost($price);
                    $rate->setPrice($price);
                    $result->append($rate);
                }
            }
            
        }
        if ((str_contains($this->request->getRequestString(), '/shipping-information') && $this->request->getMethod() == 'POST') ||
            (str_contains($this->request->getRequestString(), '/set-payment-information') && $this->request->getMethod() == 'POST')) {
            if (!empty($this->checkoutSession->getQuote()->getRates())) {
                $rates = $this->serializer->unserialize($this->checkoutSession->getQuote()->getRates());
               
                foreach ($result->getAllRates() as $rate) {
                    if ($rate->getData('carrier') == $rates['carrier_code'] && $rate->getData('method') == $rates['method_code']) {
                        $amountShipping = 0;
                        foreach ($rates['items'] as $shop => $selected) {
                            $amountShipping += $selected['amount'];
                        }
                        $amountShipping = round($amountShipping, 2);
                        // $rate->setCost($amountShipping);
                        // $rate->setPrice($amountShipping);
                    }
                }
            }
        }
        return $result;
    }
    function is_serialized( $data, $strict = true ) {
        // If it isn't a string, it isn't serialized.
        if ( ! is_string( $data ) ) {
            return false;
        }
        $data = trim( $data );
        if ( 'N;' === $data ) {
            return true;
        }
        if ( strlen( $data ) < 4 ) {
            return false;
        }
        if ( ':' !== $data[1] ) {
            return false;
        }
        if ( $strict ) {
            $lastc = substr( $data, -1 );
            if ( ';' !== $lastc && '}' !== $lastc ) {
                return false;
            }
        } else {
            $semicolon = strpos( $data, ';' );
            $brace     = strpos( $data, '}' );
            // Either ; or } must exist.
            if ( false === $semicolon && false === $brace ) {
                return false;
            }
            // But neither must be in the first X characters.
            if ( false !== $semicolon && $semicolon < 3 ) {
                return false;
            }
            if ( false !== $brace && $brace < 4 ) {
                return false;
            }
        }
        $token = $data[0];
        switch ( $token ) {
            case 's':
                if ( $strict ) {
                    if ( '"' !== substr( $data, -2, 1 ) ) {
                        return false;
                    }
                } elseif ( false === strpos( $data, '"' ) ) {
                    return false;
                }
                // Or else fall through.
            case 'a':
            case 'O':
                return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
            case 'b':
            case 'i':
            case 'd':
                $end = $strict ? '$' : '';
                return (bool) preg_match( "/^{$token}:[0-9.E+-]+;$end/", $data );
        }
        return false;
    } 
    private function getCountrySelected(){
        
        if (!empty($this->request->getContent()) && !(str_contains($this->request->getRequestString(), '/checkout/cart/delete')) && !(str_contains($this->request->getRequestString(), '/checkout/sidebar/removeItem'))) {
            if($this->is_serialized($this->request->getContent())){
                $payload = $this->serializer->unserialize($this->request->getContent());
            }else{
                $payload = $this->request->getContent();
            }
            // $payload = $this->unserialize($this->request->getContent());
            if (isset($payload['address']) && isset($payload['address']['country_id'])) {
                $countryCode = $payload['address']['country_id'];
                $this->countryCookie->setPublicCookie(\MagePal\CustomShippingRate\Model\CountryCookie::COUNTRY_KEY, $countryCode);
                return $countryCode;
            }
        }
        return $this->config->getDefaultCountry();
    }
    private function getConfigStandShipping()
    {
        $path = 'carriers/' . $this->_code . '/' . 'freeshipstandard';

        return $this->_scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    private function getConfigExpressShipping()
    {
        $path = 'carriers/' . $this->_code . '/' . 'freeshipexpress';

        return $this->_scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    private function isFreeShip($rate)
    {
        if (isset($rate['method']))
        {
            $method = $rate['method'];
        } else{
            $method = '';
        }
        $total = $this->checkoutSession->getQuote()->getGrandTotal();
        if ($method == 'Standard Shipping') {
            $freeShippingAmount = $this->getConfigStandShipping();
            if ($total > $freeShippingAmount) {
                return true;
            }
        } else if ($method == 'Express Shipping') {
            $freeShippingAmount = $this->getConfigExpressShipping();
            if ($total > $freeShippingAmount) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        $result = [];
        foreach ($this->_customShippingRateHelper->getShippingType() as $shippingType) {
            $result[$shippingType['code']] = $shippingType['title'];
        }
        return $result;
    }

    /**
     * @return bool
     */
    public function isTrackingAvailable()
    {
        return false;
    }

    /**
     * @return bool
     * @throws LocalizedException
     */
    protected function hideShippingMethodOnFrontend()
    {
        return !$this->getConfigFlag('show_on_frontend');
    }

    /**
     * @return bool
     * @throws LocalizedException
     */
    protected function isAdmin()
    {
        return $this->_state->getAreaCode() == FrontNameResolver::AREA_CODE;
    }

    public function getCountryNameByCode($code)
    {
        $countries = [
            'AD' => 'Andorra',
            'AE' => 'United Arab Emirates',
            'AF' => 'Afghanistan',
            'AG' => 'Antigua and Barbuda',
            'AI' => 'Anguilla',
            'AL' => 'Albania',
            'AM' => 'Armenia',
            'AN' => 'Netherlands Antilles',
            'AO' => 'Angola',
            'AR' => 'Argentina',
            'AT' => 'Austria',
            'AU' => 'Australia',
            'AW' => 'Aruba',
            'AX' => 'Aland Island (Finland)',
            'AZ' => 'Azerbaijan',
            'BA' => 'Bosnia-Herzegovina',
            'BB' => 'Barbados',
            'BD' => 'Bangladesh',
            'BE' => 'Belgium',
            'BF' => 'Burkina Faso',
            'BG' => 'Bulgaria',
            'BH' => 'Bahrain',
            'BI' => 'Burundi',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BN' => 'Brunei Darussalam',
            'BO' => 'Bolivia',
            'BR' => 'Brazil',
            'BS' => 'Bahamas',
            'BT' => 'Bhutan',
            'BW' => 'Botswana',
            'BY' => 'Belarus',
            'BZ' => 'Belize',
            'CA' => 'Canada',
            'CC' => 'Cocos Island (Australia)',
            'CD' => 'Congo, Democratic Republic of the',
            'CF' => 'Central African Republic',
            'CG' => 'Congo, Republic of the',
            'CH' => 'Switzerland',
            'CI' => 'Ivory Coast (Cote d Ivoire)',
            'CK' => 'Cook Islands (New Zealand)',
            'CL' => 'Chile',
            'CM' => 'Cameroon',
            'CN' => 'China',
            'CO' => 'Colombia',
            'CR' => 'Costa Rica',
            'CU' => 'Cuba',
            'CV' => 'Cape Verde',
            'CX' => 'Christmas Island (Australia)',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DE' => 'Germany',
            'DJ' => 'Djibouti',
            'DK' => 'Denmark',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'DZ' => 'Algeria',
            'EC' => 'Ecuador',
            'EE' => 'Estonia',
            'EG' => 'Egypt',
            'ER' => 'Eritrea',
            'ES' => 'Spain',
            'ET' => 'Ethiopia',
            'FI' => 'Finland',
            'FJ' => 'Fiji',
            'FK' => 'Falkland Islands',
            'FM' => 'Micronesia, Federated States of',
            'FO' => 'Faroe Islands',
            'FR' => 'France',
            'GA' => 'Gabon',
            'GB' => 'United Kingdom of Great Britain and Northern Ireland',
            'GD' => 'Grenada',
            'GE' => 'Georgia, Republic of',
            'GF' => 'French Guiana',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GL' => 'Greenland',
            'GM' => 'Gambia',
            'GN' => 'Guinea',
            'GP' => 'Guadeloupe',
            'GQ' => 'Equatorial Guinea',
            'GR' => 'Greece',
            'GS' => 'South Georgia (Falkland Islands)',
            'GT' => 'Guatemala',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HK' => 'Hong Kong',
            'HN' => 'Honduras',
            'HR' => 'Croatia',
            'HT' => 'Haiti',
            'HU' => 'Hungary',
            'ID' => 'Indonesia',
            'IE' => 'Ireland',
            'IL' => 'Israel',
            'IN' => 'India',
            'IQ' => 'Iraq',
            'IR' => 'Iran',
            'IS' => 'Iceland',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'JO' => 'Jordan',
            'JP' => 'Japan',
            'KE' => 'Kenya',
            'KG' => 'Kyrgyzstan',
            'KH' => 'Cambodia',
            'KI' => 'Kiribati',
            'KM' => 'Comoros',
            'KN' => 'Saint Kitts (Saint Christopher and Nevis)',
            'KP' => 'North Korea (Korea, Democratic People\'s Republic of)',
            'KR' => 'South Korea (Korea, Republic of)',
            'KW' => 'Kuwait',
            'KY' => 'Cayman Islands',
            'KZ' => 'Kazakhstan',
            'LA' => 'Laos',
            'LB' => 'Lebanon',
            'LC' => 'Saint Lucia',
            'LI' => 'Liechtenstein',
            'LK' => 'Sri Lanka',
            'LR' => 'Liberia',
            'LS' => 'Lesotho',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'LV' => 'Latvia',
            'LY' => 'Libya',
            'MA' => 'Morocco',
            'MC' => 'Monaco (France)',
            'MD' => 'Moldova',
            'MG' => 'Madagascar',
            'MK' => 'Macedonia, Republic of',
            'ML' => 'Mali',
            'MM' => 'Burma',
            'MN' => 'Mongolia',
            'MO' => 'Macao',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MS' => 'Montserrat',
            'MT' => 'Malta',
            'MU' => 'Mauritius',
            'MV' => 'Maldives',
            'MW' => 'Malawi',
            'MX' => 'Mexico',
            'MY' => 'Malaysia',
            'MZ' => 'Mozambique',
            'NA' => 'Namibia',
            'NC' => 'New Caledonia',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'NI' => 'Nicaragua',
            'NL' => 'Netherlands',
            'NO' => 'Norway',
            'NP' => 'Nepal',
            'NR' => 'Nauru',
            'NZ' => 'New Zealand',
            'OM' => 'Oman',
            'PA' => 'Panama',
            'PE' => 'Peru',
            'PF' => 'French Polynesia',
            'PG' => 'Papua New Guinea',
            'PH' => 'Philippines',
            'PK' => 'Pakistan',
            'PL' => 'Poland',
            'PM' => 'Saint Pierre and Miquelon',
            'PN' => 'Pitcairn Island',
            'PT' => 'Portugal',
            'PY' => 'Paraguay',
            'QA' => 'Qatar',
            'RE' => 'Reunion',
            'RO' => 'Romania',
            'RS' => 'Serbia',
            'RU' => 'Russia',
            'RW' => 'Rwanda',
            'SA' => 'Saudi Arabia',
            'SB' => 'Solomon Islands',
            'SC' => 'Seychelles',
            'SD' => 'Sudan',
            'SE' => 'Sweden',
            'SG' => 'Singapore',
            'SH' => 'Saint Helena',
            'SI' => 'Slovenia',
            'SK' => 'Slovak Republic',
            'SL' => 'Sierra Leone',
            'SM' => 'San Marino',
            'SN' => 'Senegal',
            'SO' => 'Somalia',
            'SR' => 'Suriname',
            'ST' => 'Sao Tome and Principe',
            'SV' => 'El Salvador',
            'SY' => 'Syrian Arab Republic',
            'SZ' => 'Eswatini',
            'TC' => 'Turks and Caicos Islands',
            'TD' => 'Chad',
            'TG' => 'Togo',
            'TH' => 'Thailand',
            'TJ' => 'Tajikistan',
            'TK' => 'Tokelau (Union Group) (Western Samoa)',
            'TL' => 'East Timor (Timor-Leste, Democratic Republic of)',
            'TM' => 'Turkmenistan',
            'TN' => 'Tunisia',
            'TO' => 'Tonga',
            'TR' => 'Turkey',
            'TT' => 'Trinidad and Tobago',
            'TV' => 'Tuvalu',
            'TW' => 'Taiwan',
            'TZ' => 'Tanzania',
            'UA' => 'Ukraine',
            'UG' => 'Uganda',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VA' => 'Vatican City',
            'VC' => 'Saint Vincent and the Grenadines',
            'VE' => 'Venezuela',
            'VG' => 'British Virgin Islands',
            'VN' => 'Vietnam',
            'VU' => 'Vanuatu',
            'WF' => 'Wallis and Futuna Islands',
            'WS' => 'Western Samoa',
            'YE' => 'Yemen',
            'YT' => 'Mayotte (France)',
            'ZA' => 'South Africa',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
            'US' => 'United States',
        ];

        return $countries[$code];
    }
}
