<?php
namespace Gradus\Importer\Model\ResourceModel\Imports;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'import_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Gradus\Importer\Model\Imports', 'Gradus\Importer\Model\ResourceModel\Imports');
        $this->_map['fields']['import_id'] = 'main_table.import_id';
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