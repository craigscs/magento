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

    /**
     * @param Action\Context $context
     */
    public function __construct(Action\Context $context,
                                Filesystem $fileSystem,
                                UploaderFactory $uploaderFactory)
    {
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
            $destinationPath = "shell/import";
            try {
                $uploader = $this->uploaderFactory->create(array('fileId' => $this->fileId))
                    ->setAllowCreateFolders(true)
                    ->setAllowedExtensions($this->allowedExtensions);
                if (!$uploader->save($destinationPath)) {
                    throw new LocalizedException(
                        __('File cannot be saved to path: $1', $destinationPath)
                    );
                }
                $out = array();
                chdir("shell/import");
                exec("features.php", $out);
                $this->messageManager->addSuccessMessage(json_encode($out));
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __($e->getMessage())
                );
                return $resultRedirect->setPath('*/*/edit', ['import_id' => $this->getRequest()->getParam('import_id')]);
            }
            return $resultRedirect->setPath('*/*/');
        }
    }
}
