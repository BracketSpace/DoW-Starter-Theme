<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Abstracts;

use DoWStarterTheme\Common\Traits\Sluggable as SluggableTrait;

/**
 * Adds support for object slug method.
 */
abstract class Sluggable
{
    use SluggableTrait;
}
