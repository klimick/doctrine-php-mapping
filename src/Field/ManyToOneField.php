<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field;

use Klimick\DoctrinePhpMapping\Mapping\EntityMapping;
use Klimick\DoctrinePhpMapping\Field\Common\FetchTrait;
use Klimick\DoctrinePhpMapping\Field\Common\JoinColumnTrait;

/**
 * @template-covariant TEntity of object
 * @template-covariant TNullable of bool
 * @psalm-immutable
 */
final class ManyToOneField
{
    use FetchTrait;
    use JoinColumnTrait;

    /**
     * @param class-string<EntityMapping<TEntity>> $mapping
     * @param TNullable $nullable
     */
    public function __construct(public string $mapping, public bool $nullable = false)
    {
    }

    /**
     * @return ManyToOneField<TEntity, true>
     */
    public function nullable(): self
    {
        return new self($this->mapping, nullable: true);
    }
}
