<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Model\Order;

use Amasty\StoreCredit\Model\History\HistoryRepository;
use Amasty\StoreCredit\Model\History\MessageProcessor;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;

class OrderValidator
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var HistoryRepository
     */
    private $historyRepository;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        HistoryRepository $historyRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->historyRepository = $historyRepository;
    }

    /**
     * @param Order $order
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isOrderProcessedBefore(Order $order): bool
    {
        $actionData = '["' . $order->getIncrementId() . '"]';

        //it can slow down processing
        $this->searchCriteriaBuilder->addFilter('action_data', $actionData);
        $this->searchCriteriaBuilder->addFilter('action', MessageProcessor::BUY_STORE_CREDIT_PRODUCT);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        return (bool)$this->historyRepository->getList($searchCriteria)->getTotalCount();
    }
}
