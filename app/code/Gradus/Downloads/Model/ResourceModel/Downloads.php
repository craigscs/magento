<?php
namespace Gradus\Downloads\Model\ResourceModel;

class Downloads extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('downloads', 'download_id');
    }
}