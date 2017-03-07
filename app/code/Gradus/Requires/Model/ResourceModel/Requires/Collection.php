<?php
namespace Gradus\Requires\Model\ResourceModel\Requires;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Gradus\Requires\Model\Requires', 'Gradus\Requires\Model\ResourceModel\Requires');
        $this->_map['fields']['id'] = 'main_table.id';
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