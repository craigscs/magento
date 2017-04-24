<?php

namespace Gradus\Compatibility\Model\Product\Link\CollectionProvider;

class Compatibility implements \Magento\Catalog\Model\ProductLink\CollectionProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLinkedProducts(\Magento\Catalog\Model\Product $product)
    {
        return $product->getCompatibilityProducts();
    }
}