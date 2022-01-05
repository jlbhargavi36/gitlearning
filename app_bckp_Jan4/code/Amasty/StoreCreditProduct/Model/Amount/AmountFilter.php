<?php
declare(strict_types=1);

namespace Amasty\StoreCreditProduct\Model\Amount;

class AmountFilter
{
    const ALL_WEBSITES = '0';

    /**
     * @param array $amounts
     * @param string $websiteId
     * @return array
     */
    public function filterByWebsite(array $amounts, string $websiteId): array
    {
        foreach ($amounts as $key => $amount) {
            if ($amount['website_id'] !== $websiteId && $amount['website_id'] !== self::ALL_WEBSITES) {
                unset($amounts[$key]);
            }
        }

        return $amounts;
    }
}
