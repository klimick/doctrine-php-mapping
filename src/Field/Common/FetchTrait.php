<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\Common;

/**
 * @psalm-immutable
 */
trait FetchTrait
{
    public string $fetch = 'LAZY';

    public function fetchLazy(): static
    {
        $self = clone $this;
        $self->fetch = 'LAZY';

        return $self;
    }

    public function fetchEager(): static
    {
        $self = clone $this;
        $self->fetch = 'EAGER';

        return $self;
    }

    public function fetchExtraLazy(): static
    {
        $self = clone $this;
        $self->fetch = 'EXTRA_LAZY';

        return $self;
    }
}
