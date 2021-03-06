<?php
use Magento\Framework\App\Bootstrap;
require __DIR__ . '/../../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();
// Set the state (not sure if this is neccessary)
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('adminhtml');

$pr = $obj->create('Magento\Catalog\Model\ProductRepository');
$process = $obj->create('Magento\Catalog\Model\Product\Gallery\Processor');
$file = fopen('shell/import/csv/images.csv', 'r');
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

while (($row = fgetcsv($file)) !== FALSE) {
    if ($cc ==0) {
        $cc++;
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
    try {
        if (isset($links[$sku])) {
            $sku = $links[$sku];
        }
        $p = $pr->get($sku);
        foreach ($value as $v) {
            $g = $p->getMediaGallery();
            foreach ($g['images'] as $gg) {
                if ($gg['label'] == $v['caption']) {
                    printf("We found the image already, skipping\n");
                    continue;
                }
            }
            $url = $v['file'];
            $img = 'pub/media/bhimp/' . $v['name'];
            file_put_contents($img, file_get_contents($url));
            $p->addImageToMediaGallery('/bhimp/' . $v['name'], array('image', 'small_image', 'thumbnail'), false, false);
            $gallery = $p->getData('media_gallery');
            $lastimg = array_pop($gallery['images']);
            $process->updateImage($p, $lastimg['file'], array('label' => $v['caption'], 'label_default' => $v['caption'], 'position' => $v['pos']));
            printf("Added image: ".$v['caption'].' to sku '.$sku."\n");
            unlink('pub/media/bhimp/' . $v['name']);
        }
            $p->save();
        printf("SKU saved: ".$sku."\n");
    } catch (\Exception $e) {
        printf($e->getMessage()." :: ".$sku);
    }
}