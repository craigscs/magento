<?php

namespace Gradus\Requires\Block\Adminhtml\Requires\Edit\Tab;

/**
 * Blog post edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    protected $_productCollectionFactory;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Catalog\Model\ProductFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('requires');
        $isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Requires')]);
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
            $fieldset->addField(
                'entity_id',
                'select',
                [
                    'name' => 'entity_id',
                    'class' => 'select2_input',
                    'multiple' => '',
                    'label' => __('Product'),
                    'title' => __('Product'),
                    'required' => true,
                    'disabled' => $isElementDisabled,
                    'options' => array($model->getData('entity_id') => $model->getData('entity_id')),
                ]
            );
            $fieldset->addField(
                'secondary_id',
                'select',
                [
                    'name' => 'secondary_id',
                    'class' => 'select2_input',
                    'multiple' => '',
                    'label' => __('Secondary Product'),
                    'title' => __('Secondary Product'),
                    'required' => true,
                    'disabled' => $isElementDisabled,
                    'options' => array($model->getData('secondary_id') => $model->getData('secondary_id')),
                ]
            );
            $fieldset->addField(
                'requirement',
                'select',
                [
                    'name' => 'requirement',
                    'class' => 'select2_input',
                    'multiple' => '',
                    'label' => __('Required Product'),
                    'title' => __('Required Product'),
                    'required' => true,
                    'disabled' => $isElementDisabled,
                    'options' => array($model->getData('requirement') => $model->getData('requirement')),
                ]
            );
        } else {
            $fieldset->addField(
                'entity_id',
                'select',
                [
                    'name' => 'entity_id',
                    'class' => 'select2_input',
                    'multiple' => '',
                    'label' => __('Product'),
                    'title' => __('Product'),
                    'required' => true,
                    'disabled' => $isElementDisabled,
                    'options' => array('0' => "Please select one"),
                ]
            );
            $fieldset->addField(
                'secondary_id',
                'select',
                [
                    'name' => 'secondary_id',
                    'class' => 'select2_input',
                    'multiple' => '',
                    'label' => __('Secondary Product'),
                    'title' => __('Secondary Product'),
                    'required' => true,
                    'disabled' => $isElementDisabled,
                    'options' => array('0' => "Please select one"),
                ]
            );
            $fieldset->addField(
                'requirement',
                'select',
                [
                    'name' => 'requirement',
                    'class' => 'select2_input',
                    'multiple' => '',
                    'label' => __('Required Product'),
                    'title' => __('Required Product'),
                    'required' => true,
                    'disabled' => $isElementDisabled,
                    'options' => array('0' => "Please select one"),
                ]
            );
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function getFiles()
    {
        $collection = $this->_productCollectionFactory->create()->getCollection()->addAttributeToSelect('*');
//        $collection->addAttributeToFilter('gradus_type', 'Product');
        $op = array('Sku | Name');
        foreach ($collection as $c) {
            $op[$c->getSku()] = $c->getSku().' | '.$c->getData('name');
        }
        return $op;
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Requires');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Requires');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
