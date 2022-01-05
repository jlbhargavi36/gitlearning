<?php

namespace Amasty\StoreCredit\Model\StoreCredit;

use Amasty\StoreCredit\Api\ManageCustomerStoreCreditInterface;
use Amasty\StoreCredit\Api\StoreCreditRepositoryInterface;
use Amasty\StoreCredit\Model\ConfigProvider;
use Amasty\StoreCredit\Model\History\HistoryFactory;
use Amasty\StoreCredit\Model\History\HistoryRepository;
use Amasty\StoreCredit\Model\History\MessageProcessor;
use Amasty\StoreCredit\Model\StoreCredit\ResourceModel\StoreCredit as StoreCreditResource;
use Amasty\StoreCredit\Utils\Email;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ManageCustomerStoreCredit implements ManageCustomerStoreCreditInterface
{
    /**
     * @var StoreCreditRepositoryInterface
     */
    private $storeCreditRepository;

    /**
     * @var StoreCredit
     */
    private $storeCredit;

    /**
     * @var HistoryRepository
     */
    private $historyRepository;

    /**
     * @var HistoryFactory
     */
    private $historyFactory;

    /**
     * @var Email
     */
    private $email;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        StoreCreditRepositoryInterface $storeCreditRepository,
        StoreCreditResource $storeCredit,
        HistoryRepository $historyRepository,
        HistoryFactory $historyFactory,
        ConfigProvider $configProvider,
        CustomerRepository $customerRepository,
        Email $email,
        StoreManagerInterface $storeManager
    ) {
        $this->storeCreditRepository = $storeCreditRepository;
        $this->storeCredit = $storeCredit;
        $this->historyRepository = $historyRepository;
        $this->historyFactory = $historyFactory;
        $this->email = $email;
        $this->configProvider = $configProvider;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addOrSubtractStoreCredit(
        $customerId,
        $amount,
        $action,
        $actionData = [],
        $storeId = 0,
        $message = '',
        bool $visibleForCustomer = false
    ) {
        $storeCredit = $this->storeCreditRepository->getByCustomerId($customerId);
        $newStoreCredit = $storeCredit->getStoreCredit() + (float)$amount;
        if ($newStoreCredit < 0) {
            throw new LocalizedException(__('Store Credit couldn\'t be less than zero.'));
        }
        $storeCredit->setStoreCredit($newStoreCredit);
        try {
            $this->storeCredit->save($storeCredit);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save store credit. Error: %1', $e->getMessage()));
        }

        try {
            $actionData = array_values($actionData);
            /** @var \Amasty\StoreCredit\Model\History\History $history */
            $history = $this->historyFactory->create();
            $history->setCustomerHistoryId($this->historyRepository->getNextCustomerHistoryId($customerId))
                ->setCustomerId($customerId)
                ->setIsDeduct($amount < 0)
                ->setDifference(abs($amount))
                ->setStoreCreditBalance($storeCredit->getStoreCredit())
                ->setStoreId($storeId)
                ->setAction($action)
                ->setActionData(json_encode($actionData))
                ->setMessage($message);
            $history->setIsVisibleForCustomer($visibleForCustomer);
            $history = $this->historyRepository->save($history);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save store credit history. Error: %1', $e->getMessage()));
        }

        // @phpstan-ignore-next-line
        try {
            if ($this->configProvider->isEmailEnabled()
                && in_array($action, $this->configProvider->getEmailActions())
            ) {
                $customer = $this->customerRepository->getById($customerId);
                $actionAdd = $actionRemove = $actionCreditMemo = $actionOrderPay = $actionOrderCancel = false;
                $actionBuyStoreCredit = false;
                switch ($action) {
                    case MessageProcessor::ADMIN_BALANCE_CHANGE_PLUS:
                        $actionAdd = true;
                        break;
                    case MessageProcessor::ADMIN_BALANCE_CHANGE_MINUS:
                        $actionRemove = true;
                        break;
                    case MessageProcessor::CREDIT_MEMO_REFUND:
                        $actionCreditMemo = true;
                        break;
                    case MessageProcessor::ORDER_PAY:
                        $actionOrderPay = true;
                        break;
                    case MessageProcessor::ORDER_CANCEL:
                        $actionOrderCancel = true;
                        break;
                    case MessageProcessor::BUY_STORE_CREDIT_PRODUCT:
                        $actionBuyStoreCredit = true;
                        break;
                }
                $vars = compact(
                    'actionAdd',
                    'actionRemove',
                    'actionCreditMemo',
                    'actionOrderPay',
                    'actionOrderCancel',
                    'actionBuyStoreCredit'
                );
                $vars['customerName'] = $customer->getFirstname();
                $vars['storeCredit'] = $history->getAbsFormatDifference(
                    null,
                    $this->storeManager->getStore($storeId)->getCurrentCurrencyCode()
                );
                $vars['newBalance'] = $history->getFormatStoreCreditBalance(
                    null,
                    $this->storeManager->getStore($storeId)->getCurrentCurrencyCode()
                );
                if (!empty($actionData[0])) {
                    $vars['orderId'] = $actionData[0];
                }
                if (!empty($message) && $visibleForCustomer) {
                    $vars['message'] = $message;
                }

                $this->email->sendEmail(
                    [
                        'email' => $customer->getEmail(),
                        'name' => $customer->getFirstname()
                    ],
                    ConfigProvider::EMAIL_TEMPLATE,
                    $vars,
                    \Magento\Framework\App\Area::AREA_FRONTEND,
                    $this->configProvider->getEmailSender(),
                    $this->configProvider->getEmailReplyTo(),
                    $storeId
                );
            }
        } catch (\Exception $e) {
            null;
        }
    }
}
