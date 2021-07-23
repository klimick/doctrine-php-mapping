<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\OwningSide;

use Klimick\DoctrinePhpMapping\EntityMapping;
use Klimick\DoctrinePhpMapping\Field\Common\IndexByTrait;
use Klimick\DoctrinePhpMapping\Field\Common\JoinTableTrait;

/**
 * @template TEntity of object
 * @template TInversedBy of non-empty-literal-string
 * @psalm-immutable
 */
final class ManyToManyField
{
    use IndexByTrait;
    use JoinTableTrait;

    /**
     * @param class-string<EntityMapping<TEntity>> $mapping
     * @param TInversedBy $inversedBy
     */
    public function __construct(public string $mapping, public string $inversedBy)
    {
    }
}
