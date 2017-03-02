<?php

namespace Gradus\Compatibility\Model;

class Product extends \Magento\Catalog\Model\Product
{
    /**
     * Retrieve array of custom type products
     *
     * @return array
     */
    public function getCompatibilityProducts()
    {
        if (!$this->hasCompatibilityProducts()) {
            $products = [];
            foreach ($this->getCompatibilityProductCollection() as $product) {
                $products[] = $product;
            }
            $this->setCompatibilityProducts($products);
        }
        return $this->getData('compatibility_products');
    }
    /**
     * Retrieve custom type products identifiers
     *
     * @return array
     */
    public function getCompatibilityIds()
    {
        if (!$this->hasCompatibilityProductIds()) {
            $ids = [];
            foreach ($this->getCompatibilityProducts() as $product) {
                $ids[] = $product->getId();
            }
            $this->setCompatibilityProductIds($ids);
        }
        return $this->getData('compatibility_product_ids');
    }
    /**
     * Retrieve collection custom type product
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     */
    public function getCompatibilityProductCollection()
    {
        $collection = $this->getLinkInstance()->useCompatibilityLinks()->getProductCollection()->setIsStrongMode();
        $collection->setProduct($this);
        return $collection;
    }
    /**
     * Retrieve collection custom type link
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Collection
     */
    public function getCompatibilityLinkCollection()
    {
        $collection = $this->getLinkInstance()->useCompatibilityLinks()->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }
    
}