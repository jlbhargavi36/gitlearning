<?php

namespace Aceturtle\General\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Twilio\Rest\Client;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Catalog\Model\Product\Gallery\ImagesConfigFactoryInterface;
use Magento\Catalog\Model\Product\Image\UrlBuilder;
use Magento\Framework\App\ObjectManager;

class Data extends AbstractHelper
{
    const XML_RUBICON = 'rubicon/';
    const XML_PATH_TWILIO = 'twilio/';
    const XML_OTP_MESSAGE = 'otp_message';
    const XML_MODULE_ENABLED = 'enable';

    /**
     * Custom directory relative to the "media" folder
     */
    const DIRECTORY = 'catalog/product/';

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;

    /**
     * @var \Magento\Framework\Image\Factory
     */
    protected $_imageFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;


    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Image\Factory $imageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */


    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\Session $session,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->customer = $customer;
        $this->session = $session;
        $this->messageManager = $messageManager;
        $this->collectionFactory = $collectionFactory;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->_request = $request;
        $this->logger = $logger;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_imageFactory = $imageFactory;
        $this->_storeManager = $storeManager;
    }

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue($field, ScopeInterface::SCOPE_STORE, $storeId);
    }
    public function getSlaUrl($sellerId)
    {
        return $this->getSlaConfig($sellerId);
    }
    // to get current handler
    public function getCurrentHandler()
    {
        return $this->_request->getFullActionName();
    }
    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_TWILIO . 'general/' . $code, $storeId);
    }

    public function getOtpMessage($otp)
    {
        $messageTemplate = $this->getGeneralConfig(self::XML_OTP_MESSAGE);
        if (preg_match('/\{\{([a-z0-9_]+)\}\}/', $messageTemplate, $matches)) {
            $message = str_replace('{{' . $matches[1] . '}}', $otp, $messageTemplate);
        }
        return $message;
    }


    public function isModuleEnabled()
    {
        return $this->getGeneralConfig(self::XML_MODULE_ENABLED);
    }


    public function sendVerificationCode($message, $mobileNo){
	$accountSid = $this->getGeneralConfig('account_sid');
        $authToken = $this->getGeneralConfig('auth_token');
        $twilioNumber = $this->getGeneralConfig('twilio_number');
	 $client = new Client($accountSid, $authToken);
	    $result = $client->messages->create(
		 $mobileNo,
		  [
		    'from' => $twilioNumber,
		     'body' => $message
		   ]
            );
	return $result;
	}

    public function sendSms($message, $mobileNo,$reqmobileNo)
    {
	   $result = $this->jsonResultFactory->create();
	   $customerCollection = $this->collectionFactory->create();
        $customer = $customerCollection->addAttributeToSelect('*')
                    ->addAttributeToFilter('mobile_no', $reqmobileNo)
                    ->load();
        $accountSid = $this->getGeneralConfig('account_sid');
        $authToken = $this->getGeneralConfig('auth_token');
        $twilioNumber = $this->getGeneralConfig('twilio_number');
        $client = new Client($accountSid, $authToken);
    	
        if (!$customer->getData()) {
                        $data = ['code' => 400, 'msg' => "You are not registered with us. Please Sign Up"];
                        $result->setData($data);
    		$message = __('You are not registered with us. Please sign up');
                   $this->messageManager->addErrorMessage($message);
                        return $result;
            }
             else{
    		try {
                $this->session->setMobileno($reqmobileNo); 
                $checkmobileno = (int) $this->session->getMobileno();

                if (empty($_SESSION['failed_login'])) 
                    {
                        $_SESSION['failed_login'] = 1;
                        
                    } 
                    elseif (isset($reqmobileNo)) 
                    {
                        $_SESSION['failed_login']++;
                    }
                   
                    if ($_SESSION['failed_login'] > 3) {
                       echo $_SESSION['failed_login'];

                       $expireAfter = 30;
     
                    //Check to see if our "last action" session
                    //variable has been set.
                    if(isset($_SESSION['last_action'])){
                        
                        //Figure out how many seconds have passed
                        //since the user was last active.
                        $secondsInactive = time() - $_SESSION['last_action'];
                        
                        //Convert our minutes into seconds.
                        $expireAfterSeconds = $expireAfter * 60;
                        echo $expireAfterSeconds;
                        
                        //Check to see if they have been inactive for too long.
                        if($secondsInactive >= $expireAfterSeconds){
                            //User has been inactive for too long.
                            //Kill their session.

                            session_unset();
                            session_destroy();
                        }
        
                }

                        $_SESSION['last_action'] = time();

                            $message = __('Too many login attempts.Try again after 30 minutes');
                   $this->messageManager->addErrorMessage($message);

                        return $result;
                      
                        
                    } 
                   
    		    $result = $client->messages->create(
    		        $mobileNo,
    		        [
    		            'from' => $twilioNumber,
    		            'body' => $message
    		        ]
                );
                  
    		} catch (\Exception $e) {
    		    $this->logger->info('Issue with sending sms : ' . $e->getMessage());
    		}
    	}
    }

    /**
     * First check this file on FS
     *
     * @param string $filename
     * @return bool
     */
    protected function _fileExists($filename)
    {
        if ($this->_mediaDirectory->isFile($filename)) {
            return true;
        }
        return false;
    }

    /**
     * Resize image
     * @return string
     */
    public function resize($image, $width = null, $height = null)
    {
        $mediaFolder = self::DIRECTORY;

        $path = $mediaFolder . 'cache';
        if ($width !== null) {
            $path .= '/' . $width . 'x';
            if ($height !== null) {
                $path .= $height ;
            }
        }

        $absolutePath = $this->_mediaDirectory->getAbsolutePath($mediaFolder) . $image;
        $imageResized = $this->_mediaDirectory->getAbsolutePath($path) . $image;

        if (!$this->_fileExists($path . $image) && $this->_fileExists($mediaFolder . $image)) {
            $imageFactory = $this->_imageFactory->create();
            $imageFactory->open($absolutePath);
            $imageFactory->backgroundColor([0, 0, 0]);
            $imageFactory->constrainOnly(true);
            $imageFactory->keepAspectRatio(true);
            $imageFactory->keepFrame(false);
            $imageFactory->resize($width, $height);

            $imageFactory->save($imageResized);

        }

        return $this->_storeManager
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $path . $image;
    }



	public function isLoggedIn()
    {
         if($this->session->isLoggedIn()) {
		return true;
	  }else{
	  	return false;
	  }
    }

}
