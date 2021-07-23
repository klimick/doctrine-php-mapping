<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field;

use Klimick\DoctrinePhpMapping\EntityMapping;
use Klimick\DoctrinePhpMapping\Field\Common\CascadeTrait;
use Klimick\DoctrinePhpMapping\Field\Common\FetchTrait;
use Klimick\DoctrinePhpMapping\Field\Common\JoinColumn;
use Klimick\DoctrinePhpMapping\Field\Common\JoinColumnTrait;

/**
 * @template TEntity of object
 * @template TNullable of bool
 * @psalm-immutable
 */
final class ManyToOneField
{
    use FetchTrait;
    use CascadeTrait;
    use JoinColumnTrait;

    /**
     * @param class-string<EntityMapping<TEntity>> $mapping
     * @param TNullable $nullable
     */
    public function __construct(public string $mapping, bool $nullable = false)
    {
        $this->joinColumn = new JoinColumn($nullable);
    }

    /**
     * @return ManyToOneField<TEntity, true>
     */
    public function nullable(): self
    {
        $self = clone $this;
        $self->joinColumn = $this->joinColumn->nullable();

        /** @var ManyToOneField<TEntity, true> */
        return $self;
    }
}
