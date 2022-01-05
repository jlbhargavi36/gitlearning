<?php 
namespace Aceturtle\Productposition\Model\Import; 
use Aceturtle\Productposition\Model\Import\CustomImport\RowValidatorInterface as ValidatorInterface; 
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface; 
use Magento\Framework\App\ResourceConnection;
 class CustomImport extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity {
  const SKUID = 'sku'; 
  const CATEGORYID = 'category_id'; 
  const POSITION = 'position'; 
  const TABLE_Entity = 'catalog_category_product'; /** * Validation failure message template definitions * * @var array */ 
  protected $_messageTemplates = [ ValidatorInterface::ERROR_TITLE_IS_EMPTY => 'Name is empty',
    ];
 
     protected $_permanentAttributes = [self::SKUID];
    /**
     * If we should check column names
     *
     * @var bool
     */
    protected $needColumnCheck = false;
    protected $groupFactory;
    /**
     * Valid column names
     *
     * @array
     */
    protected $validColumnNames = [
    self::SKUID,
    self::CATEGORYID,
    self::POSITION,
    ];
 
    /**
     * Need to log in import history
     *
     * @var bool
     */
    protected $logInHistory = true;
 
    protected $_validators = [];
 
 
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_connection;
    protected $_resource;
    
    protected $_productRepository;
    
    protected $_messageManager;
 
    /**
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    public function __construct(
    \Magento\Framework\Json\Helper\Data $jsonHelper,
    \Magento\ImportExport\Helper\Data $importExportData,
    \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
    \Magento\Framework\App\ResourceConnection $resource,
    \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
    \Magento\Framework\Stdlib\StringUtils $string,
    ProcessingErrorAggregatorInterface $errorAggregator,
    \Magento\Customer\Model\GroupFactory $groupFactory,
     \Magento\Catalog\Model\Product $productRepository,
     \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
    $this->jsonHelper = $jsonHelper;
    $this->_importExportData = $importExportData;
    $this->_resourceHelper = $resourceHelper;
    $this->_dataSourceModel = $importData;
    $this->_resource = $resource;
    $this->_connection = $resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
    $this->errorAggregator = $errorAggregator;
    $this->groupFactory = $groupFactory;
     $this->_productRepository = $productRepository;
     $this->_messageManager = $messageManager;
    }
 
 
    public function getValidColumnNames()
    {
      return $this->validColumnNames;
    }
 
    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
      return 'position_import';
    }
 
    /**
     * Row validation.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    { 
       if (isset($this->_validatedRows[$rowNum])) {
        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
       }
       $this->_validatedRows[$rowNum] = true;
       return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }
 
   /**
     * Create Advanced message data from raw data.
     *
     * @throws \Exception
     * @return bool Result of operation.
     */
    protected function _importData()
    {
        $this->saveEntity();
        return true;
    }
    /**
     * Save entity
     *
     * @return $this
     */
    public function saveEntity()
    {
        $this->saveAndReplaceEntity();
        return $this;
    }
 
    /**
     * Replace entity data
     *
     * @return $this
     */
    public function replaceEntity()
    {
       $this->saveAndReplaceEntity();
       return $this;
    }
    /**
     * Deletes entity data from raw data.
     *
     * @return $this
     */
    public function deleteEntity()
    {
     $listTitle = [];
     while ($bunch = $this->_dataSourceModel->getNextBunch()) {
        foreach ($bunch as $rowNum => $rowData) {
            $this->validateRow($rowData, $rowNum);
            if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                $rowTtile = $rowData[self::SKUID];
                $listTitle[] = $rowTtile;
            }
            if ($this->getErrorAggregator()->hasToBeTerminated()) {
                $this->getErrorAggregator()->addRowToSkip($rowNum);
            }
        }
     }
     if ($listTitle) {
        $this->deleteEntityFinish(array_unique($listTitle),self::TABLE_Entity);
     }
     return $this;
    }
     
    /**
     * Save and replace entity
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function saveAndReplaceEntity()
    {
      $behavior = $this->getBehavior();
      $listTitle = [];
      while ($bunch = $this->_dataSourceModel->getNextBunch()) {
        $entityList = [];
        foreach ($bunch as $rowNum => $rowData) {
            if (!$this->validateRow($rowData, $rowNum)) {
                $this->addRowError(ValidatorInterface::ERROR_TITLE_IS_EMPTY, $rowNum);
                continue;
            }
            if ($this->getErrorAggregator()->hasToBeTerminated()) {
                $this->getErrorAggregator()->addRowToSkip($rowNum);
                continue;
            }
 
            $rowTtile= $rowData[self::SKUID];
            $listTitle[] = $rowTtile;
            $entityList[$rowTtile][] = [
              self::SKUID => $rowData[self::SKUID],
              self::CATEGORYID => $rowData[self::CATEGORYID],
                self::POSITION => $rowData[self::POSITION],
            ];
        }
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {
            if ($listTitle) {
                if ($this->deleteEntityFinish(array_unique(  $listTitle), self::TABLE_Entity)) {
                    $this->saveEntityFinish($entityList, self::TABLE_Entity);
                }
            }
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $behavior) {
            $this->saveEntityFinish($entityList, self::TABLE_Entity);
        }
    }
    return $this;
    }
 
    /**
     * Save custom data.
     *
     * @param array $entityData
     * @param string $table
     * @return $this
     */
    protected function saveEntityFinish(array $entityData, $table)
    { //print_r($entityData);
      if ($entityData) {
        $tableName = $this->_connection->getTableName($table);
        $entityIn = [];
        foreach ($entityData as $id => $entityRows) {
          /*      foreach ($entityRows as $row) {
                    $entityIn[] = $row; 
                }  */
            $this->updatePositionUsingsku($entityRows);
    
            }
         // $this->updatePositionUsingsku($categoryId, $positionUpdate);        
                         
      }
      return $this;
    }
 
     /**
     * Delete custom data.
     *
     * @param array $entityData
     * @param string $table
     * @return $this
     */
    protected function deleteEntityFinish(array $ids, $table)
    {
      if ($table && $listTitle) {
            try {
                $this->countItemsDeleted += $this->_connection->delete(
                    $this->_connection->getTableName($table),
                    $this->_connection->quoteInto('id IN (?)', $ids)
                );
                return true;
            } catch (\Exception $e) {
                return false;
            }
 
      } else {
        return false;
      }
    }
    
    /*******************************Update the position starts*********************/
      public function updatePositionUsingsku($positionUpdate)
    { 
        $adapter = $this->_connection;
        foreach ($positionUpdate as $row) {              
            $productPosition = $row["position"];
            //Update product position using csv
            if ($row["sku"]!="" && $row["category_id"]!="") {
                $sku=$row["sku"];
                $categoryId=$row["category_id"];
                $productId = $this->_productRepository->getIdBySku($sku);
                if ($productId) {
                    $where = array(
                        'category_id = ?' => (int) $categoryId,
                        'product_id = ?' => (int) $productId
                    );
                    $position = $productPosition;
                    $bind = array('position' => (int) $position);
                    $adapter->update(self::TABLE_Entity, $bind, $where);
                    $this->_messageManager->addSuccess(__(' %1  Position Updated Successfully.', $sku));
                } else {
                    $this->_messageManager->addError(__("%1 is not available", $sku));
                }
            } else {
                $this->_messageManager->addError(__("sku is not available"));
            }
        }
    }
  /*******************************Update the position ends*********************/

    
    
    
}

