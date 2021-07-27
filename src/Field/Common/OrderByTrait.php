<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\Common;

/**
 * @psalm-type OrderByValues = non-empty-array<non-empty-literal-string, OrderBy::*>
 * @psalm-immutable
 */
trait OrderByTrait
{
    /** @var null|OrderByValues */
    public null|array $orderBy = null;

    /**
     * @param OrderByValues $value
     */
    public function orderBy(array $value): static
    {
        $self = clone $this;
        $self->orderBy = $value;

        return $self;
    }
}
