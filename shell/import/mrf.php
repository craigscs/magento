<?php
use Magento\Framework\App\Bootstrap;
require __DIR__ . '/../../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();
// Set the state (not sure if this is neccessary)
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('adminhtml');

$pr = $obj->create('Magento\Catalog\Model\ProductRepository');
$file = fopen('shell/import/csv/mrf.csv', 'r');
$c = 0;
while (($row = fgetcsv($file, 4096)) !== false)
{
    if ($c ==0) {
        $c++;
        continue;
    }
    {   if(!isset($productData[$row['0']]))
    {
        $productData[$row['0']] = array();
    }
        $productData[$row['0']] = array(
            "mfr" => $row['1'],
        );
    }
}
fclose($file);
foreach ($productData as $sku => $value) {
    $p = $pr->get($sku);
    $p->setData("mfr_num", $value['mfr']);
    $p->getResource()->saveAttribute($p, 'mfr_num');
    echo "SKU ".$sku." saved.";
}