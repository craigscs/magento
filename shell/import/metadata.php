<?php
use Magento\Framework\App\Bootstrap;
require __DIR__ . '/../../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();
// Set the state (not sure if this is neccessary)
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('adminhtml');

$pr = $obj->create('Magento\Catalog\Model\ProductRepository');
$file = fopen('shell/import/metadata.csv', 'r');
$c = 0;
while (($rowData = fgetcsv($file, 4096)) !== false)
{
    if ($c ==0) {
        $c++;
        continue;
    }
    {   if(!isset($productData[$row['2']]))
    {
        $productData[$row['2']] = array();
    }
        $productData[$row['2']] = array(
            "keyword" => $row['3'],
            "description" => $row['4']
        );
    }
}
fclose($file);
foreach ($productData as $sku => $value) {
    $p = $pr->get($sku);
    $product->setData("meta_keyword", $overviewData['keyword']);
    $product->setData("meta_description", $overviewData['description']);
    $p->getResource()->saveAttribute($p, 'meta_keyword');
    $p->getResource()->saveAttribute($p, 'meta_description');
    echo "SKU ".$sku." saved.";
}