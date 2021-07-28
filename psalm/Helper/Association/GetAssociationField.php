<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Helper\Association;

use Psalm\Type;
use Fp\Functional\Option\Option;
use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;
use Klimick\DoctrinePhpMapping\Field\InverseSide;
use Klimick\DoctrinePhpMapping\Field\OwningSide;
use function Fp\Collection\first;
use function Fp\Collection\firstOf;
use function Fp\Collection\second;
use function Fp\Evidence\proveNonEmptyString;
use function Fp\Evidence\proveOf;

final class GetAssociationField
{
    private const SUPPORTED_CLASSES = [
        InverseSide\OneToManyField::class,
        InverseSide\ManyToManyField::class,
        InverseSide\OneToOneField::class,
        OwningSide\OneToOneField::class,
        OwningSide\ManyToOneField::class,
        OwningSide\ManyToManyField::class,
    ];

    /**
     * @return Option<non-empty-string>
     */
    public static function from(AfterMethodCallAnalysisEvent $event): Option
    {
        return Option::fromNullable($event->getReturnTypeCandidate())
            ->flatMap(fn($type) => firstOf($type->getAtomicTypes(), Type\Atomic\TGenericObject::class))
            ->filter(fn($class_name) => in_array($class_name->value, self::SUPPORTED_CLASSES, true))
            ->flatMap(fn($association_class) => second($association_class->type_params))
            ->flatMap(fn($association_type_param) => first($association_type_param->getAtomicTypes()))
            ->flatMap(fn($association_type_atomic) => proveOf($association_type_atomic, Type\Atomic\TLiteralString::class))
            ->flatMap(fn($association_field) => proveNonEmptyString($association_field->value));
    }
}
