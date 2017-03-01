<?php
use Magento\Framework\App\Bootstrap;
require __DIR__ . '/../../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();
// Set the state (not sure if this is neccessary)
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('adminhtml');

$pr = $obj->create('Magento\Catalog\Model\ProductRepository');
$file = fopen('images.csv', 'r');
$c = 0;
while (($row = fgetcsv($file)) !== FALSE) {
    if ($c ==0) {
        $c++;
        continue;
    }
    if(!isset($productData[$row[1]]))
    {
        $productData[$row[1]] = array();
    }
    $productData[$row[1]][$row[2]] = array(
        "caption" => $row[3],
        "pos" => $row[2],
        "name" => $row[4],
        'file' => $row[6]
    );
}
fclose($file);
foreach ($productData as $sku => $value) {
    $p = $pr->get($sku);
    $img = file_get_contents($value[1]['file']);
    $im = imagecreatefromstring($img);
    imagepng($im, "image/".$value[1]['name']);
    $imagePath = "http://gradus.dev/shell/import/".$value[1]['name']; // path of the image])
    var_dump($imagePath);
    $p->addImageToMediaGallery($imagePath, array('image', 'small_image', 'thumbnail'), false, false);
    //    $p->setData('features', json_encode($value));
//    $p->getResource()->saveAttribute($p, 'features');
}