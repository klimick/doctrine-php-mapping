<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\InverseSide;

use Klimick\DoctrinePhpMapping\Mapping\EntityMapping;
use Klimick\DoctrinePhpMapping\Field\Common\CascadeTrait;
use Klimick\DoctrinePhpMapping\Field\Common\IndexByTrait;
use Klimick\DoctrinePhpMapping\Field\Common\JoinTableTrait;

/**
 * @template TEntity of object
 * @template TMappedBy of non-empty-literal-string
 * @psalm-immutable
 */
final class ManyToManyField
{
    /**
     * @param class-string<EntityMapping<TEntity>> $mapping
     * @param TMappedBy $mappedBy
     */
    public function __construct(public string $mapping, public string $mappedBy)
    {
    }
}
