/*
 * Copyright (C) Philipp Breitsprecher, Inc - All Rights Reserved
 * @project Mage2 GD
 * @file requirejs-config.js
 * @author Philipp Breitsprecher
 * @date 18.11.25, 15:41
 * @email philippbreitsprecher@gmail.com
 */

var config = {
    config: {
        mixins: {
            'Magento_Catalog/js/options': {
                'Sickdaflip_ProductOptionsMedia/js/options-mixin': true
            }
        }
    }
};