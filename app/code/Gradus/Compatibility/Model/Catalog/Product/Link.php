<?php

namespace Gradus\Compatibility\Model\Catalog\Product;

class Link extends \Magento\Catalog\Model\Product\Link
{
    const LINK_TYPE_COMPATIBILITY= 7;
    const LINK_TYPE_ACCESSORIES= 6;
    const LINK_TYPE_PARTS= 8;

    /**
     * @return \Magento\Catalog\Model\Product\Link $this
     */
    public function useCompatibilityLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_COMPATIBILITY);
        return $this;
    }
    public function useAccessoriesLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_ACCESSORIES);
        return $this;
    }
    public function usePartsLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_PARTS);
        return $this;
    }

    /**
     * Save data for product relations
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product\Link
     */
    public function saveProductRelations($product)
    {
        parent::saveProductRelations($product);
        $data = $product->getCompatibilityData();
        $accessories = $product->getAccessoriesData();
        $parts = $product->getPartsData();

        if(!is_null($data)) {
            $this->_getResource()->saveProductLinks($product, $data, self::LINK_TYPE_COMPATIBILITY);
        }
        if(!is_null($accessories)) {
            $this->_getResource()->saveProductLinks($product, $accessories, self::LINK_TYPE_ACCESSORIES);
        }
        if(!is_null($parts)) {
            $this->_getResource()->saveProductLinks($product, $parts, self::LINK_TYPE_PARTS);
        }
    }
}