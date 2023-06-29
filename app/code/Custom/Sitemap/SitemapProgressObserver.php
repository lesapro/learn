<?php
namespace Custom\Sitemap\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection;

class SitemapProgressObserver implements ObserverInterface
{
    protected $logger;
    protected $connection;

    public function __construct(
        LoggerInterface $logger,
        ResourceConnection $resourceConnection
    ) {
        $this->logger = $logger;
        $this->connection = $resourceConnection->getConnection();
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $storeId = $observer->getData('store_id');
        $progress = $observer->getData('progress');

        // Ghi lại thông tin tiến trình vào log hoặc bảng cơ sở dữ liệu
        $this->logger->debug('Sitemap generation progress for store ID ' . $storeId . ': ' . $progress);
    }
}
