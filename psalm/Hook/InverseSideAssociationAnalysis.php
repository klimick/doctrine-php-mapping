<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Hook;

use PhpParser\Node;
use Psalm\Type;
use Psalm\Plugin\EventHandler\AfterMethodCallAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;
use Klimick\DoctrinePhpMapping\Field\InverseSide\ManyToManyField;
use Klimick\DoctrinePhpMapping\Field\InverseSide\OneToManyField;
use Klimick\DoctrinePhpMapping\Field\InverseSide\OneToOneField;
use Klimick\DoctrinePhpMapping\Field\OwningSide;
use Klimick\DoctrinePhpMapping\Mapping\EntityMapping;
use Klimick\PsalmDoctrinePhpMapping\RaiseIssue;
use Fp\Functional\Option\Option;
use function Fp\Collection\first;
use function Fp\Collection\firstOf;
use function Fp\Collection\second;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveString;
use function Fp\Evidence\proveTrue;
use function Fp\Evidence\proveNonEmptyString;

/**
 * @psalm-import-type Association from EntityMapping
 */
final class InverseSideAssociationAnalysis implements AfterMethodCallAnalysisInterface
{
    private const METHOD_MANY_TO_MANY = 'manyToMany';
    private const METHOD_ONE_TO_MANY = 'oneToMany';
    private const METHOD_ONE_TO_ONE = 'oneToOne';

    private const SUPPORTED_METHODS = [
        self::METHOD_MANY_TO_MANY,
        self::METHOD_ONE_TO_MANY,
        self::METHOD_ONE_TO_ONE,
    ];

    private const SUPPORTED_CLASSES = [
        OneToManyField::class,
        ManyToManyField::class,
        OneToOneField::class,
    ];

    public static function afterMethodCallAnalysis(AfterMethodCallAnalysisEvent $event): void
    {
        Option::do(function() use ($event) {
            $statements_source = $event->getStatementsSource();

            $method_call = yield proveOf($event->getExpr(), Node\Expr\MethodCall::class);
            $method_name = yield proveOf($method_call->name, Node\Identifier::class)->map(fn($id) => $id->name);

            yield proveTrue(in_array($method_name, self::SUPPORTED_METHODS, true));

            $mapped_by_field = yield self::getMappedByField($event);
            $mapping_class = yield self::getAssociationMappingClass($method_call);
            $association_fields = self::getAssociationFields($method_name, $mapping_class);

            if (!array_key_exists($mapped_by_field, $association_fields)) {
                RaiseIssue::for($statements_source)->noMappedByFieldAtOwningSide(
                    node: $method_call,
                    field: $mapped_by_field,
                    mapping_class: $mapping_class,
                    association_type: $method_name,
                );
            } elseif (!self::isOwningSideAssociation($association_fields[$mapped_by_field], $method_name)) {
                RaiseIssue::for($statements_source)->invalidAssociationAtOwningSide(
                    node: $method_call,
                    field: $mapped_by_field,
                    mapping_class: $mapping_class,
                );
            }
        });
    }

    /**
     * @psalm-param InverseSideAssociationAnalysis::METHOD_* $association_method
     * @param Association $association
     */
    private static function isOwningSideAssociation(object $association, string $association_method): bool
    {
        return match ($association_method) {
            self::METHOD_ONE_TO_ONE => $association::class === OwningSide\OneToOneField::class,
            self::METHOD_ONE_TO_MANY => $association::class === OwningSide\ManyToOneField::class,
            self::METHOD_MANY_TO_MANY => $association::class === OwningSide\ManyToManyField::class,
        };
    }

    /**
     * @psalm-param InverseSideAssociationAnalysis::METHOD_* $association_method
     * @param class-string<EntityMapping<object>> $entity_mapping
     * @return array<non-empty-string, Association>
     */
    private static function getAssociationFields(string $association_method, string $entity_mapping): array
    {
        $fields = Option::try(fn() => match ($association_method) {
            self::METHOD_ONE_TO_ONE => $entity_mapping::oneToOne(),
            self::METHOD_ONE_TO_MANY => $entity_mapping::manyToOne(),
            self::METHOD_MANY_TO_MANY => $entity_mapping::manyToMany(),
        });

        return $fields->getOrElse([]);
    }

    /**
     * @param Type\Union $type
     * @return Option<non-empty-string>
     */
    private static function getMappedByField(AfterMethodCallAnalysisEvent $event): Option
    {
        return Option::fromNullable($event->getReturnTypeCandidate())
            ->flatMap(fn($type) => firstOf($type->getAtomicTypes(), Type\Atomic\TGenericObject::class))
            ->filter(fn($class_name) => in_array($class_name->value, self::SUPPORTED_CLASSES, true))
            ->flatMap(fn($one_to_many) => second($one_to_many->type_params))
            ->flatMap(fn($mapped_by_param) => first($mapped_by_param->getAtomicTypes()))
            ->flatMap(fn($mapped_by_atomic) => proveOf($mapped_by_atomic, Type\Atomic\TLiteralString::class))
            ->flatMap(fn($literal) => proveNonEmptyString($literal->value));
    }

    /**
     * @return Option<class-string<EntityMapping<object>>>
     */
    private static function getAssociationMappingClass(Node\Expr\MethodCall $method_call): Option
    {
        return first($method_call->args)
            ->flatMap(fn($first_arg) => proveOf($first_arg->value, Node\Expr\ClassConstFetch::class))
            ->flatMap(fn($class_const_fetch) => proveOf($class_const_fetch->class, Node\Name::class))
            ->flatMap(fn($name) => proveString($name->getAttribute('resolvedName')))
            ->filter(fn($class_name) => class_exists($class_name))
            ->filter(fn($class_name) => is_subclass_of($class_name, EntityMapping::class));
    }
}
