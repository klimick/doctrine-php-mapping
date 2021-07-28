<?php /** @noinspection PhpUnusedAliasInspection */

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Helper\Association;

use Klimick\DoctrinePhpMapping\Mapping\EntityMapping;
use Klimick\DoctrinePhpMapping\Field\OwningSide;
use Klimick\DoctrinePhpMapping\Field\InverseSide;
use Klimick\PsalmDoctrinePhpMapping\Helper\Association\GetAssociationFields;

/**
 * @psalm-import-type Association from EntityMapping
 */
final class IsAssociationValid
{
    /**
     * @psalm-param Association $association
     * @psalm-param GetAssociationFields::INVERSE_SIDE_* $association_method
     */
    public static function atOwningSide(object $association, string $association_method): bool
    {
        return match ($association_method) {
            GetAssociationFields::INVERSE_SIDE_ONE_TO_ONE => $association::class === OwningSide\OneToOneField::class,
            GetAssociationFields::INVERSE_SIDE_ONE_TO_MANY => $association::class === OwningSide\ManyToOneField::class,
            GetAssociationFields::INVERSE_SIDE_MANY_TO_MANY => $association::class === OwningSide\ManyToManyField::class,
        };
    }

    /**
     * @psalm-param Association $association
     * @psalm-param GetAssociationFields::OWNING_SIDE_* $association_method
     */
    public static function atInverseSide(object $association, string $association_method): bool
    {
        return match ($association_method) {
            GetAssociationFields::OWNING_SIDE_ONE_TO_ONE => $association::class === InverseSide\OneToOneField::class,
            GetAssociationFields::OWNING_SIDE_MANY_TO_ONE => $association::class === InverseSide\OneToManyField::class,
            GetAssociationFields::OWNING_SIDE_MANY_TO_MANY => $association::class === InverseSide\ManyToManyField::class,
        };
    }
}
