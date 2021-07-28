<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field;

use Klimick\DoctrinePhpMapping\Mapping\EntityMapping;
use Klimick\DoctrinePhpMapping\Field\Common\CascadeTrait;
use Klimick\DoctrinePhpMapping\Field\Common\FetchTrait;
use Klimick\DoctrinePhpMapping\Field\Common\JoinColumnTrait;
use Klimick\DoctrinePhpMapping\Field\Common\OrphanRemovalTrait;

/**
 * @template-covariant TEntity of object
 * @template-covariant TNullable of bool
 * @psalm-immutable
 */
final class OneToOneField
{
    use FetchTrait;
    use CascadeTrait;
    use JoinColumnTrait;
    use OrphanRemovalTrait;

    /**
     * @param class-string<EntityMapping<TEntity>> $mapping
     * @param TNullable $nullable
     */
    public function __construct(public string $mapping, public bool $nullable = false)
    {
    }

    /**
     * @return OneToOneField<TEntity, true>
     */
    public function nullable(): self
    {
        return new self($this->mapping, nullable: true);
    }
}
