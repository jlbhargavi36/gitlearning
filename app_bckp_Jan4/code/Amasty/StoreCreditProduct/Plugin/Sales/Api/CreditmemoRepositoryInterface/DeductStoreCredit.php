<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Plugin\Sales\Api\CreditmemoRepositoryInterface;

use Amasty\StoreCreditProduct\Model\Order\CreditMemo\CreditMemoProcessor;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Model\Order\Creditmemo;

class DeductStoreCredit
{
    /**
     * @var CreditMemoProcessor
     */
    private $creditMemoProcessor;

    public function __construct(
        CreditMemoProcessor $creditMemoProcessor
    ) {
        $this->creditMemoProcessor = $creditMemoProcessor;
    }

    /**
     * @param CreditmemoRepositoryInterface $subject
     * @param Creditmemo $creditmemo
     * @return Creditmemo
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(CreditmemoRepositoryInterface $subject, Creditmemo $creditmemo): Creditmemo
    {
        if ($creditmemo->getCustomerId()) {
            $this->creditMemoProcessor->process($creditmemo);
        }

        return $creditmemo;
    }
}
