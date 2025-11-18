<?php
/*
 * Copyright (C) Philipp Breitsprecher, Inc - All Rights Reserved
 * @project Mage2 GD
 * @file CustomOptions.php
 * @author Philipp Breitsprecher
 * @date 18.11.25
 * @email philippbreitsprecher@gmail.com
 */

declare(strict_types=1);

namespace Sickdaflip\ProductOptionsMedia\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Textarea;
use Magento\Ui\Component\Form\Field;

class CustomOptions extends AbstractModifier
{
    const GROUP_CUSTOM_OPTIONS_NAME = 'custom_options';
    const GRID_OPTIONS_NAME = 'options';
    const CONTAINER_OPTION = 'container_option';
    const GRID_TYPE_SELECT_NAME = 'values';

    /**
     * @inheritdoc
     */
    public function modifyData(array $data): array
    {
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta): array
    {
        // Full path to the option values record children
        $path = self::GROUP_CUSTOM_OPTIONS_NAME
            . '/children/'
            . self::GRID_OPTIONS_NAME
            . '/children/record/children/'
            . self::CONTAINER_OPTION
            . '/children/'
            . self::GRID_TYPE_SELECT_NAME
            . '/children/record/children';

        if (isset($meta[self::GROUP_CUSTOM_OPTIONS_NAME])) {
            $meta = $this->addFieldsToPath($meta, $path);
        }

        return $meta;
    }

    /**
     * Add image and description fields to specified path
     *
     * @param array $meta
     * @param string $path
     * @return array
     */
    private function addFieldsToPath(array $meta, string $path): array
    {
        $pathParts = explode('/', $path);
        $current = &$meta;

        foreach ($pathParts as $part) {
            if (!isset($current[$part])) {
                $current[$part] = [];
            }
            $current = &$current[$part];
        }

        $current['image'] = $this->getImageFieldConfig(45);
        $current['description'] = $this->getDescriptionFieldConfig(46);

        return $meta;
    }

    /**
     * Get image field configuration
     *
     * @param int $sortOrder
     * @return array
     */
    private function getImageFieldConfig(int $sortOrder): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Image'),
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'dataScope' => 'image',
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                        'validation' => [
                            'required-entry' => false
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get description field configuration
     *
     * @param int $sortOrder
     * @return array
     */
    private function getDescriptionFieldConfig(int $sortOrder): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Description'),
                        'componentType' => Field::NAME,
                        'formElement' => Textarea::NAME,
                        'dataScope' => 'description',
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                        'rows' => 2,
                        'validation' => [
                            'required-entry' => false
                        ],
                    ],
                ],
            ],
        ];
    }
}
