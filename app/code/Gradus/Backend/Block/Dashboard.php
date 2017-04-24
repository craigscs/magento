<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Gradus\Backend\Block;

class Dashboard extends \Magento\Backend\Block\Template
{
    /**
     * Location of the "Enable Chart" config param
     */
    const XML_PATH_ENABLE_CHARTS = 'admin/dashboard/enable_charts';

    /**
     * @var string
     */
    protected $_template = 'dashboard/index.phtml';

    protected $imports;
    protected $products;
    protected $pages;

    public function __construct(\Magento\Backend\Block\Template\Context $context,
                                \Gradus\Importer\Model\ResourceModel\Imports\Collection $imports,
                                \Magento\Catalog\Model\ResourceModel\Product\Collection $prods,
                                \Magento\Cms\Model\ResourceModel\Page\Collection $pages,
                                array $data)
    {
        $imports->addFieldToSelect('*')->setOrder('import_id');
        $prods->addFieldToSelect('*');
        $pages->addFieldToSelect('*');
        $this->imports = $imports;
        $this->pages = $pages;
        $this->products = $prods;
        parent::__construct($context, $data);
    }


    /**
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->addChild('lastOrders', \Magento\Backend\Block\Dashboard\Orders\Grid::class);

        $this->addChild('totals', \Magento\Backend\Block\Dashboard\Totals::class);

        $this->addChild('sales', \Magento\Backend\Block\Dashboard\Sales::class);

        $isChartEnabled = $this->_scopeConfig->getValue(
            self::XML_PATH_ENABLE_CHARTS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($isChartEnabled) {
            $block = $this->getLayout()->createBlock(\Magento\Backend\Block\Dashboard\Diagrams::class);
        } else {
            $block = $this->getLayout()->createBlock(
                \Magento\Backend\Block\Template::class
            )->setTemplate(
                'dashboard/graph/disabled.phtml'
            )->setConfigUrl(
                $this->getUrl(
                    'adminhtml/system_config/edit',
                    ['section' => 'admin', '_fragment' => 'admin_dashboard-link']
                )
            );
        }
        $this->setChild('diagrams', $block);

        $this->addChild('grids', \Magento\Backend\Block\Dashboard\Grids::class);

        parent::_prepareLayout();
    }

    public function getLastImport()
    {
        return $this->imports->getFirstItem();
    }

    public function getYesterdayProducts()
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P1D'));
        $collections = $this->products;
        $collections = $collections
            ->filter
            ->addFieldToFilter('updated_at', ['gteq' => $date])
            ->getData();
        return $collections;
    }

    public function getMissingInfoPages()
    {
        $collections = $this->products;
        $collections = $collections
            ->addFieldToFilter('description', null)
            ->getData();
        return $collections;
    }

    public function getYesterdayPages()
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P1D'));
        $collections = $this->pages
            ->addFieldToFilter('update_time', ['gteq' => $date])
            ->getData();
        return $collections;
    }

    /**
     * @return string
     */
    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return $this->getUrl('adminhtml/*/*', ['_current' => true, 'period' => null]);
    }
}
