<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\Common;

/**
 * @psalm-immutable
 */
trait JoinTableTrait
{
    /** @var null|non-empty-literal-string */
    public null|string $joinTable = null;

    /** @var list<JoinColumn> */
    public array $joinColumns = [];

    /** @var list<JoinColumn> */
    public array $inverseJoinColumns = [];

    /**
     * @param non-empty-literal-string $value
     */
    public function joinTable(string $value): static
    {
        $self = clone $this;
        $self->joinTable = $value;

        return $self;
    }

    /**
     * @param non-empty-list<JoinColumn> $value
     */
    public function joinColumns(array $value): static
    {
        $self = clone $this;
        $self->joinColumns = $value;

        return $self;
    }

    /**
     * @param non-empty-list<JoinColumn> $value
     */
    public function inverseJoinColumns(array $value): static
    {
        $self = clone $this;
        $self->inverseJoinColumns = $value;

        return $self;
    }
}
