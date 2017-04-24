<?php

namespace Gradus\Compatibility\Model\Catalog\Product;

class Link extends \Magento\Catalog\Model\Product\Link
{
    const LINK_TYPE_COMPATIBILITY= 7;

    /**
     * @return \Magento\Catalog\Model\Product\Link $this
     */
    public function useCompatibilityLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_COMPATIBILITY);
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
        if(!is_null($data)) {
            $this->_getResource()->saveProductLinks($product, $data, self::LINK_TYPE_COMPATIBILITY);
        }
    }
}