<?php
/**
 * Created by PhpStorm.
 * User: Amit Thakur
 * Date: 2/8/18
 * Time: 12:43 PM
 */

namespace Aceturtle\Sortby\Plugin\Product\ProductList;


class Toolbar
{

    /**
     * Plugin
     *
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Data\Collection $collection
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function aroundSetCollection(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
        \Closure $proceed,
        $collection
    ) {
        $currentOrder = $subject->getCurrentOrder();
        $result = $proceed($collection);
	// echo $currentOrder;
        if ($currentOrder) {
            if ($currentOrder == 'high_to_low') {
                $subject->getCollection()->getSelect()->reset("select")
                       ->columns(['high_to_low' => (new \Zend_Db_Expr("(SELECT MIN(final_price) AS minfnlprice FROM 
                       `catalog_product_super_link` slink INNER JOIN catalog_product_index_price pprice ON 
                       slink.product_id=pprice.entity_id INNER JOIN cataloginventory_stock_item pinv ON pinv.product_id=slink.product_id 
                       AND pinv.qty>0 WHERE slink.parent_id=e.entity_id GROUP BY pprice.entity_id ORDER BY minfnlprice 
                       LIMIT 1 
                       OFFSET 0)"))]);
               $subject->getCollection()->getSelect()->order('high_to_low DESC');
            
            } elseif ($currentOrder == 'low_to_high') {
                $subject->getCollection()->getSelect()->reset("select")
                       ->columns(['low_to_high' => (new \Zend_Db_Expr("(SELECT MIN(final_price) AS minfnlprice FROM 
                       `catalog_product_super_link` slink INNER JOIN catalog_product_index_price pprice ON 
                       slink.product_id=pprice.entity_id INNER JOIN cataloginventory_stock_item pinv ON pinv.product_id=slink.product_id 
                       AND pinv.qty>0 WHERE slink.parent_id=e.entity_id GROUP BY pprice.entity_id ORDER BY minfnlprice 
                       LIMIT 1 
                       OFFSET 0)"))]);
               $subject->getCollection()->getSelect()->order('low_to_high', 'asc'); 
               
            } elseif ($currentOrder == 'percentage_sorting')
            {
                $subject->getCollection()->setOrder('percentage_sorting', 'desc');
            }
            elseif ($currentOrder == 'new') {
                $subject->getCollection()->setOrder('created_at', 'desc');
            }
	    elseif ($currentOrder == 'position') {

	     $subject->getCollection()->getSelect()->reset('order');
	     $subject->getCollection()->getSelect()->order('cat_index.position DESC');

		
            }
	   elseif ($currentOrder == 'bestseller') {
                $subject->getCollection()->getSelect()->joinLeft(
                    'sales_order_item',
                    'e.entity_id = sales_order_item.product_id',
                    array('qty_ordered' => 'SUM(sales_order_item.qty_ordered)'))
                ->group('e.entity_id')
                    ->order('qty_ordered '.'desc');
            }
        }

	//echo $subject->getCollection()->getSelect()->__toString();
	//die();
        return $result;
    }

}
