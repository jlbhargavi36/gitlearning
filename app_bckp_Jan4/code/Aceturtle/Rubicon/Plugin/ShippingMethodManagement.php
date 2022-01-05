<?php
namespace Aceturtle\Rubicon\Plugin;

use Magento\Quote\Api\Data\AddressInterface;

class ShippingMethodManagement
{
    public $pincode;

    public function __construct(
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->addressRepository = $addressRepository;
	$this->scopeConfig = $scopeConfig;
    }

    public function beforeEstimateByExtendedAddress(\Magento\Quote\Model\ShippingMethodManagement $subject,$cartId, AddressInterface $address)
    {
        $data = $address->getData();
        $pin = $address->getData('postcode');
        $this->fetchPincode($pin);
    }

    public function beforeEstimateByAddressId(\Magento\Quote\Model\ShippingMethodManagement $subject,$cartId, $addressId){
        $address = $this->addressRepository->getById($addressId);
        //var_dump($address);exit;
        $pin = $address->getPostcode();
        $this->fetchPincode($pin);
    }

    public function afterEstimateByAddressId(\Magento\Quote\Model\ShippingMethodManagement $subject, $result)
    {
        $pincode = $this->pincode;
        return $this->triggerValidatePincode($pincode,$result);

    }


    public function afterEstimateByExtendedAddress(\Magento\Quote\Model\ShippingMethodManagement $subject, $result)
    {
        $pincode = $this->pincode;
        return $this->triggerValidatePincode($pincode,$result);
    }

    public function fetchPincode($pin){
        $this->pincode = $pin;
    }

    public function triggerValidatePincode($pincode,$result)
    {
        $emptyShippingMethods = array();
        if($this->validatePincode($pincode) || $pincode === null)
        {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pincode_default_success.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $pincodeResponse = array(
                'pincode_response' =>
                    array(
                        'pincode' => $pincode,
                        'status' => 'success'
                    )
            );
            $logger->info(json_encode($pincodeResponse));
            return $result;
        }
        else
        {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pincode_default_error.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $pincodeResponse = array(
                'pincode_response' =>
                    array(
                        'pincode' => $pincode,
                        'status' => 'error'
                    )
            );
            $logger->info(json_encode($pincodeResponse));
            return $emptyShippingMethods;
        }
    }


public function validatePincode($pincode)
    {

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

	$api_key = "WU5pMkhCsW5avAz5DLySS8hkYbjnMrs986ubM7Sh"; 


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

        //  curl_close($ch);
        
        $pincodeResponse = array(
            'pincode_response' => array(
                'request' => $data,
                'response' => $responseDecode,
                'error' => $error
            )
        );
        $logger->info(json_encode($pincodeResponse));

        if ($error)
        {
            $pincodeResponse = array(
                'pincode_curlerror' => array(
                    'request' => $data,
                    'error' => $error
                )
            );
            $logger->error(json_encode($pincodeResponse));
            return true;
        }
        else
        {
            if ($responseDecode['statusCode'] == 'AT1001')
            {
                return true;
            }
            elseif ($responseDecode['statusCode'] != 'AT1001')
            {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pincode_response.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $pincodeResponse = array(
                    'pincode_response_error' => array(
                        'data' => $data,
                        'pincode_response_error' => 'error'
                    )
                );
                $logger->info(json_encode($pincodeResponse));
                return false;
            }
            else
            {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/pincode_response.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $pincodeResponse = array(
                    'pincode_response_unknown' => array(
                        'data' => $data,
                        'pincode_response_unknown' => 'unknown response',
                    )
                );
                $logger->info(json_encode($pincodeResponse));
                return false;
            }
        }
    }



}
