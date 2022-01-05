<?php

declare(strict_types=1);

namespace Amasty\Xsearch\Model\Slider;

use Magento\Catalog\Model\Product;

interface SliderProductsProviderInterface
{
    /**
     * @return Product[]
     */
    public function getProducts(): iterable;
}
