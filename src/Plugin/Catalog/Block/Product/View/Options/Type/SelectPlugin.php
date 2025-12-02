<?php
/*
 * Copyright (C) Philipp Breitsprecher, Inc - All Rights Reserved
 * @project Mage2 GD
 * @file SelectPlugin.php
 * @author Philipp Breitsprecher
 * @date 20.11.25
 * @email philippbreitsprecher@gmail.com
 */

declare(strict_types=1);

namespace FlipDev\ProductOptionsMedia\Plugin\Catalog\Block\Product\View\Options\Type;

use Magento\Catalog\Block\Product\View\Options\Type\Select;
use FlipDev\ProductOptionsMedia\Helper\Config as ConfigHelper;

/**
 * Plugin to override select option block template
 */
class SelectPlugin
{
    private ConfigHelper $configHelper;

    public function __construct(ConfigHelper $configHelper)
    {
        $this->configHelper = $configHelper;
    }

    /**
     * Set custom template for select block only if module is enabled
     *
     * @param Select $subject
     * @return void
     */
    public function beforeToHtml(Select $subject): void
    {
        // Only override template if module is enabled
        if ($this->configHelper->isEnabled()) {
            $subject->setTemplate('FlipDev_ProductOptionsMedia::product/view/options/type/select.phtml');
        }
    }
}
