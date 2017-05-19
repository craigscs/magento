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
    protected $assets;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Catalog\Model\Product $pf,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        $this->assets = $assetRepo;
        $this->pf = $pf;
        $this->jf = $resultJsonFactory;
        parent::__construct($context, $productBuilder, $resultPageFactory);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $field = $this->getRequest()->getParam('field');
        $prod = $this->pf->load($id);

        $result = $this->$field($prod);
        $res = $this->jf->create()->setData(['res'=>$result['res'], 'mes' => $result['mes']]);
        return $res;
    }

    public function InTheBox($prod)
    {
        $box = $prod->getData('in_the_box');
        $inbox = json_decode($box);
        $q = '';
        $count = 1;
        if (count($inbox) > 0) {
            foreach ($inbox as $h) {
                $q .= '<div style="margin:auto" class="gradus_fields" id="in_the_box_'.$count.'">';
                $q .= '<div class="draggable-handle inthebox_drag"><b>'.$count.'</b></div>';
                $q .= '<label class="gradus_label" for="in_the_box_amount_'.$count.'">Amount</label>';
                $q .= '<input name="in_the_box['.$count.'][count]" data-form-part="product_form" style="margin-left:6px;" class="gradus_text_mini"';
                $q .= 'id="in_the_box_amount_'.$count.'" value="'.$h->count.'" />';
                $q .= '<label class="gradus_label" for="in_the_box_value_'.$count.'">Item</label>';
                $q .= '<input name="in_the_box['.$count.'][value]" data-form-part="product_form" style="margin-left:6px; width:70%"';
                $q .= 'class="gradus_text_medium" style="width:70%" id="in_the_box_value_'.$count.'" value="'.$h->value.'" />';
                $q .= '<a src="javascript:void(0)" style="margin-left:6px;" onclick="deleteinthebox(\'in_the_box_'.$count.'\')">';
                $q .= '<img style="width:20px;" src="'.$this->assets->getUrl("Gradus_Catalog::images/trash.png").'"></a></div>';
                $count++;
            }
        }
        return array('res' =>$q, 'mes' => "Includes updated");
    }

    public function techspecs($prod)
    {
        $spec = $prod->getData('tech_specs');
        $specs = json_decode($spec);
        $q = '';
        $count = 1;
        if (count($specs) > 0) {
            $q = '';
            foreach ($specs as $h) {
                $q .= '<div class="header_div" id="header_div_' . $count . '">';
                $q .= '<h3 id="header_title_' . $count . '">';
                $q .= '<div class="draggable-handle header_drag"><b></b>' . $count . '</b></div>';
                $q .= '<div style="display:inline-block" id="h_text_' . $count . '" class="header_txt">' . $h->header->header_name . '</div>';
                $q .= '<a class="delete_icon" style="float:right" src="javascript:void(0)" onclick="deleteHeader(\'header_div_' . $count . '\')">';
                $q .= '<img style="width:20px;" src="' . $this->assets->getUrl("Gradus_Catalog::images/trash.png") . '"></a></h3>';

                $q .= '<div><table class="tech_spec_table" id="header_table_' . $count . '">';
                $q .= '<tr class="header_row"><td>';
                $q .= '<label class="gradus_label" for="header_text_' . $count . '">Header Text</label>';
                $q .= '<input style="width:85%" class="gradus_text_large" onkeyup="updateHeader(this.value, \'h_text_' . $count . '\')" id="header_title_' . $count . '"';
                $q .= 'data-form-part="product_form" name="techspec[' . $count . '][header][header_name]"';
                $q .= 'id="header_text_' . $count . '" value="' . $h->header->header_name . '"/>';
                $q .= '</td></tr>';
                $q .= '<tr style="height:10px;"></tr>';
                $q .= '<tr>';
                $speccount = 1;
                $q .= '<td class="spec_td">';
                foreach ($h as $a) {
                    foreach ($a as $s) {
                        if (property_exists($s, 'name')) {
                            $q .= '<div class="specs_row" id ="spec_row_' . $count . '_' . $speccount . '">';
                            $q .= '<div style="display:inline-block; margin-left:-30px; cursor:move" class="draggable-handle2 specs_drag"><b>' . $speccount . '</b></div>';
                            $q .= '<label class="gradus_label" for="spec_name_.' . $speccount . '">Spec Name</label>';
                            $q .= '<a class="delete_icon" src="javascript:void(0)" onclick="deleteHeader(\'spec_row_' . $count . '_' . $speccount . '\')">';
                            $q .= '<img style="width:20px;" src="' . $this->assets->getUrl("Gradus_Catalog::images/trash.png") . '"></a>';
                            $q .= '<input style="width:99%" class="gradus_text_large" data-form-part="product_form" name="techspec[' . $count . '][header][' . $speccount . '][name]" ';
                            $q .= 'id="spec_name_".' . $speccount . '" value="' . $s->name . '"/>';
                            $q .= '<label class="gradus_label" for="spec_desc_".' . $speccount . '>Spec Description</label>';
                            $q .= '<textarea style="width:99%" class="gradus_text_large" data-form-part="product_form" name="techspec[' . $count . '][header][' . $speccount . '][desc]"';
                            $q .= 'id="spec_desc_' . $speccount . '">' . $s->desc . '</textarea>';
                            $q .= '<a class="delete_icon" src="javascript:void(0)" onclick="deleteHeader(\'spec_row_' . $count . '_' . $speccount . '\')"></a>';

                            $q .= '</div>';
                            $speccount++;
                        }
                    }
                }
                $q .= '</td></tr>';


                $q .= '</table>';
                $q .= '<button style="margin-top:10px;" class="action-secondary" onclick="addSpec(\'header_table_' . $count . '\')">Add Spec</button></div>';
                $q .= '</div>';
                $q .= '</div>';
                $count++;
            }
        }
        return array('res' =>$q, 'mes' => "Includes updated");
    }

    public function highlights($prod)
    {
        $highlight = $prod->getData('highlights');
        $highlights = json_decode($highlight);
        $q = '';
        $count = 1;
        if (count($highlights) > 0) {
            foreach ($highlights as $h) {
                $q .= '<div style="margin:auto" class="gradus_fields" id="highlight_'.$count.'">';
                $q .= '<div class="draggable-handle highlight_drag"><b>'.$count.'</b></div>';
                $q .= '<label class="gradus_label" for="highlight_'.$count.'">Highlight</label>';
                $q .= '<input data-form-part="product_form" name="highlights['.$count.']" style="margin-left:6px;" class="gradus_text_large" id="hightlight_'.$count.'" value="'.$h.'" />';
                $q .= ' <a src="javascript:void(0)" onclick="deleteHighlight(\'highlight_'.$count.'\')">';
                $q .= '<img style="width:20px;" src="'.$this->assets->getUrl("Gradus_Catalog::images/trash.png").'"></a></div>';
                $count++;
            }
        }
        return array('res' =>$q, 'mes' => "Includes updated");
    }

    public function features($prod)
    {
        $feat = $prod->getData('features');
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
        return array('res' =>$q, 'mes' => "Includes updated");
    }
}