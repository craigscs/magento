<?php
namespace Gradus\Importer\Block\Adminhtml;

class Blog extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'Gradus_Importer';
        $this->_headerText = __('Manage Import');
        $this->_addButtonLabel = __('Run new Import');
        parent::_construct();
    }
}