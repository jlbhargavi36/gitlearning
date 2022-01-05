<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Pack\Cart;

class ProductRegistry
{
    /**
     * @var array
     */
    private $products;

    public function __construct(array $products = [])
    {
        $this->products = $products;
    }

    public function addProduct(int $productId, array $options): void
    {
        $this->products[$productId] = $options;
    }

    public function addProducts(array $products): void
    {
        $this->products = $this->products + $products;
    }

    public function getProducts(): array
    {
        return $this->products;
    }
}
