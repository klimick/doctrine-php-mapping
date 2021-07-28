<?php /** @noinspection PhpUnusedAliasInspection */

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Mapping;

use Klimick\DoctrinePhpMapping\Field\EmbedField;
use Klimick\DoctrinePhpMapping\Field\Field;
use Klimick\DoctrinePhpMapping\Field\IdField;
use Klimick\DoctrinePhpMapping\Field\ManyToManyField;
use Klimick\DoctrinePhpMapping\Field\ManyToOneField;
use Klimick\DoctrinePhpMapping\Field\InverseSide;
use Klimick\DoctrinePhpMapping\Field\OwningSide;
use Klimick\DoctrinePhpMapping\Field\OneToOneField;
use Klimick\DoctrinePhpMapping\Mapping\Inheritance\InheritanceInterface;
use Klimick\DoctrinePhpMapping\Mapping\Inheritance\NoInheritance;

/**
 * @psalm-type ManyToManyAssociation
 *     = ManyToManyField<object>
 *     | OwningSide\ManyToManyField<object, non-empty-literal-string>
 *     | InverseSide\ManyToManyField<object, non-empty-literal-string>
 *
 * @psalm-type ManyToOneAssociation
 *     = ManyToOneField<object, bool>
 *     | OwningSide\ManyToOneField<object, non-empty-literal-string, bool>
 *
 * @psalm-type OneToOneAssociation
 *     = OneToOneField<object, bool>
 *     | OwningSide\OneToOneField<object, non-empty-literal-string, bool>
 *     | InverseSide\OneToOneField<object, non-empty-literal-string>
 *
 * @psalm-type OneToManyAssociation = InverseSide\OneToManyField<object, non-empty-literal-string>
 *
 * @psalm-type Association
 *     = ManyToManyAssociation
 *     | ManyToOneAssociation
 *     | OneToOneAssociation
 *     | OneToManyAssociation
 *
 * @template-covariant TEntity of object
 */
abstract class EntityMapping
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

    public static function inheritance(): InheritanceInterface
    {
        return NoInheritance::instance();
    }

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
     * @return array<non-empty-literal-string, ManyToManyAssociation>
     */
    public static function manyToMany(): array
    {
        return [];
    }

    /**
     * @return array<non-empty-literal-string, ManyToOneAssociation>
     */
    public static function manyToOne(): array
    {
        return [];
    }

    /**
     * @return array<non-empty-literal-string, OneToManyAssociation>
     */
    public static function oneToMany(): array
    {
        return [];
    }

    /**
     * @return array<non-empty-literal-string, OneToOneAssociation>
     */
    public static function oneToOne(): array
    {
        return [];
    }
}
