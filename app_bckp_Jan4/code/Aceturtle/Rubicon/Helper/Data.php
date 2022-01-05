<?php
namespace Aceturtle\Rubicon\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const XML_RUBICON = 'rubicon_order_api/';

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getConfigValue($field, $storeId = null)
    {
        return $this
            ->scopeConfig
            ->getValue($field, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getOrderDataConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_RUBICON . 'orderdata/' . $code, $storeId);
    }

    public function getOrderDataUrl($sellerId)
    {
        return $this->getOrderDataConfig($sellerId);
    }

    public function cancelorder($finalrepostdata, $api_key, $corderurl)
    {
	$finalrepostdata = $finalrepostdata;
	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Cancle-R3-api.log');
	$logger = new \Zend\Log\Logger();
	$logger->addWriter($writer);
	$url = $corderurl.'?handler='. urlencode ('{"name":"Magento"}');
	$logger->info('Url : '. $url);
	$logger->info('x-api-key : '. $api_key);
	$logger->info('API Request --' . print_r($finalrepostdata, true));
        
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_POSTFIELDS => $finalrepostdata,
	  CURLOPT_HTTPHEADER => array(
	    'x-api-key: '.$api_key,
	    'Content-Type: application/json'
	  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);
	echo $response;



	$logger->info('API Response --' . print_r($response, true));
        if ($err)
        {
            $errr = 'error:' . $err;
	    $logger->info('API Error --' . print_r($errr, true));
        }

        return $response;
    }

    public function returnorder($returndata, $api_key, $rorderurl)
    {

	$returndata = $returndata;
	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Return-R3-api.log');
	$logger = new \Zend\Log\Logger();
	$logger->addWriter($writer);

	$logger->addWriter($writer);
	$url = $rorderurl.'?handler='. urlencode ('{"name":"Magento"}');
	$logger->info('Url : '. $url);
	$logger->info('x-api-key : '. $api_key);
	$logger->info('API Request --' . print_r($returndata, true));

       $curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_POSTFIELDS => $returndata,
	  CURLOPT_HTTPHEADER => array(
	    'x-api-key: '.$api_key,
	    'Content-Type: application/json'
	  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);

        if ($err)
        {
            $errr = 'error:' . $err;
	    $logger->info('API Error --' . print_r($errr, true));
        }
	$logger->info('API Response --' . print_r($response, true));
        return $response;
    }


	public function getPincodeStatus($pincode){

	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pincode_store_response.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
	
	
        $url = $this
            ->scopeConfig
            ->getValue('rubicon_order_api/pincodeserviceablility/pincodeurl', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $channel_id = $this
            ->scopeConfig
            ->getValue('rubicon_order_api/pincodeserviceablility/channelid', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $api_key = $this
            ->scopeConfig
            ->getValue('rubicon_order_api/general/xapikey', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

	//$api_key = "NXclhoiTYi44mmGezvBvl2jeXk2xrQ3g32RX3qoF"; 

	$logger->info("URL : ".$url);
	$logger->info("channel_id : ".$channel_id);
	$logger->info("x-api-key : ".$api_key);

        $data = array(
            "pincode" => $pincode,
            "channel_id" => $channel_id
        );

        $content = json_encode($data);
        $headers = array(
            'Content-Type: application/json',
            'x-api-key :' . $api_key
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $content,
            CURLOPT_HTTPHEADER => $headers,
        ));

        $curlResponse = curl_exec($curl);
	$error = curl_error($curl);
        curl_close($curl);
        $responseDecode = json_decode($curlResponse, true);

	 if ($responseDecode['statusCode'] == 'AT1001')
            {
                return true;
            }
	else 
	    {
		return false;
	    }
	

	}

	function getStateCode($state) {
	   try{
	      $stateCode = array( "Andaman and Nicobar Islands" => "35",
				"Andhra Pradesh" => "37",
				"Arunachal Pradesh" => "12",
				"Assam" => "18",
				"Bihar" => "10",
				"Chandigarh" => "22",
				"Chhattisgarh" => "22",
				"Dadra and Nagar Haveli" => "26",
				"Daman and Diu" => "26",
				"Delhi" => "07",
				"Goa" => "30",
				"Gujarat" => "24",
				"Haryana" => "06",
				"Himachal Pradesh" => "02",
				"Jammu and Kashmir" => "01",
				"Jharkhand" => "20",
				"Karnataka" => "29",
				"Kerala" => "32",
				"Lakshadweep" => "31",
				"Madhya Pradesh" => "23",
				"Maharashtra" => "27",
				"Manipur" => "14",
				"Meghalaya" => "17",
				"Mizoram" => "15",
				"Nagaland" => "13",
				"Odisha" => "21",
				"Puducherry" => "34",
				"Punjab" => "03",
				"Rajasthan" => "08",
				"Sikkim" => "11",
				"Tamil Nadu" => "33",
				"Telangana" => "36",
				"Tripura" => "16",
				"Uttar Pradesh" => "09",
				"Uttarakhand" => "05",
				"West Bengal" => "19");
		if (array_key_exists($state,$stateCode)){
	     	$code = $stateCode[$state]; 
		}else{
		$code = ""; 
		} 
	} catch (\Magento\Framework\Exception\LocalizedException $e) {
		$code = ""; 
	} 
	return $code;
    }
}

?>

