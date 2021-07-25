<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\Common;

use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * @psalm-immutable
 */
trait FetchTrait
{
    /** @psalm-var ClassMetadataInfo::FETCH_* */
    public int $fetch = ClassMetadataInfo::FETCH_LAZY;

    public function fetchLazy(): static
    {
        $self = clone $this;
        $self->fetch = ClassMetadataInfo::FETCH_LAZY;

        return $self;
    }

    public function fetchEager(): static
    {
        $self = clone $this;
        $self->fetch = ClassMetadataInfo::FETCH_EAGER;

        return $self;
    }

    public function fetchExtraLazy(): static
    {
        $self = clone $this;
        $self->fetch = ClassMetadataInfo::FETCH_EXTRA_LAZY;

        return $self;
    }
}
