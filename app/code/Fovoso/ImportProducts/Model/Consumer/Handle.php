<?php

namespace Fovoso\ImportProducts\Model\Consumer;

use Magento\Framework\App\ResourceConnection;
use Fovoso\ImportProducts\Logger\Logger;
use Magento\Framework\MessageQueue\MessageLockException;
use Magento\Framework\MessageQueue\ConnectionLostException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\MessageQueue\CallbackInvoker;
use Magento\Framework\MessageQueue\ConsumerConfigurationInterface;
use Magento\Framework\MessageQueue\EnvelopeInterface;
use Magento\Framework\MessageQueue\QueueInterface;
use Magento\Framework\MessageQueue\LockInterface;
use Magento\Framework\MessageQueue\MessageController;
use Magento\Framework\MessageQueue\ConsumerInterface;
use Magento\Framework\ObjectManagerInterface;

class Handle implements ConsumerInterface
{

    protected $consumerHandles = [
        'importProductsConsumer' => '\Fovoso\ImportProducts\Model\Consumer\ImportProducts',
        'importShippingsConsumer' => '\Fovoso\ImportProducts\Model\Consumer\ImportShippings',
    ];

    /**
     * @var \Magento\Framework\MessageQueue\CallbackInvoker
     */
    private $invoker;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @var \Magento\Framework\MessageQueue\ConsumerConfigurationInterface
     */
    private $configuration;

    /**
     * @var \Magento\Framework\MessageQueue\MessageController
     */
    private $messageController;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ObjectManagerInterface
     */
    private $objectmanager;

    /**
     * Initialize dependencies.
     *
     * @param CallbackInvoker $invoker
     * @param ResourceConnection $resource
     * @param MessageController $messageController
     * @param ConsumerConfigurationInterface $configuration
     * @param ObjectManagerInterface $objectmanager
     * @param LoggerInterface $logger
     */
    public function __construct(
        CallbackInvoker $invoker,
        ResourceConnection $resource,
        MessageController $messageController,
        ConsumerConfigurationInterface $configuration,
        ObjectManagerInterface $objectmanager,
        Logger $logger
    ) {
        $this->invoker = $invoker;
        $this->resource = $resource;
        $this->messageController = $messageController;
        $this->configuration = $configuration;
        $this->objectmanager = $objectmanager;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function process($maxNumberOfMessages = null)
    {
        $queue = $this->configuration->getQueue();
        if (!isset($maxNumberOfMessages)) {
            $queue->subscribe($this->getTransactionCallback($queue));
        } else {
            $this->invoker->invoke($queue, $maxNumberOfMessages, $this->getTransactionCallback($queue));
        }
    }

    /**
     * Get transaction callback. This handles the case of async.
     *
     * @param QueueInterface $queue
     * @return \Closure
     */
    private function getTransactionCallback(QueueInterface $queue)
    {
        return function (EnvelopeInterface $message) use ($queue) {
            /** @var LockInterface $lock */
            $lock = null;
            try {
                $consumerName = $this->configuration->getConsumerName();
                $handleObject =  $this->consumerHandles[$consumerName] ?? null;
                $lock = $this->messageController->lock($message, $consumerName);
                if($handleObject){
                    $body = $message->getBody();
                    $result = $this->objectmanager->create($handleObject)->execute($body);
                    if ($result === false) {
                        $queue->reject($message);
                    }
                }
                $queue->acknowledge($message);
            } catch (MessageLockException $exception) {
                $queue->acknowledge($message);
            } catch (ConnectionLostException $e) {
                $queue->acknowledge($message);
                if ($lock) {
                    $this->unlockQueue($lock);
                }
            } catch (NotFoundException $e) {
                $queue->acknowledge($message);
                $this->logger->warning($e->getMessage());
            } catch (\Exception $e) {
                $queue->reject($message, false, $e->getMessage());
                $queue->acknowledge($message);
                if ($lock) {
                    $this->unlockQueue($lock);
                }
            }
        };
    }

    private function unlockQueue($lock){
        $this->resource->getConnection()->delete($this->resource->getTableName('queue_lock'), ['id = ?' => $lock->getId()]);
    }
}
