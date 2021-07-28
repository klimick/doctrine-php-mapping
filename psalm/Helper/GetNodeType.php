<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Helper;

use Fp\Functional\Option\Option;
use PhpParser\Node;
use Psalm\Plugin\EventHandler\Event\AfterFunctionLikeAnalysisEvent;
use Psalm\Type;

/**
 * @psalm-type NodeWithType
 *     = Node\Expr
 *     | Node\Name
 *     | Node\Stmt\Return_
 */
final class GetNodeType
{
    /**
     * @param NodeWithType $node
     * @return Option<Type\Union>
     */
    public static function for(Node $node, AfterFunctionLikeAnalysisEvent $from): Option
    {
        return Option::fromNullable($from->getNodeTypeProvider()->getType($node));
    }
}
