<?php
use Magento\Framework\App\Bootstrap;
require __DIR__ . '/../../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();
// Set the state (not sure if this is neccessary)
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('adminhtml');

$pr = $obj->create('Magento\Catalog\Model\ProductRepository');
$file = fopen('shell/import/csv/techspecs.csv', 'r');
$c = 0;
$cc = 0;

$file2 = fopen('shell/import/csv/links.csv', 'r');
$links = array();
while (($row = fgetcsv($file2, 4096)) !== false) {
    if ($c == 0) {
        $c++;
        continue;
    }
    $links[$row[3]] = $row[2];
}

while (($row = fgetcsv($file, 4096)) !== false)
{
    if ($cc ==0) {
        $cc++;
        continue;
    }
    if(!isset($headers[$row[2]]))
    {
        $headers[$row[2]] = array();
    }
    if (empty($row[5])) {
        if ($row[4] == '') {
            $h = "Misc";
        } else {
            $h = $row[4];
            var_dump($h);
        }
    }
    if (!empty($row[5])) {
        $things[$row[2]][$h][$row[4]] = array(
            "label" => $row[4],
            "value" => $row[5],
        );
    }
}
fclose($file);
foreach ($things as $sku => $t) {

    $tech = array();
    $hcount = 0;
    $namecount = 0;
    foreach ($t as $hname => $vals) {
        $tech[$namecount]['header']['header_name'] = $hname;
       foreach ($vals as $q) {
           $tech[$namecount]['header'][] = array(
               'name' => $q['label'],
               'desc' => $q['value']
           );
       }
        $namecount++;
    }
    try {
        if (isset($links[$sku])) {
            $sku = $links[$sku];
        }
        $p = $pr->get($sku);
        $p->setData("tech_specs", json_encode($tech));
        $p->getResource()->saveAttribute($p, 'tech_specs');
        printf("SKU SAVED: ".$sku."\n");
//        die();
    } catch (\Exception $e) {
        printf($e->getMessage());
    }
}