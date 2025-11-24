<?php
/*
 * Copyright (C) Philipp Breitsprecher, Inc - All Rights Reserved
 * @project Mage2 GD
 * @file ValuePlugin.php
 * @author Philipp Breitsprecher
 * @date 18.11.25, 12:36
 * @email philippbreitsprecher@gmail.com
 */

declare(strict_types=1);

namespace Sickdaflip\ProductOptionsMedia\Plugin\Catalog\Model\Product\Option;

use Magento\Catalog\Model\Product\Option\Value;

/**
 * Plugin to add image and description support to option values
 */
class ValuePlugin
{
    /**
     * Add image and description to value data after load
     *
     * @param Value $subject
     * @param Value $result
     * @return Value
     */
    public function afterLoad(Value $subject, Value $result): Value
    {
        // Data is already loaded from DB via parent::_afterLoad()
        // Just ensure getters work correctly
        return $result;
    }

    /**
     * Save image and description before save
     *
     * @param Value $subject
     * @return void
     */
    public function beforeSave(Value $subject): void
    {
        // Convert image path to relative path before saving
        if ($subject->hasData('image')) {
            $imagePath = $subject->getData('image');
            if ($imagePath) {
                $imagePath = $this->toRelativePath($imagePath);
            }
            $subject->setData('image', $imagePath);
        }

        if ($subject->hasData('description')) {
            $subject->setData('description', $subject->getData('description'));
        }
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
}