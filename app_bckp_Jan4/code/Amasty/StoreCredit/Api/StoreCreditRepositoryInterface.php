<?php

namespace Amasty\StoreCredit\Api;

interface StoreCreditRepositoryInterface
{
    /**
     * @param int $customerId
     *
     * @return \Amasty\StoreCredit\Api\Data\StoreCreditInterface
     */
    public function getByCustomerId($customerId);
}
