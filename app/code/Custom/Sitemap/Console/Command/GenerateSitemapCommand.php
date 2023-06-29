<?php
namespace Custom\Sitemap\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Sitemap\Model\Sitemap;
use Magento\Framework\Event\ManagerInterface as EventManager;

class GenerateSitemapCommand extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Sitemap
     */
    private $sitemap;

    /**
     * @var State
     */
    private $state;

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * GenerateSitemapCommand constructor.
     * @param StoreManagerInterface $storeManager
     * @param Sitemap $sitemap
     * @param State $state
     * @param EventManager $eventManager
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Sitemap $sitemap,
        State $state,
        EventManager $eventManager
    ) {
        parent::__construct();
        $this->storeManager = $storeManager;
        $this->sitemap = $sitemap;
        $this->state = $state;
        $this->eventManager = $eventManager;
    }

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this->setName('sitemap:generate-custom')
            ->setDescription('Generate sitemap for multiple stores')
            ->setDefinition([]);
        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|null
     */
    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        try {
            $this->state->setAreaCode(Area::AREA_FRONTEND);
        } catch (\Exception $e) {
            // Do nothing if area code is already set
        }

        $stores = $this->storeManager->getStores();
        foreach ($stores as $store) {
            $storeId = $store->getId();
            $storeCode = $store->getCode();
            $filename = 'sitemap_' . $storeCode . '.xml';
            $filePath = '/crawler/' . $filename; // Thay đường dẫn tới custom folder của bạn

            // Ghi lại tiến trình tạo sitemap
            $this->eventManager->dispatch('custom_sitemap_generate_sitemap_progress', [
                'store_id' => $storeId,
                'progress' => 'In progress',
            ]);

            $this->sitemap->generateXml($storeId);
            $this->sitemap->saveSitemap($storeId, $filePath);

            $this->eventManager->dispatch('custom_sitemap_generate_sitemap_progress', [
                'store_id' => $storeId,
                'progress' => 'Completed',
            ]);

            $output->writeln("<info>Generated sitemap for store ID $storeId.</info>");
        }

        return Cli::RETURN_SUCCESS;
    }
}
