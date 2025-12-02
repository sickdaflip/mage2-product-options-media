<?php
/*
 * Copyright (C) Philipp Breitsprecher, Inc - All Rights Reserved
 * @project Mage2 GD
 * @file Config.php
 * @author Philipp Breitsprecher
 * @date 01.12.25
 * @email philippbreitsprecher@gmail.com
 */

declare(strict_types=1);

namespace FlipDev\ProductOptionsMedia\Helper;

use FlipDev\Core\Helper\Config as CoreConfig;

/**
 * Configuration helper for Product Options Media module
 */
class Config
{
    private const XML_PATH_ENABLED = 'product_options_media/general/enabled';
    private const XML_PATH_MAX_VISIBLE = 'product_options_media/dropdown/max_visible_options';
    private const XML_PATH_MAX_HEIGHT = 'product_options_media/dropdown/max_height';
    private const XML_PATH_ENABLE_SEARCH = 'product_options_media/dropdown/enable_search';
    private const XML_PATH_MAX_TAG_LENGTH = 'product_options_media/tags/max_tag_length';
    private const XML_PATH_SHOW_IMAGES_DROPDOWN = 'product_options_media/display/show_images_in_dropdown';
    private const XML_PATH_SHOW_IMAGES_TAGS = 'product_options_media/display/show_images_in_tags';
    private const XML_PATH_SHOW_PRICES = 'product_options_media/display/show_prices_in_dropdown';
    private const XML_PATH_SHOW_DESCRIPTIONS = 'product_options_media/display/show_descriptions';
    private const XML_PATH_ENABLE_DARK_MODE = 'product_options_media/display/enable_dark_mode';

    private CoreConfig $coreConfig;

    public function __construct(CoreConfig $coreConfig)
    {
        $this->coreConfig = $coreConfig;
    }

    /**
     * Check if module is enabled (depends on Core being enabled)
     */
    public function isEnabled(?int $storeId = null): bool
    {
        // First check if FlipDev Core is enabled
        if (!$this->coreConfig->isEnabled($storeId)) {
            return false;
        }

        // Then check this module's config
        return $this->coreConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            $storeId
        );
    }

    /**
     * Get max visible options before "Show more"
     */
    public function getMaxVisibleOptions(?int $storeId = null): int
    {
        return (int) $this->coreConfig->getValue(
            self::XML_PATH_MAX_VISIBLE,
            $storeId
        ) ?: 4;
    }

    /**
     * Get dropdown max height in pixels
     */
    public function getMaxHeight(?int $storeId = null): int
    {
        return (int) $this->coreConfig->getValue(
            self::XML_PATH_MAX_HEIGHT,
            $storeId
        ) ?: 288;
    }

    /**
     * Check if search is enabled
     */
    public function isSearchEnabled(?int $storeId = null): bool
    {
        return $this->coreConfig->isSetFlag(
            self::XML_PATH_ENABLE_SEARCH,
            $storeId
        );
    }

    /**
     * Get max tag length for truncation
     */
    public function getMaxTagLength(?int $storeId = null): int
    {
        return (int) $this->coreConfig->getValue(
            self::XML_PATH_MAX_TAG_LENGTH,
            $storeId
        ) ?: 25;
    }

    /**
     * Check if images should be shown in dropdown
     */
    public function showImagesInDropdown(?int $storeId = null): bool
    {
        return $this->coreConfig->isSetFlag(
            self::XML_PATH_SHOW_IMAGES_DROPDOWN,
            $storeId
        );
    }

    /**
     * Check if images should be shown in tags
     */
    public function showImagesInTags(?int $storeId = null): bool
    {
        return $this->coreConfig->isSetFlag(
            self::XML_PATH_SHOW_IMAGES_TAGS,
            $storeId
        );
    }

    /**
     * Check if prices should be shown in dropdown
     */
    public function showPricesInDropdown(?int $storeId = null): bool
    {
        return $this->coreConfig->isSetFlag(
            self::XML_PATH_SHOW_PRICES,
            $storeId
        );
    }

    /**
     * Check if descriptions should be shown
     */
    public function showDescriptions(?int $storeId = null): bool
    {
        return $this->coreConfig->isSetFlag(
            self::XML_PATH_SHOW_DESCRIPTIONS,
            $storeId
        );
    }

    /**
     * Check if dark mode is enabled
     */
    public function isDarkModeEnabled(?int $storeId = null): bool
    {
        return $this->coreConfig->isSetFlag(
            self::XML_PATH_ENABLE_DARK_MODE,
            $storeId
        );
    }

    /**
     * Check if debug mode is enabled (from Core)
     */
    public function isDebugMode(?int $storeId = null): bool
    {
        return $this->coreConfig->isDebugMode($storeId);
    }

    /**
     * Get all config values as array (for JavaScript)
     */
    public function getConfigArray(?int $storeId = null): array
    {
        return [
            'enabled' => $this->isEnabled($storeId),
            'maxVisible' => $this->getMaxVisibleOptions($storeId),
            'maxHeight' => $this->getMaxHeight($storeId),
            'searchEnabled' => $this->isSearchEnabled($storeId),
            'maxTagLength' => $this->getMaxTagLength($storeId),
            'showImagesDropdown' => $this->showImagesInDropdown($storeId),
            'showImagesTags' => $this->showImagesInTags($storeId),
            'showPrices' => $this->showPricesInDropdown($storeId),
            'showDescriptions' => $this->showDescriptions($storeId),
            'darkMode' => $this->isDarkModeEnabled($storeId),
        ];
    }
}
