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

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions as MagentoCustomOptions;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Model\StoreManagerInterface;
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
     * @var LocatorInterface
     */
    private LocatorInterface $locator;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param ArrayManager $arrayManager
     * @param LocatorInterface $locator
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ArrayManager $arrayManager,
        LocatorInterface $locator,
        StoreManagerInterface $storeManager
    ) {
        $this->arrayManager = $arrayManager;
        $this->locator = $locator;
        $this->storeManager = $storeManager;
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

        $this->addImageField();
        $this->addDescriptionField();

        return $this->meta;
    }

    /**
     * Add image field to option values
     *
     * @return void
     */
    private function addImageField(): void
    {
        $sortOrder = 45; // After price field

        $imagePath = $this->arrayManager->findPath(
            MagentoCustomOptions::CONTAINER_OPTION,
            $this->meta,
            null,
            'children'
        );

        if ($imagePath) {
            $imageContainer = $imagePath . '/' . MagentoCustomOptions::GRID_TYPE_SELECT_NAME
                . '/children/record/children/';

            $this->meta = $this->arrayManager->merge(
                $imageContainer,
                $this->meta,
                [
                    'image' => $this->getImageFieldConfig($sortOrder)
                ]
            );
        }
    }

    /**
     * Add description field to option values
     *
     * @return void
     */
    private function addDescriptionField(): void
    {
        $sortOrder = 46; // After image field

        $descriptionPath = $this->arrayManager->findPath(
            MagentoCustomOptions::CONTAINER_OPTION,
            $this->meta,
            null,
            'children'
        );

        if ($descriptionPath) {
            $descriptionContainer = $descriptionPath . '/' . MagentoCustomOptions::GRID_TYPE_SELECT_NAME
                . '/children/record/children/';

            $this->meta = $this->arrayManager->merge(
                $descriptionContainer,
                $this->meta,
                [
                    'description' => $this->getDescriptionFieldConfig($sortOrder)
                ]
            );
        }
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
                        'rows' => 3,
                        'validation' => [
                            'required-entry' => false
                        ],
                    ],
                ],
            ],
        ];
    }
}
