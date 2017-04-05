<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Gradus\Catalog\Model\Attribute\Source;

use \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class Scopes implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 4,
                'label' => __('Specification'),
            ],
            [
                'value' => 3,
                'label' => __('Layered Navigation'),
            ],
            [
                'value' => ScopedAttributeInterface::SCOPE_STORE,
                'label' => __('Store View'),
            ],
            [
                'value' => ScopedAttributeInterface::SCOPE_WEBSITE,
                'label' => __('Web Site'),
            ],
            [
                'value' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'label' => __('Global'),
            ],
        ];
    }
}
