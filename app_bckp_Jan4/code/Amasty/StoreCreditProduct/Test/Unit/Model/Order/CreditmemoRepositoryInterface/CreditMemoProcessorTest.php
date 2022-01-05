<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Test\Unit\Model\Order\CreditmemoRepositoryInterface;

use Amasty\StoreCredit\Model\History\MessageProcessor;
use Amasty\StoreCredit\Model\StoreCredit\ManageCustomerStoreCredit;
use Amasty\StoreCreditProduct\Model\Order\CreditMemo\CreditMemoProcessor;
use Amasty\StoreCreditProduct\Model\Product\Type\StoreCreditProductType;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Item;
use Magento\Sales\Model\Order\Item as OrderItem;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @see CreditMemoProcessor
 */
class CreditMemoProcessorTest extends TestCase
{
    const STORE_CREDIT_PRODUCT_PRICE = 10;

    const ITEM_QTY = 1;

    const SINGLE_ITEM_AMOUNT = -10;

    const FEW_ITEMS_AMOUNT = -20;

    const MESSAGE = '';

    const VISIBLE_FOR_CUSTOMER = false;

    /**
     * @var CreditMemoProcessor
     */
    private $subject;

    /**
     * @var ManageCustomerStoreCredit|MockObject
     */
    private $manageCustomerStoreCreditMock;

    /**
     * @var Item|MockObject
     */
    private $storeCreditItemMock;

    /**
     * @var Item|MockObject
     */
    private $item;

    /**
     * @var Order|MockObject
     */
    private $orderMock;

    protected function setUp(): void
    {
        $this->manageCustomerStoreCreditMock = $this->createMock(ManageCustomerStoreCredit::class);
        $this->orderMock = $this->createMock(Order::class);
        $storeCreditOrderItemMock = $this->createConfiguredMock(
            OrderItem::class,
            ['getProductType' => StoreCreditProductType::PRODUCT_TYPE]
        );
        $this->storeCreditItemMock = $this->createConfiguredMock(
            Item::class,
            [
                'getOrderItem' => $storeCreditOrderItemMock,
                'getBasePrice' => self::STORE_CREDIT_PRODUCT_PRICE,
                'getQty' => self::ITEM_QTY

            ]
        );
        $orderItemMock = $this->createConfiguredMock(
            OrderItem::class,
            ['getProductType' => '']
        );
        $this->item = $this->createConfiguredMock(
            Item::class,
            ['getOrderItem' => $orderItemMock]
        );
        $this->subject = new CreditMemoProcessor($this->manageCustomerStoreCreditMock);
    }

    /**
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @covers CreditMemoProcessor::process
     */
    public function testProcessWithAmountAndSingleItem(): void
    {
        $creditMemoMock = $this->createConfiguredMock(
            Creditmemo::class,
            [
                'getItems' => [$this->storeCreditItemMock],
                'getOrder' => $this->orderMock
            ]
        );
        $this->manageCustomerStoreCreditMock->expects($this->once())->method('addOrSubtractStoreCredit')->with(
            $creditMemoMock->getCustomerId(),
            self::SINGLE_ITEM_AMOUNT,
            MessageProcessor::REFUND_STORE_CREDIT_PRODUCT,
            [$creditMemoMock->getOrder()->getIncrementId()],
            self::MESSAGE,
            self::VISIBLE_FOR_CUSTOMER
        );
        $this->subject->process($creditMemoMock);
    }

    /**
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @covers CreditMemoProcessor::process
     */
    public function testProcessWithAmountAndFewItems(): void
    {
        $firstItem = $this->storeCreditItemMock;
        $secondItem = $this->storeCreditItemMock;

        $creditMemoMock = $this->createConfiguredMock(
            Creditmemo::class,
            [
                'getItems' => [$firstItem, $secondItem],
                'getOrder' => $this->orderMock
            ]
        );

        $this->manageCustomerStoreCreditMock->expects($this->once())->method('addOrSubtractStoreCredit')->with(
            $creditMemoMock->getCustomerId(),
            self::FEW_ITEMS_AMOUNT,
            MessageProcessor::REFUND_STORE_CREDIT_PRODUCT,
            [$creditMemoMock->getOrder()->getIncrementId()],
            self::MESSAGE,
            self::VISIBLE_FOR_CUSTOMER
        );
        $this->subject->process($creditMemoMock);
    }

    /**
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @covers CreditMemoProcessor::process
     */
    public function testProcessWithoutAmount(): void
    {
        $creditMemoMock = $this->createConfiguredMock(
            Creditmemo::class,
            [
                'getItems' => [$this->item]
            ]
        );
        $this->manageCustomerStoreCreditMock->expects($this->never())->method('addOrSubtractStoreCredit');
        $this->subject->process($creditMemoMock);
    }
}
