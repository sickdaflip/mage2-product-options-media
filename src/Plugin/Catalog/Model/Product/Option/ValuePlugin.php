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
        // Ensure image and description are in the data array for saving
        if ($subject->hasData('image')) {
            $subject->setData('image', $subject->getData('image'));
        }

        if ($subject->hasData('description')) {
            $subject->setData('description', $subject->getData('description'));
        }
    }
}