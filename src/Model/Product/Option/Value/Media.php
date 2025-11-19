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

        // Convert full URL to relative path
        $imagePath = $this->toRelativePath($imagePath);

        // Remove leading slash if present
        $imagePath = ltrim($imagePath, '/');

        // Check if file exists in media directory
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

        // If it's just a filename (no directory separator), check legacy path first
        if (strpos($imagePath, '/') === false) {
            $legacyPath = 'catalog/customoption/' . $imagePath;
            if ($mediaDirectory->isFile($legacyPath)) {
                return $this->storeManager->getStore()
                        ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $legacyPath;
            }
        }

        // Check direct path
        if ($mediaDirectory->isFile($imagePath)) {
            return $this->storeManager->getStore()
                    ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $imagePath;
        }

        // Legacy path fallback with basename (M1.9 compatibility)
        $legacyPath = 'catalog/customoption/' . basename($imagePath);
        if ($mediaDirectory->isFile($legacyPath)) {
            return $this->storeManager->getStore()
                    ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $legacyPath;
        }

        // Return null if file not found
        return null;
    }

    /**
     * Convert full URL or various formats to relative path
     *
     * @param string $path
     * @return string
     */
    private function toRelativePath(string $path): string
    {
        // Handle full URLs (https://domain.com/media/...)
        if (preg_match('#https?://[^/]+/media/(.+)#', $path, $matches)) {
            return $matches[1];
        }

        // Handle Magento directive format {{media url="..."}}
        if (preg_match('/\{\{media url="([^"]+)"\}\}/', $path, $matches)) {
            return $matches[1];
        }

        // Remove leading /media/ or media/
        $path = preg_replace('#^/?media/#', '', $path);

        return $path;
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