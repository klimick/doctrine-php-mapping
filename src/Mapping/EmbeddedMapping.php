<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Mapping;

use Klimick\DoctrinePhpMapping\Field\Field;

/**
 * @template TEmbedded of object
 */
abstract class EmbeddedMapping
{
    final private function __construct()
    {
    }

    final public static function isTransient(): bool
    {
        return true;
    }

    /**
     * @return class-string<TEmbedded>
     */
    abstract public static function forClass(): string;

    /**
     * @return array<non-empty-literal-string, Field>
     */
    abstract public static function fields(): array;
}
