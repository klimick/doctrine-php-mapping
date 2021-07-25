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
     * @param non-empty-literal-string $name
     * @param list<JoinColumn> $joinColumns
     * @param list<JoinColumn> $inverseJoinColumns
     */
    public function joinTable(string $name, array $joinColumns = [], array $inverseJoinColumns = []): static
    {
        $self = clone $this;
        $self->joinTable = $name;
        $self->joinColumns = $joinColumns;
        $self->inverseJoinColumns = $inverseJoinColumns;

        return $self;
    }
}
