<?php

declare(strict_types=1);

namespace Core\Container\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Stateless
{
}