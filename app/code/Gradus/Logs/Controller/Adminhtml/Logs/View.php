<?php
/**
 * Created by PhpStorm.
 * User: Craig
 * Date: 3/16/2017
 * Time: 11:43 AM
 */

namespace Gradus\Logs\Controller\Adminhtml\Logs;

use Magento\Framework\App\ResponseInterface;

class View extends \Magento\Backend\App\Action
{
    public function __construct(\Magento\Backend\App\Action\Context $context)
    {
        parent::__construct($context);;
    }

    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $block = $this->_view->getLayout()->getBlock('logs.logs.view');
        $this->_view->renderLayout();
    }
}