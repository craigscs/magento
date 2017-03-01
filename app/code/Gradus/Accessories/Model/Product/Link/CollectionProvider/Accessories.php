<?php

namespace Gradus\Accessories\Model\Product\Link\CollectionProvider;

class Accessories implements \Magento\Catalog\Model\ProductLink\CollectionProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLinkedProducts(\Magento\Catalog\Model\Product $product)
    {
        return $product->getAccessoriesProducts();
    }
}