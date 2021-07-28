<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Hook;

use PhpParser\Node;
use Psalm\Type\Union;

final class EntityProperty
{
    public function __construct(
        public string $name,
        public Node $node,
        public Union $type,
    ) {}
}
