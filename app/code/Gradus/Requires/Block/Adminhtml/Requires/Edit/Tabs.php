<?php
namespace Gradus\Requires\Block\Adminhtml\Requires\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('requires_tab');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Requires'));
    }
}
