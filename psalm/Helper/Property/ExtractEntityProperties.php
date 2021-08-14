<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Helper\Property;

use Klimick\DoctrinePhpMapping\Field\ManyToOneField;
use Klimick\DoctrinePhpMapping\Field\OneToOneField;
use Klimick\DoctrinePhpMapping\Field\OwningSide;
use Klimick\DoctrinePhpMapping\Field\InverseSide;
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
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Union;
use function Fp\Collection\at;
use function Fp\Evidence\proveString;

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

        return Option::do(function() use ($visitor, $event) {
            $return_node = yield $visitor->getReturn();
            $property_nodes = yield $visitor->getFieldNodes();

            return yield GetNodeType::for($return_node, $event)
                ->flatMap(fn($union) => GetSingleAtomic::forOf($union, TKeyedArray::class))
                ->flatMap(fn($atomic) => self::mapFieldsToProperties($atomic, $property_nodes));
        });
    }

    /**
     * @param array<string, Node> $property_nodes
     * @return Option<non-empty-list<EntityProperty>>
     */
    private static function mapFieldsToProperties(TKeyedArray $atomic, array $property_nodes): Option
    {
        return Option::do(function() use ($atomic, $property_nodes) {
            $entity_properties = [];

            foreach ($atomic->properties as $key => $mapping_property_type) {
                $property_name = yield proveString($key);
                $property_node = yield at($property_nodes, $property_name);
                $property_type = yield self::getEntityPropertyType($mapping_property_type);

                $entity_properties[] = new EntityProperty($property_name, $property_node, $property_type);
            }

            return $entity_properties;
        });
    }

    /**
     * @return Option<Union>
     */
    private static function getEntityPropertyType(Union $mapping_property_type): Option
    {
        return self::getForField($mapping_property_type)
            ->orElse(fn() => self::getForOneToOne($mapping_property_type))
            ->orElse(fn() => self::getForManyToOne($mapping_property_type));
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
}
