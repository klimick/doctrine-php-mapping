<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\Common;

/**
 * @template TNullable of bool
 * @psalm-immutable
 */
final class JoinColumn
{
    public const ON_DELETE_CASCADE = 'CASCADE';
    public const ON_DELETE_NO_ACTION = 'NO ACTION';
    public const ON_DELETE_SET_NULL = 'SET NULL';

    public ?string $name = null;
    public string $referencedColumnName = 'id';
    public bool $unique = false;
    public bool $nullable;
    /** @psalm-var JoinColumn::ON_DELETE_* */
    public string $onDelete = self::ON_DELETE_NO_ACTION;

    public function __construct(bool $nullable = false)
    {
        $this->nullable = $nullable;
    }

    public function referencedColumnName(string $value): self
    {
        $self = clone $this;
        $self->referencedColumnName = $value;

        return $this;
    }

    public function unique(): self
    {
        $self = clone $this;
        $self->unique = true;

        return $this;
    }

    /**
     * @internal
     */
    public function nullable(): self
    {
        $self = clone $this;
        $self->nullable = true;

        return $this;
    }

    /**
     * @psalm-param JoinColumn::ON_DELETE_* $value
     */
    public function onDelete(string $value): self
    {
        $self = clone $this;
        $self->onDelete = $value;

        return $self;
    }
}
