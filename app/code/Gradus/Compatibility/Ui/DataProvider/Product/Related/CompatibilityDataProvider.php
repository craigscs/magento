<?php

namespace Gradus\Compatibility\Ui\DataProvider\Product\Related;

use Magento\Catalog\Ui\DataProvider\Product\Related\AbstractDataProvider;

/**
 * Class CustomTypeDataProvider
 */
class CompatibilityDataProvider extends AbstractDataProvider
{
    /**
     * {@inheritdoc
     */
    protected function getLinkType()
    {
        return 'compatibility';
    }
}
