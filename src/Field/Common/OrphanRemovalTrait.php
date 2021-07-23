<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\Common;

/**
 * @psalm-immutable
 */
trait OrphanRemovalTrait
{
    public bool $orphanRemoval = false;

    public function orphanRemoval(): static
    {
        $self = clone $this;
        $self->orphanRemoval = true;

        return $self;
    }
}
