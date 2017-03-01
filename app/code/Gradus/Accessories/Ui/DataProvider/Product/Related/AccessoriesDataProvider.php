<?php

namespace Gradus\Accessories\Ui\DataProvider\Product\Related;

use Magento\Catalog\Ui\DataProvider\Product\Related\AbstractDataProvider;

/**
 * Class CustomTypeDataProvider
 */
class AccessoriesDataProvider extends AbstractDataProvider
{
    /**
     * {@inheritdoc
     */
    protected function getLinkType()
    {
        return 'accessories';
    }
}
