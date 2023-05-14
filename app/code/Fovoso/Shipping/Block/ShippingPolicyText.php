<?php

namespace Fovoso\Shipping\Block;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class ShippingPolicyText
 * @package Fovoso\Shipping\Block
 */
class ShippingPolicyText extends \Magento\Framework\View\Element\Template
{
    /**
     * @var BlockRepositoryInterface
     */
    protected $blockRepository;

    /**
     * ShippingPolicyText constructor.
     * @param BlockRepositoryInterface $blockRepository
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        BlockRepositoryInterface $blockRepository,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->blockRepository = $blockRepository;
    }

    /**
     * @return false|string|null
     */
    public function getCmsHtml()
    {
        try {
            $block = $this->blockRepository->getById('shipping_policy');
            $content = $block->getContent();
        } catch (\Exception $e) {
            $content = false;
        }

        return $content;
    }
}
