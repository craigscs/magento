<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Gradus\Catalog\Ui\DataProvider\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\UrlInterface;
use Magento\Framework\Registry;
use Magento\Framework\AuthorizationInterface;
use Magento\Ui\Component;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Ui\Component\Container;

/**
 * Class Attributes
 */
class CopyData extends AbstractModifier
{

    const GROUP_SORT_ORDER = 15;
    const GROUP_NAME = 'Attributes';
    const GROUP_CODE = 'attributes';

    public function modifyData(array $data)
    {
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        $meta = $this->addCopyModal($meta);
        $meta = $this->addCopyTemplate($meta);
        return $meta;
    }

    private function addCopyTemplate(array $meta)
    {
        $meta['add_copy_modal']['children']['copy_template'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => '',
                        'componentType' => Component\Container::NAME,
                        'autoRender' => false,
                        'ns' => 'product_attributes_grid',
                        'render_url' => 'mui/index/render',
                        'immediateUpdateBySelection' => true,
                        'behaviourType' => 'edit',
                        'externalFilterMode' => true,
                        'dataLinks' => ['imports' => false, 'exports' => true],
                        'formProvider' => 'ns = ${ $.namespace }, index = product_form',
                        'groupCode' => static::GROUP_CODE,
                        'groupName' => static::GROUP_NAME,
                        'groupSortOrder' => static::GROUP_SORT_ORDER,
                        'addAttributeUrl' =>
                            "asd",
                        'productId' => 2,
                        'productType' => 1,
                        'loading' => false,
                        'imports' => [
                            'attributeSetId' => '${ $.provider }:data.product.attribute_set_id'
                        ],
                        'exports' => [
                            'attributeSetId' => '${ $.externalProvider }:params.template_id'
                        ]
                    ],
                ],
            ]
        ];
        return $meta;
    }

    private function addCopyModal(array $meta)
    {
        $meta['add_copy_modal']['arguments']['data']['config'] = [
            'isTemplate' => false,
            'componentType' => Component\Modal::NAME,
            'dataScope' => '',
            'provider' => 'product_form.product_form_data_source',
            'imports' => [
                'state' => '!index=product_attribute_add_form:responseStatus'
            ],
            'options' => [
                'title' => __('Copy Data'),
                'buttons' => [
                    [
                        'text' => 'Cancel',
                        'actions' => [
                            [
                                'targetName' => '${ $.name }',
                                'actionName' => 'actionCancel'
                            ]
                        ]
                    ],
                    [
                        'text' => __('Copy'),
                        'class' => 'action-primary',
                        'actions' => [
                            [
                                'targetName' => '${ $.name }.product_attributes_grid',
                                'actionName' => 'copy'
                            ],
                            [
                                'closeModal'
                            ]
                        ]
                    ]
                ],
            ],
        ];
        return $meta;
    }
}