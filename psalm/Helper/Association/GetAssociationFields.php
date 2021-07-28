<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Helper\Association;

use Fp\Functional\Option\Option;
use Klimick\DoctrinePhpMapping\Mapping\EntityMapping;

/**
 * @psalm-import-type Association from EntityMapping
 */
final class GetAssociationFields
{
    public const INVERSE_SIDE_MANY_TO_MANY = 'manyToMany';
    public const INVERSE_SIDE_ONE_TO_MANY = 'oneToMany';
    public const INVERSE_SIDE_ONE_TO_ONE = 'oneToOne';

    public const OWNING_SIDE_MANY_TO_MANY = 'manyToMany';
    public const OWNING_SIDE_MANY_TO_ONE = 'manyToOne';
    public const OWNING_SIDE_ONE_TO_ONE = 'oneToOne';

    /**
     * @psalm-param GetAssociationFields::OWNING_SIDE_* $owning_side_association_method
     * @param class-string<EntityMapping<object>> $entity_mapping
     * @return array<non-empty-string, Association>
     */
    public static function atInverseSide(string $owning_side_association_method, string $entity_mapping): array
    {
        $fields = Option::try(fn() => match ($owning_side_association_method) {
            self::OWNING_SIDE_ONE_TO_ONE => $entity_mapping::oneToOne(),
            self::OWNING_SIDE_MANY_TO_ONE => $entity_mapping::oneToMany(),
            self::OWNING_SIDE_MANY_TO_MANY => $entity_mapping::manyToMany(),
        });

        return $fields->getOrElse([]);
    }

    /**
     * @psalm-param GetAssociationFields::INVERSE_SIDE_* $inverse_side_association_method
     * @param class-string<EntityMapping<object>> $entity_mapping
     * @return array<non-empty-string, Association>
     */
    public static function atOwningSide(string $inverse_side_association_method, string $entity_mapping): array
    {
        $fields = Option::try(fn() => match ($inverse_side_association_method) {
            self::INVERSE_SIDE_ONE_TO_ONE => $entity_mapping::oneToOne(),
            self::INVERSE_SIDE_ONE_TO_MANY => $entity_mapping::manyToOne(),
            self::INVERSE_SIDE_MANY_TO_MANY => $entity_mapping::manyToMany(),
        });

        return $fields->getOrElse([]);
    }
}
