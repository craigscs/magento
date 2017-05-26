<?php

namespace Gradus\Compatibility\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\Component\Form\Fieldset;

class Related extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Related
{
    const DATA_SCOPE_COMPATIBILITY = 'compatibility';
    const DATA_SCOPE_ACCESSORIES = 'accessories';
    const DATA_SCOPE_PARTS = 'parts';

    /**
     * @var string
     */
    private static $previousGroup = 'search-engine-optimization';

    /**
     * @var int
     */
    private static $sortOrder = 90;

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                static::GROUP_RELATED => [
                    'children' => [
                        $this->scopePrefix . static::DATA_SCOPE_RELATED => $this->getRelatedFieldset(),
                        $this->scopePrefix . static::DATA_SCOPE_UPSELL => $this->getUpSellFieldset(),
                        $this->scopePrefix . static::DATA_SCOPE_CROSSSELL => $this->getCrossSellFieldset(),
                        $this->scopePrefix . static::DATA_SCOPE_COMPATIBILITY => $this->getCompatibilityFieldset(),
                        $this->scopePrefix . static::DATA_SCOPE_ACCESSORIES => $this->getAccessoriesFieldset(),
                        $this->scopePrefix . static::DATA_SCOPE_PARTS => $this->getPartsFieldset()
                    ],
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Related Products, Up-Sells, Cross-Sells, Accessories, Compatibility and Parts'),
                                'collapsible' => true,
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::DATA_SCOPE,
                                'sortOrder' =>
                                    $this->getNextGroupSortOrder(
                                        $meta,
                                        self::$previousGroup,
                                        self::$sortOrder
                                    ),
                            ],
                        ],

                    ],
                ],
            ]
        );

        return $meta;
    }

    /**
     * Prepares config for the Custom type products fieldset
     *
     * @return array
     */
    protected function getCompatibilityFieldset()
    {
        $url = $this->urlBuilder->getUrl('requires/requires/new');
        $content = __(
            'Compatibility products are shown to customers in addition to the item the customer is looking at.  <b><a target="_blank" href="'.$url.'">Add a new compatible require</a></b>.'
        );

        return [
            'children' => [
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Add Compatibility Products'),
                    $this->scopePrefix . static::DATA_SCOPE_COMPATIBILITY
                ),
                'modal' => $this->getGenericModal(
                    __('Add Compatibility Products'),
                    $this->scopePrefix . static::DATA_SCOPE_COMPATIBILITY
                ),
                static::DATA_SCOPE_COMPATIBILITY => $this->getGrid($this->scopePrefix . static::DATA_SCOPE_COMPATIBILITY),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Compatibility Products'),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => 90,
                    ],
                ],
            ]
        ];
    }
    protected function getAccessoriesFieldset()
    {
        $content = __(
            'Accessories products are shown to customers in addition to the item the customer is looking at.'
        );

        return [
            'children' => [
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Add Accessories Products'),
                    $this->scopePrefix . static::DATA_SCOPE_ACCESSORIES
                ),
                'modal' => $this->getGenericModal(
                    __('Add Accessories Products'),
                    $this->scopePrefix . static::DATA_SCOPE_ACCESSORIES
                ),
                static::DATA_SCOPE_ACCESSORIES => $this->getGrid($this->scopePrefix . static::DATA_SCOPE_ACCESSORIES),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Accessories Products'),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => 90,
                    ],
                ],
            ]
        ];
    }

    protected function getPartsFieldset()
    {
        $content = __(
            'Parts products are shown to customers in addition to the item the customer is looking at.'
        );

        return [
            'children' => [
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Add Parts Products'),
                    $this->scopePrefix . static::DATA_SCOPE_PARTS
                ),
                'modal' => $this->getGenericModal(
                    __('Add Parts Products'),
                    $this->scopePrefix . static::DATA_SCOPE_PARTS
                ),
                static::DATA_SCOPE_PARTS => $this->getGrid($this->scopePrefix . static::DATA_SCOPE_PARTS),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Parts Products'),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => 90,
                    ],
                ],
            ]
        ];
    }

    /**
     * Retrieve all data scopes
     *
     * @return array
     */
    protected function getDataScopes()
    {
        return [
            static::DATA_SCOPE_RELATED,
            static::DATA_SCOPE_CROSSSELL,
            static::DATA_SCOPE_UPSELL,
            static::DATA_SCOPE_COMPATIBILITY,
            static::DATA_SCOPE_ACCESSORIES,
            static::DATA_SCOPE_PARTS
        ];
    }
}