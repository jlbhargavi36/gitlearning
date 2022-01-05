<?php

namespace Aceturtle\Rubicon\Observer;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory;

class BeforeOrderPlace implements ObserverInterface
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

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CollectionFactory $addressCollection,
        ProductRepositoryInterface $productRepository,
        CustomerRepositoryInterface $customerRepository,
        TimezoneInterface $timezone,
	\Magento\Framework\ObjectManagerInterface $objectmanager
    ) {
        $this->orderRepository = $orderRepository;
        $this->addressCollection = $addressCollection;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->timezone = $timezone;
	$this->_objectManager = $objectmanager;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/before_order.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $event = $observer->getEvent();
        $order = $observer->getEvent()->getOrder();
       /* $logger->info('orderData' . json_encode($order));

	$data = $this->getOrderDetails($order);
	$logger->info('API Body' . json_encode($data));
        $content = json_encode($data);
	 try {
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://in.data.aceturtle.in/test/rubicon/order_v2?handler=%7B%22name%22:%22Magento%22%7D',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => $content,
		  CURLOPT_HTTPHEADER => array(
		    'x-api-key: wQz6sGxko3UMc10Sg2Ur1dEbcThS3D4aelv9cB9e',
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

	$model = $this->_objectManager->create('Aceturtle\Orderlog\Model\Items');
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
*/

/*
        $headers = array(
            'Accept: application/json',
            'x-api-key: wQz6sGxko3UMc10Sg2Ur1dEbcThS3D4aelv9cB9e'
        );
        $ch = curl_init('https://in.data.aceturtle.in/test/rubicon/order_v2?handler={"name":"Magento"}');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	
        $response = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);
	$logger->info('API Response code ' . $httpcode);
	$logger->info('API response' . $response);
	$logger->info('API error ' . $err);
        //return $response;
       // print_r($err);
       //print_r($response);
*/

        //foreach ($orderIds as $orderId) {
            $result = [];
            $result['correlation_id'] = rand();
            $result['topic'] = "MAGNETO/ORDER.CHECKOUT/CHECKOUT.CREATE";
            $result['subject'] = "MAGENTO/NOMADX/ORDER.CHECKOUT.CREATE";
            $result['eventType'] = "ORDER/CREATE";
            $result['data'] = [];
            //$order = $this->orderRepository->get($orderId);

            $customer = [];
            $customer['first_name'] = $order->getCustomerFirstname();
            $customer['last_name'] = $order->getCustomerLastname();
            $customer['email'] = $order->getCustomerEmail();
            $result['data']['customer'] = $customer;

            $orderData = [];
            $orderData['channel'] = "Rayban Monobrand";

            $orderData['status'] = $order->getStatus();
            $orderData['delivery_type'] = $order->getDeliveryType();
            if (!empty($order->getCollectionAddress())) {
                $orderData['collection_address'] = $order->getCollectionAddress();
            }

            $orderData['increment_id'] = $order->getIncrementId();
            $orderData['shipping_method'] = $order->getShippingMethod();
            $orderData['shipping_amount'] = $order->getShippingAmount();
            if ($order->getDiscountAmount()) {
                $orderData['discount_amount'] = str_replace('-', '', $order->getDiscountAmount());
            }
            $orderData['tax_amount'] = $order->getTaxAmount();
            //$orderData['created_at'] = $order->getCreatedAt();

            $orderData['created_at'] = $this->timezone->date($order->getCreatedAt())->format('Y-m-d H:i:s');

            $orderData['total'] = $order->getGrandTotal();
            $orderData['total_qty'] = $order->getTotalQtyOrdered();
            $orderData['coupon_code'] = $order->getDiscountDescription();
            $orderItems = [];
            foreach ($order->getAllVisibleItems() as $item) {
                $orderItems[] = array_merge($item->debug());
            }
            $orderData['items'] = $orderItems;
            $result['data']['orderData'] = $orderData;

            $address = [];
            $shippingAddress = $this->getShippingAddress($order);
            if (isset($shippingAddress)) {
                $address['shipping_address'] = $shippingAddress;
            }
            $billingAddress = $this->getBillingAddress($order);
            $address['billing_address'] = $billingAddress;
            $result['data']['address'] = $address;

            $payment = [];
            $paymentInfo = $order->getPayment();
            $method = $paymentInfo->getMethodInstance();
            $paymentTitle = $method->getTitle();
            $paymentCode = $method->getCode();
            $payment['code'] = $paymentCode;
            $payment['title'] = $paymentTitle;
            $result['data']['payment'] = $payment;
            $result['data']['payment']['last_transaction_id'] = $paymentInfo->getLastTransId();

            $logger->info(json_encode($result));
            // if ($paymentCode == 'cashondelivery' || $order->getStatus() == 'processing') {
            //     $logger->info(json_encode($result));
            // }
        //}
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
        $data['channel_shipment_id'] = "TEST04S107317424";
        $data['channel_location_id'] = null;
        $data['channel_location_name'] = "DV00305972";
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
        $data['weight'] = "0";
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
        $data['pricing_details']['tax_amount'] = $item->getTaxAmount();
        $data['pricing_details']['tax_type'] = '';
        $data['pricing_details']['tax_percent'] = $item->getTaxPercent();
        $data['pricing_details']['igst_amount'] = 0;
        $data['pricing_details']['igst_percent'] = 0;
        $data['pricing_details']['cgst_amount'] = 0;
        $data['pricing_details']['cgst_percent'] = 0;
        $data['pricing_details']['sgst_amount'] = 0;
        $data['pricing_details']['sgst_percent'] = 0;
        $data['pricing_details']['discount_percent'] = $item->getDiscountPercent();
        $data['pricing_details']['discount_amount'] = $item->getDiscountAmount();
        $data['pricing_details']['mrp'] = $item->getOriginalPrice();
        $data['pricing_details']['price'] = $item->getPrice();
        $data['pricing_details']['unit_marketplace_discount'] = 0;
        $data['pricing_details']['unit_seller_discount'] = 0;
        $data['pricing_details']['shipping_amount'] = 0;
        $data['pricing_details']['item_seller_promotional_discount'] = 0;
        $data['pricing_details']['item_marketplace_discount'] = 0;
        $data['pricing_details']['shipping_tax'] = 0;
        $data['pricing_details']['grand_total'] = $item->getRowTotal();

        $data["courier_details"] = array(
            "courier_code" => "",
            "courier_id" => null,
            "courier_name" => "",
            "marketplace_courier_id" => null
        );
        $data['return_id'] = '';
        $data['shipment_ids'] = array();
        $data['image_link1'] = '';
        return $data;
    }

    public function getOrderDetails($order)
    {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/orderApi.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

	

        $result['is_active'] = true;
        $result['country_code'] = "IN";
        $result['origin_store_id'] = null;
        $result['closed'] = false;
        $result['is_shipment_created'] = false;
        $result['channel_name'] = "Ajio";
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
        $result['customer_credit_amount'] = $order->getAmstorecreditAmount();
        $result['coupon_amount'] = $order->getDiscountAmount();
        $result['coupon_code'] = $order->getDiscountDescription();
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
        $result['payment_method'] = $method->getTitle();
        $result['payment_method_original'] = $method->getCode();
        $result['payment_transaction_id'] = $paymentInfo->getLastTransId();
        $result['order_total'] = $order->getTotalQtyOrdered();
        $result['grand_total'] = $order->getGrandTotal();
        $result['tax_amount'] = $order->getTaxAmount();
        $billingAddress = $order->getBillingAddress($order);
        $logger->info("Billing address " . json_encode($billingAddress->getData()));
        $street = $billingAddress->getStreet();
        $result['billing_address'] = array(
            "address" => $street[0] . " " . $street[1],
            "city" => $billingAddress->getCity() ,
            "country" => $billingAddress->getCountryId() ,
            "email" => $billingAddress->getEmail() ,
            "locality" => '',
            "name" => $billingAddress->getFirstname() . " " . $billingAddress->getLastname() ,
            "phone" => $billingAddress->getTelephone() ,
            "pin" => $billingAddress->getPostcode() ,
            "state" => $billingAddress->getRegion()
        );
        $shippingAddress = $order->getShippingAddress($order);
        $street = $shippingAddress->getStreet();
        $result['shipping_address'] = array(
            "address" => $street[0] . " " . $street[1],
            "city" => $shippingAddress->getCity() ,
            "country" => $shippingAddress->getCountryId() ,
            "email" => $shippingAddress->getEmail() ,
            "locality" => '',
            "name" => $shippingAddress->getFirstname() . " " . $shippingAddress->getLastname() ,
            "phone" => $shippingAddress->getTelephone() ,
            "pin" => $shippingAddress->getPostcode() ,
            "state" => $shippingAddress->getRegion()

        );
        $result['customer_details'] = array(
            "name" => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname() ,
            "email" => $order->getCustomerEmail() ,
            "phone" => "NA",
            "region" => ""
        );
        $result['order_no'] = $order->getIncrementId();

        $logger->info(json_encode($result));
        return $result;

    }

    /************************ ************/

	
}
