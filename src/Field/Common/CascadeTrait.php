<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\Common;

/**
 * @psalm-immutable
 */
trait CascadeTrait
{
    /**
     * @var list<string>
     */
    public array $cascade = [];

    public function cascadePersist(): static
    {
        $self = clone $this;
        $self->cascade = [...$self->cascade, 'persist'];

        return $self;
    }

    public function cascadeRemove(): static
    {
        $self = clone $this;
        $self->cascade = [...$self->cascade, 'remove'];

        return $self;
    }

    public function cascadeDetach(): static
    {
        $self = clone $this;
        $self->cascade = [...$self->cascade, 'detach'];

        return $self;
    }

    public function cascadeMerge(): static
    {
        $self = clone $this;
        $self->cascade = [...$self->cascade, 'merge'];

        return $self;
    }
}
