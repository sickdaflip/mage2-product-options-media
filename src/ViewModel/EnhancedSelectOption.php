<?php
/*
 * Copyright (C) Philipp Breitsprecher, Inc - All Rights Reserved
 * @project Mage2 GD
 * @file EnhancedSelectOption.php
 * @author Philipp Breitsprecher
 * @date 21.11.25
 * @email philippbreitsprecher@gmail.com
 *
 * Pure Alpine.js + Tailwind CSS Enhanced Select ViewModel
 */

declare(strict_types=1);

namespace Sickdaflip\ProductOptionsMedia\ViewModel;

use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Option;
use Magento\Framework\Escaper;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Sickdaflip\ProductOptionsMedia\Helper\Config as ConfigHelper;

class EnhancedSelectOption implements ArgumentInterface
{
    private Escaper $escaper;
    private MediaHelper $mediaHelper;
    private PricingHelper $pricingHelper;
    private ConfigHelper $configHelper;
    private array $preconfiguredValues = [];
    private static array $renderedOptions = [];

    public function __construct(
        Escaper $escaper,
        MediaHelper $mediaHelper,
        PricingHelper $pricingHelper,
        ConfigHelper $configHelper
    ) {
        $this->escaper = $escaper;
        $this->mediaHelper = $mediaHelper;
        $this->pricingHelper = $pricingHelper;
        $this->configHelper = $configHelper;
    }

    public function setPreconfiguredValues(array $values): self
    {
        $this->preconfiguredValues = $values;
        return $this;
    }

    public function getOptionHtml($option, Product $product, $preconfiguredValue = null): string
    {
        $optionType = $option->getType();
        $optionId = $option->getId();

        // Prevent duplicate rendering - track by option ID
        $renderKey = $optionId . '_' . spl_object_hash($option);
        if (isset(self::$renderedOptions[$renderKey])) {
            // Option already rendered, return empty to prevent duplicate
            return '<!-- Enhanced Select Option ' . $optionId . ' already rendered -->';
        }
        self::$renderedOptions[$renderKey] = true;

        // Get preconfigured value for this option
        $preconfig = $preconfiguredValue ?? ($this->preconfiguredValues[$optionId] ?? null);

        if ($optionType === Option::OPTION_TYPE_DROP_DOWN) {
            return $this->renderEnhancedSelect($option, false, $preconfig);
        }

        if ($optionType === Option::OPTION_TYPE_MULTIPLE) {
            return $this->renderEnhancedSelect($option, true, $preconfig);
        }

        return '';
    }

    private function renderEnhancedSelect(Option $option, bool $isMultiple, $preconfiguredValue = null): string
    {
        $optionId = $option->getId();
        $selectId = 'select_' . $optionId;
        $required = $option->getIsRequire();
        $options = $this->buildOptionsArray($option);
        $optionsJson = $this->escaper->escapeHtmlAttr(json_encode($options));
        $arraySign = $isMultiple ? '[]' : '';

        // Generate unique wrapper ID to prevent duplicates
        $wrapperId = 'enhanced-select-wrapper-' . $optionId . '-' . uniqid();

        // Normalize preconfigured values to array of strings
        $selectedValues = [];
        if ($preconfiguredValue !== null) {
            if (is_array($preconfiguredValue)) {
                $selectedValues = array_map('strval', $preconfiguredValue);
            } else {
                $selectedValues = [(string)$preconfiguredValue];
            }
        }

        // Build initial config for Alpine
        $selectedConfig = $isMultiple
            ? 'selectedMultiple: ' . json_encode($selectedValues)
            : 'selected: ' . json_encode($selectedValues[0] ?? '');

        $html = <<<HTML
<!-- Enhanced Select Container (ID: {$wrapperId}) -->
<div id="{$wrapperId}" class="enhanced-select-container">
<!-- Hidden original select for form submission -->
<select name="options[{$optionId}]{$arraySign}"
        id="{$selectId}"
        class="hidden product-custom-option"
        {$this->getRequiredAttr($required)}
        {$this->getMultipleAttr($isMultiple)}>
    {$this->renderNativeOptions($option, $required, $selectedValues)}
</select>

<!-- Alpine.js Enhanced Select -->
<div x-data="enhancedSelect({
        selectId: '{$selectId}',
        options: {$optionsJson},
        multiple: {$this->boolToJs($isMultiple)},
        required: {$this->boolToJs($required)},
        {$selectedConfig},
        placeholder: '{$this->escaper->escapeJs(__('Select %1...', $option->getTitle()) . ($required ? ' *' : ''))}',
        maxVisible: {$this->configHelper->getMaxVisibleOptions()},
        maxTagLength: {$this->configHelper->getMaxTagLength()},
        searchEnabled: {$this->boolToJs($this->configHelper->isSearchEnabled())},
        showImagesDropdown: {$this->boolToJs($this->configHelper->showImagesInDropdown())},
        showImagesTags: {$this->boolToJs($this->configHelper->showImagesInTags())},
        showPrices: {$this->boolToJs($this->configHelper->showPricesInDropdown())},
        showDescriptions: {$this->boolToJs($this->configHelper->showDescriptions())}
     })"
     @keydown.escape="close()"
     @click.outside="close()"
     class="relative">

    <!-- Trigger Button -->
    <button type="button"
            id="{$selectId}_trigger"
            aria-label="{$this->escaper->escapeHtmlAttr($option->getTitle() . ($required ? ' (required)' : ''))}"
            @click="open()"
            class="relative w-full min-h-12 px-4 py-3 text-left bg-white border border-gray-300 rounded-lg shadow-sm cursor-pointer
                   hover:border-primary focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary
                   dark:bg-neutral-900 dark:border-neutral-700 dark:hover:border-primary transition-colors">

        <!-- Single Select Display -->
        <template x-if="!isMultiple">
            <div class="flex items-center gap-3 pr-8">
                <template x-if="selectedOption?.image">
                    <img :src="selectedOption.image" :alt="selectedOption.title" class="w-8 h-8 rounded-full object-cover">
                </template>
                <span x-show="selectedOption" class="text-gray-900 dark:text-white" x-text="selectedOption?.title"></span>
                <span x-show="selectedOption" class="text-sm font-medium"
                      :class="selectedOption?.price > 0 ? 'text-red-600' : 'text-green-600'"
                      x-text="formatPrice(selectedOption)"></span>
                <span x-show="!selectedOption" class="text-gray-400 dark:text-neutral-500" x-text="placeholder"></span>
            </div>
        </template>

        <!-- Multi Select Tags -->
        <template x-if="isMultiple">
            <div class="flex flex-wrap gap-2 pr-8 overflow-hidden">
                <template x-for="opt in selectedListDisplay" :key="opt.value">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary/10 text-primary rounded-full text-sm"
                          :title="opt.title">
                        <template x-if="opt.image">
                            <img :src="opt.image" :alt="opt.title" class="w-5 h-5 rounded-full object-cover">
                        </template>
                        <span x-text="opt.titleShort"></span>
                        <button type="button" @click.stop="removeTag(opt.value)"
                                class="ml-1 hover:text-red-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </span>
                </template>
                <span x-show="selectedList.length === 0" class="text-gray-400 dark:text-neutral-500" x-text="placeholder"></span>
            </div>
        </template>

        <!-- Dropdown Arrow -->
        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-90': isOpen }"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </span>
    </button>

    <!-- Dropdown Panel -->
    <div x-show="isOpen"
         class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden
                dark:bg-neutral-900 dark:border-neutral-700"
         x-cloak>

        <!-- Search Input -->
        <div class="p-3 border-b border-gray-100 dark:border-neutral-800">
            <input type="text"
                   x-ref="searchInput"
                   x-model="search"
                   placeholder="{$this->escaper->escapeHtmlAttr(__('Search...'))}"
                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white
                          placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary
                          dark:bg-neutral-800 dark:border-neutral-600 dark:text-white dark:placeholder-neutral-500">
        </div>

        <!-- Options List -->
        <ul class="max-h-60 overflow-y-auto">
            <template x-for="opt in visibleOptions" :key="opt.value">
                <li @click="select(opt.value)"
                    :class="{ 'bg-primary/10': isSelected(opt.value) }"
                    class="flex items-center gap-3 px-4 py-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-neutral-800 transition-colors">

                    <!-- Checkbox for Multiple -->
                    <template x-if="isMultiple">
                        <span :class="isSelected(opt.value) ? 'bg-primary border-primary text-white' : 'border-gray-300 dark:border-neutral-600'"
                              class="flex-shrink-0 w-5 h-5 border-2 rounded flex items-center justify-center">
                            <svg x-show="isSelected(opt.value)" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                    </template>

                    <!-- Option Image -->
                    <template x-if="opt.image">
                        <img :src="opt.image" :alt="opt.title" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                    </template>

                    <!-- Option Content -->
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-gray-900 dark:text-white truncate" x-text="opt.title"></div>
                        <template x-if="opt.description">
                            <div class="text-sm text-gray-500 dark:text-neutral-400 truncate" x-text="opt.description"></div>
                        </template>
                    </div>

                    <!-- Price -->
                    <span class="text-sm font-semibold flex-shrink-0"
                          :class="opt.price > 0 ? 'text-red-600' : 'text-green-600'"
                          x-text="formatPrice(opt)"></span>

                    <!-- Selected Check (Single) -->
                    <template x-if="!isMultiple && isSelected(opt.value)">
                        <svg class="w-5 h-5 text-primary flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </template>
                </li>
            </template>

            <!-- Show More -->
            <li x-show="hasMore && !showAll"
                @click.stop="showAll = true"
                class="px-4 py-3 text-center cursor-pointer hover:bg-gray-50 dark:hover:bg-neutral-800 text-primary font-medium transition-colors">
                {$this->escaper->escapeHtml(__('Show'))} <span x-text="moreCount"></span> {$this->escaper->escapeHtml(__('more'))}...
            </li>

            <!-- Show Less -->
            <li x-show="showAll && filteredOptions.length > maxVisible"
                @click.stop="showAll = false"
                class="px-4 py-3 text-center cursor-pointer hover:bg-gray-50 dark:hover:bg-neutral-800 text-primary font-medium transition-colors">
                {$this->escaper->escapeHtml(__('Show less'))}
            </li>

            <!-- No Results -->
            <li x-show="filteredOptions.length === 0" class="px-4 py-6 text-center text-gray-500 dark:text-neutral-400">
                {$this->escaper->escapeHtml(__('No options found'))}
            </li>
        </ul>
    </div>
</div>
</div><!-- End Enhanced Select Container -->
HTML;

        return $html;
    }

    private function buildOptionsArray(Option $option): array
    {
        // Use associative array with value ID as key to automatically prevent duplicates
        $optionsKeyed = [];

        foreach ($option->getValues() as $value) {
            $valueId = (string)$value->getOptionTypeId();

            // Use value ID as array key - automatically prevents duplicates
            if (!isset($optionsKeyed[$valueId])) {
                $optionsKeyed[$valueId] = [
                    'value' => $valueId,
                    'title' => $value->getTitle(),
                    'price' => (float)$value->getPrice(),
                    'priceType' => $value->getPriceType() ?: 'fixed',
                ];

                if ($value->getData('image')) {
                    $optionsKeyed[$valueId]['image'] = $this->mediaHelper->getImageUrl($value->getData('image'));
                }

                if ($value->getData('description')) {
                    $optionsKeyed[$valueId]['description'] = strip_tags($value->getData('description'));
                }
            }
        }

        // Convert to indexed array for JSON encoding - guaranteed no duplicates
        return array_values($optionsKeyed);
    }

    private function renderNativeOptions(Option $option, bool $required, array $selectedValues = []): string
    {
        $html = '';
        if (!$required) {
            $html .= '<option value="">' . $this->escaper->escapeHtml(__('-- Please Select --')) . '</option>';
        }

        // Build unique options using associative array
        $uniqueOptions = [];
        foreach ($option->getValues() as $value) {
            $valueId = (string)$value->getOptionTypeId();
            if (!isset($uniqueOptions[$valueId])) {
                $uniqueOptions[$valueId] = $value;
            }
        }

        // Render deduplicated options
        foreach ($uniqueOptions as $valueId => $value) {
            $isSelected = in_array($valueId, $selectedValues, true);
            $html .= sprintf(
                '<option value="%s"%s>%s</option>',
                $this->escaper->escapeHtmlAttr($valueId),
                $isSelected ? ' selected' : '',
                $this->escaper->escapeHtml($value->getTitle())
            );
        }

        return $html;
    }

    private function getRequiredAttr(bool $required): string
    {
        return $required ? 'required' : '';
    }

    private function getMultipleAttr(bool $multiple): string
    {
        return $multiple ? 'multiple' : '';
    }

    private function boolToJs(bool $value): string
    {
        return $value ? 'true' : 'false';
    }
}
