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

namespace Sickdaflip\ProductOptionsMedia\Plugin\Catalog\Block\Product\View\Options\Type;

use Magento\Catalog\Block\Product\View\Options\Type\Select;

/**
 * Plugin to override select option block template
 */
class SelectPlugin
{
    /**
     * Set custom template for select block
     *
     * @param Select $subject
     * @return void
     */
    public function beforeToHtml(Select $subject): void
    {
        // Override select template
        $subject->setTemplate('Sickdaflip_ProductOptionsMedia::product/view/options/type/select.phtml');
    }
}
