<?php

namespace Amasty\CPS\Api\Data;

interface BrandProductInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const MAIN_TABLE = 'amasty_brand_product';
    const AMBRAND_ID = 'ambrand_id';
    const PRODUCT_ID = 'product_id';
    const STORE_ID = 'store_id';
    const POSITION = 'position';
    const IS_PINNED = 'is_pinned';
    /**#@-*/

    const BRAND_USE_DEFAULT_STORE_SETTING = 'use_default_store_sorting';

    /**
     * @param int $brandId
     * @param int $storeId
     * @return array
     */
    public function getBrandProductPositionDataByStore($brandId, $storeId);

    /**
     * @param int $brandId
     * @param array $productPositionData
     * @param array$pinnedProductIds
     * @return BrandProductInterface
     */
    public function updateBrandProductPositionDataByBrand($brandId, $productPositionData = [], $pinnedProductIds = []);

    /**
     * @return BrandProductInterface
     */
    public function clearBrandsPositionData();
}
