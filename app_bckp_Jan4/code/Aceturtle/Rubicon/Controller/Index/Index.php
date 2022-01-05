<?php
namespace Aceturtle\Rubicon\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_helper;
    
    public function __construct(\Magento\Framework\App\Action\Context $context, \Aceturtle\Rubicon\Helper\Data $helper, \Magento\Framework\Controller\Result\JsonFactory $resultPageFactory)
    {
        $this->_helper = $helper;
        parent::__construct($context);
	$this->resultPageFactory = $resultPageFactory;
    }
    
    public function execute()
    {
            $pincode = $this->getRequest()->getParam('pincode');
            $pincodeStatus = $this->_helper->getPincodeStatus($pincode);

            if($pincodeStatus){
                $message = "<h3 class='pincode_avail'>Pincode is serviceable , Delivery Timelines between 4 to 7 Days.</h3>";
            }else{
                $message = "<h3 class='pincode_notavail'>Pincode not serviceable</h3>";
            }
            
            echo $message;
    }
    
}
