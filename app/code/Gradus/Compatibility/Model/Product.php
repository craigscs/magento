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
    public function getAccessoriesProducts()
    {
        if (!$this->hasAccessoriesProducts()) {
            $products = [];
            foreach ($this->getAccessoriesProductCollection() as $product) {
                $products[] = $product;
            }
            $this->setAccessoriesProducts($products);
        }
        return $this->getData('accessories_products');
    }
    public function getPartsProducts()
    {
        if (!$this->hasPartsProducts()) {
            $products = [];
            foreach ($this->getPartsProductCollection() as $product) {
                $products[] = $product;
            }
            $this->setPartsProducts($products);
        }
        return $this->getData('parts_products');
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
    public function getAccessoriesIds()
    {
        if (!$this->hasAccessoriesroductIds()) {
            $ids = [];
            foreach ($this->getAccessoriesProducts() as $product) {
                $ids[] = $product->getId();
            }
            $this->setAccessoriesProductIds($ids);
        }
        return $this->getData('accessories_product_ids');
    }
    public function getPartsIds()
    {
        if (!$this->hasPartsroductIds()) {
            $ids = [];
            foreach ($this->getPartsProducts() as $product) {
                $ids[] = $product->getId();
            }
            $this->setPartsProductIds($ids);
        }
        return $this->getData('parts_product_ids');
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
    public function getAccessoriesProductCollection()
    {
        $collection = $this->getLinkInstance()->useAccessoriesLinks()->getProductCollection()->setIsStrongMode();
        $collection->setProduct($this);
        return $collection;
    }
    public function getPartsProductCollection()
    {
        $collection = $this->getLinkInstance()->usePartsLinks()->getProductCollection()->setIsStrongMode();
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
    public function getAccessoriesLinkCollection()
    {
        $collection = $this->getLinkInstance()->useAccessoriesLinks()->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }
    public function getPartsLinkCollection()
    {
        $collection = $this->getLinkInstance()->usePartsLinks()->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }
}