<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Helper;

use Fp\Functional\Option\Option;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use function Fp\Evidence\proveOf;
use function Symfony\Component\String\s;

final class ReturnTypeFinder extends NodeVisitorAbstract
{
    private null|Node\Stmt\Return_ $return = null;
    private bool $multiple = false;

    public function leaveNode(Node $node): void
    {
        if (!($node instanceof Node\Stmt\Return_)) {
            return;
        }

        if (null === $this->return) {
            $this->return = $node;
        } else {
            $this->multiple = true;
        }
    }

    /**
     * @return Option<Node\Stmt\Return_>
     */
    public function getReturn(): Option
    {
        return Option::fromNullable($this->return);
    }

    /**
     * @return Option<array<string, Node>>
     */
    public function getFieldNodes(): Option
    {
        return Option::do(function() {
            $return_stmt = yield $this->getReturn();
            $fields_array = yield proveOf($return_stmt->expr, Node\Expr\Array_::class);

            $nodes = [];

            foreach ($fields_array->items as $item) {
                $array_item = yield proveOf($item, Node\Expr\ArrayItem::class);
                $key = yield proveOf($array_item->key, Node\Scalar\String_::class);

                $nodes[$key->value] = $array_item;
            }

            return $nodes;
        });
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }
}
