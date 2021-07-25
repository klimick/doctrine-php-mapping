<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\Common;

/**
 * @psalm-immutable
 */
trait ColumnTrait
{
    public ?string $column = null;

    public function column(string $value): self
    {
        $self = clone $this;
        $self->column = $value;

        return $self;
    }
}
