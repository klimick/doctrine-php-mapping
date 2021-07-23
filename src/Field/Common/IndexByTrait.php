<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\Common;

/**
 * @psalm-immutable
 */
trait IndexByTrait
{
    public null|string $indexBy = null;

    /**
     * @param non-empty-string $value
     */
    public function indexBy(string $value): static
    {
        $self = clone $this;
        $self->indexBy = $value;

        return $self;
    }
}
