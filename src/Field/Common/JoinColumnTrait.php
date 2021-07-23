<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\Common;

/**
 * @psalm-immutable
 */
trait JoinColumnTrait
{
    public JoinColumn $joinColumn;

    public function joinColumn(JoinColumn $joinColumn): self
    {
        $self = clone $this;
        $self->joinColumn = $joinColumn;

        return $self;
    }
}
