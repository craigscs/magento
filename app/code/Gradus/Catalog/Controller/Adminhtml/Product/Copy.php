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
        $res = $this->jf->create()->setData(
            ['dataindex' => $result['data-index'], 'messages' => $result['messages'],'res'=>$result['res'], 'field_type' => $result['field_type'],'func' => $result['q'], 'field'=>$result['field']]);
        return $res;
    }

    public function name($prod)
    {
        return array(
            'res'=>$prod->getName(),
            'q'=>'copyname',
            'field'=>'product[name]',
            'field_type'=>"byName",
            'messages' => array(
                'success_message' => 'Name field successfully copied'
            )
        );
    }

    public function InTheBox($prod)
    {
        $box = $prod->getData('in_the_box');
        $inbox = json_decode($box);
        return array(
            'res' =>$inbox,
            'field_type' => 'dynamic',
            'field' => 'inthebox',
            'q' => 'copyinthebox',
            'data-index' => 'block_inthebox.tab',
            'messages' => array(
                'success_message' => 'Includes field successfully copied'
            )
        );
    }

    public function categories($prod)
    {
        $cats = $prod->getCategoryIds();
        var_dump($cats); die();
    }

    public function techspecs($prod)
    {
        $spec = $prod->getData('tech_specs');
        $specs = json_decode($spec);
        return array('field_type' => "dynamic",
            'field' => 'techspecs',
            'res' => $specs,
            'q' => "copyHeaders",
            'data-index' => 'block_techspecs.tab',
            'messages' => array(
                'success_message' => 'Tech Specs field successfully copied'
            )
            );
    }

    public function description($prod)
    {
        return array(
            'res' => $prod->getData('description'),
            'q' => 'setFields',
            'field_type' => "byName",
            'field' => 'description',
            'messages' => array(
                'success_message' => 'Description field successfully copied'
            )
        );
    }
    public function short_description($prod)
    {
        return array(
            'res' => $prod->getData('short_description'),
            'q' => 'setFields',
            'field_type' => "byName",
            'field' => 'short_description',
            'messages' => array(
                'success_message' => 'Short Description field successfully copied'
            )
        );
    }
    public function overview_note($prod)
    {
        return array(
            'res' => $prod->getData('overview_note'),
            'q' => 'setFields',
            'field_type' => "byName",
            'field' => 'overview_note',
            'messages' => array(
                'success_message' => 'Overview Note field successfully copied'
            )
        );
    }

    public function metaTitle($prod)
    {
        return array(
            'q' => 'setFields',
            'res' => $prod->getData('meta_title'),
            'field_type' => 'byName',
            'field' => 'product[meta_title]',
            'messages' => array(
                'success_message' => 'Meta Title field successfully copied'
            )
        );
    }
    public function metaKeywords($prod)
    {
        return array(
            'q' => 'setFields',
            'res' => $prod->getData('meta_keyword'),
            'field_type' => 'byName',
            'field' => 'product[meta_keyword]',
            'messages' => array(
                'success_message' => 'Meta Keyword field successfully copied'
            )
        );
    }
    public function metaDescription($prod)
    {
        return array(
            'q' => 'setFields',
            'res' => $prod->getData('meta_description'),
            'field_type' => 'byName',
            'field' => 'product[meta_description]',
            'messages' => array(
                'success_message' => 'Meta Description field successfully copied'
            )
        );
    }

    public function highlights($prod)
    {
        $highlight = $prod->getData('highlights');
        $highlights = json_decode($highlight);
        return array(
            'res' =>$highlights,
            'q' => 'copyHighlight',
            'field'=>'highlights',
            'field_type' => "dynamic",
            'data-index' => 'block_highlights.tab',
            'messages' => array(
                'success_message' => 'Highlights field successfully copied'
            )
        );
    }

    public function features($prod)
    {
        $feat = $prod->getData('features');
        $feats = json_decode($feat);
        return array(
            'res' =>$feats,
            'q' => "copyFeatures",
            'field' => 'features',
            'field_type' => 'dynamic',
            'data-index' => 'block_features.tab',
            'messages' => array(
                'success_message' => 'Features field successfully copied'
            )
        );
    }
}