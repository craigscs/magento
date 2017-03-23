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

class NewAction extends \Magento\Catalog\Controller\Adminhtml\Product\NewAction
{
    public function __construct(Action\Context $context, Product\Builder $productBuilder, Initialization\StockDataFilter $stockFilter, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory)
    {
        $_SESSION['prod_type'] = null;
        $_SESSION['written'] = false;
        parent::__construct($context, $productBuilder, $stockFilter, $resultPageFactory, $resultForwardFactory);
    }

    public function execute()
    {
        return parent::execute();
    }
}