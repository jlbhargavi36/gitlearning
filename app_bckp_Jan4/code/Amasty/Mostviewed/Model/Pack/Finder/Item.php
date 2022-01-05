<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Pack\Finder;

class Item
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $productId;

    /**
     * @var float
     */
    private $qty;

    public function init(int $id, int $productId, float $qty): void
    {
        $this->id = $id;
        $this->productId = $productId;
        $this->qty = $qty;
    }

    public function decrease(float $qty): void
    {
        $this->qty -= $qty;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getQty(): float
    {
        return $this->qty;
    }
}
