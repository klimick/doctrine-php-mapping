<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\OwningSide;

use Klimick\DoctrinePhpMapping\EntityMapping;
use Klimick\DoctrinePhpMapping\Field\Common\CascadeTrait;
use Klimick\DoctrinePhpMapping\Field\Common\FetchTrait;
use Klimick\DoctrinePhpMapping\Field\Common\JoinColumn;
use Klimick\DoctrinePhpMapping\Field\Common\JoinColumnTrait;
use Klimick\DoctrinePhpMapping\Field\Common\OrphanRemovalTrait;

/**
 * @template TEntity of object
 * @template TInversedBy of non-empty-literal-string
 * @template TNullable of bool
 * @psalm-immutable
 */
final class OneToOneField
{
    use FetchTrait;
    use CascadeTrait;
    use JoinColumnTrait;

    /**
     * @param class-string<EntityMapping<TEntity>> $mapping
     * @param TInversedBy $inversedBy
     * @param TNullable $nullable
     */
    public function __construct(public string $mapping, public string $inversedBy, bool $nullable = false)
    {
        $this->joinColumn = new JoinColumn($nullable);
    }

    /**
     * @return OneToOneField<TEntity, TInversedBy, true>
     */
    public function nullable(): self
    {
        $self = clone $this;
        $self->joinColumn = $this->joinColumn->nullable();

        /** @var OneToOneField<TEntity, TInversedBy, true> */
        return $self;
    }
}