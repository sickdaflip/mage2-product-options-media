# Product Options Media for Magento 2

Add images and descriptions to custom product options for better product configuration experience.

**Migrated from Magento 1.9** - maintains backward compatibility with existing data!

## Features

### 📸 Image Support
- **Two upload methods:**
    - **Media Gallery Integration** - Select from existing images, organized folder structure
    - **Direct Upload** - Quick upload for immediate use, M1.9 migration compatible
- Display images in frontend option selection
- Responsive image display with Tailwind CSS
- Lazy loading for performance
- Automatic path resolution (supports both legacy and new paths)

### 📝 Description Support
- Add detailed descriptions to option values
- Modal popup for descriptions in frontend
- HTML support with WYSIWYG editor compatibility
- Alpine.js powered modals

### 🎨 Modern Frontend
- Built for Hyvä Theme
- Alpine.js for reactivity
- Tailwind CSS for styling
- Fully responsive design

### 🔄 Migration Ready
- Same database columns as M1.9 version
- Data migrates automatically via Magento Data Migration Tool
- No manual data conversion needed

## Requirements

- Magento 2.4.x
- PHP 8.4+
- Hyvä Theme (for frontend templates)

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

**⚠️ Warning:** This permanently deletes all images and descriptions from your product options!

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
- `image` (VARCHAR 255) - stores image filename
- `description` (TEXT) - stores HTML description

## Usage

### Admin - Add Images & Descriptions

1. Go to **Catalog → Products**
2. Edit a product
3. Navigate to **Customizable Options**
4. Add/Edit a Select/Radio/Checkbox option
5. For each option value:
    - **Image** (Two methods):
        - **Method 1 (Recommended):** Click "Select from Gallery" to choose from existing images
        - **Method 2 (Quick/Legacy):** Click "Direct Upload" to upload immediately (M1.9 compatible)
    - **Description**: Enter text/HTML description

**Image Path Storage:**
- Media Gallery images: `catalog/product/...` (or custom path)
- Direct uploads: `catalog/customoption/...` (M1.9 compatible path)
- Both paths work seamlessly in frontend

### Frontend - Display

Images and descriptions are automatically displayed when:
- Option type is Radio or Checkbox
- Image/Description is set for the option value

**Display Features:**
- Images shown as thumbnails (80x80px)
- Description accessible via "More Info" button
- Modal popup with full description
- Responsive grid layout

**Using in Custom Templates:**

```php
<?php
// Get media helper
$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$mediaHelper = $objectManager->get(\Sickdaflip\ProductOptionsMedia\Model\Product\Option\Value\Media::class);

// Get image URL (handles both legacy and new paths)
$imageUrl = $mediaHelper->getImageUrl($optionValue->getData('image'));

// Get description
$description = $optionValue->getData('description');
?>

<?php if ($imageUrl): ?>
    <img src="<?= $escaper->escapeUrl($imageUrl) ?>" alt="<?= $escaper->escapeHtmlAttr($optionValue->getTitle()) ?>">
<?php endif; ?>

<?php if ($description): ?>
    <div class="description">
        <?= /* @noEscape */ $description ?>
    </div>
<?php endif; ?>
```

**Path Resolution:**
The `Media` helper automatically resolves paths from both:
- Legacy M1.9 path: `catalog/customoption/image.jpg`
- Media Gallery path: `catalog/product/option-images/image.jpg`
- Any custom media path

## File Storage

Images are stored in:
```
pub/media/catalog/customoption/
```

**Supported formats:** JPG, JPEG, PNG, GIF, SVG, WebP

## Migration from Magento 1.9

If migrating from the M1.9 version of this module:

1. Run standard Magento Data Migration Tool
2. Copy images from old media folder:
   ```bash
   cp -r /old/media/catalog/customoption/* /new/pub/media/catalog/customoption/
   ```
3. Install this module
4. Done! All data and images are preserved

## Customization

### Modify Image Display

Edit template:
```
view/frontend/templates/product/view/options/type/select.phtml
```

### Modify Admin UI

Edit template:
```
view/adminhtml/templates/product/edit/options.phtml
```

### Change Image Upload Path

Override controller:
```
Controller/Adminhtml/Product/Option/UploadImage.php
```

## Technical Details

### Database Columns

```sql
ALTER TABLE catalog_product_option_type_value
    ADD COLUMN image VARCHAR(255) NULL COMMENT 'Option Value Image',
ADD COLUMN description TEXT NULL COMMENT 'Option Value Description';
```

### Event Flow

1. Admin uploads image → AJAX to `UploadImage` controller
2. Image saved to `pub/media/catalog/customoption/`
3. Filename stored in database
4. Frontend loads image from media URL
5. Description shown in Alpine.js modal

## Troubleshooting

**Images not uploading:**
- Check `pub/media/catalog/customoption/` is writable
- Verify max upload size in php.ini
- Check browser console for JavaScript errors

**Images not displaying:**
- Verify files exist in media folder
- Check image filenames in database
- Clear cache: `bin/magento cache:flush`

**Descriptions not showing:**
- Ensure Alpine.js is loaded (Hyvä requirement)
- Check browser console for errors
- Verify description data in database

## Version History

### 1.0.0
- Initial Magento 2.4.x release
- Migrated from M1.9 codebase
- Modern architecture with Declarative Schema
- Hyvä Theme integration
- Alpine.js modals
- PHP 8.4 compatibility

## Author

Philipp Breitsprecher  
philippbreitsprecher@gmail.com

## License

Proprietary

---

**Need support?** Open an issue on GitHub or contact the author.