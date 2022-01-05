Setup:
$ php bin/magento maintenance:enable
$ php bin/magento module:enable Aceturtle_CartPopup
$ php bin/magento setup:upgrade
$ php bin/magento cache:clean
$ php bin/magento maintenance:disable


Description:

This extension have ajax add to cart feature. Once user will click on add to cart button then popup will open, with mini bag details like product image,sku,name,price and description. Two button will display at the bottom of the pop up. one is for continue shopping and second is for go to cart page. 
