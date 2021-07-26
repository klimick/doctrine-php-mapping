<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\Common;

/**
 * @psalm-type OrderByValues = non-empty-array<non-empty-literal-string, OrderBy::*>
 * @psalm-immutable
 */
trait OrderByTrait
{
    /** @var OrderByValues */
    public null|array $orderBy = null;

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
