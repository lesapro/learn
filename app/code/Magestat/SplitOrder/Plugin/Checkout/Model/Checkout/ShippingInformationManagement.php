<?php
namespace Magestat\SplitOrder\Plugin\Checkout\Model\Checkout;

class ShippingInformationManagement
{
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;
    private \Magento\Framework\Serialize\Serializer\Json $serializer;
    private \Magento\Framework\App\RequestInterface $request;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    ) {
        $this->request = $request;
        $this->serializer = $serializer;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {

        $rateInfo = $addressInformation->getExtensionAttributes();
        $quote = $this->quoteRepository->getActive($cartId);

        if (empty($rateInfo)) {
            return [$cartId, $addressInformation];
        }
        if ($rateInfo->getRates()) {
            $quote->setData('rates', $rateInfo->getRates());
        }

        return [$cartId, $addressInformation];
    }
}
