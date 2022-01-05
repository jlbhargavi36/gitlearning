<?php

namespace Aceturtle\Rubicon\Observer;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory;

class OrderSuccess implements ObserverInterface
{

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;
    private $productRepository;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    public $isGiftCardAmount = '0';
    public $subTotal;
    public $storeCredit;
    public $giftVoucherCode;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CollectionFactory $addressCollection,
        ProductRepositoryInterface $productRepository,
        CustomerRepositoryInterface $customerRepository,
        TimezoneInterface $timezone,
	\Magento\Framework\ObjectManagerInterface $objectmanager,
	//\Aceturtle\Qwikcilver\Model\GiftapplyFactory $giftapplyFactory,
	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
	\Aceturtle\Rubicon\Helper\Data $helper
    ) {
        $this->orderRepository = $orderRepository;
        $this->addressCollection = $addressCollection;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->timezone = $timezone;
    	$this->_objectManager = $objectmanager;
    	// $this->giftapplyFactory = $giftapplyFactory;
    	$this->scopeConfig = $scopeConfig;
	$this->_helper = $helper;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/order.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

	$url = $this->scopeConfig->getValue('rubicon_order_api/orderdata/createorderurl', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $api_key = $this->scopeConfig->getValue('rubicon_order_api/general/xapikey', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	$channel_name = $this->scopeConfig->getValue('rubicon_order_api/general/channelname', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	$channel_location_name = $this->scopeConfig->getValue('rubicon_order_api/general/channellocationname', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	
	$logger->info("order create url : ".$url);
	$logger->info("order ApI Key".$api_key);
	

        $orderIds = $observer->getEvent()->getOrderIds();

        foreach ($orderIds as $orderId) {
            $order = $this->orderRepository->get($orderId);
	    $statuscode = $order->getStatus();
	//$statuscode = "processing";

	$paymentInfo = $order->getPayment();
	$method = $paymentInfo->getMethodInstance();

	  if($statuscode == "processing" || $method->getCode()=='cashondelivery') {
	       /*  R3 API Code */
    	$data = $this->getOrderDetails($order);
	$logger->info('API Body' . json_encode($data));
        $content = json_encode($data);


  	
	
	 try {
		$curl = curl_init();

		curl_setopt_array($curl, array(
		//https://in.data.aceturtle.in/test/rubicon/order_v2?handler={"name":"Magento"}
		 // CURLOPT_URL => 'https://in.data.aceturtle.in/test/rubicon/order_v2?handler=%7B%22name%22:%22Magento%22%7D',
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => $content,
		  CURLOPT_HTTPHEADER => array(
		    'x-api-key: '.$api_key,
		    'Content-Type: application/json'
		  ),
		));

	$response = curl_exec($curl);
	$err = curl_error($curl);
	$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$logger->info('API Response code ' . $httpcode);
	$logger->info('API response' . $response);
	$logger->info('API error ' . $err);
	curl_close($curl);


	if($httpcode=='200' && $method->getCode()=='cashondelivery'){
		$newState = \Magento\Sales\Model\Order::STATE_PROCESSING;
		$order->setState($newState)->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
		$order->save();
	}

	$model = $this->_objectManager->create('Aceturtle\Rubicon\Model\Items');
	$model->setOrderno($order->getIncrementId());
	$model->setApiResponse($response);
	$model->setApiResponse($response);
	$model->setApiStatus($httpcode);
	$model->setApiAttempts(1);
	$model->save();
	 } catch (\Magento\Framework\Exception\LocalizedException $e) {
	  // $this->messageManager->addError($e->getMessage());
	$logger->info('API error' . json_encode($e->getMessage()));
	}

	/* R3 API Codes End here */
	} // checking if condition for status and cod

           } 
    }

    public function getShippingAddress($order)
    {
        $shipAddress = '';
        if (!$order->getIsVirtual()) {
            $orderShippingId = $order->getShippingAddressId();
            $shipAddress = $this->addressCollection->create()->addFieldToFilter('entity_id', [$orderShippingId])->getFirstItem()->getData();
        }
        return $shipAddress;
    }

    public function getBillingAddress($order)
    {
        $orderBillingId = $order->getBillingAddressId();
        $billAddress = $this->addressCollection->create()->addFieldToFilter('entity_id', [$orderBillingId])->getFirstItem()->getData();
        return $billAddress;
    }
    public function loadProduct($sku)
    {
        return $this->productRepository->get($sku);
    }

	
      /********************* ***********/

    public function getItemDetails($item)
    {

	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cron-orderApi-item.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

	$channel_name = $this->scopeConfig->getValue('rubicon_order_api/general/channelname', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	$channel_location_name = $this->scopeConfig->getValue('rubicon_order_api/general/channellocationname', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

	$logger->info('in Item dump :'. json_encode($item->debug()));
	if($item->getProductType()=='configurable'){	// If Configurable products
	// Checking for Super Attributes
	$attrInfo = $item->getProductOptions();
		foreach($attrInfo['attributes_info'] as $supAttr){
			$opton[$supAttr['label']] = $supAttr['value'];
			if($supAttr['label'] == 'size' || $supAttr['label'] == 'Size'){
				$opton['size'] = $supAttr['value'];
			}
			if($supAttr['label'] == 'color' || $supAttr['label'] == 'Color'){
				$opton['color'] = $supAttr['value'];
			}
		}
	}

        $data = [];
        $data['item_no'] = $item->getItemId();
        $data['channel_shipment_id'] = "";
        $data['channel_location_id'] = "";
        $data['channel_location_name'] = $channel_location_name; //"Lee Monobrand";
        $data['additional_details'] = array(
            'mp_sku' => $item->getSku()
        );
        $data['name'] = $item->getName();
        $data['brand_id'] = "";
        $data['brand_name'] = 'Rayban';
        $data['seller_sku'] = $item->getSku();
        $data['sku'] = $item->getSku();
        $data['mp_sku'] = $item->getSku();
        $data['article_no'] = '';
        $data['ean'] = '';
        $data['upc'] = '';
        $data['color'] = (isset($opton['color'])) ? $opton['color'] : '';
        $data['size'] = (isset($opton['size'])) ? $opton['Size'] : '';
        $data['category'] = ''; //$item->getName();
        $data['short_description'] = $item->getName();
        $data['package_name'] = '';
        $data['height'] = "0";
        $data['weight'] = "250";
        $data['breadth'] = "0";
        $data['length'] = "0";
        $data['qty_ordered'] = $item->getQtyOrdered();
        $data['qty_cancelled'] = "0";
        $data['reason_for_cancellation'] = '';
        $data['on_hold'] = '';
        $data['expected_delivery_time'] = null;
        $data['processing_start_time'] = '';
        $data['processing_cutoff_time'] = null;
        $data['cod_amount_due'] = "0";
        $data['is_replacement'] = false;
	$data['gift_voucher_code'] = $this->giftVoucherCode;
        $data['pricing_details']['tax_amount'] = 0; //$item->getTaxAmount();
        $data['pricing_details']['tax_type'] = '';
        $data['pricing_details']['tax_percent'] = '0';//$item->getTaxPercent();
        $data['pricing_details']['igst_amount'] = 0;
        $data['pricing_details']['igst_percent'] = 0;
        $data['pricing_details']['cgst_amount'] = 0;
        $data['pricing_details']['cgst_percent'] = 0;
        $data['pricing_details']['sgst_amount'] = 0;
        $data['pricing_details']['sgst_percent'] = 0;
        $data['pricing_details']['discount_percent'] = number_format($item->getDiscountPercent(),2,".",""); //$item->getDiscountPercent();
        $data['pricing_details']['discount_amount'] = number_format($item->getDiscountAmount(),2,".",""); //$item->getDiscountAmount();
        $data['pricing_details']['mrp'] = number_format($item->getOriginalPrice(),2,".",""); //$item->getOriginalPrice();
        $data['pricing_details']['price'] = number_format($item->getPrice(),2,".",""); //$item->getPrice();
        $data['pricing_details']['unit_marketplace_discount'] = 0;
        $data['pricing_details']['unit_seller_discount'] = 0;
        $data['pricing_details']['shipping_amount'] = 0;
        $data['pricing_details']['item_seller_promotional_discount'] = 0;
        $data['pricing_details']['item_marketplace_discount'] = 0;
        $data['pricing_details']['shipping_tax'] = 0;
        $data['pricing_details']['grand_total'] = number_format($item->getRowTotal(),2,".","");;
	if($this->storeCredit>0){
	$itemRowlevelStoreCredit = ($item->getRowTotal() / $this->subTotal) * $this->storeCredit; 
	$data['pricing_details']['store_credit'] = number_format($itemRowlevelStoreCredit,2,".","");;
	$logger->info('Item StoreCredit amount = '. $itemRowlevelStoreCredit. ' = Row Total '. $item->getRowTotal() .'/ subtotal '.$this->subTotal .' * Total StoreCredit Amount'. $this->storeCredit);
	}else{
	$data['pricing_details']['store_credit'] = 0;
	}
	if($this->isGiftCardAmount>0){
		$itemRowlevelgiftCard = ($item->getRowTotal() / $this->subTotal) * $this->isGiftCardAmount;
		$data['pricing_details']['gift_voucher_amount'] = number_format($itemRowlevelgiftCard,2,".","");;
		$logger->info('Item Giftcard amount = '. $itemRowlevelgiftCard. ' = Row Total '. $item->getRowTotal() .'/ subtotal '.$this->subTotal .' * Total GiftCard Amount'. $this->isGiftCardAmount);
	}else{
	    $data['pricing_details']['gift_voucher_amount'] = 0;
	}
	

        $data["courier_details"] = array(
            "courier_code" => "",
            "courier_id" => null,
            "courier_name" => "",
            "marketplace_courier_id" => null
        );
        $data['return_id'] = '';
        $data['shipment_ids'] = array();
        $data['image_link1'] = '';
	$logger->info('in Item dump :'. json_encode($data));
        return $data;
    }

    public function getOrderDetails($order)
    {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cron-orderApi.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

	$channel_name = $this->scopeConfig->getValue('rubicon_order_api/general/channelname', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	$channel_location_name = $this->scopeConfig->getValue('rubicon_order_api/general/channellocationname', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

	$this->subTotal = $order->getSubtotal();
	$logger->info(json_encode($order->getDiscountDescription()));
	$logger->info("condition :".json_encode(strpos($order->getDiscountDescription(), 'Giftcard')));
	if (strpos($order->getDiscountDescription(), "Giftcard") !== false) {
	/*$logger->info('inside Gift-card condition');
		
		//$giftapply = $this->giftapplyFactory->create();
		$giftApplyCollection = $this->giftapplyFactory->create()->getCollection()->addFieldToFilter('order_no', ['eq' => $order->getIncrementId()]);
		    if($giftApplyCollection) {
			$logger->info('inside if condition giftApplyCollection');
		    	foreach ($giftApplyCollection as $giftApplyData) {
		    		$this->isGiftCardAmount = $giftApplyData->getAmount();
				$this->giftVoucherCode = $giftApplyData->getAccountNo();
				$logger->info('gift card amount + '. $this->isGiftCardAmount);
		    	}
		    }
	*/
	}
	$this->storeCredit = "0"; //$order->getAmstorecreditAmount();
        $result['is_active'] = true;
        $result['country_code'] = "IN";
        $result['origin_store_id'] = null;
        $result['closed'] = false;
        $result['is_shipment_created'] = false;
        $result['channel_name'] = $channel_name; //"Lee Monobrand";
        $result['channel_id'] = '';
        foreach ($order->getAllVisibleItems() as $item)
        {
            $getItemDetails[] = array_merge($this->getItemDetails($item));
            $logger->info(json_encode($getItemDetails));
        }
        $result['order_items'] = $getItemDetails;

        $result['collection_point_id'] = null;
        $result['instant_click_and_collect'] = '';
        $result['delivery_type'] = 'Home Delivery';
        $result['delivery_type_original'] = 'home_delivery';
        $result['member_no'] = '';
        $result['shipping_amount'] = $order->getShippingAmount();
        $result['customer_credit_amount'] = '';//$order->getAmstorecreditAmount();
	if($this->isGiftCardAmount>0){
		$discountAmount = abs($order->getDiscountAmount()) - $this->isGiftCardAmount;
	}else{
		$discountAmount = abs($order->getDiscountAmount());
	}	
        $result['coupon_amount'] = $discountAmount;
        $result['coupon_code'] = str_replace("Giftcard","",$order->getDiscountDescription());
	
        $result['csr_emp_name'] = '';
        $result['csr_emp_id'] = '';
        $result['order_collection_to_time'] = '';
        $result['shipping_method'] = 'Standard';
        $result['shipping_method_original'] = 'Standard';
        $result['updated_at'] = $this
            ->timezone
            ->date($order->getCreatedAt())
            ->format('Y-m-d H:i:s');
        $result['created_at'] = $this
            ->timezone
            ->date($order->getCreatedAt())
            ->format('Y-m-d H:i:s');
        $result['order_date'] = $order->getCreatedAt();
        $payment = [];
        $paymentInfo = $order->getPayment();
        $method = $paymentInfo->getMethodInstance();
        $result['payment_method'] = ($method->getCode()=='cashondelivery')? "COD" : "Prepaid"; //$method->getTitle();
        $result['payment_method_original'] = $method->getCode();
        $result['payment_transaction_id'] = $paymentInfo->getLastTransId();
        /*  */
        
        $payment_details = array(
            "payment_mode" => ($method->getCode()=='cashondelivery')? "COD" : $method->getCode(),
            "card_type" => '' ,
            "card_category" => '' ,
            "payment_amount" => ($method->getCode()=='cashondelivery')? $paymentInfo->getAmountOrdered() : $paymentInfo->getBaseAmountPaidOnline() ,
            "transaction_ref_no" => $paymentInfo->getLastTransId(),
            "transaction_charge" => '' 
        );
        $result['payment_details'] = array($payment_details);
        
        /*  */
        $result['order_total'] =  number_format($order->getGrandTotal(),2,".",""); //$order->getGrandTotal(); //$order->getTotalQtyOrdered();
        $result['grand_total'] = number_format($order->getGrandTotal(),2,".",""); //$order->getGrandTotal();
        $result['tax_amount'] = 0; //$order->getTaxAmount();
        $billingAddress = $order->getBillingAddress($order);
        $logger->info("Billing address " . json_encode($billingAddress->getData()));
        $street = $billingAddress->getStreet();
	$address = (is_array($street)) ? implode(", ",$street) : $street;
	$stateCodeId = $this->_helper->getStateCode($billingAddress->getRegion());
        $result['billing_address'] = array(
            "address" => preg_replace('/[^A-Za-z0-9. -]/', '', $address),
            "city" => $billingAddress->getCity() ,
            "country" => $billingAddress->getCountryId() ,
            "email" => $billingAddress->getEmail() ,
            "locality" => '',
            "name" => $billingAddress->getFirstname() . " " . $billingAddress->getLastname() ,
            "phone" => $billingAddress->getTelephone() ,
            "pin" => $billingAddress->getPostcode() ,
            "state" => $billingAddress->getRegion(),
	    "state_code" => $stateCodeId
        );
        $shippingAddress = $order->getShippingAddress($order);
        $street = $shippingAddress->getStreet();
	$address = (is_array($street)) ? implode(", ",$street) : $street;
	$mobile = $shippingAddress->getTelephone();
	$stateCodeId = $this->_helper->getStateCode($shippingAddress->getRegion());
        $result['shipping_address'] = array(
            "address" => preg_replace('/[^A-Za-z0-9. -]/', '', $address),
            "city" => $shippingAddress->getCity() ,
            "country" => $shippingAddress->getCountryId() ,
            "email" => $shippingAddress->getEmail() ,
            "locality" => '',
            "name" => $shippingAddress->getFirstname() . " " . $shippingAddress->getLastname() ,
            "phone" => $shippingAddress->getTelephone() ,
            "pin" => $shippingAddress->getPostcode() ,
            "state" => $shippingAddress->getRegion(),
	    "state_code" => $stateCodeId
        );
        $result['customer_details'] = array(
            "name" => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname() ,
            "email" => $order->getCustomerEmail() ,
            "phone" => $mobile,
            "region" => ""
        );
        $result['order_no'] = $order->getIncrementId();

        $logger->info(json_encode($result));
        return $result; 

    }

    /************************ ************/

}
