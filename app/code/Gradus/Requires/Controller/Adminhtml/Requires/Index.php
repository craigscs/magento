<?php

/**
 * Created by PhpStorm.
 * User: Craig
 * Date: 3/7/2017
 * Time: 10:06 AM
 */

namespace Gradus\Requires\Controller\Adminhtml\Requires;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;


class Index extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('SR_Weblog::blogpost');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Requires'));

        return $resultPage;
    }
}
