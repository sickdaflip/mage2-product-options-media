<?php
/*
 * Copyright (C) Philipp Breitsprecher, Inc - All Rights Reserved
 * @project Mage2 GD
 * @file CheckablePlugin.php
 * @author Philipp Breitsprecher
 * @date 20.11.25
 * @email philippbreitsprecher@gmail.com
 */

declare(strict_types=1);

namespace Sickdaflip\ProductOptionsMedia\Plugin\Catalog\Block\Product\View\Options\Type\Select;

use Magento\Catalog\Block\Product\View\Options\Type\Select\Checkable;

/**
 * Plugin to override checkable option block template
 */
class CheckablePlugin
{
    /**
     * Set custom template for checkable block
     *
     * @param Checkable $subject
     * @return void
     */
    public function beforeToHtml(Checkable $subject): void
    {
        // Override checkable template
        $subject->setTemplate('Sickdaflip_ProductOptionsMedia::product/composite/fieldset/options/view/checkable.phtml');
    }
}
