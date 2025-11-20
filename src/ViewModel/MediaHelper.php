<?php
/*
 * Copyright (C) Philipp Breitsprecher, Inc - All Rights Reserved
 * @project Mage2 GD
 * @file MediaHelper.php
 * @author Philipp Breitsprecher
 * @date 20.11.25
 * @email philippbreitsprecher@gmail.com
 */

declare(strict_types=1);

namespace Sickdaflip\ProductOptionsMedia\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Sickdaflip\ProductOptionsMedia\Model\Product\Option\Value\Media;

/**
 * ViewModel for Media helper access in templates
 */
class MediaHelper implements ArgumentInterface
{
    private Media $media;

    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    /**
     * Get image URL from path
     *
     * @param string|null $imagePath
     * @return string|null
     */
    public function getImageUrl(?string $imagePath): ?string
    {
        return $this->media->getImageUrl($imagePath);
    }

    /**
     * Check if image exists
     *
     * @param string|null $imagePath
     * @return bool
     */
    public function imageExists(?string $imagePath): bool
    {
        return $this->media->imageExists($imagePath);
    }
}
