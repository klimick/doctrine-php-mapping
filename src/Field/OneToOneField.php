<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field;

use Klimick\DoctrinePhpMapping\EntityMapping;
use Klimick\DoctrinePhpMapping\Field\Common\CascadeTrait;
use Klimick\DoctrinePhpMapping\Field\Common\FetchTrait;
use Klimick\DoctrinePhpMapping\Field\Common\JoinColumn;
use Klimick\DoctrinePhpMapping\Field\Common\JoinColumnTrait;
use Klimick\DoctrinePhpMapping\Field\Common\OrphanRemovalTrait;

/**
 * @template TEntity of object
 * @template TNullable of bool
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
    public function __construct(public string $mapping, bool $nullable = false)
    {
        $this->joinColumn = new JoinColumn($nullable);
    }

    /**
     * @return OneToOneField<TEntity, true>
     */
    public function nullable(): self
    {
        $self = clone $this;
        $self->joinColumn = $this->joinColumn->nullable();

        /** @var OneToOneField<TEntity, true> */
        return $self;
    }
}
