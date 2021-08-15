<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Helper\Property;

use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Klimick\DoctrinePhpMapping\Field\ManyToManyField;
use Klimick\DoctrinePhpMapping\Field\ManyToOneField;
use Klimick\DoctrinePhpMapping\Field\OneToOneField;
use Klimick\DoctrinePhpMapping\Field\OwningSide;
use Klimick\DoctrinePhpMapping\Field\InverseSide;
use Klimick\DoctrinePhpMapping\Mapping\EmbeddedMapping;
use Klimick\DoctrinePhpMapping\Mapping\EntityMapping;
use Klimick\DoctrinePhpMapping\Mapping\MappedSuperclassMapping;
use Klimick\PsalmDoctrinePhpMapping\Helper\Nullability;
use Fp\Functional\Option\Option;
use Klimick\DoctrinePhpMapping\Field\Field;
use Klimick\DoctrinePhpMapping\Field\IdField;
use Klimick\PsalmDoctrinePhpMapping\Helper\GetNodeType;
use Klimick\PsalmDoctrinePhpMapping\Helper\GetSingleAtomic;
use Klimick\PsalmDoctrinePhpMapping\Helper\ReturnTypeFinder;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use Psalm\Plugin\EventHandler\Event\AfterFunctionLikeAnalysisEvent;
use Psalm\Type;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Union;
use function Fp\Collection\at;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveString;
use function Fp\Evidence\proveTrue;

final class ExtractEntityProperties
{
    /**
     * @return Option<non-empty-list<EntityProperty>>
     */
    public static function by(Node\Stmt\ClassMethod $class_method, AfterFunctionLikeAnalysisEvent $event): Option
    {
        $visitor = new ReturnTypeFinder();
        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($class_method->stmts ?? []);

        return Option::do(function() use ($visitor, $class_method, $event) {
            $return_node = yield $visitor->getReturn();
            $property_nodes = yield $visitor->getFieldNodes();
            $method_data = yield self::getMethodData($class_method, $event);

            return yield GetNodeType::for($return_node, $event)
                ->flatMap(fn($union) => GetSingleAtomic::forOf($union, TKeyedArray::class))
                ->flatMap(fn($atomic) => self::mapFieldsToProperties($event, $atomic, $property_nodes, $method_data));
        });
    }

    /**
     * @return Option<array<string, mixed>>
     */
    private static function getMethodData(Node\Stmt\ClassMethod $class_method, AfterFunctionLikeAnalysisEvent $event): Option
    {
        return Option::do(function() use ($class_method, $event) {
            $mapping_class = yield Option::fromNullable($event->getContext()->self)
                ->filter(fn($class) => is_subclass_of($class, EntityMapping::class) ||
                    is_subclass_of($class, EmbeddedMapping::class) ||
                    is_subclass_of($class, MappedSuperclassMapping::class));

            $method_name = yield proveOf($class_method->name, Node\Identifier::class)
                ->map(fn($id) => $id->name);

            /** @var mixed $method_data */
            $method_data = yield Option::try(fn(): mixed => call_user_func([$mapping_class, $method_name]));
            yield proveTrue(is_array($method_data));

            $valid_method_data = [];

            foreach ($method_data as $key => $_val) {
                yield proveTrue(is_string($key));

                /** @psalm-suppress MixedAssignment */
                $valid_method_data[$key] = $_val;
            }

            return $valid_method_data;
        });
    }

    /**
     * @param array<string, Node> $property_nodes
     * @param array<string, mixed> $method_data
     * @return Option<non-empty-list<EntityProperty>>
     */
    private static function mapFieldsToProperties(
        AfterFunctionLikeAnalysisEvent $event,
        TKeyedArray $atomic,
        array $property_nodes,
        array $method_data,
    ): Option
    {
        return Option::do(function() use ($event, $atomic, $property_nodes, $method_data) {
            $entity_properties = [];

            foreach ($atomic->properties as $key => $mapping_property_type) {
                $property_name = yield proveString($key);
                $property_node = yield at($property_nodes, $property_name);
                $property_type = yield self::getEntityPropertyType($event, $property_node, $property_name, $mapping_property_type, $method_data);

                $entity_properties[] = new EntityProperty($property_name, $property_node, $property_type);
            }

            return $entity_properties;
        });
    }

    /**
     * @param array<string, mixed> $method_data
     * @return Option<Union>
     */
    private static function getEntityPropertyType(
        AfterFunctionLikeAnalysisEvent $event,
        Node $property_node,
        string $property_name,
        Union $mapping_property_type,
        array $method_data,
    ): Option
    {
        return self::getForField($mapping_property_type)
            ->orElse(fn() => self::getForOneToOne($mapping_property_type))
            ->orElse(fn() => self::getForManyToOne($mapping_property_type))
            ->orElse(fn() => self::getForOneToMany($event, $mapping_property_type, $property_node, $property_name, $method_data))
            ->orElse(fn() => self::getForManyToMany($event, $mapping_property_type, $property_node, $property_name, $method_data));
    }

    /**
     * @return Option<Union>
     */
    private static function getForField(Union $mapping_property_type): Option
    {
        return Option::do(function() use ($mapping_property_type) {
            $mapping_property_atomic = yield GetSingleAtomic::forOf($mapping_property_type, TGenericObject::class)
                ->filter(fn($atomic) => in_array($atomic->value, [Field::class, IdField::class], true));

            // Filed class keep property type at 0 index
            $property_type_index = 0;

            $entity_property_atomic = yield at($mapping_property_atomic->type_params, $property_type_index)
                ->flatMap(fn($union) => GetSingleAtomic::for($union));

            return yield self::isFieldNullable($mapping_property_atomic)
                ->map(fn($nullable) => Nullability::makeNullableIf($nullable, $entity_property_atomic));
        });
    }

    /**
     * @return Option<bool>
     */
    private static function isFieldNullable(TGenericObject $mapping_property_atomic): Option
    {
        // Primary key cannot be nullable
        if ($mapping_property_atomic->value === IdField::class) {
            return Option::some(false);
        }

        // Filed class keep nullability info at 2 index
        $property_nullability_index = 2;

        return at($mapping_property_atomic->type_params, $property_nullability_index)
            ->flatMap(fn($isNullable) => Nullability::fromTypelevel($isNullable));
    }

    /**
     * @return Option<Union>
     */
    private static function getForOneToOne(Union $mapping_property_type): Option
    {
        return Option::do(function() use ($mapping_property_type) {
            $mapping_property_atomic = yield GetSingleAtomic::forOf($mapping_property_type, TGenericObject::class)
                ->filter(fn($atomic) => in_array($atomic->value, [
                    OneToOneField::class,
                    OwningSide\OneToOneField::class,
                    InverseSide\OneToOneField::class,
                ], true));

            // OneToOne class keep property type at 0 index
            $property_type_index = 0;

            $entity_property_atomic = yield at($mapping_property_atomic->type_params, $property_type_index)
                ->flatMap(fn($union) => GetSingleAtomic::for($union));

            return yield self::isOneToOneNullable($mapping_property_atomic)
                ->map(fn($nullable) => Nullability::makeNullableIf($nullable, $entity_property_atomic));
        });
    }

    /**
     * @return Option<bool>
     */
    private static function isOneToOneNullable(TGenericObject $mapping_property_atomic): Option
    {
        // Inverse side of OneToOne cannot be nullable
        if ($mapping_property_atomic->value === InverseSide\OneToOneField::class) {
            return Option::some(false);
        }

        // Plain OneToOne and owning side OneToOne keep nullability at different positions
        $property_nullability_index = match ($mapping_property_atomic->value) {
            OneToOneField::class => 1,
            OwningSide\OneToOneField::class => 2,
        };

        return at($mapping_property_atomic->type_params, $property_nullability_index)
            ->flatMap(fn($isNullable) => Nullability::fromTypelevel($isNullable));
    }

    /**
     * @return Option<Union>
     */
    private static function getForManyToOne(Union $mapping_property_type): Option
    {
        return Option::do(function() use ($mapping_property_type) {
            $mapping_property_atomic = yield GetSingleAtomic::forOf($mapping_property_type, TGenericObject::class)
                ->filter(fn($atomic) => in_array($atomic->value, [
                    ManyToOneField::class,
                    OwningSide\ManyToOneField::class,
                ], true));

            // ManyToOne class keep property type at 0 index
            $property_type_index = 0;

            $entity_property_atomic = yield at($mapping_property_atomic->type_params, $property_type_index)
                ->flatMap(fn($union) => GetSingleAtomic::for($union));

            return yield self::isManyToOneNullable($mapping_property_atomic)
                ->map(fn($nullable) => Nullability::makeNullableIf($nullable, $entity_property_atomic));
        });
    }

    /**
     * @return Option<bool>
     */
    private static function isManyToOneNullable(TGenericObject $mapping_property_atomic): Option
    {
        // Plain ManyToOne and owning side ManyToOne keep nullability at different positions
        $property_nullability_index = match ($mapping_property_atomic->value) {
            ManyToOneField::class => 1,
            OwningSide\ManyToOneField::class => 2,
        };

        return at($mapping_property_atomic->type_params, $property_nullability_index)
            ->flatMap(fn($isNullable) => Nullability::fromTypelevel($isNullable));
    }

    /**
     * @param array<string, mixed> $method_data
     * @return Option<Union>
     */
    private static function getForOneToMany(
        AfterFunctionLikeAnalysisEvent $event,
        Union $mapping_property_type,
        Node $property_node,
        string $property_name,
        array $method_data,
    ): Option
    {
        return Option::do(function() use ($event, $mapping_property_type, $property_node, $property_name, $method_data) {
            $mapping_property_atomic = yield GetSingleAtomic::forOf($mapping_property_type, TGenericObject::class)
                ->filter(fn($atomic) => InverseSide\OneToManyField::class === $atomic->value);

            // OneToMany class keep property type at 0 index
            $property_type_index = 0;

            $entity_property_atomic = yield at($mapping_property_atomic->type_params, $property_type_index)
                ->flatMap(fn($union) => GetSingleAtomic::for($union));

            $key_type = yield IndexByPropertyTypeFetcher::fetchFor($event, $property_node, $property_name, $method_data);
            $val_type = new Union([$entity_property_atomic]);

            return new Union([
                new TGenericObject(DoctrineCollection::class, [$key_type, $val_type])
            ]);
        });
    }

    /**
     * @param array<string, mixed> $method_data
     * @return Option<Union>
     */
    private static function getForManyToMany(
        AfterFunctionLikeAnalysisEvent $event,
        Union $mapping_property_type,
        Node $property_node,
        string $property_name,
        array $method_data,
    ): Option
    {
        return Option::do(function() use ($event, $property_node, $mapping_property_type, $property_name, $method_data) {
            $mapping_property_atomic = yield GetSingleAtomic::forOf($mapping_property_type, TGenericObject::class)
                ->filter(fn($atomic) => in_array($atomic->value, [
                    ManyToManyField::class,
                    OwningSide\ManyToManyField::class,
                    InverseSide\ManyToManyField::class,
                ], true));

            // ManyToMany class keep property type at 0 index
            $property_type_index = 0;

            $entity_property_atomic = yield at($mapping_property_atomic->type_params, $property_type_index)
                ->flatMap(fn($union) => GetSingleAtomic::for($union));

            $key_type = yield IndexByPropertyTypeFetcher::fetchFor($event, $property_node, $property_name, $method_data);
            $val_type = new Union([$entity_property_atomic]);

            return new Union([
                new TGenericObject(DoctrineCollection::class, [$key_type, $val_type])
            ]);
        });
    }
}
