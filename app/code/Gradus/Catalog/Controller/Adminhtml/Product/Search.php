<?php
/**
 * Created by PhpStorm.
 * User: Craig
 * Date: 3/23/2017
 * Time: 10:58 AM
 */

namespace Gradus\Catalog\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization;

class Search extends \Magento\Catalog\Controller\Adminhtml\Product\Edit
{
    protected $pf;
    protected $pc;
    protected $jf;
    protected $assets;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Catalog\Model\Product $pf,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $pc,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->pf = $pf;
        $this->pc = $pc;
        $this->jf = $resultJsonFactory;
        parent::__construct($context, $productBuilder, $resultPageFactory);
    }

    public function execute()
    {
       $searchtext = $this->getRequest()->getParam('q');
        $col = $this->pc->create();
        $col->addFieldToSelect('*')
            ->addFieldToFilter('mfr_num', array('like' => '%'.$searchtext.'%'));

        $jsonres = array();
        foreach ($col as $c) {
            $p['id'] = $c->getId();
            $p['mfr_num'] = $c->getData('mfr_num');
            $p['product_name'] = $c->getName();
            $p['sku'] = $c->getData('sku');
            $jsonres[] = $p;
        }

        $res = $this->jf->create()->setData(['items'=>$jsonres, 'total_count' => count($col)]);
        return $res;
    }
}