<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Hook;

use Klimick\DoctrinePhpMapping\Field\Field;
use Klimick\DoctrinePhpMapping\Field\IdField;
use Klimick\DoctrinePhpMapping\Mapping\EntityMapping;
use Klimick\PsalmDoctrinePhpMapping\Helper\GetNodeType;
use Klimick\PsalmDoctrinePhpMapping\Helper\GetSingleAtomic;
use Klimick\PsalmDoctrinePhpMapping\Helper\ReturnTypeFinder;
use Klimick\PsalmDoctrinePhpMapping\RaiseIssue;
use PhpParser\Node;
use Fp\Functional\Option\Option;
use PhpParser\NodeTraverser;
use Psalm\Codebase;
use Psalm\Internal\Type\Comparator\UnionTypeComparator;
use Psalm\Plugin\EventHandler\AfterFunctionLikeAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterFunctionLikeAnalysisEvent;
use Psalm\Storage\ClassLikeStorage;
use Psalm\Type;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Union;
use function Fp\Collection\at;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveString;
use function Fp\Evidence\proveTrue;


/**
 * @psalm-immutable
 */
final class FieldsAnalysis implements AfterFunctionLikeAnalysisInterface
{
    private const PROPERTY_TYPE_INDEX = 0;
    private const PROPERTY_NULLABILITY_INDEX = 2;

    public static function afterStatementAnalysis(AfterFunctionLikeAnalysisEvent $event): ?bool
    {
        Option::do(function() use ($event) {
            $codebase = $event->getCodebase();
            $self = $event->getContext()->self;

            yield proveTrue(null !== $self && is_subclass_of($self, EntityMapping::class));

            $entity_class = yield Option::try(fn() => $self::forClass());
            $entity_class_storage = yield Option::try(fn() => $codebase->classlike_storage_provider->get($entity_class));

            $class_method = yield proveOf($event->getStmt(), Node\Stmt\ClassMethod::class);
            $method_name = yield proveOf($class_method->name, Node\Identifier::class)->map(fn($id) => $id->name);

            yield proveTrue('fields' === $method_name);

            $entity_properties = yield self::getEntityProperties($class_method, $event);

            self::validate($entity_properties, $codebase, $entity_class_storage, $event);
        });

        return null;
    }

    /**
     * @param non-empty-list<EntityProperty> $properties
     */
    private static function validate(array $properties, Codebase $codebase, ClassLikeStorage $entity, AfterFunctionLikeAnalysisEvent $event): void
    {
        foreach ($properties as $field_info) {
            if (!array_key_exists($field_info->name, $entity->properties)) {
                RaiseIssue::for($event)
                    ->fields()
                    ->propertyDoesNotExistInEntity(
                        node: $field_info->node,
                        class: $entity->name,
                        property: $field_info->name,
                    );

                continue;
            }

            $type_from_entity = $entity->properties[$field_info->name]->type ?? Type::getMixed();
            $atomics_count_from_mapping = count($field_info->type->getAtomicTypes());
            $atomics_count_from_entity = count($type_from_entity->getAtomicTypes());

            $type_matched = $atomics_count_from_mapping === $atomics_count_from_entity &&
                UnionTypeComparator::isContainedBy($codebase, $field_info->type, $type_from_entity);

            if (!$type_matched) {
                RaiseIssue::for($event)
                    ->fields()
                    ->propertyTypeMismatchIssue(
                        node: $field_info->node,
                        class: $entity->name,
                        property: $field_info->name,
                        in_mapping: $field_info->type,
                        in_entity: $type_from_entity,
                    );
            }
        }
    }

    /**
     * @return Option<non-empty-list<EntityProperty>>
     */
    private static function getEntityProperties(Node\Stmt\ClassMethod $class_method, AfterFunctionLikeAnalysisEvent $event): Option
    {
        $visitor = new ReturnTypeFinder();
        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($class_method->stmts ?? []);

        return Option::do(function() use ($visitor, $event) {
            $return_node = yield $visitor->getReturn();
            $field_nodes = yield $visitor->getFieldNodes();

            return yield GetNodeType::for($return_node, $event)
                ->flatMap(fn($union) => GetSingleAtomic::forOf($union, TKeyedArray::class))
                ->flatMap(fn($atomic) => self::mapFieldsToProperties($atomic, $field_nodes));
        });
    }

    /**
     * @param array<string, Node> $field_nodes
     * @return Option<non-empty-list<EntityProperty>>
     */
    private static function mapFieldsToProperties(TKeyedArray $atomic, array $field_nodes): Option
    {
        return Option::do(function() use ($atomic, $field_nodes) {
            $entity_properties = [];

            foreach ($atomic->properties as $key => $mapping_field_type) {
                $property_name = yield proveString($key);
                $field_node = yield at($field_nodes, $property_name);
                $property_type = yield self::getEntityPropertyType($mapping_field_type);

                $entity_properties[] = new EntityProperty($property_name, $field_node, $property_type);
            }

            return $entity_properties;
        });
    }

    /**
     * @return Option<Union>
     */
    private static function getEntityPropertyType(Union $mapping_field_type): Option
    {
        return Option::do(function() use ($mapping_field_type) {
            $mapping_field_atomic = yield GetSingleAtomic::forOf($mapping_field_type, TGenericObject::class)
                ->filter(fn($atomic) => in_array($atomic->value, [Field::class, IdField::class], true));

            $entity_property_atomic = yield at($mapping_field_atomic->type_params, self::PROPERTY_TYPE_INDEX)
                ->flatMap(fn($union) => GetSingleAtomic::for($union));

            $nullable = false;

            if ($mapping_field_atomic->value === Field::class) {
                $nullable = yield at($mapping_field_atomic->type_params, self::PROPERTY_NULLABILITY_INDEX)
                    ->flatMap(fn($union) => GetSingleAtomic::for($union))
                    ->flatMap(fn($atomic) => match ($atomic::class) {
                        Type\Atomic\TTrue::class => Option::some(true),
                        Type\Atomic\TFalse::class => Option::some(false),
                        default => Option::none(),
                    });
            }

            return $nullable
                ? new Union([new Type\Atomic\TNull(), $entity_property_atomic])
                : new Union([$entity_property_atomic]);
        });
    }
}
