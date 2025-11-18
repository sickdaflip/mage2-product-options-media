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
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions as MagentoCustomOptions;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Textarea;
use Magento\Ui\Component\Form\Field;

class CustomOptions extends AbstractModifier
{
    /**
     * @var ArrayManager
     */
    private ArrayManager $arrayManager;

    /**
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

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
        $this->meta = $meta;

        // Path to option values grid (select/radio/checkbox options)
        $valuePath = $this->arrayManager->findPath(
            MagentoCustomOptions::GRID_TYPE_SELECT_NAME,
            $this->meta,
            null,
            'children'
        );

        if ($valuePath) {
            // Add fields to the record children
            $recordPath = $valuePath . '/children/record/children';

            $this->meta = $this->arrayManager->merge(
                $recordPath,
                $this->meta,
                [
                    'image' => $this->getImageFieldConfig(45),
                    'description' => $this->getDescriptionFieldConfig(46)
                ]
            );
        }

        return $this->meta;
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
