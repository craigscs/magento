<?php
use Magento\Framework\App\Bootstrap;
require __DIR__ . '/../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();
// Set the state (not sure if this is neccessary)
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('adminhtml');

$pr = $obj->create('Magento\Catalog\Model\ProductRepository');

$collection = $obj->create('\Magento\Catalog\Model\ResourceModel\Product\Collection')
    ->addAttributeToSelect('mfr_num');

$fout = fopen('mrf.csv', 'w');
fputcsv($fout, array('Sku', 'Mfr'));

foreach ($collection as $p) {
    fputcsv($fout, array($p->getData('sku'), $p->getData('mfr_num')));
}