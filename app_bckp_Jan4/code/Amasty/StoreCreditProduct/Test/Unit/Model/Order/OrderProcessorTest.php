<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Test\Unit\Model\Order;

use Amasty\StoreCredit\Model\StoreCredit\ManageCustomerStoreCredit;
use Amasty\StoreCreditProduct\Model\Order\OrderProcessor;
use Amasty\StoreCreditProduct\Model\Order\OrderValidator;
use Amasty\StoreCreditProduct\Model\Product\Type\StoreCreditProductType;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @see OrderProcessor
 */
class OrderProcessorTest extends TestCase
{
    const STORE_CREDIT_PRODUCT_PRICE = 10;

    const ITEM_QTY = 1;

    /**
     * @var OrderProcessor
     */
    private $subject;

    /**
     * @var ManageCustomerStoreCredit|MockObject
     */
    private $manageCustomerStoreCreditMock;

    /**
     * @var OrderValidator|MockObject
     */
    private $orderValidatorMock;

    protected function setUp(): void
    {
        $this->manageCustomerStoreCreditMock = $this->createMock(ManageCustomerStoreCredit::class);
        $this->orderValidatorMock = $this->createMock(OrderValidator::class);

        $this->subject = new OrderProcessor(
            $this->manageCustomerStoreCreditMock,
            $this->orderValidatorMock
        );
    }

    /**
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @covers OrderProcessor::process
     */
    public function testProcessWithAmountAndProcessedOrder(): void
    {
        $itemMock = $this->createConfiguredMock(
            Item::class,
            [
                'getProductType' => StoreCreditProductType::PRODUCT_TYPE,
                'getBasePrice' => self::STORE_CREDIT_PRODUCT_PRICE,
                'getQtyOrdered' => self::ITEM_QTY

            ]
        );
        $orderMock = $this->createConfiguredMock(
            Order::class,
            [
                'getAllItems' => [$itemMock]
            ]
        );
        $this->orderValidatorMock->method('isOrderProcessedBefore')->willReturn(true);
        $this->manageCustomerStoreCreditMock->expects($this->never())->method('addOrSubtractStoreCredit');
        $this->subject->process($orderMock);
    }

    /**
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @covers OrderProcessor::process
     */
    public function testProcessWithAmountAndNotProcessedOrder(): void
    {
        $itemMock = $this->createConfiguredMock(
            Item::class,
            [
                'getProductType' => StoreCreditProductType::PRODUCT_TYPE,
                'getBasePrice' => self::STORE_CREDIT_PRODUCT_PRICE,
                'getQtyOrdered' => self::ITEM_QTY
            ]
        );
        $orderMock = $this->createConfiguredMock(
            Order::class,
            [
                'getAllItems' => [$itemMock]
            ]
        );
        $this->orderValidatorMock->method('isOrderProcessedBefore')->willReturn(false);
        $this->manageCustomerStoreCreditMock->expects($this->once())->method('addOrSubtractStoreCredit');
        $this->subject->process($orderMock);
    }

    /**
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @covers OrderProcessor::process
     */
    public function testProcessWithoutAmountAndProcessedOrder(): void
    {
        $itemMock = $this->createConfiguredMock(
            Item::class,
            ['getProductType' => '', 'getQtyOrdered' => self::ITEM_QTY]
        );
        $orderMock = $this->createConfiguredMock(
            Order::class,
            [
                'getAllItems' => [$itemMock]
            ]
        );
        $this->orderValidatorMock->method('isOrderProcessedBefore')->willReturn(true);
        $this->manageCustomerStoreCreditMock->expects($this->never())->method('addOrSubtractStoreCredit');
        $this->subject->process($orderMock);
    }

    /**
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @covers OrderProcessor::process
     */
    public function testProcessWithoutAmountAndNotProcessedOrder(): void
    {
        $itemMock = $this->createConfiguredMock(
            Item::class,
            ['getProductType' => '', 'getQtyOrdered' => self::ITEM_QTY]
        );
        $orderMock = $this->createConfiguredMock(
            Order::class,
            [
                'getAllItems' => [$itemMock]
            ]
        );
        $this->orderValidatorMock->method('isOrderProcessedBefore')->willReturn(false);
        $this->manageCustomerStoreCreditMock->expects($this->never())->method('addOrSubtractStoreCredit');
        $this->subject->process($orderMock);
    }
}
