<?php
/*
 * Copyright (C) Philipp Breitsprecher, Inc - All Rights Reserved
 * @project Mage2 GD
 * @file UploadImage.php
 * @author Philipp Breitsprecher
 * @date 18.11.25, 12:37
 * @email philippbreitsprecher@gmail.com
 */

declare(strict_types=1);

namespace Sickdaflip\ProductOptionsMedia\Controller\Adminhtml\Product\Option;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\UploaderFactory;

/**
 * Upload controller for custom option images
 */
class UploadImage extends Action
{
    public const ADMIN_RESOURCE = 'Magento_Catalog::products';

    private JsonFactory $resultJsonFactory;
    private Filesystem $filesystem;
    private UploaderFactory $uploaderFactory;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->filesystem = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
    }

    /**
     * Upload image for custom option
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        try {
            $uploader = $this->uploaderFactory->create(['fileId' => 'image']);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'svg', 'webp']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);

            $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
            $path = 'catalog/customoption';

            $uploadResult = $uploader->save($mediaDirectory->getAbsolutePath($path));

            if (!$uploadResult) {
                throw new \Exception('File upload failed.');
            }

            return $result->setData([
                'success' => true,
                'file' => $uploadResult['file'],
                'url' => $this->_url->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA])
                    . $path . '/' . $uploadResult['file']
            ]);

        } catch (\Exception $e) {
            return $result->setData([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}