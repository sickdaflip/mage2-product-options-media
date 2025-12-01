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
- PHP 8.4+
- Hyva Theme (for frontend display)

## Installation

```bash
composer config repositories.sickdaflip/mage2-product-options-media vcs https://github.com/sickdaflip/mage2-product-options-media.git
composer require sickdaflip/mage2-product-options-media:dev-main
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
composer remove sickdaflip/mage2-product-options-media
composer config --unset repositories.sickdaflip/mage2-product-options-media
```

Database columns remain intact for potential re-enabling later.

## Database Schema

Adds two columns to `catalog_product_option_type_value`:
- `image` (VARCHAR 255) - stores image path/filename
- `description` (TEXT) - stores HTML description

## Configuration

Navigate to **Stores → Configuration → FlipDev → Product Options Media**

The admin configuration panel features a custom FlipDev logo for easy identification.

### Available Settings:

**General Settings:**
- **Enable/disable module** - When disabled, falls back to standard Magento/Hyvä option templates

**Dropdown Settings:**
- Max visible options (default: 4)
- Dropdown max height in pixels (default: 288)
- Enable/disable search

**Tag Settings:**
- Max tag length for truncation (default: 25)

**Display Settings:**
- Show/hide images in dropdown
- Show/hide images in tags
- Show/hide prices in dropdown
- Show/hide descriptions
- Enable/disable dark mode support

All settings are configurable per store view.

### Module Disable Behavior

When the module is disabled via admin configuration:
- Template override plugins skip execution
- Standard Magento/Hyvä option templates are used
- All product options display normally without enhanced features
- No data is lost - re-enabling restores all functionality

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

**New:** This module now includes frontend templates for Hyvä Theme! No need to create templates in your theme.

The module provides the following templates:
- **options.phtml** - Main wrapper template for all custom options
- **checkable.phtml** - Template for radio and checkbox options (with image and description support)
- **select.phtml** - Template for dropdown and multi-select options (with image and description support)

#### How Templates Are Loaded

Templates are automatically loaded via **Magento Plugins** (not layout XML):
- `OptionsPlugin` - Overrides main options template
- `SelectPlugin` - Overrides select/dropdown template
- `CheckablePlugin` - Overrides radio/checkbox template

**Smart Template Override:** Plugins check if module is enabled before overriding templates. When disabled, standard Magento/Hyvä templates are used automatically.

#### Features Included:

1. **Automatic Image Display** - Images are displayed inline with option labels
2. **Description Modals** - Alpine.js powered modals for option descriptions
3. **Show More/Less** - Automatically collapses options when more than 3 values
4. **Price Calculation** - Full integration with Hyvä's price calculation system
5. **Form Validation** - Proper HTML5 validation with accessibility support
6. **Responsive Design** - Tailwind CSS styles included

#### Customization

If you want to customize the templates, you can override them in your theme:

```
app/design/frontend/[Vendor]/[Theme]/Sickdaflip_ProductOptionsMedia/templates/...
```

#### Template Files

The module includes these templates (located in `view/frontend/templates/`):

**Main Options Template:**
```
product/view/options/options.phtml
```
Renders all custom options and delegates to type-specific templates.

**Checkable Options (Radio & Checkbox):**
```
product/composite/fieldset/options/view/checkable.phtml
```
Features:
- **Alpine.js Dropdown with Tags** - Modern multi-select interface with tag display
- **Image Thumbnails** - Images displayed in dropdown options and selected tags
- **Description Support** - Option descriptions shown in dropdown
- **Search & Filter** - Live search within options
- **Show More/Less** - Pagination for large option sets (default: 4 visible)
- **Character Limiting** - Tag titles truncated at 25 characters with tooltip
- **Overflow Protection** - Tags container with overflow:hidden
- **Keyboard Navigation** - Full keyboard support (Tab, Enter, Space, Escape)
- **ARIA Labels** - Screen reader support with descriptive labels
- **Dark Mode** - Full dark mode support
- "None" option for non-required radio buttons
- Full price calculation integration

**Select Options (Dropdown & Multi-Select):**
```
product/view/options/type/select.phtml
```
Delegates to EnhancedSelectOption ViewModel for enhanced dropdown experience with same features as checkable options.

All templates use:
- **MediaHelper ViewModel** for image URL resolution
- **Alpine.js** for modals and interactivity
- **x-on:** syntax (not @ shorthand) for better compatibility
- **Tailwind CSS** for styling

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

**Duplicate options rendering (SOLVED in v1.1.0):**
- Fixed: Browser pre-rendering conflict with Alpine.js x-defer attribute caused duplicate inputs/selects
- Solution: Text-based deduplication runs at init() and before dropdown opens
- No user action required - automatically fixed in latest version

## Version History

### 1.2.0 (Current)
**Major UX & Accessibility Overhaul + Admin Configuration**

**New Features:**
- **Alpine.js Dropdown with Tags** - Modern multi-select interface for radio/checkbox options
- **Enhanced Select Component** - Custom dropdown for select/multiple with image support
- **Admin Configuration Panel** - FlipDev → Product Options Media with full customization
- **FlipDev Branding** - Custom logo in admin navigation for easy module identification
- **Configurable Settings** - Max visible options, tag length, search, images, prices, descriptions
- **Module Enable/Disable** - Toggle entire module functionality from admin with proper fallback
- **Descriptive Placeholders** - Self-explanatory placeholders (e.g., "Select Color... *")
- **Character Limiting** - Tag titles truncated at 25 chars with full text tooltip (configurable)
- **Search & Filter** - Live search within dropdown options (can be disabled)
- **Show More/Less** - Pagination for large option sets (configurable threshold)

**Fixes & Improvements:**
- **Module Disable Fix** - Plugins now check config before overriding templates, fallback to standard Hyvä templates when disabled
- **Race Condition Fix** - Removed x-transition to prevent Alpine.js promise cancellation errors
- **Reactivity Fix** - Changed x-if to x-show for reliable tag rendering (DOM presence maintained)
- **Immutable Arrays** - Using filter() and spread operator for reliable Alpine reactivity
- **$nextTick Timing** - Proper sync timing with $nextTick before DOM updates
- **Label Accessibility** - Removed separate labels in favor of integrated placeholders + aria-label
- **Keyboard Navigation** - Full support: Tab, Enter, Space, Escape
- **ARIA Support** - aria-label attributes for screen readers
- **Dark Mode** - Complete dark mode styling
- **i18n Support** - Dynamic translation with "Select %1..." pattern (de_DE + en_US)
- **Code Cleanup** - Removed debug console.warn() statements, fixed syntax errors

**Technical:**
- Plugin-based template override (OptionsPlugin, SelectPlugin, CheckablePlugin)
- ConfigHelper dependency injection in all plugins for dynamic enable/disable
- No more "Incorrect use of <label for=FORM_ELEMENT>" warnings
- Removed inline JavaScript comments (parse error prevention)
- Deduplication improvements (multi-layered approach)
- BFCache handling for back/forward navigation
- ACL permissions for admin configuration
- Store view-level configuration support

### 1.1.0
- Fixed duplicate rendering of checkbox/radio options caused by Alpine.js x-defer timing
- Removed frontend CSS file (no longer needed)
- Improved Radio button UX (clicking same option again closes dropdown)
- Added text-based deduplication for robust duplicate prevention
- Fixed transition errors when opening/closing dropdowns

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


