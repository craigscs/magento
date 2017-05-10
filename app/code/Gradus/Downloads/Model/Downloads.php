<?php
namespace Gradus\Downloads\Model;

class Downloads extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Gradus\Downloads\Model\ResourceModel\Downloads');
    }
}