<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\OwningSide;

use Klimick\DoctrinePhpMapping\Mapping\EntityMapping;
use Klimick\DoctrinePhpMapping\Field\Common\FetchTrait;
use Klimick\DoctrinePhpMapping\Field\Common\JoinColumnTrait;

/**
 * @template-covariant TEntity of object
 * @template-covariant TInversedBy of non-empty-literal-string
 * @template-covariant TNullable of bool
 * @psalm-immutable
 */
final class ManyToOneField
{
    use FetchTrait;
    use JoinColumnTrait;

    /**
     * @param class-string<EntityMapping<TEntity>> $mapping
     * @param TInversedBy $inversedBy
     * @param TNullable $nullable
     */
    public function __construct(public string $mapping, public string $inversedBy, public bool $nullable = false)
    {
    }

    /**
     * @return ManyToOneField<TEntity, TInversedBy, true>
     */
    public function nullable(): self
    {
        return new self($this->mapping, $this->inversedBy, nullable: true);
    }
}
