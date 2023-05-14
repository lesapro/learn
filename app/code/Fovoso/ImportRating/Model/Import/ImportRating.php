<?php

namespace Fovoso\ImportRating\Model\Import;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\ImportExport\Helper\Data as ImportHelper;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Magento\ImportExport\Model\ResourceModel\Import\Data;
use Magento\Review\Model\Review;
use Magento\UrlRewrite\Model\OptionProvider;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite as ServiceUrlRewrite;

class ImportRating extends AbstractEntity
{
    const ENTITY_CODE = 'import_rating';
    const TABLE = 'rating';

    protected $needColumnCheck = true;
    protected $logInHistory = true;

    protected $validColumnNames = [
        'sku',
        'rating',
        'nickname',
        'title',
        'detail',
        'images'
    ];

    protected $connection;

    protected $optionProvider;

    protected $storeData;

    private $resource;

    protected $reviewFactory;

    protected $productRepository;

    protected $logger;

    protected $ratingFactory;

    protected $storeManager;
    protected $directoryList;
    protected $file;
    protected $reviewMediaFactory;
    protected $filesystem;
    protected $_mediaDirectory;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \RLTSquare\ProductReviewImages\Model\ReviewMediaFactory $reviewMediaFactory,
        DirectoryList $directoryList,
        File $file,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        JsonHelper $jsonHelper,
        ImportHelper $importExportData,
        Data $importData,
        ResourceConnection $resource,
        Helper $resourceHelper,
        ProcessingErrorAggregatorInterface $errorAggregator,
        OptionProvider $optionProvider
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->resource = $resource;
        $this->connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
        $this->optionProvider = $optionProvider->toOptionArray();
        $this->reviewFactory = $reviewFactory;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
        $this->ratingFactory = $ratingFactory;
        $this->storeManager = $storeManager;
        $this->directoryList = $directoryList;
        $this->file = $file;
        $this->reviewMediaFactory = $reviewMediaFactory;
        $this->filesystem = $filesystem;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
    }

    public function getEntityTypeCode()
    {
        return self::ENTITY_CODE;
    }

    /**
     * Get available columns
     *
     * @return array
     */
    public function getValidColumnNames(): array
    {
        return $this->validColumnNames;
    }

    public function validateRow(array $rowData, $rowNum): bool
    {
        if (!$rowData['sku'] || !$rowData['nickname'] || !$rowData['detail'] || !$rowData['rating'] || !$rowData['title']) {
            $this->addRowError(__('`sku`, `nickname`, `detail`, `rating`, `title` Is Required'), $rowNum);
        }

        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }

        $this->_validatedRows[$rowNum] = true;

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    protected function _importData(): bool
    {
        if (Import::BEHAVIOR_APPEND == $this->getBehavior()) {
            $this->saveEntity();
        }
        return true;
    }

    private function saveEntity()
    {
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $row) {
                    if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
                $product = $this->productRepository->get($row['sku']);
                $review = $this->reviewFactory->create()->setData($row);
                $validate = $review->validate();
                if ($validate === true) {
                    try {
                        $review->setEntityId($review->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE))
                               ->setEntityPkValue($product->getId())
                               ->setStatusId(Review::STATUS_APPROVED)
                               ->setStores(array_keys($this->storeManager->getStores()))
                               ->save();

                        $this->ratingFactory->create()
                                            ->setRatingId(1)
                                            ->setReviewId($review->getId())
                                            ->addOptionVote($row['rating'], $product->getId());

                        $review->aggregate();
                        $reviewImages = explode(',', $row['images']);
                        foreach ($reviewImages as $image) {
                            $target = $this->_mediaDirectory->getAbsolutePath('review_images');
                            $imagePath = $target.'/'.baseName($image);
                            $result = $this->file->read($image, $imagePath);
                            if ($result) {
                                $reviewMedia = $this->reviewMediaFactory->create();
                                $reviewMedia->setMediaUrl('/'.baseName($image));
                                $reviewMedia->setReviewId($review->getId());
                                $reviewMedia->save();
                            }
                        }
                    } catch (\Exception $e) {
                        $this->logger->debug('Import rating error: '.$e->getMessage());
                    }
                }
                $this->countItemsCreated ++;
            }
        }
    }

    protected function getMediaDirTmpDir()
    {
        return $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
    }

    private function getAvailableColumns(): array
    {
        return $this->validColumnNames;
    }
}