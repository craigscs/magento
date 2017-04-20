<?php
/**
 * Created by PhpStorm.
 * User: Craig
 * Date: 3/16/2017
 * Time: 12:57 PM
 */

namespace Gradus\Catalog\Block\Adminhtml;


class ProductModal extends  \Magento\Backend\Block\Template
{
    protected $_template = 'product/mainproducts.phtml';

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