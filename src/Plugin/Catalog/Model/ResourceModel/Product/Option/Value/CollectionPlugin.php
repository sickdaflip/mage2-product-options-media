<?php
/*
 * Copyright (C) Philipp Breitsprecher, Inc - All Rights Reserved
 * @project Mage2 GD
 * @file CollectionPlugin.php
 * @author Philipp Breitsprecher
 * @date 20.11.25
 * @email philippbreitsprecher@gmail.com
 */

declare(strict_types=1);

namespace Sickdaflip\ProductOptionsMedia\Plugin\Catalog\Model\ResourceModel\Product\Option\Value;

use Magento\Catalog\Model\ResourceModel\Product\Option\Value\Collection;

/**
 * Plugin to ensure image and description columns are loaded in the option value collection
 */
class CollectionPlugin
{
    /**
     * Modify select to include custom columns before load
     *
     * @param Collection $subject
     * @param bool $printQuery
     * @param bool $logQuery
     * @return array
     */
    public function beforeLoad(Collection $subject, $printQuery = false, $logQuery = false): array
    {
        if (!$subject->isLoaded()) {
            // Get the select object and add our custom columns
            $select = $subject->getSelect();
            $select->columns(['image', 'description']);
        }
        return [$printQuery, $logQuery];
    }
}
