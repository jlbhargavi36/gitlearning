<?php

namespace Productvideo\upload\Model;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Store\Model\StoreManagerInterface;


class VideoManagement {
                /**
                     * @var \Magento\Framework\Webapi\Rest\Request
                     */
                protected $request;
                protected $_catrank;
                protected $scopeConfig;
                private $directory;
                protected $product;
                protected $productCollection;
                protected $productVideo;


                    /**
                     * constructor
                     *
                     * @param \Magento\Framework\Webapi\Rest\Request $request
                     */

                    public function __construct(
                        ResponseHttp $responseHttp,
                        \Magento\Framework\Webapi\Rest\Request $request,
                        StoreManagerInterface $storeManager,
                        \Magento\Framework\UrlInterface $url,
                        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                        \Magento\Framework\ObjectManagerInterface $objectmanager,
                         \Magento\Framework\Module\Dir $directory,
                       \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
                        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory ,
                        \Magento\ProductVideo\Model\Product\Attribute\Media\VideoEntryFactory $productVideoFactory   


                    ) {
                      $this->request = $request;
                      $this->storeManager = $storeManager;
                      $this->url = $url;
                      $this->scopeConfig = $scopeConfig;
                      $this->_objectManager = $objectmanager;
                      $this->directory = $directory;
                      $this->product = $productRepository;
                      $this->productCollection = $productCollectionFactory;
                      $this->productVideo = $productVideoFactory;    

                  }
                 /**
                     * {@inheritdoc}
                     */
         public function getVideo($url){ 
 	//	print_r( $this->request->getPathInfo()); exit;         
 	/*	$product_preview = $this->productVideo->create();
 	    $product_id="13555";	  
	    $product_preview->setEntityId($product_id);
	    $product_preview->_setResourceModel('Magento\ProductVideo\Model\ResourceModel\Video');
	    $product_preview->setMediaType('external-video');
	    $product_preview->setTypes('image');
	    $product_preview->setVideoUrl('https://vimeo.com/channels/mercedesbenz/156395271');
	    $product_preview->setVideoTitle('example');
	    $product_preview->setVideoDescription('Dynamic HD preview for product');
	    echo $product_preview->getselect();*/
	    //print_r($product_preview->getData());
	       $response='_videoInformation:{channel: "Voice of People Today"
channelId: "UCNc5KHbWdkvwaCbv_D_HG2Q"
description: "#calvinklein #ckjeans #ck  ↵↵↵Calvin Klein is one of the leading fashion design and marketing studios in the world.↵↵Unfortunately, like many iconic brands, our designs are targeted by criminals who are producing and selling counterfeit products intended to deceive consumers and profit from our success. Several reports have highlighted that the major profits that can be made from selling counterfeit products are used to fund terrorism and organized crime. Furthermore, there is often a terrible human cost as counterfeiting is often associated with child labor and modern slavery.↵↵In order to fight this illegal trade in counterfeit products and protect consumers, we have developed an established, global enforcement program. We work diligently with customs and law enforcement agencies to disrupt and eliminate the sale of counterfeit Calvin Klein products both online and on the ground.↵↵IDENTIFYING COUNTERFEITS↵On a daily basis we are reporting websites, social media pages and online marketplace accounts that are offering counterfeit Calvin Klein products. We recognize it can be difficult to distinguish between genuine and counterfeit product, especially online. Therefore, always be cautious of buying any Calvin Klein product from websites that offer substantial discounts and avoid any products claiming to be factory seconds or overruns.↵↵Based on our experience we have identified several characteristics of websites that are offering counterfeit product or will fail to deliver the product you ordered:↵↵A.   The website will often have little to no contact information such as company names, physical or email addresses and telephone numbers.↵↵B.   The website will not protect your personal data and credit card information and will not feature HTTPS:// or show a small padlock symbol next to the website address (URL).↵↵C.   The website will sometimes mirror an official Calvin Klein website even-though the domain name may not relate to the brand.↵↵Please note that purchasing products from unauthorized retailers is always at your own risk. You will find genuine Calvin Klein product at Calvinklein.com, Calvin Klein stores or authorized Calvin Klein retailers.  ↵↵Private detectives are gazing at rear ends these days to try to spot the difference. Counterfeiting of designer jeans is such a big business that makers of Calvin Klein, Jordache, Gloria Vanderbilt and other high-fashion trousers and T-shirts are spending millions of dollars to nab manufacturers and sellers of the phony products."
duration: "00:07:58"
thumbnail: "https://i.ytimg.com/vi/Tn3Nsz6EZzo/hqdefault.jpg"
title: "Real vs Fake Calvin Klein Jeans. How to spot counterfeit Calvin Klein"
uploaded: "2020-02-28 13:23:05Z"
useYoutubeNocookie: false
videoId: "Tn3Nsz6EZzo"
videoProvider: "youtube"}';
	       
	       
	        return $response;    
	        
	        
	        
	        }       

 		         
 		         
 		
 		  }
 		 
