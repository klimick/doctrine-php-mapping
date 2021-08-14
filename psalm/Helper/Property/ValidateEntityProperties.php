<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Helper\Property;

use Klimick\PsalmDoctrinePhpMapping\RaiseIssue;
use Psalm\Codebase;
use Psalm\Internal\Type\Comparator\UnionTypeComparator;
use Psalm\Plugin\EventHandler\Event\AfterFunctionLikeAnalysisEvent;
use Psalm\Storage\ClassLikeStorage;
use Psalm\Type;

final class ValidateEntityProperties
{
    /**
     * @param non-empty-list<EntityProperty> $properties_from_mapping
     */
    public static function againstMappingProperties(
        AfterFunctionLikeAnalysisEvent $event,
        ClassLikeStorage $entity,
        array $properties_from_mapping,
    ): void
    {
        foreach ($properties_from_mapping as $property_from_mapping) {
            if (!array_key_exists($property_from_mapping->name, $entity->properties)) {
                RaiseIssue::for($event)
                    ->properties()
                    ->propertyDoesNotExistInEntity(
                        node: $property_from_mapping->node,
                        class: $entity->name,
                        property: $property_from_mapping->name,
                    );

                continue;
            }

            $type_from_entity = $entity->properties[$property_from_mapping->name]->type ?? Type::getMixed();
            $atomics_count_from_mapping = count($property_from_mapping->type->getAtomicTypes());
            $atomics_count_from_entity = count($type_from_entity->getAtomicTypes());

            $type_matched = $atomics_count_from_mapping === $atomics_count_from_entity &&
                UnionTypeComparator::isContainedBy($event->getCodebase(), $property_from_mapping->type, $type_from_entity);

            if (!$type_matched) {
                RaiseIssue::for($event)
                    ->properties()
                    ->propertyTypeMismatchIssue(
                        node: $property_from_mapping->node,
                        class: $entity->name,
                        property: $property_from_mapping->name,
                        in_mapping: $property_from_mapping->type,
                        in_entity: $type_from_entity,
                    );
            }
        }
    }
}
