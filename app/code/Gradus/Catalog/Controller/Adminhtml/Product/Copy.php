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
        $res = $this->jf->create()->setData(['res'=>$result['res'], 'func' => $result['q']]);
        return $res;
    }

    public function InTheBox($prod)
    {
        $box = $prod->getData('in_the_box');
        $inbox = json_decode($box);
        return array('res' =>$inbox, 'q' => "");
    }


    public function techspecs($prod)
    {
        $spec = $prod->getData('tech_specs');
        $specs = json_decode($spec);
        return array('res' => $specs, 'q' => "copyHeaders");
    }

    public function description($prod)
    {
        return array(
            'res' => array('description'=>$prod->getData('description'), 'short_description' => $prod->getgDatagetData('short_description'), 'overview_note' => $prod->getData('overview_note')),
            'q' => 'setFields'
        );
    }

    public function highlights($prod)
    {
        $highlight = $prod->getData('highlights');
        $highlights = json_decode($highlight);
        return array('res' =>$highlights, 'q' => 'copyHighlight');
    }

    public function features($prod)
    {
        $feat = $prod->getData('features');
        $feats = json_decode($feat);
        return array('res' =>$feats, 'q' => "copyFeatures");
    }
}