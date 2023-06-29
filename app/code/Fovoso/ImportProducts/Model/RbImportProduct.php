<?php

namespace Fovoso\ImportProducts\Model;

use Magento\Framework\Exception\AuthorizationException;
use Fovoso\ImportProducts\Api\RbImportProductInterface;

class RbImportProduct implements RbImportProductInterface
{

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * @var \Magento\Framework\MessageQueue\PublisherInterface
     */
    protected $publisher;

    /**
     * @var \Magento\Framework\Webapi\Rest\Request
     */
    protected $request;

    /**
     * @var \Fovoso\ImportProducts\Api\RbMessageInterface
     */
    private $rbMessageInterface;

    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Framework\MessageQueue\PublisherInterface $publisher,
        \Magento\Framework\Webapi\Rest\Request $request,
        \Fovoso\ImportProducts\Api\RbMessageInterface $rbMessageInterface
    ) {
        $this->serializer = $serializer;
        $this->publisher = $publisher;
        $this->request = $request;
        $this->rbMessageInterface = $rbMessageInterface;
    }

    public function execute()
    {
        if (!$this->authencation()) {
            throw new AuthorizationException(__("User name or password is invalid."));
        }
        $body = $this->request->getBodyParams();
        $products = [];
        $shippingInfo = [];
        foreach ($body as $data) {
            $product = $data['product'] ?? [];
            if ($product) {
                $shippings = $product['shippings'] ?? [];
                if ($shippings) {
                    $shippingInfo[] = [
                        'sku' => $product['sku'] ?? '',
                        'shipping_meta' => $shippings,
                        'shipping_fee' => $product['shipping_fee'] ?? 0
                    ];
                    unset($product['shippings']);
                }
                $products[] = $product;
            }
        }
        if ($products) {
            $productMessage = $this->rbMessageInterface->settype('importProducts')->setMessage($products);
            $this->publisher->publish('post_import_products.topic', $productMessage);
        }
        if ($shippingInfo) {
            $shippingMessage = $this->rbMessageInterface->settype('importShippings')->setMessage($shippingInfo);
            $this->publisher->publish('post_import_shippings.topic', $shippingMessage);
        }

        return $this->serializer->serialize(['result' => true]);
    }

    private function authencation()
    {
        $userName = $this->request->getHeader('userName');
        $password = $this->request->getHeader('password');
        return (bool)($userName == 'fovoso' && $password == 'fovoso123dfgr4');
    }
}
