# Product Options Media for Magento 2

Add images and descriptions to custom product options for better product configuration experience.

**Migrated from Magento 1.9** - maintains backward compatibility with existing data!

## Features

### Image Support
- **Two upload methods:**
    - **Media Gallery Integration** - Select from existing images, organized folder structure
    - **Direct Upload** - Quick upload for immediate use, M1.9 migration compatible
- Display images in frontend option selection
- Responsive image display with Tailwind CSS
- Lazy loading for performance
- Automatic path resolution (supports both legacy and new paths)

### Description Support
- Add detailed descriptions to option values
- Modal popup for descriptions in frontend
- HTML support with WYSIWYG editor compatibility
- Alpine.js powered modals

### Modern Frontend
- Built for Hyva Theme
- Alpine.js for reactivity
- Tailwind CSS for styling
- Fully responsive design

### Migration Ready
- Same database columns as M1.9 version
- Data migrates automatically via Magento Data Migration Tool
- No manual data conversion needed

## Requirements

- Magento 2.4.x
- PHP 8.1+
- Hyva Theme (for frontend display)

## Installation

```bash
composer config repositories.mage2-product-options-media vcs https://github.com/sickdaflip/mage2-product-options-media.git
composer require sickdaflip/module-product-options-media:^1.0
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

## Uninstallation

To completely remove the module including database changes:

```bash
bin/magento module:uninstall Sickdaflip_ProductOptionsMedia --remove-data
```

This will:
- Remove module from `app/etc/config.php`
- Drop `image` column from `catalog_product_option_type_value`
- Drop `description` column from `catalog_product_option_type_value`
- Clean up module files (if installed via Composer)

**Warning:** This permanently deletes all images and descriptions from your product options!

### Manual Uninstall (without --remove-data)

If you want to keep the data:

```bash
bin/magento module:disable Sickdaflip_ProductOptionsMedia
bin/magento setup:upgrade
bin/magento cache:flush
```

Database columns remain intact for potential re-enabling later.

## Database Schema

Adds two columns to `catalog_product_option_type_value`:
- `image` (VARCHAR 255) - stores image path/filename
- `description` (TEXT) - stores HTML description

## Usage

### Admin - Add Images and Descriptions

1. Go to **Catalog > Products**
2. Edit a product
3. Navigate to **Customizable Options**
4. Add/Edit a Select/Radio/Checkbox option
5. For each option value:
    - **Image**: Use "Upload" for direct upload or "Gallery" to select from Media Gallery
    - **Description**: Enter text/HTML description

**Image Path Storage:**
- Media Gallery images: Stored as relative path (e.g., `wysiwyg/images/option.jpg`)
- Direct uploads: Stored in `catalog/customoption/` (M1.9 compatible)
- Both paths work seamlessly in frontend

### Frontend - Template Integration

**Important:** This module does not include frontend templates. You must create the templates in your Hyva theme to display images and descriptions.

#### 1. Create CSS Styles

Add to your theme's CSS (e.g., in your Tailwind source file):

```css
.product-options-wrapper {

    input[type='radio'], input[type='checkbox'] {
        @apply sr-only;
    }

    input[type='radio'] + label, input[type='checkbox'] + label {
        @apply w-full border border-primary rounded-md cursor-pointer p-2;
    }

    input[type='radio']:checked + label, input[type='checkbox']:checked + label {
        @apply text-green-500 border-green-500;
    }

    input[type='radio']:checked + label span.text, input[type='checkbox']:checked + label span.text {
        @apply text-green-500 border-green-500;
        &:after {
            content: "\2713 ";
        }
        .price-notice .price {
            @apply text-green-500;
        }
    }

}
```

**Note:** Use `@apply sr-only;` instead of `@apply hidden;` to keep inputs focusable for form validation.

#### 2. Create Template

Create the template in your theme:

```
app/design/frontend/[Vendor]/[Theme]/Magento_Catalog/templates/product/composite/fieldset/options/view/checkable.phtml
```

Full template code for Hyva Theme:

```php
<?php
declare(strict_types=1);

use Hyva\Theme\Model\ViewModelRegistry;
use Hyva\Theme\ViewModel\ProductPrice;
use Magento\Catalog\Block\Product\View\Options\Type\Select\Checkable;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Pricing\Price\CustomOptionPrice;
use Magento\Framework\Escaper;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Sickdaflip\ProductOptionsMedia\ViewModel\MediaHelper;

/**
 * @var Checkable $block
 * @var Escaper $escaper
 * @var ViewModelRegistry $viewModels
 * @var SecureHtmlRenderer $secureRenderer
 */

$product = $block->getProduct();

/** @var ProductPrice $productPriceViewModel */
$productPriceViewModel = $viewModels->require(ProductPrice::class);

/** @var Hyva\Theme\ViewModel\HeroiconsOutline $heroicons */
$heroicons = $viewModels->require(\Hyva\Theme\ViewModel\HeroiconsOutline::class);

/** @var MediaHelper $mediaHelper */
$mediaHelper = $viewModels->require(MediaHelper::class);

$option = $block->getOption();

if ($option): ?>
    <?php
    $configValue = $block->getPreconfiguredValue($option);
    $optionType = $option->getType();
    $arraySign = $optionType === Option::OPTION_TYPE_CHECKBOX ? '[]' : '';

    // Sammle alle Descriptions für das gemeinsame Modal
    $descriptions = [];
    foreach ($option->getValues() as $value) {
        if ($value->getData('description')) {
            $optionId = $option->getId() . '_' . $value->getOptionTypeId();
            $descriptions[$optionId] = [
                'title' => $value->getTitle(),
                'description' => $value->getData('description')
            ];
        }
    }
    ?>

    <div x-data="initOptionModal()"
         x-init="descriptions = JSON.parse('<?= $escaper->escapeJs(json_encode($descriptions)) ?>')">

        <div class="options-list nested"
             id="options-<?= $escaper->escapeHtmlAttr($option->getId()) ?>-list"
             data-max-items="3">
        <?php if ($optionType === Option::OPTION_TYPE_RADIO && !$option->getIsRequire()): ?>
            <div class="field choice">
                <input type="radio"
                       id="options_<?= $escaper->escapeHtmlAttr($option->getId()) ?>"
                       class="radio product-custom-option"
                       name="options[<?= $escaper->escapeHtmlAttr($option->getId()) ?>]"
                       value=""
                       checked
                       @change="typeof updateCustomOptionValue === 'function' && updateCustomOptionValue($dispatch, '<?= $escaper->escapeHtmlAttr($option->getId()) ?>', $event.target)"
                />
                <label class="label text-center"
                       for="options_<?= $escaper->escapeHtmlAttr($option->getId()) ?>">
            <span>
                <?= $escaper->escapeHtml(__('None')) ?>
            </span>
                </label>
            </div>
        <?php endif; ?>
        <?php foreach ($option->getValues() as $value): ?>
            <?php
            $checked = '';
            $count++;
            if ($arraySign) {
                $checked = is_array($configValue) && in_array($value->getOptionTypeId(), $configValue) ? 'checked' : '';
            } else {
                $checked = $configValue == $value->getOptionTypeId() ? 'checked' : '';
            }
            $dataSelector = 'options[' . $option->getId() . ']';
            if ($arraySign) {
                $dataSelector .= '[' . $value->getOptionTypeId() . ']';
            }

            $optionId = $option->getId() . '_' . $value->getOptionTypeId();

            $valuePrice = $productPriceViewModel->getCustomOptionPrice($value, CustomOptionPrice::PRICE_CODE, $product);
            if ($productPriceViewModel->displayPriceInclAndExclTax()) {
                $valueBasePrice = $value->getPrice(true);
            }

            // Get image and description
            $hasImage = $value->getData('image');
            $hasDescription = $value->getData('description');
            $imageUrl = $hasImage ? $mediaHelper->getImageUrl($value->getData('image')) : null;
            ?>
            <div class="field choice <?= $hasImage ? 'with-image' : '' ?> <?= $hasDescription ? 'with-description' : '' ?>">
                <input type="<?= $escaper->escapeHtmlAttr($optionType) ?>"
                       class="<?= $optionType === Option::OPTION_TYPE_RADIO
                               ? 'form-radio'
                               : 'form-checkbox' ?>
                       product-custom-option"
                       name="options[<?= $escaper->escapeHtmlAttr($option->getId()) ?>]<?= /* @noEscape */
                       $arraySign ?>"
                       id="options_<?= $escaper->escapeHtmlAttr($optionId) ?>"
                       value="<?= $escaper->escapeHtmlAttr($value->getOptionTypeId()) ?>"
                        <?= $escaper->escapeHtml($checked) ?>
                        <?php if ($option->getIsRequire()): ?>
                            <?php if ($optionType === Option::OPTION_TYPE_RADIO): ?>
                                required
                            <?php endif; ?>
                            data-required
                            oninvalid="this.setCustomValidity(this.dataset.validationMessage)"
                            oninput="this.setCustomValidity('')"
                            data-validation-message="<?= $escaper
                                    ->escapeHtmlAttr(__("Please select one of the options.")) ?>"
                        <?php endif; ?>
                       data-price-amount="<?= $escaper->escapeHtmlAttr($valuePrice) ?>"
                        <?php if ($productPriceViewModel->displayPriceInclAndExclTax()): ?>
                            data-base-price-amount="<?= $escaper->escapeHtmlAttr($valueBasePrice) ?>"
                        <?php endif; ?>
                       data-price-type="<?= $escaper->escapeHtmlAttr($value->getPriceType()) ?>"
                       @change="typeof updateCustomOptionValue === 'function' && updateCustomOptionValue($dispatch, '<?= $escaper->escapeHtmlAttr($optionId) ?>', $event.target)"
                />
                <label class="label flex flex-row text-center items-center justify-center gap-x-2"
                       for="options_<?= $escaper->escapeHtmlAttr($optionId) ?>"
                >
                    <?php if ($imageUrl): ?>
                            <img
                                src="<?= $escaper->escapeUrl($imageUrl) ?>"
                                alt="<?= $escaper->escapeHtmlAttr($value->getTitle()) ?>"
                                class="object-contain"
                                loading="lazy"
                                width="75"
                                height="75"
                            >
                    <?php endif; ?>
                    <span class="text"><?= $escaper->escapeHtml($value->getTitle()) ?> <?= /* @noEscape */ $block->formatPrice($value) ?></span>
                </label>
                <?php if ($hasDescription): ?>
                    <button
                            type="button"
                            @click.prevent="openModal = '<?= $escaper->escapeHtmlAttr($optionId) ?>'"
                    >
                        <span class="bg-primary text-sm w-6 h-6 border border-l-0 border-primary rounded-r-lg flex items-center justify-center text-white font-medium">i</span>
                    </button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        </div>

        <span class="toggle-option cursor-pointer" data-translated-more="<?= $escaper->escapeHtml(__('Show more')); ?>"
              data-translated-less="<?= $escaper->escapeHtml(__('Show less')); ?>">
            <span class="js-show-more-text flex justify-center mt-2">
                <?= $escaper->escapeHtml(__('Show more')); ?> <?= $escaper->escapeHtml($option->getTitle()) ?> <?= $heroicons->arrowSmDownHtml('', 24, 24, ['aria-hidden="true"']) ?>
            </span>
            <span class="js-show-less-text flex justify-center mt-2" style="display: none;">
                <?= $escaper->escapeHtml(__('Show less')); ?> <?= $escaper->escapeHtml($option->getTitle()) ?> <?= $heroicons->arrowSmUpHtml('', 24, 24, ['aria-hidden="true"']) ?>
            </span>
        </span>

        <!-- EIN EINZIGES Modal für alle Descriptions -->
        <template x-if="openModal !== null">
            <div @keydown.escape.window="openModal = null"
                 class="fixed inset-0 z-50 overflow-y-auto"
                 x-cloak>
                <div @click="openModal = null"
                     class="fixed inset-0 bg-black opacity-50 transition-opacity"></div>

                <div class="flex items-center justify-center min-h-screen p-4">
                    <div
                            x-data="{
                                get modalData() {
                                    return descriptions[openModal] || { title: '', description: '' };
                                }
                            }"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            @click.stop
                            class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-lg w-full p-6 relative text-gray-700">
                        <template x-if="modalData.title">
                            <p class="text-lg font-bold text-gray-900 mb-4" x-text="modalData.title"></p>
                        </template>

                        <template x-if="modalData.description">
                            <div class="prose prose-sm max-w-none text-gray-700" x-html="modalData.description"></div>
                        </template>

                        <div class="mt-6 flex justify-end">
                            <button
                                    type="button"
                                    @click="openModal = null"
                                    class="btn btn-primary"
                            >
                                <?= $escaper->escapeHtml(__('Close')) ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        function initOptionModal() {
            return {
                openModal: null,
                descriptions: {}
            }
        }
    </script>
<?php endif; ?>
```

#### 3. Add JavaScript for Show More/Less

Add this script to your theme (e.g., in the template footer or in a separate JS file):

```javascript
<script>
    const DISPLAY_SHOW = 'flex';
    const DISPLAY_HIDE = 'none';
    const DEFAULT_LIMIT = 3;

    function initializeOptionList(listContainer, toggleSpan) {
        const maxItems = parseInt(listContainer?.dataset.maxItems, 10) || DEFAULT_LIMIT;
        const items = listContainer.querySelectorAll('.field.choice');
        const moreText = toggleSpan.querySelector('.js-show-more-text');
        const lessText = toggleSpan.querySelector('.js-show-less-text');

        if (items.length <= maxItems) {
            toggleSpan.style.display = DISPLAY_HIDE;
            return;
        }

        // Initial hide
        for (let i = 0; i < items.length; i++) {
            if (i >= maxItems) {
                items[i].style.display = DISPLAY_HIDE;
            }
        }

        // Show all function
        const showAll = () => {
            for (let i = maxItems; i < items.length; i++) {
                items[i].style.display = DISPLAY_SHOW;
            }
            moreText.style.display = DISPLAY_HIDE;
            lessText.style.display = DISPLAY_SHOW;
        };

        // Hide extra function
        const hideExtra = () => {
            for (let i = maxItems; i < items.length; i++) {
                items[i].style.display = DISPLAY_HIDE;
            }
            lessText.style.display = DISPLAY_HIDE;
            moreText.style.display = DISPLAY_SHOW;
        };

        // Toggle click handler
        toggleSpan.addEventListener('click', () => {
            const isShowingMore = items[maxItems] && items[maxItems].style.display !== DISPLAY_HIDE;
            if (isShowingMore) {
                hideExtra();
            } else {
                showAll();
            }
        });

        // Show all items when validation fails on hidden input
        items.forEach((item, index) => {
            if (index >= maxItems) {
                const input = item.querySelector('input');
                if (input) {
                    input.addEventListener('invalid', (e) => {
                        showAll();
                    });
                }
            }
        });
    }

    const toggleSpans = document.querySelectorAll('.toggle-option');
    toggleSpans.forEach(spanElement => {
        const listContainer = spanElement.previousElementSibling;
        if (listContainer && listContainer.classList.contains('options-list')) {
            initializeOptionList(listContainer, spanElement);
        }
    });
</script>
```

**Path Resolution:**
The `Media` helper automatically resolves paths from:
- Legacy M1.9 path: `catalog/customoption/image.jpg`
- Just filename: `image.jpg` (looks in `catalog/customoption/`)
- Media Gallery path: `wysiwyg/images/image.jpg`
- Any custom media path

## File Storage

Images uploaded via Direct Upload are stored in:
```
pub/media/catalog/customoption/
```

Images selected from Media Gallery keep their original path.

**Supported formats:** JPG, JPEG, PNG, GIF, SVG, WebP

## Migration from Magento 1.9

If migrating from the M1.9 version of this module:

1. Run standard Magento Data Migration Tool
2. Copy images from old media folder:
   ```bash
   cp -r /old/media/catalog/customoption/* /new/pub/media/catalog/customoption/
   ```
3. Install this module
4. Create frontend templates in your theme
5. Done! All data and images are preserved

**Note:** Legacy images stored as just filenames (e.g., `image.jpg`) are automatically resolved from `catalog/customoption/`.

## Customization

### Change Image Upload Path

Override controller:
```
Controller/Adminhtml/Product/Option/UploadImage.php
```

### Modify Admin UI

The admin fields are added via UI Component Modifier:
```
Ui/DataProvider/Product/Form/Modifier/CustomOptions.php
```

Upload and Gallery buttons are added via:
```
view/adminhtml/templates/product/edit/options-media.phtml
```

### Frontend Media Helper

The module provides a ViewModel for accessing image URLs in templates:
```
ViewModel/MediaHelper.php
```

This ViewModel is used instead of ObjectManager for proper dependency injection in templates.

## Technical Details

### Database Columns

```sql
ALTER TABLE catalog_product_option_type_value
    ADD COLUMN image VARCHAR(255) NULL COMMENT 'Option Value Image',
    ADD COLUMN description TEXT NULL COMMENT 'Option Value Description';
```

### Path Conversion

The module automatically converts absolute URLs to relative paths when saving. This ensures:
- Portability between environments (dev/staging/production)
- Smaller database storage
- Consistent path handling

### Event Flow

1. Admin uploads image or selects from gallery
2. Path is converted to relative path before saving
3. Relative path stored in database
4. Frontend loads image via Media helper
5. Helper resolves path and returns full URL

## Troubleshooting

**Images not uploading:**
- Check `pub/media/catalog/customoption/` is writable
- Verify max upload size in php.ini
- Check browser console for JavaScript errors

**Images not displaying:**
- Verify files exist in media folder
- Check image paths in database
- Clear cache: `bin/magento cache:flush`
- For legacy images, ensure they are in `pub/media/catalog/customoption/`

**Descriptions not showing:**
- Ensure Alpine.js is loaded (Hyva requirement)
- Check browser console for errors
- Verify description data in database

**Admin fields not showing:**
- Run `bin/magento setup:di:compile`
- Clear cache: `bin/magento cache:flush`
- Check var/log for errors

**Form validation error "not focusable":**
- Use `@apply sr-only;` instead of `@apply hidden;` for hiding inputs
- Only apply `required` attribute to radio buttons, not checkboxes (use `data-required` for Hyva validation)
- JavaScript will automatically show hidden items when validation fails

**updateCustomOptionValue is not defined:**
- Use `typeof updateCustomOptionValue === 'function' && updateCustomOptionValue(...)` to prevent race conditions
- This checks if the function exists before calling it

## Version History

### 1.0.0
- Initial Magento 2.4.x release
- Migrated from M1.9 codebase
- Modern architecture with Declarative Schema
- Hyva Theme compatibility
- Alpine.js modals
- PHP 8.1+ compatibility
- Automatic URL to relative path conversion

## Author

Philipp Breitsprecher
philippbreitsprecher@gmail.com

## License

Proprietary

---

**Need support?** Open an issue on GitHub or contact the author.
