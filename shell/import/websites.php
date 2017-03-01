<?php
use Magento\Framework\App\Bootstrap;
require __DIR__ . '/../../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();
// Set the state (not sure if this is neccessary)
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('adminhtml');
$file = fopen('shell/import/csv/websites.csv', 'r');
$c = 0;
$cats = array();
while (($rowData = fgetcsv($file)) !== FALSE) {
    if ($c ==0) {
        $c++;
        continue;
    }
    $cats[] = array('name' => $rowData[1],
        'code' => $rowData[2]
    );
}
fclose($file);
ksort($cats);
foreach ($cats as $value) {
    $skip = false;
    $websiteFactory = $obj->create('\Magento\Store\Model\WebsiteFactory');
    $website = $websiteFactory->create();
    $websiteResourceModel = $obj->create('Magento\Store\Model\ResourceModel\Website');
/// Add a new sub category under root category
    $website->load($value['code']);
    if (!$website->getId()) {
        $website->setCode($value['code']);
        $website->setName($value['name']);
        $website->setDefaultGroupId(3);
        $websiteResourceModel->save($website);
    }
}