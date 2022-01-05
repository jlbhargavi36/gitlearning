<?php
declare(strict_types=1);

namespace Amasty\StoreCredit\Model\History\Repository;

use Amasty\StoreCredit\Api\Data\HistoryInterface;

class GetHistoryAdminNameDummy implements GetHistoryAdminNameInterface
{
    public function execute(HistoryInterface $history): ?string
    {
        return null;
    }
}
