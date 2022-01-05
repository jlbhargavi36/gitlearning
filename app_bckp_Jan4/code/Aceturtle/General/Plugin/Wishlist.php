<?php
/**
 * Created by PhpStorm.
 * User: amit
 * Date: 26/3/19
 * Time: 1:17 PM
 */

namespace Aceturtle\General\Plugin;


class Wishlist
{
    public function afterGetSharedStoreIds(\Magento\Wishlist\Model\Wishlist $wishlist, $result) {
        return $wishlist->getStore()->getId();
    }
}