<?php
namespace Aceturtle\Rubicon\Controller\Index;

class Cancel extends \Magento\Framework\App\Action\Action
{
    protected $_helper;
    protected $scopeConfig;

    public function __construct(
	\Magento\Framework\App\Action\Context $context, 
	\Aceturtle\Rubicon\Helper\Data $helper, 
	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
	\Magento\Framework\Message\ManagerInterface $messageManager
	)
    {
        $this->_helper = $helper;
        $this->scopeConfig = $scopeConfig;
	$this->_messageManager = $messageManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $postData = $this->getRequest()
            ->getParams();

        $data = array(
            "sku" => $postData['sku'],
            "shipment_no" => $postData['shipment_no'],
            "cancel_type" => 'website',
            //"cancel_reason" => $postData['reason'],
            "cancel_reason" => 'Product Not Required',
            "created_by" => $postData['created_by'],
            "cancel_date" => date("Y-m-d h:i:s") ,
            "qty_to_cancel" => $postData['qty_to_cancel'],
            "status" => 18
        );

        $finalrepostdata = json_encode($data, true);
        //print_r($finalrepostdata);
        //$finalrepostdata  = json_decode($finalresponse);
        //print_r($finalrepostdata);
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cancelorder.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Cancel data ' . print_r($finalrepostdata, true));

        $corderurl = $this
            ->scopeConfig
            ->getValue('rubicon_order_api/cancelorder_grp/ordercancelurl', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $api_key = $this
            ->scopeConfig
            ->getValue('rubicon_order_api/general/xapikey', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $apiResponse = $this
            ->_helper
            ->cancelorder($finalrepostdata, $api_key, $corderurl,);

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cancelorder.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        //$logger->info('Simple Text Log'); // Simple Text Log
        $logger->info('API Response --' . print_r($apiResponse, true));
	
	$this->_messageManager->addSuccess(__("Item has been canceled."));
        echo $apiResponse;
    }

}


