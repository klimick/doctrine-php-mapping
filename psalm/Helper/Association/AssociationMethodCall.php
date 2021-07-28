<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Helper\Association;

use Fp\Functional\Option\Option;
use PhpParser\Node;
use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveTrue;

/**
 * @template TMethodName of string
 */
final class AssociationMethodCall
{
    /**
     * @param TMethodName $name
     */
    public function __construct(public Node\Expr\MethodCall $node, public string $name)
    {
    }

    /**
     * @template TMethod of string
     *
     * @param list<TMethod> $supported_methods
     * @return Option<AssociationMethodCall<TMethod>>
     */
    public static function from(AfterMethodCallAnalysisEvent $event, string $association_function, array $supported_methods): Option
    {
        return Option::do(function() use ($event, $association_function, $supported_methods) {
            $method_call = yield proveOf($event->getExpr(), Node\Expr\MethodCall::class);
            $method_name = yield proveOf($method_call->name, Node\Identifier::class)->map(fn($id) => $id->name);

            $func_call = yield proveOf($method_call->var, Node\Expr\FuncCall::class);
            $func_name = yield proveOf($func_call->name, Node\Name::class);

            yield proveTrue($association_function === $func_name->getAttribute('resolvedName'));
            yield proveTrue(in_array($method_name, $supported_methods, true));

            /** @var AssociationMethodCall<TMethod> */
            return new self($method_call, $method_name);
        });
    }
}
