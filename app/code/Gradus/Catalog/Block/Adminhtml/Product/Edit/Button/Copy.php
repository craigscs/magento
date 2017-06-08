<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Gradus\Catalog\Block\Adminhtml\Product\Edit\Button;
use Magento\Catalog\Block\Adminhtml\Product\Attribute\Button\Generic;

/**
 * Class AddAttribute
 */
class Copy extends Generic
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [
            'label' => __('Copy Data'),
            'class' => 'action-secondary',
            'on_click' => 'openCopyModal()',
            'sort_order' => 20
        ];
    }
}
