<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Mapping;

use Klimick\DoctrinePhpMapping\Field\EmbedField;
use Klimick\DoctrinePhpMapping\Field\Field;
use Klimick\DoctrinePhpMapping\Field\IdField;
use Klimick\DoctrinePhpMapping\Field\ManyToManyField;
use Klimick\DoctrinePhpMapping\Field\ManyToOneField;
use Klimick\DoctrinePhpMapping\Field\OneToOneField;
use Klimick\DoctrinePhpMapping\Field\OwningSide;
use Klimick\DoctrinePhpMapping\Field\InverseSide;

/**
 * @template-covariant TEntity of object
 */
abstract class MappedSuperclassMapping
{
    final private function __construct()
    {
    }

    final public static function isTransient(): bool
    {
        return false;
    }

    /**
     * @return class-string<TEntity>
     */
    abstract public static function forClass(): string;

    /**
     * @return array<non-empty-literal-string, IdField | Field>
     */
    public static function fields(): array
    {
        return [];
    }

    /**
     * @return array<non-empty-literal-string, EmbedField>
     */
    public static function embedded(): array
    {
        return [];
    }

    /**
     * @return array<non-empty-literal-string, ManyToManyField | OwningSide\ManyToManyField | InverseSide\ManyToManyField>
     */
    public static function manyToMany(): array
    {
        return [];
    }

    /**
     * @return array<non-empty-literal-string, ManyToOneField | OwningSide\ManyToOneField>
     */
    public static function manyToOne(): array
    {
        return [];
    }

    /**
     * @return array<non-empty-literal-string, InverseSide\OneToManyField>
     */
    public static function oneToMany(): array
    {
        return [];
    }

    /**
     * @return array<non-empty-literal-string, OneToOneField | OwningSide\OneToOneField | InverseSide\OneToOneField>
     */
    public static function oneToOne(): array
    {
        return [];
    }
}
