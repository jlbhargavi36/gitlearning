/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Configurable variation left qty.
 */
define([
    'jquery',
    'underscore',
    'mage/url'
], function ($, _, urlBuilder) {
    'use strict';

    return function (productSku, salesChannel, salesChannelCode) {
        var selectorInfoStockSkuQty = '.availability.only',
            selectorInfoStockSkuQtyValue = '.availability.only > strong',
            productQtyInfoBlock = $(selectorInfoStockSkuQty),
            productQtyInfo = $(selectorInfoStockSkuQtyValue);

        if (!_.isUndefined(productSku) && productSku !== null) {
            $.ajax({
                url: urlBuilder.build('inventory_catalog/product/getQty/'),
                dataType: 'json',
                data: {
                    'sku': productSku,
                    'channel': salesChannel,
                    'salesChannelCode': salesChannelCode
                }
            }).done(function (response) {
                if (response.qty !== null) {
                    productQtyInfo.text(response.qty);
		    console.log(response.qty);
                    	if(response.qty>0){
                    	productQtyInfoBlock.show();
			}else{
			productQtyInfoBlock.hide();
			}
                } else {
                    productQtyInfoBlock.hide();
                }
            }).fail(function () {
                productQtyInfoBlock.hide();
            });
        } else {
            productQtyInfoBlock.hide();
        }
    };
});
