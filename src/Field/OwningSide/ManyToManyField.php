<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\OwningSide;

use Klimick\DoctrinePhpMapping\Field\Common\FetchTrait;
use Klimick\DoctrinePhpMapping\Field\Common\IndexByTrait;
use Klimick\DoctrinePhpMapping\Field\Common\OrderByTrait;
use Klimick\DoctrinePhpMapping\Mapping\EntityMapping;
use Klimick\DoctrinePhpMapping\Field\Common\CascadeTrait;
use Klimick\DoctrinePhpMapping\Field\Common\JoinTableTrait;

/**
 * @template-covariant TEntity of object
 * @template-covariant TInversedBy of non-empty-literal-string
 * @psalm-immutable
 */
final class ManyToManyField
{
    use CascadeTrait;
    use FetchTrait;
    use JoinTableTrait;
    use IndexByTrait;
    use OrderByTrait;

    /**
     * @param class-string<EntityMapping<TEntity>> $mapping
     * @param TInversedBy $inversedBy
     */
    public function __construct(public string $mapping, public string $inversedBy)
    {
    }
}
