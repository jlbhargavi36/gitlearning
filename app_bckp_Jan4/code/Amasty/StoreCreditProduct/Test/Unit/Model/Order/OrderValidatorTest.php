<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Test\Unit\Model\Order;

use Amasty\StoreCredit\Model\History\HistoryRepository;
use Amasty\StoreCreditProduct\Model\Order\OrderValidator;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use Magento\Ui\Model\BookmarkSearchResults;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @see OrderValidator
 */
class OrderValidatorTest extends TestCase
{
    const ORDER_INCREMENT_ID = '000001';

    /**
     * @var OrderValidator
     */
    private $subject;

    /**
     * @var HistoryRepository|MockObject
     */
    private $historyRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var Order|MockObject
     */
    private $orderMock;

    /**
     * @var BookmarkSearchResults|MockObject
     */
    private $searchResultsMock;

    /**
     * @var SearchCriteria|MockObject
     */
    private $searchCriteriaMock;

    protected function setUp(): void
    {
        $this->searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $this->searchResultsMock = $this->createMock(BookmarkSearchResults::class);
        $this->searchCriteriaBuilderMock = $this->createConfiguredMock(
            SearchCriteriaBuilder::class,
            ['create' => $this->searchCriteriaMock]
        );
        $this->historyRepositoryMock = $this->createConfiguredMock(
            HistoryRepository::class,
            ['getList' => $this->searchResultsMock]
        );
        $this->orderMock = $this->createConfiguredMock(
            Order::class,
            ['getIncrementId' => self::ORDER_INCREMENT_ID]
        );

        $this->subject = new OrderValidator(
            $this->searchCriteriaBuilderMock,
            $this->historyRepositoryMock
        );
    }

    /**
     * @throws NoSuchEntityException
     * @covers OrderValidator::isOrderProcessedBefore
     */
    public function testIsOrderProcessedBeforeWithProcessedOrder(): void
    {
        $this->searchResultsMock->method('getTotalCount')->willReturn(1);
        $this->assertEquals(true, $this->subject->isOrderProcessedBefore($this->orderMock));
    }

    /**
     * @throws NoSuchEntityException
     * @covers OrderValidator::isOrderProcessedBefore
     */
    public function testIsOrderProcessedBeforeWithoutProcessedOrder(): void
    {
        $this->searchResultsMock->method('getTotalCount')->willReturn(0);
        $this->assertEquals(false, $this->subject->isOrderProcessedBefore($this->orderMock));
    }
}
