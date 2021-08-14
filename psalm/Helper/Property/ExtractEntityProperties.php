<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Helper\Property;

use Psalm\Type;
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
    private const PROPERTY_TYPE_INDEX = 0;
    private const PROPERTY_NULLABILITY_INDEX = 2;

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
        return Option::do(function() use ($mapping_property_type) {
            $mapping_property_atomic = yield GetSingleAtomic::forOf($mapping_property_type, TGenericObject::class)
                ->filter(fn($atomic) => in_array($atomic->value, [Field::class, IdField::class], true));

            $entity_property_atomic = yield at($mapping_property_atomic->type_params, self::PROPERTY_TYPE_INDEX)
                ->flatMap(fn($union) => GetSingleAtomic::for($union));

            return yield self::isEntityPropertyNullable($mapping_property_atomic)->map(
                fn($nullable) => $nullable
                    ? new Union([new Type\Atomic\TNull(), $entity_property_atomic])
                    : new Union([$entity_property_atomic])
            );
        });
    }

    /**
     * @return Option<bool>
     */
    private static function isEntityPropertyNullable(TGenericObject $mapping_property_atomic): Option
    {
        if ($mapping_property_atomic->value !== Field::class) {
            return Option::some(false);
        }

        return at($mapping_property_atomic->type_params, self::PROPERTY_NULLABILITY_INDEX)
            ->flatMap(fn($union) => GetSingleAtomic::for($union))
            ->flatMap(fn($atomic) => match ($atomic::class) {
                Type\Atomic\TTrue::class => Option::some(true),
                Type\Atomic\TFalse::class => Option::some(false),
                default => Option::none(),
            });
    }
}
