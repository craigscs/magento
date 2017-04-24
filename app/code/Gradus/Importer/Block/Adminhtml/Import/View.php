<?php
/**
 * Created by PhpStorm.
 * User: Craig
 * Date: 3/16/2017
 * Time: 12:57 PM
 */

namespace Gradus\Importer\Block\Adminhtml\Import;


class View extends  \Magento\Backend\Block\Template
{
    protected $_template = 'import/view.phtml';
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

    public function setImport($q)
    {
        $this->import = $q;
    }

    public function getImport()
    {
        return $this->import;
    }
}
