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
        if (!isset($meta['custom_options'])) {
            return $meta;
        }

        // Check the path exists
        $path = &$meta['custom_options'];
        if (isset($path['children']['options']['children']['record']['children']['container_option']['children']['values']['children']['record']['children'])) {
            $path['children']['options']['children']['record']['children']['container_option']['children']['values']['children']['record']['children']['image'] = $this->getImageFieldConfig(41);
            $path['children']['options']['children']['record']['children']['container_option']['children']['values']['children']['record']['children']['description'] = $this->getDescriptionFieldConfig(42);
        }

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
                        'fit' => true,
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
                        'fit' => true,
                    ],
                ],
            ],
        ];
    }
}
