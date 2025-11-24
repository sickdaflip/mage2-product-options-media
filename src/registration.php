<?php
/*
 * Copyright (C) Philipp Breitsprecher, Inc - All Rights Reserved
 * @project Mage2 GD
 * @file registration.php
 * @author Philipp Breitsprecher
 * @date 18.11.25, 12:33
 * @email philippbreitsprecher@gmail.com
 */

declare(strict_types=1);

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Sickdaflip_ProductOptionsMedia',
    __DIR__
);