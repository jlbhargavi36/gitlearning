<?php
namespace Aceturtle\Rubicon\Controller\Index;

class Returnorder extends \Magento\Framework\App\Action\Action
{
    protected $_helper;
    protected $scopeConfig;
    protected $resultJsonFactory;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Aceturtle\Rubicon\Helper\Data $helper, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory)
    {
        $this->_helper = $helper;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $resultJson = $this
            ->resultJsonFactory
            ->create();
        $postData = $this->getRequest()
            ->getParams();

        if ($postData['payment'] == 'cashondelivery')
        {
            $paymentmethod = 'cod';
        }
        $pickupAddress = $postData['firstname'] . ' ' . $postData['lastname'] . ", " . $postData['street'] . ", " . $postData['city'] . ", " . $postData['region'] . ", " . $postData['postcode'];

        $data = array(
            "sku" => $postData['sku'],
            "return_status" => "Return Requested",
            "return_address" => $pickupAddress,
            "is_pickup_required" => 'true',
            "return_type" => "RMA",
            "is_active" => true,
            "created_at" => date("Y-m-d h:i:s") ,
            "created_by" => $postData['firstname'],
            "reason_to_return" => $postData['reason'],
            "return_date" => date("Y-m-d h:i:s") ,
            "qty_to_return" => $postData['itemqty'],
            "shipment_no" => $postData['shipment_no'],
            "status" => 20,
            "channel_return_no" => "TEST12348",
	    "return_pickup" => true,
	    "return_dropoff"=> false
        );

        //$data = array("rubiconData"=>$data);
        $returndata = json_encode($data, true);
        // print_r($returndata);
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/returnorder.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('return data ' . print_r($returndata, true));

        $rorderurl = $this->scopeConfig->getValue('rubicon_order_api/returnorder_grp/orderreturnlurl', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $api_key = $this
            ->scopeConfig
            ->getValue('rubicon_order_api/general/xapikey', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $apiResponse = $this->_helper->returnorder($returndata, $api_key, $rorderurl);

        $logger->info('API Response --' . print_r($apiResponse, true));
        //echo $apiResponse;
        $response = ['success' => 'true', 'return' => $apiResponse];
        $resultJson->setData($response);
        $apiResponseArray = json_decode($apiResponse, true);

        $json = $this->resultJsonFactory->create();
        return $json->setData($apiResponseArray);
    }
}


