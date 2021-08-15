<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\Common;

/**
 * @psalm-immutable
 */
trait IndexByTrait
{
    /** @var null|non-empty-literal-string */
    public null|string $indexBy = null;

    /**
     * @param non-empty-literal-string $value
     */
    public function indexBy(string $value): static
    {
        $self = clone $this;
        $self->indexBy = $value;

        return $self;
    }
}
