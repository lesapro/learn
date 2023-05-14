<?php
/**
 * Class Wishlist
 *
 * PHP version 7
 *
 * @category Sparsh
 * @package  Sparsh_QuickView
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\QuickView\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Wishlist
 *
 * @category Sparsh
 * @package  Sparsh_QuickView
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Wishlist
{
    /**
     * Wishlist constructor.
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->redirect = $redirect;
        $this->urlInterface = $urlInterface;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productRepository = $productRepository;
        $this->request = $request;
    }

    /**
     * @param \Magento\Wishlist\Controller\Index\Add $subject
     * @param \Closure $proceed
     * @return \Magento\Framework\Controller\Result\Json|mixed
     */
    public function aroundExecute(\Magento\Wishlist\Controller\Index\Add $subject, \Closure $proceed)
    {
        $resultProceed = $proceed();
        $requestParams = $this->request->getParams();
        
        $productId = isset($requestParams['product']) ? (int)$requestParams['product'] : null;
        if (!$productId) {
            return $resultProceed;
        }

        try {
            $product = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            $product = null;
        }

        if (strpos($this->redirect->getRefererUrl(), 'quickview') !== false) {
            if (strpos($this->urlInterface->getCurrentUrl(), 'loginPost') !== false ||
                strpos($this->urlInterface->getCurrentUrl(), 'createpost') !== false) {
                return $resultProceed;
            }
            $result = $this->resultJsonFactory->create();
            $response = [
                'success' => 'true',
                'message' => $product->getName() . ' has been added to your Wish List.'
            ];
            return $result->setData($response);
        } else {
            return $resultProceed;
        }
    }
}
