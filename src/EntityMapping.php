<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping;

use Klimick\DoctrinePhpMapping\Field\EmbedField;
use Klimick\DoctrinePhpMapping\Field\Field;
use Klimick\DoctrinePhpMapping\Field\ManyToManyField;
use Klimick\DoctrinePhpMapping\Field\ManyToOneField;
use Klimick\DoctrinePhpMapping\Field\InverseSide\OneToManyField;
use Klimick\DoctrinePhpMapping\Field\OneToOneField;

/**
 * @template TEntity of object
 * @psalm-immutable
 */
abstract class EntityMapping
{
    /**
     * @return array<non-empty-string, Field>
     */
    public static function fields(): array
    {
        return [];
    }

    /**
     * @return array<non-empty-string, EmbedField>
     */
    public static function embedded(): array
    {
        return [];
    }

    /**
     * @return array<non-empty-string, ManyToManyField>
     */
    public static function manyToMany(): array
    {
        return [];
    }

    /**
     * @return array<non-empty-string, ManyToOneField>
     */
    public static function manyToOne(): array
    {
        return [];
    }

    /**
     * @return array<non-empty-string, OneToManyField>
     */
    public static function oneToMany(): array
    {
        return [];
    }

    /**
     * @return array<non-empty-string, OneToOneField>
     */
    public static function oneToOne(): array
    {
        return [];
    }
}
