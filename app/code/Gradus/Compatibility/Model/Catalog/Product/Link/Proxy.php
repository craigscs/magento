<?php

namespace Gradus\Compatibility\Model\Catalog\Product\Link;

class Proxy extends \Magento\Catalog\Model\Product\Link\Proxy
{
    /**
     * {@inheritdoc}
     */
    public function useCompatibilityLinks()
    {
        return $this->_getSubject()->useCompatibilityLinks();
    }
    public function useAccessoriesLinks()
    {
        return $this->_getSubject()->useAccessoriesLinks();
    }
    public function usePartsLinks()
    {
        return $this->_getSubject()->usePartsLinks();
    }
}