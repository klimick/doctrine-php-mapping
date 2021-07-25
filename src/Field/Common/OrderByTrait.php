<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\Common;

/**
 * @psalm-type OrderByValues = array<non-empty-literal-string, OrderBy::*>
 * @psalm-immutable
 */
trait OrderByTrait
{
    public array $orderBy = [];

    /**
     * @param OrderByValues $value
     * @return $this
     */
    public function orderBy(array $value): self
    {
        $self = clone $this;
        $self->orderBy = $value;

        return $self;
    }
}
