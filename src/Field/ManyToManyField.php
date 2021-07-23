<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field;

use Klimick\DoctrinePhpMapping\EntityMapping;
use Klimick\DoctrinePhpMapping\Field\Common\CascadeTrait;
use Klimick\DoctrinePhpMapping\Field\Common\FetchTrait;
use Klimick\DoctrinePhpMapping\Field\Common\IndexByTrait;
use Klimick\DoctrinePhpMapping\Field\Common\JoinTableTrait;
use Klimick\DoctrinePhpMapping\Field\Common\OrphanRemovalTrait;

/**
 * @template TEntity of object
 * @psalm-immutable
 */
final class ManyToManyField
{
    use FetchTrait;
    use IndexByTrait;
    use CascadeTrait;
    use JoinTableTrait;
    use OrphanRemovalTrait;

    /**
     * @param class-string<EntityMapping<TEntity>> $mapping
     */
    public function __construct(public string $mapping)
    {
    }
}