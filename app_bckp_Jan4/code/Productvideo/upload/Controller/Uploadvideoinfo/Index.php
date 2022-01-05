<?php
namespace Productvideo\upload\Controller\Uploadvideoinfo;
use Magento\Framework\Api\Data\VideoContentInterface;
use Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface;
use Magento\Framework\App\Action\Context;
use Magento\ProductVideo\Model\Product\Attribute\Media\ExternalVideoEntryConverter;
use Magento\Store\Model\Store;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Framework\Filesystem;


class Index extends \Magento\Framework\App\Action\Action
{

    private $mimeTypes = [
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
    ];

    private $defaultMimeType = 'application/octet-stream';
    private $productFactory;
    private $externalVideoEntryConverter;
    private $productRepository;
    private $contentValidator;
    private $curl;
    private $imageContentFactory;
    protected $request;
    protected $_fileUploaderFactory;
    protected $filesystem;
    protected $iofile;
    protected $iodir;
    protected $_logger;


    /**
     * @param Context $context
     * @param ProductInterfaceFactory $productFactory
     * @param ExternalVideoEntryConverter $externalVideoEntryConverter
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Api\ImageContentValidatorInterface $contentValidator
     * @param \Magento\Framework\HTTP\Adapter\Curl $curl
     * @param \Magento\Framework\Api\Data\ImageContentInterfaceFactory $imageContentFactory
     */
    public function __construct(
        Context $context,
        ProductInterfaceFactory $productFactory,
        ExternalVideoEntryConverter $externalVideoEntryConverter,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\ImageContentValidatorInterface $contentValidator,
        \Magento\Framework\HTTP\Adapter\Curl $curl,
        \Magento\Framework\Api\Data\ImageContentInterfaceFactory $imageContentFactory,
         \Magento\Framework\App\Request\Http $request,
         \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
          Filesystem $filesystem,
         \Magento\Framework\Filesystem\Io\File $file,
         \Magento\Framework\Filesystem\DirectoryList $dir,
          \Psr\Log\LoggerInterface $logger
  
    ) {
        parent::__construct($context);
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->externalVideoEntryConverter = $externalVideoEntryConverter;
        $this->contentValidator = $contentValidator;
        $this->curl = $curl;
        $this->imageContentFactory = $imageContentFactory;
         $this->request = $request;
        $this->_fileUploaderFactory = $fileUploaderFactory; 
         $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
         $this->iofile = $file; 
        $this->iodir = $dir; 
        $this->_logger = $logger;

    }

    public function execute()
    {   $_postData = $this->getRequest()->getPost(); 
        $sampleProductId =$_postData["video_productid"]; 
        /** @var ProductInterface $product */
        $sampleProduct = $this->productFactory->create()->load($sampleProductId);  
       
      //  $upload_video = $_postData["UploadVideo"];
       // $expUp_video=explode(".",$upload_video);

        // NB! Unless you have a multi store setup and need the video only for specific store,
        // this has to be done or the video entry might get wrong store id (will only be visible on one store).
        // See \Magento\ProductVideo\Model\Plugin\Catalog\Product\Gallery\CreateHandler function afterExecute().
        // However this is a hack and better way should be implemented to make sure video will get store id before save is called.
        $sampleProduct->setStoreId(Store::DEFAULT_STORE_ID);

        // Sample youtube video
        //$videoUrl = "https://www.youtube.com/watch?v=sGF6bOi1NfA";
      /*  $videoUrl = "http://localhost/raybanupgrade/rayban2/pub/media/Cute pandas playing on the slide.mp4";
        $videoId =  "lee";
        $videoLabel = "Cute pandas playing on the slide";
        $videoDescription = "What's more entertaining than watching a panda playing on a slide?";
        $videoProvider = "lee"; 
        $thumbnailUrl = "https://i.ytimg.com/vi/sGF6bOi1NfA/hqdefault.jpg";*/
         
         
         $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	  $productForSKU = $objectManager->create('Magento\Catalog\Model\Product')->load($sampleProductId);
         $sku = $productForSKU->getSku();

             if ( ! file_exists($this->iodir->getPath('media').'/'.$sku)) {
        $this->iofile->mkdir($this->iodir->getPath('media').'/'.$sku, 0775);

    }      

	   $target = $this->mediaDirectory->getAbsolutePath($sku);        
          
   //attachment is the input file name posted from your form
            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'upload_video']);

            $_fileType = $uploader->getFileExtension();
            $newFileName = uniqid().'.'.$_fileType;
            
            /** Allowed extension types */
         //   $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv']);
            $uploader->setAllowedExtensions(['mp4']);
            /** rename file name if already exists */
            $uploader->setAllowRenameFiles(true);
            
            //$result = $uploader->save($target, $newFileName); //Use this if you want to change your file name
                       
            
   $result = $uploader->save($target); 
   $upload_video=$uploader->getUploadedFileName();
   $expUp_video=explode(".",$upload_video);
   
   $videoUrl = "https://lee.aceturtle.in/media/".$sku."/".$upload_video;
   //$videoUrlDisplay="https://lee.aceturtle.in/media/".$sku."/".$upload_video;
        $videoId =  "lee";
        $videoLabel = $expUp_video[0];
        $videoDescription = $expUp_video[0];
        $videoProvider = "lee"; 
        $thumbnailUrl = "https://i.ytimg.com/vi/sGF6bOi1NfA/hqdefault.jpg";
       // $thumbnailUrl = "https://lee.aceturtle.in/media/catalog/product/placeholder/default/watermark_2.jpg"; 
        // Check if video already exists, and if not, create a new one
        if(!$this->isExistingVideo($sampleProduct, $videoUrl)){
            $videoEntry = $this->buildVideoEntry($sampleProduct, $videoUrl, $videoId, $videoLabel, $videoDescription, $videoProvider, $thumbnailUrl);
            echo $videoUrl; 
            $this->addVideoForProduct($sampleProduct, $videoEntry);
        
        }else{
        echo $videoUrl; 
            die('Video already exists!');
        }
    }

    /**
     * @param ProductInterface $sampleProduct
     * @param string $videoUrl
     * @param string $videoId
     * @param string $videoLabel
     * @param string $videoDescription
     * @param string $videoProvider
     * @param string $thumbnailUrl
     *
     * @return ProductAttributeMediaGalleryEntryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function buildVideoEntry($sampleProduct, $videoUrl, $videoId, $videoLabel, $videoDescription, $videoProvider, $thumbnailUrl)
    {
        // Build thumbnail image data for sample video
        $parts = explode('/', $thumbnailUrl);
        $thumbnailImageName = end($parts);
        $thumbnailImage = $this->getRemoteImage($thumbnailUrl); // Fetch image from server

        /** @var \Magento\Framework\Api\Data\ImageContentInterface $imageContent */
        $imageContent = $this->imageContentFactory->create();
        $imageContent->setName($videoProvider . '_' . $videoId)
            ->setType($this->getMimeContentType($thumbnailImageName))
            ->setBase64EncodedData(base64_encode($thumbnailImage));

        // Build video data array for video entry converter
        $generalMediaEntryData = [
            ProductAttributeMediaGalleryEntryInterface::LABEL => $videoLabel,
            ProductAttributeMediaGalleryEntryInterface::TYPES => ['thumbnail', 'image', 'small_image'], // Optional, depends on what is wanted
            ProductAttributeMediaGalleryEntryInterface::CONTENT => $imageContent,
            ProductAttributeMediaGalleryEntryInterface::DISABLED => false
        ];

        $videoData = array_merge($generalMediaEntryData, [
            VideoContentInterface::TITLE => $videoLabel,
            VideoContentInterface::DESCRIPTION => $videoDescription,
            VideoContentInterface::PROVIDER => $videoProvider,
            VideoContentInterface::METADATA => null,
            VideoContentInterface::URL => $videoUrl,
            VideoContentInterface::TYPE => ExternalVideoEntryConverter::MEDIA_TYPE_CODE,
        ]);

        // Convert video data array to video entry
        return $this->externalVideoEntryConverter->convertTo($sampleProduct, $videoData);
    }

    /**
     * Copy of \Magento\Catalog\Api\ProductAttributeMediaGalleryManagementInterface create() function.
     * Only difference is that we don't reload product with sku, and thus we don't lose store id 0
     * that we set earlier for sample product.
     *
     * @param ProductInterface $sampleProduct
     * @param ProductAttributeMediaGalleryEntryInterface $videoEntry
     *
     * @return int|null
     * @throws InputException
     * @throws StateException
     */
    private function addVideoForProduct($sampleProduct, $videoEntry)
    {  
        /** @var $entry ProductAttributeMediaGalleryEntryInterface */
        $entryContent = $videoEntry->getContent();

        if (!$this->contentValidator->isValid($entryContent)) {
            throw new InputException(__('The image content is not valid.'));
        }

        $existingMediaGalleryEntries = $sampleProduct->getMediaGalleryEntries();
        $existingEntryIds = [];
        if ($existingMediaGalleryEntries == null) {
            $existingMediaGalleryEntries = [$videoEntry];
        } else {
            foreach ($existingMediaGalleryEntries as $existingEntries) {
                $existingEntryIds[$existingEntries->getId()] = $existingEntries->getId();
            }
            $existingMediaGalleryEntries[] = $videoEntry;
        }
        $sampleProduct->setMediaGalleryEntries($existingMediaGalleryEntries);
  
        try { 
       
          $product = $this->productRepository->save($sampleProduct);
         } catch (InputException $inputException) {
            throw $inputException;
        } catch (\Exception $e) {  
            throw new StateException(__('Cannot save product.'));
        }

        foreach ($product->getMediaGalleryEntries() as $entry) {
            if (!isset($existingEntryIds[$entry->getId()])) {
                return $entry->getId();
            }
        }
        throw new StateException(__('Failed to save new media gallery entry.'));
    }

    /**
     * @param ProductInterface $sampleProduct
     * @param string $url
     *
     * @return bool
     */
    private function isExistingVideo($sampleProduct, $url)
    {
        if ($mediaEntries = $sampleProduct->getMediaGalleryEntries()) {
            foreach($mediaEntries as $entryKey => $mediaEntry) {
                if($mediaEntry->getMediaType() == ExternalVideoEntryConverter::MEDIA_TYPE_CODE) {
                    $videoUrl = $mediaEntry->getExtensionAttributes()->getVideoContent()->getVideoUrl();
                    if($videoUrl == $url){
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param string $fileUrl
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getRemoteImage($fileUrl)
    {
        $this->curl->setConfig(['header' => false]);
        $this->curl->write('GET', $fileUrl);
        $image = $this->curl->read();

        if (empty($image)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Could not get preview image information. Please check your connection and try again.')
            );
        }
        return $image;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    private function getMimeContentType($filename)
    {
        $parts = explode('.',$filename);
        $ext = strtolower(array_pop($parts));
        if (array_key_exists($ext, $this->mimeTypes)) {
            return $this->mimeTypes[$ext];
        } else {
            return $this->defaultMimeType;
        }
    }
}
