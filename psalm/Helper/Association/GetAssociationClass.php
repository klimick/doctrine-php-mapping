<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Helper\Association;

use Fp\Functional\Option\Option;
use Klimick\DoctrinePhpMapping\Mapping\EntityMapping;
use PhpParser\Node;
use function Fp\Collection\first;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveString;

final class GetAssociationClass
{
    /**
     * @return Option<class-string<EntityMapping<object>>>
     */
    public static function from(Node\Expr\MethodCall $method_call): Option
    {
        return first($method_call->args)
            ->flatMap(fn($first_arg) => proveOf($first_arg->value, Node\Expr\ClassConstFetch::class))
            ->flatMap(fn($class_const_fetch) => proveOf($class_const_fetch->class, Node\Name::class))
            ->flatMap(fn($name) => proveString($name->getAttribute('resolvedName')))
            ->filter(fn($class_name) => class_exists($class_name))
            ->filter(fn($class_name) => is_subclass_of($class_name, EntityMapping::class));
    }
}
