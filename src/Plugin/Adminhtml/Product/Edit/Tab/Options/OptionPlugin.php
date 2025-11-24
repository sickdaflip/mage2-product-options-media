<?php
/*
 * Copyright (C) Philipp Breitsprecher, Inc - All Rights Reserved
 * @project Mage2 GD
 * @file OptionPlugin.php
 * @author Philipp Breitsprecher
 * @date 18.11.25, 15:37
 * @email philippbreitsprecher@gmail.com
 */

declare(strict_types=1);

namespace Sickdaflip\ProductOptionsMedia\Plugin\Adminhtml\Product\Edit\Tab\Options;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Option;

/**
 * Plugin to modify admin options template
 */
class OptionPlugin
{
    /**
     * Add media fields to option values
     *
     * @param Option $subject
     * @param array $result
     * @return array
     */
    public function afterGetOptionValues(Option $subject, array $result): array
    {
        foreach ($result as $option) {
            if ($option->getData('optionValues')) {
                foreach ($option->getData('optionValues') as &$value) {
                    // Ensure image and description keys exist
                    if (!isset($value['image'])) {
                        $value['image'] = '';
                    }
                    if (!isset($value['description'])) {
                        $value['description'] = '';
                    }
                }
            }
        }

        return $result;
    }
}