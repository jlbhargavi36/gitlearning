<?php

namespace Amasty\Mostviewed\Controller\Adminhtml\Pack;

use Amasty\Mostviewed\Model\Pack;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_Mostviewed::pack';

    /**
     * @var \Amasty\Mostviewed\Model\Backend\Pack\Registry
     */
    private $packRegistry;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Amasty\Mostviewed\Api\PackRepositoryInterface
     */
    private $packRepository;

    /**
     * @var \Amasty\Mostviewed\Model\PackFactory
     */
    private $packFactory;

    public function __construct(
        Action\Context $context,
        \Amasty\Mostviewed\Api\PackRepositoryInterface $packRepository,
        \Amasty\Mostviewed\Model\PackFactory $packFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Amasty\Mostviewed\Model\Backend\Pack\Registry $packRegistry
    ) {
        parent::__construct($context);
        $this->packRegistry = $packRegistry;
        $this->dataPersistor = $dataPersistor;
        $this->packRepository = $packRepository;
        $this->packFactory = $packFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $packId = (int)$this->getRequest()->getParam('id');
        if ($packId) {
            try {
                $model = $this->packRepository->getById($packId, true);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This Bundle Pack no longer exists.'));
                $this->_redirect('*/*/index');

                return;
            }
        } else {
            /** @var Pack $model */
            $model = $this->packFactory->create();
        }

        // set entered data if was error when we do save
        $data = $this->dataPersistor->get(Pack::PERSISTENT_NAME);
        if (!empty($data) && !$model->getPackId()) {
            $model->addData($data);
        }

        $this->packRegistry->set($model);
        $this->initAction();

        // set title and breadcrumbs
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Bundle Pack'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getPackId() ?
                __('Edit Bundle Pack # %1', $model->getPackId())
                : __('New Bundle Pack')
        );

        $breadcrumb = $model->getPackId() ?
            __('Edit Bundle Pack # %1', $model->getPackId())
            : __('New Bundle Pack');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb);

        $this->_view->renderLayout();
    }

    /**
     * Initiate action
     *
     * @return $this
     */
    private function initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(self::ADMIN_RESOURCE)
            ->_addBreadcrumb(__('Bundle Packs'), __('Bundle Packs'));

        return $this;
    }
}
