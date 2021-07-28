<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\InverseSide;

use Klimick\DoctrinePhpMapping\Field\Common\FetchTrait;
use Klimick\DoctrinePhpMapping\Field\Common\OrderByTrait;
use Klimick\DoctrinePhpMapping\Mapping\EntityMapping;
use Klimick\DoctrinePhpMapping\Field\Common\IndexByTrait;

/**
 * @template-covariant TEntity of object
 * @template-covariant TMappedBy of non-empty-literal-string
 * @psalm-immutable
 */
final class ManyToManyField
{
    use FetchTrait;
    use IndexByTrait;
    use OrderByTrait;

    /**
     * @param class-string<EntityMapping<TEntity>> $mapping
     * @param TMappedBy $mappedBy
     */
    public function __construct(public string $mapping, public string $mappedBy)
    {
    }
}
