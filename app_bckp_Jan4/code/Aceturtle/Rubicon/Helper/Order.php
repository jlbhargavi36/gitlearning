<?php
namespace Aceturtle\Rubicon\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Order extends AbstractHelper
{
    protected $scopeConfig;

    public function __construct(Context $context, \Aceturtle\Rubicon\Helper\Data $dataHelper, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Catalog\Helper\Image $imageHelper, \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone, \Magento\Customer\Model\Session $session)
    {
        $this->dataHelper = $dataHelper;
        $this->productRepository = $productRepository;
        $this->imageHelper = $imageHelper;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
        $this->_objectManager = $objectManager;
        $this->timezone = $timezone;
	$this->session = $session;
    }

    public function getProductBySku($sku)
    {
        return $this
            ->productRepository
            ->get($sku);
    }

    public function getProductImageUrl($sku)
    {
        // $sku = 'test';
        $product = $this->getProductBySku($sku);
        $image_url = $this
            ->imageHelper
            ->init($product, 'product_page_image_medium')->setImageFile($product->getImage())
            ->resize(480, 600)
            ->getUrl();
        return $image_url;
    }

    public function dateFormat($shipmentDeliveredDate, $format = null)
    {
        if (!empty($shipmentDeliveredDate))
        {
            if ($format === null)
            {
                $format = 'Y-m-d H:i:s';
            }
            $date = $this
                ->timezone
                ->date(new \DateTime($shipmentDeliveredDate))->format($format);
            //$returnableDate = $this->timezone->date('Y-m-d H:i:s', strtotime($shipmentDeliveredDate." +7 days"));
            return $date;
        }
        else
        {
            return '';
        }
    }

    public function returnDate($shipmentDeliveredDate)
    {
        $date = $this->dateFormat($shipmentDeliveredDate);
        $this
            ->dataHelper
            ->getConfigValue('');
        $returnableDate = $yesterday = date('Y-m-d H:i:s', strtotime($shipmentDeliveredDate . '-7 days'));
        return $returnableDate;
    }

    public function isReturnable($shipmentDeliveredDate)
    {
        if (!empty($shipmentDeliveredDate))
        {
            $date = $this->dateFormat($shipmentDeliveredDate);
            $today = date('Y-m-d H:i:s');
            $retundays = $this
                ->scopeConfig
                ->getValue('rubicon_order_api/returnorder_grp/return_days', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $returnableDate = $yesterday = date('Y-m-d H:i:s', strtotime($shipmentDeliveredDate . '+ ' . $retundays . ' days'));
            $isReturnable = ($returnableDate > $today) ? 1 : 0;
        }
        else
        {
            $isReturnable = 0;
        }
        return $isReturnable;
    }

    public function getORubiconShipmentData($orderId)
    {

        $statusurl = $this
            ->scopeConfig
            ->getValue('rubicon_order_api/cancelorder_grp/orderstatusurl', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $api_key = $this
            ->scopeConfig
            ->getValue('rubicon_order_api/general/xapikey', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $param_encode = urlencode('{"order_no":"' . $orderId . '"}');
        $url = $statusurl . '/shipment?query=' . $param_encode;
        //$url = "http://localhost/test/index.php";
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'x-api-key: ' . $api_key
            ) ,
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
        return $response;

    }

    public function getCurrencycode()
    {
        $currencysymbol = $this
            ->_objectManager
            ->get('Magento\Store\Model\StoreManagerInterface');
        $currencyCode = $currencysymbol->getStore()
            ->getCurrentCurrencyCode();
        return $currencyCode;
    }

    public function getLoggedCustomer()
    {
        $customerSession = $this
            ->_objectManager
            ->get('Magento\Customer\Model\Session');
        $customerId = $customerSession->getCustomerId();
        $customerObj = $this
            ->_objectManager
            ->create('Magento\Customer\Model\Customer')
            ->load($customerId);
        return $customerObj;
    }

    public function getBillingAddress()
    {
        $billingID = $this->getLoggedCustomer()
            ->getDefaultShipping();
        $billingaddress = $this
            ->_objectManager
            ->create('Magento\Customer\Model\Address')
            ->load($billingID);
        return $billingaddress;
    }

    public function getOrder($orderid)
    {
        $order = $this
            ->_objectManager
            ->create('Magento\Sales\Api\Data\OrderInterface')
            ->load($orderid);
        return $order;
    }

    public function getCancleorderdata($ordernumber)
    {
	
	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Cancle-R3-api-data.log');
	$logger = new \Zend\Log\Logger();
	$logger->addWriter($writer);

        $rurl = $this
            ->scopeConfig
            ->getValue('rubicon_order_api/cancelorder_grp/cancelledorderdata', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $api_key = $this
            ->scopeConfig
            ->getValue('rubicon_order_api/general/xapikey', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $url = $rurl . '/?query='.urlencode('{"order_no":{"$in":["' . $ordernumber . '"]}}');

	$logger->info('Url : '. $url);
	$logger->info('x-api-key : '. $api_key);	

	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'GET',
	  CURLOPT_HTTPHEADER => array(
	    'x-api-key: '.$api_key
	  ),
	));
	$err = curl_error($curl);
	$response = curl_exec($curl);
	$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$logger->info('API Response code ' . $httpcode);
        $logger->info('API response' . $response);
        $logger->info('API error ' . $err);
	curl_close($curl);

        return $response;

    }

    public function OrderReturnResponse($shipmentno, $sku)
    {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/return_Get-api.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $url = $this
            ->scopeConfig
            ->getValue('rubicon_order_api/returnorder_grp/orderreturnlurl', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $api_key = $this
            ->scopeConfig
            ->getValue('rubicon_order_api/general/xapikey', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        //$url = $rurl.'?query={"shipment_no":"'.$shipmentno.'","sku":"'.$sku.'"}';
        // $url = "https://in.data.aceturtle.in/staging/rubicon/return_request_v1";
        $url = $url . '?query=' . urlencode('{"shipment_no":"' . $shipmentno . '","sku":"' . $sku . '"}');

        $logger->info("order create url : " . $url);
        $logger->info("order ApI Key" . $api_key);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'x-api-key: ' . $api_key
            ) ,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $logger->info('API Response code ' . $httpcode);
        $logger->info('API response' . $response);
        $logger->info('API error ' . $err);
        curl_close($curl);
        $response = json_decode($response);
        return $response;

    }

    public function getReturnStatus($orderno)
    {
	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/returned_order-api.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $rurl = $this
            ->scopeConfig
            ->getValue('rubicon_order_api/returnorder_grp/orderreturnlurl', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $api_key = $this
            ->scopeConfig
            ->getValue('rubicon_order_api/general/xapikey', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        //$url = $rurl . '/?query={"order_no":"' . $orderno . '"}';
        //$url = $rurl.'/?query={"order_no":"800000052"}';
	$param_encode = urlencode('{"order_no":"' . $orderno . '"}');
        $url = $rurl . '/?query=' . $param_encode;
  
	$logger->info("order create url : " . $url);
        $logger->info("order ApI Key" . $api_key);


	/*    */

	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'GET',
	  CURLOPT_HTTPHEADER => array(
	    'x-api-key: ' . $api_key
	  ),
	));

	$response = curl_exec($curl);
	$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$err = curl_error($curl);
	curl_close($curl);
	
	/*    */

	$logger->info('API Response code ' . $httpcode);
        $logger->info('API response' . $response);
        $logger->info('API error ' . $err);

        $response = json_decode($response);
        return $response;

    }


	public function ordertext($text, $type = "text"){

		 if($this->session->isLoggedIn()) {
			return $text;
		  }else{
			if($type=='email'){
			  return $this->mask_email($text); 
			}if($type=='phone'){
			  return $this->maskPhoneNumber($text); 
			}else{
			  return $this->mask($text); 
			}
		  }
	 }

        public function mask($string)
{
    $regex = '/(?:\d[ \t-]*?){13,19}/m';

    $matches = [];

    preg_match_all($regex, $string, $matches);

    // No credit card found
    if (!isset($matches[0]) || empty($matches[0]))
    {
        return $string;
    }

    foreach ($matches as $match_group)
    {
        foreach ($match_group as $match)
        {
            $stripped_match = preg_replace('/[^\d]/', '', $match);

            $card_length = strlen($stripped_match);
            $replacement = str_pad('', $card_length - 4, $this->_replacement) . substr($stripped_match, -4);

            // If so, replace the match
            $string = str_replace($match, $replacement, $string);
        }
    }

    return $string;
}


	function mask_email($email)
	{
	    $em   = explode("@",$email);
	    $name = implode('@', array_slice($em, 0, count($em)-1));
	    $len  = floor(strlen($name)/2);

	    return substr($name,0, $len) . str_repeat('*', $len) . "@" . end($em);   
	}


	function maskPhoneNumber($number){
    
	    $mask_number =  str_repeat("*", strlen($number)-4) . substr($number, -4);
	    
	    return $mask_number;
	}

	function addressFormatte($address){
		$email = $this->ordertext($address->getEmail(),"email");
		$tel = $this->ordertext($address->getTelephone(),"phone");
		$pin = $this->ordertext($address->getPostcode(),"phone");
		
		$address->setEmail($email);
		$address->setTelephone($tel);
		$address->setPostcode($pin);
		$address = (is_array($address->getStreet())) ? $this->streetmask($address->getStreet()) : $this->ordertext($address->getStreet()); 
		return $address;
	}

	function streetmask($street){
		foreach($street as $x => $val){
			$val[$x] = $this->ordertext($val);
		}
		return array($val);
	}
}


