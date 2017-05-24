<?php
namespace  Gradus\Features\Block\Adminhtml\Catalog\Product\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;

class Features extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'product/edit/features.phtml';
    protected $col;
    protected $w;
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $col,
        \Magento\Catalog\Controller\Adminhtml\Product\Wysiwyg $wysiwyg,
        array $data = []
    )
    {
        $this->w = $wysiwyg;
        $this->col = $col;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * Retrieve product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    public function getProducts()
    {
        return $this->col->addAttributeToSelect('*')->load();
    }
    public function getIt()
    {
        return $this->w->execute()->renderResult();
    }

}