<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Plugin\Sales\Model\ResourceModel\Order;

use Amasty\StoreCreditProduct\Model\Order\OrderProcessor;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order as SalesOrder;
use Magento\Sales\Model\ResourceModel\Order as SalesOrderResource;

class AddStoreCredit
{
    /**
     * @var OrderProcessor
     */
    private $orderProcessor;

    public function __construct(
        OrderProcessor $orderProcessor
    ) {
        $this->orderProcessor = $orderProcessor;
    }

    /**
     * @param SalesOrderResource $subject
     * @param SalesOrderResource $result
     * @param SalesOrder $order
     * @return SalesOrderResource
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(
        SalesOrderResource $subject,
        SalesOrderResource $result,
        SalesOrder $order
    ): SalesOrderResource {
        if ($order->getCustomerId() && $order->getState() === SalesOrder::STATE_COMPLETE) {
            $this->orderProcessor->process($order);
        }

        return $result;
    }
}
