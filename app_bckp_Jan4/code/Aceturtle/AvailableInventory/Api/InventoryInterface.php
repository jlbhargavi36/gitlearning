<?php

namespace Aceturtle\AvailableInventory\Api;

interface InventoryInterface
{
    /**
     * @param mixed $data
     * @return mixed
     */
    public function updateInventory($data);

    /**
     * @param mixed $sku
     * @return mixed
     */
    public function getInventory($sku);
}
