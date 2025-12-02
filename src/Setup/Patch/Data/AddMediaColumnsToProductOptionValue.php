<?php
/*
 * Copyright (C) Philipp Breitsprecher, Inc - All Rights Reserved
 * @project Mage2 GD
 * @file AddMediaColumnsToProductOptionValue.php
 * @author Philipp Breitsprecher
 * @date 18.11.25, 12:36
 * @email philippbreitsprecher@gmail.com
 */

declare(strict_types=1);

namespace FlipDev\ProductOptionsMedia\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

/**
 * Add image and description columns to product option values
 *
 * This patch ensures backward compatibility with Magento 1.9 data
 * by using the same column names and types
 */
class AddMediaColumnsToProductOptionValue implements DataPatchInterface, PatchVersionInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Add columns if they don't exist (for existing installations)
     */
    public function apply(): self
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $connection = $this->moduleDataSetup->getConnection();
        $tableName = $this->moduleDataSetup->getTable('catalog_product_option_type_value');

        // Add image column if not exists
        if (!$connection->tableColumnExists($tableName, 'image')) {
            $connection->addColumn(
                $tableName,
                'image',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Option Value Image',
                ]
            );
        }

        // Add description column if not exists
        if (!$connection->tableColumnExists($tableName, 'description')) {
            $connection->addColumn(
                $tableName,
                'description',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Option Value Description',
                ]
            );
        }

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }

    /**
     * Get dependencies
     *
     * @return array<string>
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * Get aliases
     *
     * @return array<string>
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * Get patch version
     */
    public static function getVersion(): string
    {
        return '1.0.0';
    }
}