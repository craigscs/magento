<?php
use Magento\Framework\App\Bootstrap;
require __DIR__ . '/../../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();
$storeManager = $obj->get('\Magento\Store\Model\StoreManagerInterface');
$store = $storeManager->getStore();
$storeId = $store->getStoreId();

$catfact = $obj->create('\Magento\Catalog\Model\CategoryFactory');

// Set the state (not sure if this is eccessary)
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('adminhtml');

$pr = $obj->create('Magento\Catalog\Model\ProductRepository');
$file = fopen('shell/import//csv/cat.csv', 'r');
$c = 0;
$cats = array();
while (($rowData = fgetcsv($file)) !== FALSE) {
    if ($c ==0) {
        $c++;
        continue;
    }
//    if ($c > 10) continue;
    $cats[] = array('name' => $rowData[1],
        'parent' => $rowData[5], 'description' => $rowData[4],
        'url' => $rowData[3], 'pname' => $rowData[6]
    );
    $c++;
}
fclose($file);
//ksort($cats);
array_shift($cats);
foreach ($cats as $pid => $value) {
    //Try to load the category
    $collection = array();
    $collection = $catfact->create()->getCollection()->addAttributeToFilter('name',$value['name'])->setPageSize(1);
    if ($collection->getSize()) {
        $categoryId = $collection->getFirstItem()->getName();
        var_dump("WE FOUND: ".$categoryId. " : ".$collection->getFirstItem()->getId());
        continue;
    }
/// Add a new sub category under root category
    $categoryTmp = $obj->create('Magento\Catalog\Model\Category');
    $categoryTmp->setName($value['name']);
    $categoryTmp->setIsActive(true);
    $categoryTmp->setUrlKey($value['url']);
    $categoryTmp->setStoreId($storeId);
    $categoryTmp->setData('description', $value['description']);

    $pcollection = $catfact->create()->getCollection()->addAttributeToFilter('name',$value['pname'])->setPageSize(1);
    if ($pcollection->getSize()) {
        $pcategory = $pcollection->getFirstItem();
    }
    if ($pcategory->getId() > 1) {
        $categoryTmp->setParentId($pcategory->getId());
    } else {
        $categoryTmp->setParentId(1);
    }
    try {
        var_dump("THIS IS THE PARENT: ".$categoryTmp->getParentId());
        $repository = $obj->get('\Magento\Catalog\Api\CategoryRepositoryInterface');
        $repository->save($categoryTmp);
        var_dump("WE ARE SAVING: ".$value['name']);
    } catch (\Exception $e) {
        var_dump($e->getMessage());
    }
}