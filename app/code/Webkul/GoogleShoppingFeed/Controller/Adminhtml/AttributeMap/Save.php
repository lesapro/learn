<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Controller\Adminhtml\AttributeMap;

use Magento\Framework\Locale\Resolver;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Webkul\GoogleShoppingFeed\Model\AccountsFactory
     */
    private $accountsFactory;

    /**
     * @var \Webkul\GoogleShoppingFeed\Logger\Logger
     */
    private $logger;

    /**
     * @var \Webkul\GoogleShoppingFeed\Helper\Data
     */
    private $dataHelper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Webkul\GoogleShoppingFeed\Helper\Data $helperData
     * @param \Webkul\GoogleShoppingFeed\Logger\Logger $logger
     * @param \Webkul\GoogleShoppingFeed\Model\Storage\DbStorage $dbStorage
     *
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webkul\GoogleShoppingFeed\Helper\Data $helperData,
        \Webkul\GoogleShoppingFeed\Logger\Logger $logger,
        \Webkul\GoogleShoppingFeed\Model\Storage\DbStorage $dbStorage
    ) {
        $this->helperData = $helperData;
        $this->logger = $logger;
        $this->dbStorage = $dbStorage;
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        if (!$data) {
            $this->_redirect('googleshoppingfeed/*/index');
            return;
        }
        try {
            if (isset($data['offerId']) && $data['offerId']) {
                $tempData = [];
                foreach ($data as $key => $value) {
                    if (!in_array($key, ['key', 'active_tab', 'form_key'])) {
                        if ($value == 'new') {
                            $value = $this->helperData->createProductAttribute($key);
                        }
                        $tempData[] = ['google_feed_field' => $key, 'attribute_code' => $value];
                    }
                }
                if (!empty($tempData)) {
                    $this->dbStorage->insertMultiple($tempData, 'google_shopping_field_feeds');
                }
                $this->messageManager->addSuccess(__('Mapped record saved successfully.'));
            } else {
                $this->messageManager->addError(__('Invalid request'));
            }
            $this->_redirect('googleshoppingfeed/*/index');
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->logger->addError('AttributeMap Save:'.$e->getMessage());
            $this->_redirect('googleshoppingfeed/*/index');
        }
    }

    /**
     * _isAllowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_GoogleShoppingFeed::attributemap_save');
    }
}
