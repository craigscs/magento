<?php
namespace Gradus\Requires\Model;

class Requires extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Gradus\Requires\Model\ResourceModel\Requires');
    }

    public function load($modelId, $field = null)
    {
        return $this->getCollection()->addFieldToSelect('*')
            ->addFieldToFilter('entity_id', array('finset'=>$modelId));
    }
}