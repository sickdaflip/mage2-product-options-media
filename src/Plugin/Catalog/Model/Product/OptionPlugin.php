<?php
/*
 * Copyright (C) Philipp Breitsprecher, Inc - All Rights Reserved
 * @project Mage2 GD
 * @file OptionPlugin.php
 * @author Philipp Breitsprecher
 * @date 20.11.25
 * @email philippbreitsprecher@gmail.com
 */

declare(strict_types=1);

namespace Sickdaflip\ProductOptionsMedia\Plugin\Catalog\Model\Product;

use Magento\Catalog\Model\Product\Option;

/**
 * Plugin to ensure image and description fields are loaded in option values
 */
class OptionPlugin
{
    /**
     * After getting values, ensure image and description data is available
     *
     * @param Option $subject
     * @param array $result
     * @return array
     */
    public function afterGetValues(Option $subject, $result)
    {
        if (!$result) {
            return $result;
        }

        // Values are already loaded, but we need to ensure the custom fields are accessible
        // The data should already be in the value objects from the database
        foreach ($result as $value) {
            // Force load if not already loaded
            if (!$value->getData('image') && !$value->getData('description')) {
                // Data might not be loaded yet, trigger a load
                $value->load($value->getId());
            }
        }

        return $result;
    }
}
