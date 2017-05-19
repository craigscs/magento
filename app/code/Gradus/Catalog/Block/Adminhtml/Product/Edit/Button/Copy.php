<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Gradus\Catalog\Block\Adminhtml\Product\Edit\Button;

/**
 * Class AddAttribute
 */
class Copy extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [
            'label' => __('Copy From'),
            'class' => 'action-secondary',
            'on_click' => 'toggleCopy()',
            'sort_order' => 20
        ];
    }
}
