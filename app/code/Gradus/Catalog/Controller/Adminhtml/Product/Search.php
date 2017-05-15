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
       $searchtext = $this->getRequest()->getParam('text');
        $t = 'Found 0 results.';
        $col = $this->pc->create();
        $col->addFieldToSelect('*')
            ->addFieldToFilter('mfr_num', array('like' => '%'.$searchtext.'%'));

        if (count($col) > 0) {
            $t = "<div>Found " . count($col) . " item(s).</div>";
            $t .= "<table style='width:700px;' border='1px; max-height:350px;'><thead><tr><th style='padding:3px;'>Mfr</th><th style='padding:3px;'>Name</th><th style='width:30px;'></th>";
            $t .= "</tr></thead>";
            $t .= "<tbody>";

            foreach ($col as $c) {
                $t .= "<tr id='row_".$c->getId()."'><td style='padding:5px;'>" . $c->getData('mfr_num') . "</td>";
                $t .= "<td style='padding:5px;'>" . $c->getData('name') . "</td>";
                $t .= "<td style='padding:5px;'><a href='javascript:void(0)'";
                $t .= " onclick=\"copyitem('".$c->getId()."')\"";
                $t .= ">Select</a></td>";
                $t .= "</tr>";
            }
            $t .= "</tbody></table>";
        }
        $res = $this->jf->create()->setData(['res'=>$t]);
        return $res;
    }
}