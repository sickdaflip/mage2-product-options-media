<?php
/*
 * Copyright (C) Philipp Breitsprecher, Inc - All Rights Reserved
 * @project Mage2 GD
 * @file Uninstall.php
 * @author Philipp Breitsprecher
 * @date 18.11.25, 12:45
 * @email philippbreitsprecher@gmail.com
 */

declare(strict_types=1);

namespace Sickdaflip\ProductOptionsMedia\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

/**
 * Uninstall script for removing database columns
 *
 * Executed when module is uninstalled via:
 * bin/magento module:uninstall Sickdaflip_ProductOptionsMedia
 */
class Uninstall implements UninstallInterface
{
    /**
     * Remove media columns from option value table
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->startSetup();

        $connection = $setup->getConnection();
        $tableName = $setup->getTable('catalog_product_option_type_value');

        // Remove image column if exists
        if ($connection->tableColumnExists($tableName, 'image')) {
            $connection->dropColumn($tableName, 'image');
        }

        // Remove description column if exists
        if ($connection->tableColumnExists($tableName, 'description')) {
            $connection->dropColumn($tableName, 'description');
        }

        $setup->endSetup();
    }
}