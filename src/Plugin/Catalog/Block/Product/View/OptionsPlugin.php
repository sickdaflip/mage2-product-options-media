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
use Sickdaflip\ProductOptionsMedia\Helper\Config as ConfigHelper;

/**
 * Plugin to override option block templates
 */
class OptionsPlugin
{
    private ConfigHelper $configHelper;

    public function __construct(ConfigHelper $configHelper)
    {
        $this->configHelper = $configHelper;
    }

    /**
     * Set custom template for options block only if module is enabled
     *
     * @param Options $subject
     * @return void
     */
    public function beforeToHtml(Options $subject): void
    {
        // Only override template if module is enabled
        if ($this->configHelper->isEnabled()) {
            $subject->setTemplate('Sickdaflip_ProductOptionsMedia::product/view/options/options.phtml');
        }
    }
}
