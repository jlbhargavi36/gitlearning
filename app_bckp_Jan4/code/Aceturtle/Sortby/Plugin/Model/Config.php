<?php
/**
 * Created by PhpStorm.
 * User: Amit Thakur
 * Date: 2/8/18
 * Time: 12:41 PM
 */

namespace Aceturtle\Sortby\Plugin\Model;


class Config
{
    public function afterGetAttributeUsedForSortByArray(
        \Magento\Catalog\Model\Config $catalogConfig,
        $options
    ) {
        unset($options['new']);
        unset($options['price']);
        unset($options['size']);
        unset($options['name']);
        unset($options['percentage_sorting']);
        $options['low_to_high'] = __('Price - Low To High');
        $options['high_to_low'] = __('Price - High To Low');
        //$options['percentage_sorting'] = ('Discount');
        // $options['new'] = ('Sort');
        $options['position'] = ('Sort');
//      $options['bestseller'] = ('Best Seller');
        return $options;
    }

}
