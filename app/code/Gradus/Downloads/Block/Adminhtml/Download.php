<?php
namespace Gradus\Downloads\Block\Adminhtml;

class Download extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'Gradus_Download';
        $this->_headerText = __('Manage Download');
        $this->_addButtonLabel = __('Save new Download');
        parent::_construct();
    }
}