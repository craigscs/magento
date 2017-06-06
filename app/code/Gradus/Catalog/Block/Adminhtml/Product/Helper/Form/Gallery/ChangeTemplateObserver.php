<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Gradus\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery;

use Magento\Framework\Event\ObserverInterface;

class ChangeTemplateObserver implements ObserverInterface
{
    /**
     * @param mixed $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $observer->getBlock()->setTemplate('Gradus_Catalog::catalog/product/helper/gallery.phtml');
    }
}
