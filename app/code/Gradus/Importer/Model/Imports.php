<?php
namespace Gradus\Importer\Model;

class Imports extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Gradus\Importer\Model\ResourceModel\Imports');
    }
}