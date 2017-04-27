<?php
/**
 * Created by PhpStorm.
 * User: Craig
 * Date: 3/16/2017
 * Time: 12:57 PM
 */

namespace Gradus\Logs\Block\Adminhtml\Logs;


class View extends  \Magento\Backend\Block\Template
{
    protected $_template = 'logs/view.phtml';
    protected $import;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context
    ) {
        parent::__construct($context);
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
}
