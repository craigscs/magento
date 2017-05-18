<?php
namespace Gradus\Importer\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Eav\Model\Entity\Attribute\Option;
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
    protected $eavAttribute;
    protected $pf;

    protected $cont;
    protected $reg;
    protected $optionLabelFactory;
    protected $optionFactory;
    protected $attributeOptionManagement;
    protected $func;
    protected $prodlink;

    /**
     * @param Action\Context $context
     */
    public function __construct(Action\Context $context,
                                Filesystem $fileSystem,
                                UploaderFactory $uploaderFactory,
                                \Magento\Catalog\Model\ProductRepository $pr,
                                \Magento\Catalog\Model\Product $p,
                                \Magento\Catalog\Model\Product\Gallery\Processor $imgp,
                                \Magento\Eav\Model\Entity\Attribute $entityAttribute,
                                \Magento\Catalog\Model\ProductFactory $pf,
                                \Magento\Framework\Model\Context $cont,
                                \Magento\Framework\Registry $registry,
                                \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory $optionLabelFactory,
                                \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionFactory,
                                \Magento\Eav\Api\AttributeOptionManagementInterface $attributeOptionManagement,
                                \Magento\Catalog\Api\Data\ProductLinkInterface $prodlink

    )
    {
        $this->prodlink = $prodlink;
        $this->reg = $registry;
        $this->cont = $cont;
        $this->imgProcessor = $imgp;
        $this->prod = $p;
        $this->pr = $pr;
        $this->fileSystem = $fileSystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->eavAttribute = $entityAttribute;
        $this->pf = $pf;
        $this->optionLabelFactory = $optionLabelFactory;
        $this->optionFactory = $optionFactory;
        $this->attributeOptionManagement = $attributeOptionManagement;
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
            $this->makeLinks('shell/import/csv/links.csv');
            $destinationPath = "shell/import/csv/";
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
                $this->func = $data['process'];
                $res = $this->$func($data['brand'], $_FILES['upl']['name'], $data['clear']);
                $model->setData($data);
                $model->setData('messages', json_encode($this->mes));
                $model->setData('process', ucwords($func));
                $model->setData('succeeded', $this->success);
                $model->setData('brand', $data['brand']);
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

    public function skulinks($brand, $f, $clear)
    {
        try {
            $file = file_get_contents('shell/import/csv/' . $f);
            file_put_contents('shell/import/csv/links.csv', $file);
        } catch (\Exception $e) {
            $this->addError($e->getMessage(), "N/A");
        }
        $this->addSuccess("Links have been updated", "N/A");
    }

    public function attr($brand, $f, $clear)
    {
        $success = true;
        $handled = array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // instance of object manager
        $message = '';
        $file = fopen('shell/import/csv/'.$f, 'r');
        $c = 0;
        $productData = array();
        $headers = array();
        while (($row = fgetcsv($file, 4096)) !== false) {
            if ($c == 0) {
                $headers = $row;
                $c++;
                continue;
            }
            if (in_array("Multi", $row) && in_array("Text", $row) || in_array("Drop Down", $row)) {
                continue;
            }
            $hc = 0;
            foreach ($headers as $h) {
                $pd[$h] = $row[$hc];
                $hc++;
            }

            $headers = array_unique($headers);
            $headers = array_filter($headers);

            $hc = 0;
            foreach ($headers as $h) {
                $pd[$h] = $row[$hc];
                $hc++;
            }
            $productData[] = $pd;
        }

        $options = array();
        foreach ($productData as $data) {
            foreach ($data as $k => $d) {
                $options[$k] = array();
            }
            break;
        }

        foreach ($productData as $data) {
            foreach ($data as $k => $d) {
                $expanded = explode(",", $d);
                foreach ($expanded as $qq) {
                    array_push($options[$k], $qq);
                }
            }
        }

        foreach ($options as $k => $op) {
            $options[$k] = array_unique($options[$k]);
            $options[$k] = array_filter($options[$k]);
        }

        foreach ($options as $key => $op) {
            $ea = $this->eavAttribute->loadByCode('catalog_product', $key);
            $ea_type = $ea->getData('frontend_input');
            if ($ea_type == "select" || $ea_type == "multiselect") {
                foreach ($op as $o) {
                    $optionText = $ea->getSource()->getOptionId($o);
                    if (!$optionText) {
                        $optionLabel = $this->optionLabelFactory->create();
                        $optionLabel->setStoreId(0);
                        $optionLabel->setLabel($o);
                        $option = $this->optionFactory->create();
                        $option->setLabel($optionLabel);
                        $option->setStoreLabels([$optionLabel]);
                        $option->setSortOrder(0);
                        $option->setIsDefault(false);
                        $this->attributeOptionManagement->add(
                            \Magento\Catalog\Model\Product::ENTITY,
                            $ea->getId(),
                            $option
                        );
                        $this->addSuccess("Saved option: ".$o, "N/A");
                    } else {
                        $this->addSuccess("Option : ".$o." already exists", "N/A");
                    }
                }
            }
        }
    }

    public function parent($brand, $f, $clear)
    {
        $success = true;
        $handled = array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // instance of object manager
        $message = '';
        $file = fopen('shell/import/csv/' . $f, 'r');
        $c = 0;
        $productData = array();
        $headers = array();
        while (($row = fgetcsv($file, 4096)) !== false) {
            if ($c == 0) {
                $headers = $row;
                $c++;
                continue;
            }
            if (in_array("Multi", $row) && in_array("Text", $row) || in_array("Drop Down", $row)) {
                continue;
            }
            $hc = 0;
            foreach ($headers as $h) {
                $pd[$h] = $row[$hc];
                $hc++;
            }
            $productData[] = $pd;
        }
        foreach ($productData as $key => $da) {
            $pname = $da['name'];
            $product = $this->pf->create()->loadByAttribute('name', $pname);
            if (!$product) {
                $product = $this->pf->create();
                //Set inital product information
                $product->setData('name', $pname);
                $product->setSku($pname . "-" . $da['model'] . "-" . rand(1, 10000));
                $product->setTypeId('simple');
                $product->setAttributeSetId(4);
                $product->setPrice(0);
                $product->setVisibility(1);
                $product->setStatus(2);
                $product->setWeight(0);
                $product->setData('gradus_type', 6);
                $product->setData('manufactered_date', $da['manufactered_date']);
                $product->save();
            } else {
                $product->setData('name', $pname);
            }
            foreach ($da as $k => $d) {
                $product->setData($k, $d);
                $product->getResource()->saveAttribute($product, $k);
                $this->addSuccess("Product Name: " . $pname . " saved attribute: " . $k, $pname);
            }
            $product->save();
            $this->addSuccess("Sku was created and saved: ", $pname);
        }
    }

    public function inthebox($brand, $f, $clear)
    {
        $success = true;
        $message = '';
        $rowcount = 0;
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
                $rowcount++;
            }
        }
        fclose($file);
        $this->addDebug("Starting features import with ".count($productData)." products and ".$rowcount." rows.", 'None');
        foreach ($productData as $sku => $value) {
            try {
                if (isset($this->links[$sku])) {
                    $sku = $this->links[$sku];
                }
                if ($clear == "delete" || $clear == "replace") {
                    $p = $this->pr->get($sku);
                    $p->setData('in_the_box', '');
                    $p->getResource()->saveAttribute($p, 'in_the_box');
                    $this->addSuccess("Cleared includes",$sku);
                    if ($clear == "delete") {
                        continue;
                    }
                }

                $counter = 0;
                $s = '<div onclick="jQuery(\'#row_'.$sku.'\').toggle()">Details</div><div id="row_'.$sku.'" style="display:none">';
                foreach ($value as $v) {
                    $s .= "[".$counter."] ".$v['value'].": ".$v['count']."<br>";
                    $counter++;
                }
                $s .= "</div>";
                $p = $this->pr->get($sku);
                $p->setData("in_the_box", json_encode($value));
                $p->getResource()->saveAttribute($p, 'in_the_box');
                $this->addSuccess("Added include(s)".$s, $sku);
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
        $rowcount = 0;
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
            $rowcount++;
        }
        fclose($file);
        $this->addDebug("Starting highlights import with ".count($productData)." products and ".$rowcount." rows.", 'None');
        foreach ($productData as $sku => $value) {
            try {
                if (isset($this->links[$sku])) {
                    $sku = $this->links[$sku];
                }
                if ($clear == "delete" || $clear == "replace") {
                    $p = $this->pr->get($sku);
                    $p->setData('highlights', '');
                    $p->getResource()->saveAttribute($p, 'highlights');
                    $this->addSuccess("Cleared highlights",$sku);
                    if ($clear == "delete") {
                        continue;
                    }
                }

                $s = '<div onclick="jQuery(\'#row_'.$sku.'\').toggle()">Details</div><div id="row_'.$sku.'" style="display:none">';
                $counter = 0;
                foreach ($value as $v) {
                    $s .= "[".$counter."] ".$v."<br>";
                    $counter++;
                }
                $s .= "</div>";
                $p = $this->pr->get($sku);
                $p->setData("highlights", json_encode($value));
                $p->getResource()->saveAttribute($p, 'highlights');
                $this->addSuccess("Added highlight(s) ".$s, $sku);
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

                if ($clear == "delete" || $clear == "replace") {
                    $p = $this->pr->get($sku);
                    $p->setData('description', '');
                    $p->getResource()->saveAttribute($p, 'description');
                    $p->setData('overview_note', '');
                    $p->getResource()->saveAttribute($p, 'overview_note');
                    $this->addSuccess("Cleared description and overview note",$sku);
                    if ($clear == "delete") {
                        continue;
                    }
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
        $success = true;
        $message = '';
        $rowcount = 0;
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
                $productData[$row[2]][] = array(
                    "name" => $row[4],
                    "desc" => $row[5]
                );
                $rowcount++;
            }
        }
        fclose($file);
        $this->addDebug("Starting features import with ".count($productData)." products and ".$rowcount." rows.", 'None');
        foreach ($productData as $sku => $value) {
            try {
                if (isset($this->links[$sku])) {
                    $sku = $this->links[$sku];
                }

                if ($clear == "delete" || $clear == "replace") {
                    $p = $this->pr->get($sku);
                    $p->setData('features', '');
                    $p->getResource()->saveAttribute($p, 'features');
                    $this->addSuccess("Cleared features",$sku);
                    if ($clear == "delete") {
                        continue;
                    }
                }

                $s = '<div onclick="jQuery(\'#row_'.$sku.'\').toggle()">Details</div><div id="row_'.$sku.'" style="display:none">';
                $counter = 0;
                foreach ($value as $v) {
                    $s .= "[".$counter."] ".$v['name'].": ".$v['desc']."<br>";
                    $counter++;
                }
                $s .= "</div>";
                $p = $this->pr->get($sku);
                $p->setData("features", json_encode($value));
                $p->getResource()->saveAttribute($p, 'features');
                $this->addSuccess("Added feature(s) ".$s, $sku);
            } catch (\Exception $e) {
                $success = false;
                $this->addError($e->getMessage(), $sku);
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
            if ($clear == 'delete' || $clear == "replace") {
                $p->setMediaGalleryEntries(array());
                $this->pr->save($p);
                $this->addSuccess("Cleared images", $sku);
                if ($clear == "delete") { continue; }
            }
                $pgallery = $p->getMediaGallery();
                $galnames = array();
                foreach ($pgallery['images'] as $gal) {
                    $galnames[] = explode("/", $gal['file'])[3];
                }
                $value_count = 0;
                $s = '<div onclick="jQuery(\'#row_'.$sku.'\').toggle()">Details</div><div id="row_'.$sku.'" style="display:none">';
                $counter = 0;
                foreach ($value as $v) {
                    if (in_array($v['name'], $galnames)) {
                        $s .= "[".$counter."] We found the image ".$v['name']." already, skipping.";
                        $this->addDebug("We found the image ".$v['name']." already, skipping.", $sku);
                        continue;
                    }
                $url = $v['file'];
                $img = 'pub/media/bhimp/' . $v['name'];
                file_put_contents($img, file_get_contents($url));
                    if ($value_count == 0) {
                        $p->addImageToMediaGallery('/bhimp/' . $v['name'], array('image', 'small_image', 'thumbnail'), false, false);
                        $value_count++;
                    } else {
                        $p->addImageToMediaGallery('/bhimp/' . $v['name'], array(), false, false);
                    }
                    $s .= "[".$counter."] ".$v['name']."<br>";
                    $gallery = $p->getData('media_gallery');
                    $lastimg = array_pop($gallery['images']);
                    $this->imgProcessor->updateImage($p, $lastimg['file'], array('label' => $v['caption'], 'label_default' => $v['caption'], 'position' => $v['pos']));
                try {
                    unlink('pub/media/bhimp/' . $v['name']);
                } catch (\Exception $e) {}
                    $counter++;
            }
            $p->save();
            $this->addSuccess("Added images".$s,$sku);
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
        $rowcount = 0;
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
                $rowcount++;
            }
        }
        fclose($file);
        $this->addDebug("Starting accessories import with ".count($productData)." products and ".$rowcount." rows.", 'None');
        foreach ($productData as $sku => $value) {
            try {
                if (isset($this->links[$sku])) {
                    $sku = $this->links[$sku];
                }
                $s = '<div onclick="jQuery(\'#row_'.$sku.'\').toggle()">Details</div><div id="row_'.$sku.'" style="display:none">';
                $counter = 0;
                foreach ($value as $v) {
                    $lsku = "Unknown";
                    if (isset($this->links[$v['sku']])) {
                        $lsku = $this->links[$v['sku']];
                    }
                    $s .= "[".$counter."] ".$lsku."<br>";
                    $counter++;
                }
                $s .= "</div>";

                $p = $this->pr->get($sku);
                $linkDataAll = $p->getProductLinks();
                foreach ($value as $v) {
                    $lsku = "";
                    if (isset($this->links[$v['sku']])) {
                        $lsku = $this->links[$v['sku']];
                    }
                    $plink = clone $this->prodlink;
                    $linkData = $plink
                        ->setSku($p->getSku())
                        ->setLinkedProductSku($lsku)
                        ->setLinkType('accessory');
                    if (!$this->doesLinkExist($linkDataAll, $sku, $lsku, 'accessory')) {
                        $linkDataAll[] = $linkData;
                    }
                }
                $p->setProductLinks($linkDataAll);
                $p->save();
                $this->addSuccess("Added accessories ".$s, $sku);
            } catch (\Exception $e) {
                $success = false;
                $this->addError($e->getMessage().": SKU: ".$sku, $sku);
            }
        }
        $this->addDebug("Import is finished.", "None");
    }

    public function doesLinkExist($links, $sku, $linksku, $type)
    {
        foreach ($links as $l) {
            if ($l->getSku() == $sku &&
            $l->getLinkedProductSku() == $linksku &&
            $l->getLinkType() == $type) {
                return true;
            }
        }
        return false;
    }


    public function metadata($brand, $f, $clear)
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

                if ($clear == "delete" || $clear == "replace") {
                    $p = $this->pr->get($sku);
                    $p->setData('meta_keyword', '');
                    $p->getResource()->saveAttribute($p, 'meta_keyword');
                    $p->setData('meta_description', '');
                    $p->getResource()->saveAttribute($p, 'meta_description');
                    $this->addSuccess("Cleared metaData",$sku);
                    if ($clear == "delete") {
                        continue;
                    }
                }

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
            if (count($row[0]) == null) {
                continue;
            }
            $links[$row[3]] = $row[2];
        }
        $this->links = $links;
    }

    public function processFile($file, $brand, $type, $c=0)
    {
        $rowcount = 0;
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
                $rowcount++;
            }
        }
        fclose($file);
        $this->addDebug("Starting ".$type." import with ".count($productData)." products and ".$rowcount." rows.", 'None');
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
