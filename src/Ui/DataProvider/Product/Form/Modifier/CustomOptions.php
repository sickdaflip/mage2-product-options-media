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
use Psr\Log\LoggerInterface;

class CustomOptions extends AbstractModifier
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
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
        $this->logger->info('ProductOptionsMedia: modifyMeta called');
        $this->logger->info('ProductOptionsMedia: meta keys = ' . implode(', ', array_keys($meta)));

        if (!isset($meta['custom_options'])) {
            $this->logger->info('ProductOptionsMedia: custom_options not found in meta');
            return $meta;
        }

        $this->logger->info('ProductOptionsMedia: custom_options found');

        // Check the path exists
        $path = &$meta['custom_options'];
        if (isset($path['children']['options']['children']['record']['children']['container_option']['children']['values']['children']['record']['children'])) {
            $this->logger->info('ProductOptionsMedia: Full path exists, adding fields');
            $path['children']['options']['children']['record']['children']['container_option']['children']['values']['children']['record']['children']['image'] = $this->getImageFieldConfig(45);
            $path['children']['options']['children']['record']['children']['container_option']['children']['values']['children']['record']['children']['description'] = $this->getDescriptionFieldConfig(46);
        } else {
            $this->logger->info('ProductOptionsMedia: Path does not exist');
            // Log what does exist
            if (isset($path['children'])) {
                $this->logger->info('ProductOptionsMedia: custom_options children = ' . implode(', ', array_keys($path['children'])));
            }
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
