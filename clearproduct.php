<?php
//aaatuan test git
use Magento\Framework\AppInterface;
try
{
    require_once __DIR__ . '/app/bootstrap.php';
}
catch (\Exception $e)
{
    echo 'Autoload error: ' . $e->getMessage();
    exit(1);
}
try
{
    $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
    $objectManager = $bootstrap->getObjectManager();
    $appState = $objectManager->get('\Magento\Framework\App\State');
    $appState->setAreaCode('frontend');

    $productRepository = $objectManager->get('\Magento\Catalog\Model\ProductRepository');
    $registry = $objectManager->get('\Magento\Framework\Registry');

    $registry->register('isSecureArea', true);

    //There are two ways to remove products using SKU or product id.
    // using sku to remove product
    $sky="your sku here";
    $productRepository->deleteById($sky);

    //using product id to remove product
    $product_id = 1; //here your product id
    $product = $productRepository->getById($product_id);
    $productRepository->delete($product);

    echo $sky." Your Product Remove Successfully.";
}
catch(\Exception $e)
{
    print_r($e->getMessage());
}
