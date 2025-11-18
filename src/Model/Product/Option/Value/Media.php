<?php
/*
 * Copyright (C) Philipp Breitsprecher, Inc - All Rights Reserved
 * @project Mage2 GD
 * @file Media.php
 * @author Philipp Breitsprecher
 * @date 18.11.25, 12:55
 * @email philippbreitsprecher@gmail.com
 */

declare(strict_types=1);

namespace Sickdaflip\ProductOptionsMedia\Model\Product\Option\Value;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Media path resolver for option value images
 *
 * Handles both legacy (M1.9) paths and new Media Gallery paths
 */
class Media
{
    private Filesystem $filesystem;
    private StoreManagerInterface $storeManager;

    public function __construct(
        Filesystem $filesystem,
        StoreManagerInterface $storeManager
    ) {
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
    }

    /**
     * Get full URL for option value image
     *
     * Supports:
     * - Legacy: catalog/customoption/image.jpg (M1.9 migration)
     * - New: catalog/product/option-images/image.jpg (Media Gallery)
     * - Relative paths
     *
     * @param string|null $imagePath
     * @return string|null
     */
    public function getImageUrl(?string $imagePath): ?string
    {
        if (!$imagePath) {
            return null;
        }

        // Remove leading slash if present
        $imagePath = ltrim($imagePath, '/');

        // Check if file exists in media directory
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

        if ($mediaDirectory->isFile($imagePath)) {
            return $this->storeManager->getStore()
                    ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $imagePath;
        }

        // Legacy path fallback (M1.9 compatibility)
        $legacyPath = 'catalog/customoption/' . basename($imagePath);
        if ($mediaDirectory->isFile($legacyPath)) {
            return $this->storeManager->getStore()
                    ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $legacyPath;
        }

        // Return null if file not found
        return null;
    }

    /**
     * Check if image file exists
     *
     * @param string|null $imagePath
     * @return bool
     */
    public function imageExists(?string $imagePath): bool
    {
        if (!$imagePath) {
            return false;
        }

        $imagePath = ltrim($imagePath, '/');
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

        // Check direct path
        if ($mediaDirectory->isFile($imagePath)) {
            return true;
        }

        // Check legacy path
        $legacyPath = 'catalog/customoption/' . basename($imagePath);
        return $mediaDirectory->isFile($legacyPath);
    }
}