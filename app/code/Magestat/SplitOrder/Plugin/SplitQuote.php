<?php

namespace Magestat\SplitOrder\Plugin;

use Magento\Quote\Model\QuoteManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Framework\Event\ManagerInterface;
use Magestat\SplitOrder\Api\QuoteHandlerInterface;

/**
 * Class SplitQuote
 * Interceptor to \Magento\Quote\Model\QuoteManagement
 */
class SplitQuote
{
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var QuoteHandlerInterface
     */
    private $quoteHandler;
    private \Magento\Catalog\Api\ProductRepositoryInterface $productRepository;
    private \Magento\Framework\Serialize\Serializer\Json $serializer;

    /**
     * @param CartRepositoryInterface $quoteRepository
     * @param QuoteFactory $quoteFactory
     * @param ManagerInterface $eventManager
     * @param QuoteHandlerInterface $quoteHandler
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        QuoteFactory $quoteFactory,
        ManagerInterface $eventManager,
        QuoteHandlerInterface $quoteHandler,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->quoteFactory = $quoteFactory;
        $this->eventManager = $eventManager;
        $this->quoteHandler = $quoteHandler;
        $this->serializer = $serializer;
    }

    /**
     * Places an order for a specified cart.
     *
     * @param QuoteManagement $subject
     * @param callable $proceed
     * @param int $cartId
     * @param string $payment
     * @return mixed
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @see \Magento\Quote\Api\CartManagementInterface
     */
    public function aroundPlaceOrder(QuoteManagement $subject, callable $proceed, $cartId, $payment = null)
    {
        $currentQuote = $this->quoteRepository->getActive($cartId);

        // Separate all items in quote into new quotes.
        $quotes = $this->quoteHandler->normalizeQuotes($currentQuote);
        if (empty($quotes)) {
            return $result = array_values([($proceed($cartId, $payment))]);
        }
        // Collect list of data addresses.
        $addresses = $this->quoteHandler->collectAddressesData($currentQuote);

        /** @var \Magento\Sales\Api\Data\OrderInterface[] $orders */
        $orders = [];
        $orderIds = [];
        foreach ($quotes as $shop => $items) {
            /** @var \Magento\Quote\Model\Quote $split */
            $split = $this->quoteFactory->create();

            // Set all customer definition data.
            $this->quoteHandler->setCustomerData($currentQuote, $split);
            $this->toSaveQuote($split);

            // Map quote items.
            foreach ($items as $item) {
                // Add item by item.
                $item->setId(null);
                $split->addItem($item);
            }

            list($method, $carrierTitle, $shippingFee) = $this->getShippingFee($shop, $currentQuote->getRates());
            $split->getShippingAddress()->setShippingMethod($method);
            $split->getShippingAddress()->setShippingDescription($carrierTitle);

            $split->setShippingFee($shippingFee);
            $this->quoteHandler->populateQuote($quotes, $split, $items, $addresses, $payment);

            // Dispatch event as Magento standard once per each quote split.
            $this->eventManager->dispatch(
                'checkout_submit_before',
                ['quote' => $split]
            );
            $split->getShippingAddress()->setShippingMethod($method);
            $split->getShippingAddress()->setShippingDescription($carrierTitle);

            $this->toSaveQuote($split);
            $order = $subject->submit($split);

            $orders[] = $order;
            $orderIds[$order->getId()] = $order->getIncrementId();

            if (null == $order) {
                throw new LocalizedException(__('Please try to place the order again.'));
            }
        }
        $currentQuote->setIsActive(false);
        $this->toSaveQuote($currentQuote);

        $this->quoteHandler->defineSessions($split, $order, $orderIds);

        $this->eventManager->dispatch(
            'checkout_submit_all_after',
            ['orders' => $orders, 'quote' => $currentQuote]
        );
        return $this->getOrderKeys($orderIds);
    }

    private function getShippingFee($shop, $ratesStr){
        $rates = $this->serializer->unserialize($ratesStr);
        foreach ($rates['items'] as $key => $rate) {
            if ($key == $shop) {
                return [$rate['carrier_code'] . '_' .  $rate['method_code'], $rate['method_title'] . ' - ' . $rate['carrier_title'], $rate['amount']];
            }
        }
    }

    /**
     * Save quote
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return \Magestat\SplitOrder\Plugin\SplitQuote
     */
    private function toSaveQuote($quote)
    {
        $this->quoteRepository->save($quote);

        return $this;
    }

    /**
     * @param array $orderIds
     * @return array
     */
    private function getOrderKeys($orderIds)
    {
        $orderValues = [];
        foreach (array_keys($orderIds) as $orderKey) {
            $orderValues[] = (string) $orderKey;
        }
        return array_values($orderValues);
    }
}
