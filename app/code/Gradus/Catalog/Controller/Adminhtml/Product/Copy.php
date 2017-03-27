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

class Copy extends \Magento\Catalog\Controller\Adminhtml\Product\Edit
{
    protected $pf;
    protected $jf;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Catalog\Model\Product $pf,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->pf = $pf;
        $this->jf = $resultJsonFactory;
        parent::__construct($context, $productBuilder, $resultPageFactory);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $field = $this->getRequest()->getParam('field');
        $prod = $this->pf->load($id);
        $feat = $prod->getData($field);
        $feats = json_decode($feat);
        $q='';
        $count = 1;
        if (count($feats) > 0) {
            foreach ($feats as $f) {
                $q .= '<div class="specs_row" id="features_'.$count.'">';
                $q .= '<div class="draggable-handle"></div>';
                $q .= '<label for="feat_n_'.$count.'">Label '.$count.'</label>';
                $q .=  '<input style="width:15%; margin-left:5px;" id="feat_n_'.$count.'" data-form-part="product_form" value="'.$f->name.'" name="features['.$count.'][name]" />';
                $q .= '<label for="feat_d_'.$count.'">Feature '.$count.'</label>';
                $q .= '<textarea id="feat_d_'.$count.'" style="width:50%;" data-form-part="product_form" name="features['.$count.'][desc]">'.$f->desc.'</textarea>';
                $q .= ' <a class="delete_icon" src="javascript:void(0)" onclick="deleteFeature(\'features_'.$count.'\')"></a>';
                $q .= '</div>';
                $count++;
            }
        }

        $res = $this->jf->create()->setData(['res'=>$q]);
        return $res;
    }
}