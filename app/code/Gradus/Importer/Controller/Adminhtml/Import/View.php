<?php
/**
 * Created by PhpStorm.
 * User: Craig
 * Date: 3/16/2017
 * Time: 11:43 AM
 */

namespace Gradus\Importer\Controller\Adminhtml\Import;

use Magento\Framework\App\ResponseInterface;

class View extends \Magento\Backend\App\Action
{
    protected $imp;

    public function __construct(\Magento\Backend\App\Action\Context $context)
    {
        parent::__construct($context);
        $this->imp = $this->_objectManager->create('\Gradus\Importer\Model\Imports');
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('import_id');
        $imp = $this->imp->load($id);
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $block = $this->_view->getLayout()->getBlock('importer.import.view');
        $block->setImport($imp);
        $this->_view->renderLayout();
    }
}