<?php
namespace Gradus\Downloads\Model\ResourceModel\Downloads;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'download_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Gradus\Downloads\Model\Downloads', 'Gradus\Downloads\Model\ResourceModel\Downloads');
        $this->_map['fields']['download_id'] = 'main_table.download_id';
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return 1;
    }
}