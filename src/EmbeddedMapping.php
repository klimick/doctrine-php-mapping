<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping;

use Klimick\DoctrinePhpMapping\Field\Field;

/**
 * @template TEmbedded of object
 * @psalm-immutable
 */
abstract class EmbeddedMapping
{
    /**
     * @return array<non-empty-string, Field>
     */
    abstract public static function fields(): array;
}
