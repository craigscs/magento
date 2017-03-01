<?php
namespace  Gradus\TechSpecs\Block\Adminhtml\Catalog\Product\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;

class TechSpecs extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'product/edit/techspecs.phtml';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $this->addChild(
            'add_button',
            \Magento\Backend\Block\Widget\Button::class,
            ['label' => __('Add New Option'), 'class' => 'add', 'id' => 'add_new_defined_option']
        );

        $this->addChild(
            'import_button',
            \Magento\Backend\Block\Widget\Button::class,
            ['label' => __('Import Options'), 'class' => 'add', 'id' => 'import_new_defined_option']
        );

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

}