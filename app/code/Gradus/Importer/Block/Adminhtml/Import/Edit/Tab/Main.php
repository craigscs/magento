<?php

namespace Gradus\Importer\Block\Adminhtml\Import\Edit\Tab;

/**
 * Blog post edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
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
        array $data = []
    ) {
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
        $model = $this->_coreRegistry->registry('import');
        $isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'enctype'=>'multipart/form-data','action' => $this->getData('action'), 'method' => 'post']]
        );
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Import')]);
        if ($model->getId()) {
            $fieldset->addField('import_id', 'hidden', ['name' => 'import_id']);
        }
        $fieldset->addField(
            'process',
            'select',
            [
                'name' => 'process',
                'label' => __('What are you importing?'),
                'title' => __('What are you importing?'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'options' => $this->getOptions()
            ]
        );
        $fieldset->addField(
            'brand',
            'select',
            [
                'name' => 'brand',
                'label' => __('Which brand?'),
                'title' => __('Which brand?'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'options' => $this->getSites()
            ]
        );
        $fieldset->addField(
            'clear',
            'select',
            [
                'name' => 'clear',
                'label' => __('Clear Data?'),
                'title' => __('Clear Data?'),
                'required' => true,
                'disabled' => $isElementDisabled,
                'options' => array(1=>'yes',0=>'no')
            ]
        );
        $fieldset->addField(
            'upl',
            'image',
            [
                'name' => 'upl',
                'label' => __('Upload your file.'),
                'title' => __('Upload your file.'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function getSites()
    {
        $sm = $this->_storeManager;
        $sites = $sm->getWebsites();
        $s = array();
        $s['All'] = "All";
        foreach ($sites as $site) {
            $s[$site->getCode()] = $site->getName();
        }
        return $s;
    }

    public function getOptions()
    {
        return array(
            'features'=>'Features',
            'inthebox'=>'In The Boz',
            'overview'=>'Overview',
            'accessories'=>'Accessories',
            'metadata'=>'Meta Data',
            'highlights'=>'Highlights',
            'manuals'=>'Manuals',
            'techspecs'=>'Tech Specs',
        );
    }

    protected function getFiles()
    {
        $files = glob('shell/import/*.php');
        $far = array();
        foreach ($files as $f) {
            $fslash = explode("/", $f);
            $far[$fslash[2]] = $fslash[2];
        }
        return $far;
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Import');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Import');
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
