<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\Common;

/**
 * @psalm-immutable
 */
trait JoinColumnTrait
{
    public null|JoinColumn $joinColumn = null;

    /**
     * @psalm-param non-empty-literal-string $name
     * @psalm-param non-empty-literal-string $referencedColumnName
     * @psalm-param JoinColumn::ON_DELETE_* $onDelete
     */
    public function joinColumn(
        string $name,
        string $referencedColumnName = 'id',
        bool   $unique = false,
        string $onDelete = JoinColumn::ON_DELETE_NO_ACTION,
    ): static
    {
        $self = clone $this;
        $self->joinColumn = new JoinColumn($name, $referencedColumnName, $unique, $onDelete);

        return $self;
    }
}
