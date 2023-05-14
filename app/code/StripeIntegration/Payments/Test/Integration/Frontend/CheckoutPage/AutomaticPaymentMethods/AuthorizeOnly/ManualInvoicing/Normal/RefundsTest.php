<?php

namespace StripeIntegration\Payments\Test\Integration\Frontend\AutomaticPaymentMethods\AuthorizeOnly\ManualInvoicing\Normal;

class RefundsTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->tests = new \StripeIntegration\Payments\Test\Integration\Helper\Tests($this);
        $this->quote = new \StripeIntegration\Payments\Test\Integration\Helper\Quote();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store payment/stripe_payments/active 1
     * @magentoConfigFixture current_store payment/stripe_payments_basic/stripe_mode test
     * @magentoConfigFixture current_store payment/stripe_payments/payment_flow 1
     * @magentoConfigFixture current_store payment/stripe_payments_checkout/payment_action authorize
     * @magentoConfigFixture current_store payment/stripe_payments_checkout/expired_authorizations 0
     * @magentoConfigFixture current_store payment/stripe_payments_checkout/save_payment_method 0
     *
     * @magentoConfigFixture current_store currency/options/base USD
     * @magentoConfigFixture current_store currency/options/allow EUR,USD
     * @magentoConfigFixture current_store currency/options/default EUR
     *
     * @magentoDataFixture ../../../../app/code/StripeIntegration/Payments/Test/Integration/_files/Data/ApiKeys.php
     * @magentoDataFixture ../../../../app/code/StripeIntegration/Payments/Test/Integration/_files/Data/ExchangeRates.php
     * @magentoDataFixture ../../../../app/code/StripeIntegration/Payments/Test/Integration/_files/Data/Taxes.php
     * @magentoDataFixture ../../../../app/code/StripeIntegration/Payments/Test/Integration/_files/Data/Addresses.php
     * @magentoDataFixture ../../../../app/code/StripeIntegration/Payments/Test/Integration/_files/Data/Products.php
     */
    public function testRefunds()
    {
        $this->quote->create()
            ->setCustomer('Guest')
            ->setCart("Normal")
            ->setShippingAddress("Berlin")
            ->setShippingMethod("FlatRate")
            ->setBillingAddress("Berlin")
            ->setPaymentMethod("StripeCheckout");

        // Place the order
        $order = $this->quote->placeOrder();

        // Confirm the payment
        $method = "card";
        $session = $this->tests->checkout()->retrieveSession($order);
        $response = $this->tests->checkout()->confirm($session, $order, $method, "Berlin");
        $this->tests->checkout()->authenticate($response->payment_intent, $method);

        // Trigger webhooks
        $paymentIntent = $this->tests->stripe()->paymentIntents->retrieve($response->payment_intent->id);
        $this->tests->event()->triggerPaymentIntentEvents($paymentIntent);

        // Order checks
        $order = $this->tests->refreshOrder($order);
        $this->assertEquals("processing", $order->getState());
        $this->assertEquals("processing", $order->getStatus());
        $this->assertEquals(0, $order->getInvoiceCollection()->count());
        $this->assertEquals($order->getGrandTotal(), $order->getTotalDue());

        // Capture the payment
        $this->tests->invoiceOnline($order, []);

        // Stripe checks
        $lastCheckoutSession = $this->tests->getLastCheckoutSession();
        $this->tests->compare($lastCheckoutSession, [
            "amount_total" => $order->getGrandTotal() * 100,
            "payment_intent" => [
                "amount" => $order->getGrandTotal() * 100,
                "amount_capturable" => 0,
                "amount_received" => $order->getGrandTotal() * 100,
                "capture_method" => "manual",
            ],
            "customer_email" => "osterhagen@example.com"
        ]);

        // Partially refund the order
        $this->assertCount(1, $order->getInvoiceCollection());
        $invoice = $order->getInvoiceCollection()->getFirstItem();
        $this->tests->refundOnline($invoice, ['simple-product' => 1]);

        // Trigger webhooks
        $paymentIntent = $this->tests->stripe()->paymentIntents->retrieve($response->payment_intent->id);
        $this->tests->event()->trigger("charge.refunded", $paymentIntent->charges->data[0]->id);

        // Order checks
        $order = $this->tests->refreshOrder($order);
        $this->assertEquals("processing", $order->getState());
        $this->assertEquals("processing", $order->getStatus());
        $this->assertEquals($order->getGrandTotal(), $order->getTotalPaid());
        $this->assertEquals(8.50, $order->getTotalRefunded());
        $this->assertEquals(0, $order->getTotalCanceled());
        $this->assertEquals(0, $order->getTotalDue());

        // Fully refund the order
        $this->tests->refundOnline($invoice, ['simple-product' => 1, 'virtual-product' => 2], $baseShippingAmount = 10);

        // Order checks
        $order = $this->tests->refreshOrder($order);
        $this->assertEquals("closed", $order->getState());
        $this->assertEquals("closed", $order->getStatus());
        $this->assertEquals($order->getGrandTotal(), $order->getTotalPaid());
        $this->assertEquals($order->getGrandTotal(), $order->getTotalRefunded());
        $this->assertEquals(0, $order->getTotalCanceled());
        $this->assertEquals(0, $order->getTotalDue());
    }
}
