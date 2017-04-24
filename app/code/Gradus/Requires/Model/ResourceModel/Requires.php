<?php
namespace Gradus\Requires\Model\ResourceModel;

class Requires extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('compatible_requires', 'id');
    }
}