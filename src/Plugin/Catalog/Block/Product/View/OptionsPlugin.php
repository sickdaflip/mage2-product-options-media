<?php
/*
 * Copyright (C) Philipp Breitsprecher, Inc - All Rights Reserved
 * @project Mage2 GD
 * @file OptionsPlugin.php
 * @author Philipp Breitsprecher
 * @date 20.11.25
 * @email philippbreitsprecher@gmail.com
 */

declare(strict_types=1);

namespace Sickdaflip\ProductOptionsMedia\Plugin\Catalog\Block\Product\View;

use Magento\Catalog\Block\Product\View\Options;

/**
 * Plugin to override option block templates
 */
class OptionsPlugin
{
    /**
     * Set custom template for options block
     *
     * @param Options $subject
     * @return void
     */
    public function beforeToHtml(Options $subject): void
    {
        // Override main options template
        $subject->setTemplate('Sickdaflip_ProductOptionsMedia::product/view/options/options.phtml');
    }
}
