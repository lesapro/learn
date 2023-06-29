<?php
/**
 * Class View
 *
 * PHP version 7
 *
 * @category Sparsh
 * @package  Sparsh_QuickView
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\QuickView\Controller\Catalog\Product;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class View
 *
 * @category Sparsh
 * @package  Sparsh_QuickView
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class View extends \Magento\Catalog\Controller\Product
{
    /**
     * @var \Magento\Catalog\Helper\Product\View
     */
    public $viewHelper;
    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    public $resultForwardFactory;
    /**
     * @var PageFactory
     */
    public $resultPageFactory;
    /**
     * @var
     */
    public $template;
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    public $data;

    /**
     * View constructor.
     * @param Context $context
     * @param \Magento\Catalog\Helper\Product\View $viewHelper
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Json\Helper\Data $data
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Helper\Product\View $viewHelper,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Json\Helper\Data $data,
        PageFactory $resultPageFactory
    ) {
        $this->viewHelper = $viewHelper;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->data = $data;
        parent::__construct($context);
    }

    /**
     * Excecute for View Action
     *
     * @return \Magento\Framework\Controller\Result\ForwardFactory
     */
    public function execute()
    {
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId = (int) $this->getRequest()->getParam('id');
        $specifyOptions = $this->getRequest()->getParam('options');

        if ($this->getRequest()->isPost() && $this->getRequest()->getParam(self::PARAM_NAME_URL_ENCODED)) {
            $product = $this->_initProduct();
            if (!$product) {
                return $this->noProductRedirect();
            }
            if ($specifyOptions) {
                $notice = $product->getTypeInstance()->getSpecifyOptionMessage();
                $this->messageManager->addNotice($notice);
            }
            if ($this->getRequest()->isAjax()) {
                $this->getResponse()->representJson(
                    $this->data->get()->jsonEncode(
                        [
                            'backUrl' => $this->_redirect->getRedirectUrl()
                        ]
                    )
                );
                return;
            }
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setRefererOrBaseUrl();
            return $resultRedirect;
        }

        $params = new \Magento\Framework\DataObject();
        $params->setCategoryId($categoryId);
        $params->setSpecifyOptions($specifyOptions);

        try {
            $page = $this->resultPageFactory->create(
                false,
                [
                    'isIsolated' => true,
                    'template' => 'Sparsh_QuickView::product_template.phtml'
                ]
            );
            $page->addDefaultHandle();
            $page->getLayout()->getUpdate()->addHandle('catalog_product_view');
            $page->getLayout()->getUpdate()->addHandle('quickview_catalog_product_view');

            $this->viewHelper->prepareAndRender($page, $productId, $this, $params);
            return $page;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $this->noProductRedirect();
        } catch (\Exception $e) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        }
    }

    /**
     * Redirect for no product
     *
     * @return \Magento\Framework\Controller\Result\ForwardFactory
     */
    public function noProductRedirect()
    {
        $store = $this->getRequest()->getQuery('store');
        if (isset($store) && !$this->getResponse()->isRedirect()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('');
        } elseif (!$this->getResponse()->isRedirect()) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        }
    }
}
