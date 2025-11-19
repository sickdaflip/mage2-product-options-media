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

Create the following template in your theme:

```
app/design/frontend/[Vendor]/[Theme]/Magento_Catalog/templates/product/composite/fieldset/options/view/checkable.phtml
```

Example template code for Hyva Theme with Alpine.js and Tailwind CSS:

```php
<?php
declare(strict_types=1);

use Magento\Catalog\Block\Product\View\Options\Type\Select;
use Magento\Catalog\Pricing\Price\CustomOptionPriceInterface;
use Magento\Framework\Escaper;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

/** @var Select $block */
/** @var Escaper $escaper */
/** @var SecureHtmlRenderer $secureRenderer */

$_option = $block->getOption();
$_optionId = $_option->getId();
$class = ($_option->getIsRequire()) ? 'required' : '';
$_arraySign = '';
$_optionType = $_option->getType();
$count = 1;

// Get Media helper for image URLs
$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$mediaHelper = $objectManager->get(\Sickdaflip\ProductOptionsMedia\Model\Product\Option\Value\Media::class);

if ($_optionType == \Magento\Catalog\Api\Data\ProductCustomOptionInterface::OPTION_TYPE_CHECKBOX) {
    $_arraySign = '[]';
}
?>

<fieldset class="fieldset fieldset-product-options-inner <?= $escaper->escapeHtmlAttr($class) ?>"
          x-data="{ descriptionModal: null }"
          x-on:keydown.escape="descriptionModal = null">
    <legend class="legend options-list-title">
        <span><?= $escaper->escapeHtml($_option->getTitle()) ?></span>
    </legend>

    <div class="options-list nested" id="options-<?= $escaper->escapeHtmlAttr($_optionId) ?>-list">
        <?php if ($_optionType == \Magento\Catalog\Api\Data\ProductCustomOptionInterface::OPTION_TYPE_DROP_DOWN ||
            $_optionType == \Magento\Catalog\Api\Data\ProductCustomOptionInterface::OPTION_TYPE_MULTIPLE): ?>
            <!-- Select/Multi-select handling -->
            <?php /* ... your select implementation ... */ ?>
        <?php else: ?>
            <!-- Radio/Checkbox with images and descriptions -->
            <?php foreach ($_option->getValues() as $_value): ?>
                <?php
                $count++;
                $_value->setProduct($block->getProduct());
                $priceStr = $block->getFormattedPrice([
                    'is_percent' => $_value->getPriceType() == 'percent',
                    'pricing_value' => $_value->getPrice($_value->getPriceType() == 'percent')
                ]);
                $htmlValue = $_value->getOptionTypeId();

                // Get image and description
                $imagePath = $_value->getData('image');
                $imageUrl = $mediaHelper->getImageUrl($imagePath);
                $description = $_value->getData('description');
                ?>

                <div class="field choice flex items-start gap-4 py-3 border-b border-gray-200 last:border-0">
                    <input type="<?= $escaper->escapeHtmlAttr($_optionType == 'radio' ? 'radio' : 'checkbox') ?>"
                           class="mt-1 product-custom-option"
                           name="options[<?= $escaper->escapeHtmlAttr($_optionId) ?>]<?= /* @noEscape */ $_arraySign ?>"
                           id="options_<?= $escaper->escapeHtmlAttr($_optionId) ?>_<?= $escaper->escapeHtmlAttr($count) ?>"
                           value="<?= $escaper->escapeHtmlAttr($htmlValue) ?>"
                           <?php if ($_option->getIsRequire() && $_optionType === 'radio'): ?>required<?php endif; ?>
                           data-price-amount="<?= $escaper->escapeHtmlAttr($_value->getPrice(true)) ?>"
                           data-price-type="<?= $escaper->escapeHtmlAttr($_value->getPriceType()) ?>">

                    <div class="flex-1">
                        <label class="label cursor-pointer"
                               for="options_<?= $escaper->escapeHtmlAttr($_optionId) ?>_<?= $escaper->escapeHtmlAttr($count) ?>">
                            <div class="flex items-center gap-3">
                                <?php if ($imageUrl): ?>
                                    <img src="<?= $escaper->escapeUrl($imageUrl) ?>"
                                         alt="<?= $escaper->escapeHtmlAttr($_value->getTitle()) ?>"
                                         class="w-16 h-16 object-contain rounded border"
                                         loading="lazy">
                                <?php endif; ?>
                                <div>
                                    <span class="font-medium"><?= $escaper->escapeHtml($_value->getTitle()) ?></span>
                                    <?php if ($priceStr): ?>
                                        <span class="text-sm text-gray-600 ml-2"><?= /* @noEscape */ $priceStr ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </label>

                        <?php if ($description): ?>
                            <button type="button"
                                    class="text-sm text-blue-600 hover:text-blue-800 mt-1"
                                    x-on:click="descriptionModal = <?= $escaper->escapeHtmlAttr($htmlValue) ?>">
                                <?= $escaper->escapeHtml(__('More Info')) ?>
                            </button>

                            <!-- Description Modal -->
                            <template x-teleport="body">
                                <div x-show="descriptionModal === <?= $escaper->escapeHtmlAttr($htmlValue) ?>"
                                     x-transition
                                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                                     x-on:click.self="descriptionModal = null">
                                    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 max-h-[80vh] overflow-y-auto">
                                        <div class="p-4 border-b flex justify-between items-center">
                                            <h3 class="font-semibold"><?= $escaper->escapeHtml($_value->getTitle()) ?></h3>
                                            <button type="button" x-on:click="descriptionModal = null" class="text-gray-400 hover:text-gray-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="p-4 prose prose-sm max-w-none">
                                            <?= /* @noEscape */ $description ?>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</fieldset>
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
