<?php

namespace Fovoso\Shipping\Controller\Shipping;

/**
 * Class Request
 * @package Fovoso\Shipping\Controller\Shipping
 */
class Request implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * Request constructor.
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->request = $request;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $jsonResult = $this->jsonFactory->create();
        $country = $this->request->getParam('country', false);
        $keyWork = $this->request->getParam('keyword', false);
        $html = $this->layoutFactory->create()
            ->createBlock(
                \Fovoso\Shipping\Block\ShippingPolicy::class,
                "fovoso_shipping",
                [
                    'data' => [
                        'country' => $country,
                        'keyword' => $keyWork
                    ],
                ]
            )->setTemplate('Fovoso_Shipping::shipping_policy.phtml')
            ->toHtml();
        $this->request->setActionName('policy');
        $jsonResult->setData(['html' => $html]);
        return $jsonResult;
    }
}
