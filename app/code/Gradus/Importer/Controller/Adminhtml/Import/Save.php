<?php
namespace Gradus\Importer\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;

class Save extends \Magento\Backend\App\Action
{
    protected $fileSystem;
    protected $uploaderFactory;
    protected $allowedExtensions = ['csv'];
    protected $fileId = 'upl';
    protected $links;
    protected $pr;
    protected $mes = array();
    protected $success = true;
    protected $prod;
    protected $imgProcessor;

    /**
     * @param Action\Context $context
     */
    public function __construct(Action\Context $context,
                                Filesystem $fileSystem,
                                UploaderFactory $uploaderFactory,
                                \Magento\Catalog\Model\ProductRepository $pr,
                                \Magento\Catalog\Model\Product $p,
                                \Magento\Catalog\Model\Product\Gallery\Processor $imgp)
    {
        $this->imgProcessor = $imgp;
        $this->prod = $p;
        $this->pr = $pr;
        $this->fileSystem = $fileSystem;
        $this->uploaderFactory = $uploaderFactory;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $destinationPath = "shell/import/csv/";
            $this->makeLinks('shell/import/csv/links.csv');
            $func = explode(".php", $data['process'])[0];
            try {
                $model = $this->_objectManager->create('Gradus\Importer\Model\Imports');
                $uploader = $this->uploaderFactory->create(array('fileId' => $this->fileId))
                    ->setAllowCreateFolders(true)
                    ->setAllowedExtensions($this->allowedExtensions);
                if (!$uploader->save($destinationPath)) {
                    throw new LocalizedException(
                        __('File cannot be saved to path: $1', $destinationPath)
                    );
                }
                $res = $this->$func($data['brand'], $_FILES['upl']['name'], $data['clear']);
                $model->setData($data);
                $model->setData('messages', json_encode($this->mes));
                $model->setData('process', ucwords($func));
                $model->setData('succeeded', $this->success);
                $model->save();
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __($e->getMessage())
                );
                return $resultRedirect->setPath('*/*/edit', ['import_id' => $this->getRequest()->getParam('import_id')]);
            }
            try {
                unlink("shell/import/csv" . $_FILES['upl']['name']);
            } catch (\Exception $e) {}
            return $resultRedirect->setPath('*/*/');
        }
    }

    public function inthebox($brand, $f, $claer)
    {
        $success = true;
        $message = '';
        $file = fopen('shell/import/csv/'.$f, 'r');
        $c = 0;
        $productData = array();

        while (($row = fgetcsv($file, 4096)) !== false) {
            if ($c == 0) {
                $c++;
                continue;
            }
            $rowBrand = $row[0];
            $rowBrand = strtolower($rowBrand);
            if (strtolower($brand) == $rowBrand || $brand == "All") {
                if(!isset($productData[$row[2]]))
                {
                    $productData[$row[2]] = array();
                }
                $productData[$row[2]][$row['3']] = array(
                    "value" => $row[5],
                    "count" => $row[6]
                );
            }
        }
        fclose($file);
        foreach ($productData as $sku => $value) {
            try {
                if (isset($this->links[$sku])) {
                    $sku = $this->links[$sku];
                }
                $p = $this->pr->get($sku);
                $p->setData("inthebox", json_encode($value));
                $p->getResource()->saveAttribute($p, 'inthebox');
                $this->addSuccess("SKU " . $sku . " saved.", $sku);
            } catch (\Exception $e) {
                $success = false;
                $this->addError($e->getMessage().": SKU: ".$sku, $sku);
            }
        }
        $this->addDebug("Import is finished.", "None");
    }

    public function highlights($brand, $f, $clear)
    {
        $success = true;
        $message = '';
        $file = fopen('shell/import/csv/'.$f, 'r');
        $c = 0;
        $productData = array();

        while (($row = fgetcsv($file, 4096)) !== false) {
            if ($c == 0) {
                $c++;
                continue;
            }
            $rowBrand = $row[0];
            $rowBrand = strtolower($rowBrand);
            if (strtolower($brand) == $rowBrand || $brand == "All") {
                if(!isset($productData[$row[2]]))
                {
                    $productData[$row[2]] = array();
                }
                $productData[$row[2]][$row[3]] = $row[4];
            }
        }
        fclose($file);
        foreach ($productData as $sku => $value) {
            try {
                if (isset($this->links[$sku])) {
                    $sku = $this->links[$sku];
                }
                $p = $this->pr->get($sku);
                $p->setData("highlights", json_encode($value));
                $p->getResource()->saveAttribute($p, 'highlights');
                $this->addSuccess("SKU " . $sku . " saved.", $sku);
            } catch (\Exception $e) {
                $success = false;
                $this->addError($e->getMessage().": SKU: ".$sku, $sku);
            }
        }
        $this->addDebug("Import is finished.", "None");
    }

    public function overview($brand, $f, $clear)
    {
        $success = true;
        $message = '';
        $file = fopen('shell/import/csv/'.$f, 'r');
        $c = 0;
        $productData = array();

        while (($row = fgetcsv($file, 4096)) !== false) {
            if ($c == 0) {
                $c++;
                continue;
            }
            $rowBrand = $row[0];
            $rowBrand = strtolower($rowBrand);
            if (strtolower($brand) == $rowBrand || $brand == "All") {
                if(!isset($productData[$row[2]]))
                {
                    $productData[$row[2]] = array();
                }
                $productData[$row[2]] = array(
                    "overview" => $row[5],
                    "overview_note" => $row[7]
                );
            }
        }
        fclose($file);
        foreach ($productData as $sku => $value) {
            try {
                if (isset($this->links[$sku])) {
                    $sku = $this->links[$sku];
                }
                $p = $this->pr->get($sku);
                $p->setData("description", $value['overview']);
                $p->setData("overview_note", $value['overview_note']);
                $p->getResource()->saveAttribute($p, 'description');
                $p->getResource()->saveAttribute($p, 'overview_note');
                $this->addSuccess("SKU " . $sku . " saved.", $sku);
            } catch (\Exception $e) {
                $success = false;
                $this->addError($e->getMessage().": SKU: ".$sku, $sku);
            }
        }
        $this->addDebug("Import is finished.", "None");
    }

    public function features($brand, $f, $clear)
    {
        $file = fopen('shell/import/csv/'.$f, 'r');
        $c = 0;
        $productData = $this->processFile($file, $brand, "feature");
        foreach ($productData as $sku => $value) {
            try {
                if (isset($this->links[$sku])) {
                    $sku = $this->links[$sku];
                }
                $p = $this->pr->get($sku);
                $p->setData("features", json_encode($value));
                $p->getResource()->saveAttribute($p, 'features');
                $this->addSuccess("SKU " . $sku . " saved.", $sku);
            } catch (\Exception $e) {
                $success = false;
                $this->addError($e->getMessage().": SKU: ".$sku, $sku);
            }
        }
        $this->addDebug("Feature import is finished.", "None");
    }

    public function images($brand, $f, $clear)
    {
        $file = fopen('shell/import/csv/'.$f, 'r');
        $productData = array();
        $cc = 0;
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
            if (isset($links[$sku])) {
                $sku = $links[$sku];
            }
            try {
            $p = $this->pr->get($sku);

            if ($clear == 1) {
                $gals = $p->getMediaGalleryEntries();
                foreach ($gals as $key => $entry) {
                    //We can add your condition here
                    unset($gals[$key]);
                }
                $p->setMediaGalleryEntries($gals);
                $this->pr->save($p);

            }
            foreach ($value as $v) {
                $g = $p->getMediaGallery();
                foreach ($g['images'] as $gg) {
                    if ($gg['label'] == $v['caption']) {
                        $this->addDebug("We found the image already, skipping.", $sku);
                        continue;
                    }
                }
                $url = $v['file'];
                $img = 'pub/media/bhimp/' . $v['name'];
                file_put_contents($img, file_get_contents($url));
                $p->addImageToMediaGallery('/bhimp/' . $v['name'], array('image', 'small_image', 'thumbnail'), false, false);
                $gallery = $p->getData('media_gallery');
                $lastimg = array_pop($gallery['images']);
                $this->imgProcessor->updateImage($p, $lastimg['file'], array('label' => $v['caption'], 'label_default' => $v['caption'], 'position' => $v['pos']));
                $this->addSuccess("Added image: ".$v['caption'].' to sku '.$sku,$sku);
                try {
                    unlink('pub/media/bhimp/' . $v['name']);
                } catch (\Exception $e) {}
            }
            $p->save();
            $this->addSuccess("SKU saved: ".$sku,$sku);
        } catch (\Exception $e) {
        $success = false;
        $this->addError($e->getMessage().": SKU: ".$sku, $sku);
    }
        }
    }

    public function accessories($brand, $f, $clear)
    {
        $file = fopen('shell/import/csv/'.$f, 'r');
        $c = 0;
        $productData = array();

        while (($row = fgetcsv($file, 4096)) !== false) {
            if ($c == 0) {
                $c++;
                continue;
            }
            $rowBrand = $row[0];
            $rowBrand = strtolower($rowBrand);
            if (strtolower($brand) == $rowBrand || $brand == "All") {
                if(!isset($productData[$row[2]]))
                {
                    $productData[$row[2]] = array();
                    $productIndexes[$row[2]] = 1;
                }
                $productData[$row[2]][$productIndexes[$row[2]]] = array(
                    "sku" => $row[5],
                    "required" => $row[3]
                );
                $productIndexes[$row[2]]++;
            }
        }
        fclose($file);
        foreach ($productData as $sku => $value) {
            try {
                if (isset($this->links[$sku])) {
                    $sku = $this->links[$sku];
                }
                $p = $this->pr->get($sku);
                $p->setData("accessories", json_encode($value));
                $p->getResource()->saveAttribute($p, 'accessories');
                $p->save();
                $this->addSuccess("SKU " . $sku . " saved.", $sku);
            } catch (\Exception $e) {
                $success = false;
                $this->addError($e->getMessage().": SKU: ".$sku, $sku);
            }
        }
        $this->addDebug("Import is finished.", "None");
    }

    public function metaata($brand, $f, $clear)
    {
        $file = fopen('shell/import/csv/'.$f, 'r');
        $c = 0;
        $productData = array();

        while (($row = fgetcsv($file, 4096)) !== false) {
            if ($c == 0) {
                $c++;
                continue;
            }
            $rowBrand = $row[0];
            $rowBrand = strtolower($rowBrand);
            if (strtolower($brand) == $rowBrand || $brand == "All") {
                if(!isset($productData[$row[2]]))
                {
                    $productData[$row[2]] = array(
                        "keywords" => array(),
                        "desc" => array(),
                    );
                    $productIndexes[$row[2]] = 1;
                }
                $productData[$row[2]]['keywords'][] = $row[3];
                $productData[$row[2]]['desc'][] = $row[4];
            }
        }
        fclose($file);
        foreach ($productData as $sku => $metaData) {
            try {
                if (isset($this->links[$sku])) {
                    $sku = $this->links[$sku];
                }
                $p = $this->pr->get($sku);
                $metaKeywords = implode(" ", $metaData['keywords']);
                $metaDescription = implode(" ", $metaData['desc']);
                $p->setData("meta_keyword", $metaKeywords);
                $p->setData("meta_description", $metaDescription);
                $p->getResource()->saveAttribute($p, 'meta_keyword');
                $p->getResource()->saveAttribute($p, 'meta_description');
                $p->save();
                $this->addSuccess("SKU " . $sku . " saved.", $sku);
            } catch (\Exception $e) {
                $success = false;
                $this->addError($e->getMessage().": SKU: ".$sku, $sku);
            }
        }
        $this->addDebug("Import is finished.", "None");
    }

    public function makeLinks($file)
    {
        $c = 0;
        $file2 = fopen($file, 'r');
        $links = array();
        while (($row = fgetcsv($file2, 4096)) !== false) {
            if ($c == 0) {
                $c++;
                continue;
            }
            $links[$row[3]] = $row[2];
        }
        $this->links = $links;
    }

    public function processFile($file, $brand, $type, $c=0)
    {
        while (($row = fgetcsv($file, 4096)) !== false) {
            if ($c == 0) {
                $c++;
                continue;
            }
            $rowBrand = $row[0];
            $rowBrand = strtolower($rowBrand);
            if (strtolower($brand) == $rowBrand || $brand == "All") {
                if(!isset($productData[$row[2]]))
                {
                    $productData[$row[2]] = array();
                }
                $productData[$row[2]][$row[3]] = $row[4];
            }
        }
        fclose($file);
        $this->addDebug("Starting ".$type." import with ".count($productData)." products and ".count($row)." rows.", 'None');
        return $productData;
    }

    public function addSuccess($m, $sku)
    {
        $this->mes[] = array(
            'time' => date('Y/m/d h:i:s'),
            'sku' => $sku,
            'mes' => $m,
            'type' => "success"
        );
    }
    public function addDebug($m, $sku)
    {
        $this->mes[] = array(
            'time' => date('Y/m/d h:i:s'),
            'sku' => $sku,
            'mes' => $m,
            'type' => "debug"
        );
    }
    public function addError($m, $sku)
    {
        $this->mes[] = array(
            'time' => date('Y/m/d h:i:s'),
            'sku' => $sku,
            'mes' => $m,
            'type' => "error"
        );
    }

}
