<?php

namespace Gradus\Compatibility\Ui\DataProvider\Product\Related;

use Magento\Catalog\Ui\DataProvider\Product\Related\AbstractDataProvider;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductLinkInterface;
use Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Api\ProductLinkRepositoryInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreRepositoryInterface;

/**
 * Class CustomTypeDataProvider
 */
class PartsDataProvider extends AbstractDataProvider
{
    /**
     * {@inheritdoc
     */
    protected function getLinkType()
    {
        return 'parts';
    }

    public function getCollection()
    {
        /** @var Collection $collection */
        $collection = parent::getCollection();
        $collection->addAttributeToSelect('status');

        if ($this->getStore()) {
            $collection->setStore($this->getStore());
        }

        if ($_SESSION['prod_type'] == null) {
            if ($_SESSION['written'] == false) {
                echo "<p style='font-size:150%; color:#ff0000;'>Please save the product before adding compatible products</p>";
                $_SESSION['written'] = true;
            }
            $collection->addAttributeToFilter('entity_id', 0);
            return $collection;
        }

        if ($_SESSION['prod_type'] == '4') {
            $collection->addAttributeToFilter('gradus_type', '5');
        }
        if ($_SESSION['prod_type'] == '5') {
            $collection->addAttributeToFilter('gradus_type', '4');
        }

        if (!$this->getProduct()) {
            return $collection;
        }

        $collection->addAttributeToFilter(
            $collection->getIdFieldName(),
            ['nin' => [$this->getProduct()->getId()]]
        );

        return $this->addCollectionFilters($collection);
    }

}
