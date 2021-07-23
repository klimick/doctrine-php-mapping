<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field;

use Doctrine\DBAL\Types\Type;

/**
 * @template TPhpType
 * @template TDatabaseType
 * @template TNullable of bool
 *
 * @psalm-immutable
 */
final class Field
{
    public bool $nullable = false;
    public bool $unique = false;

    /** @var array<string, mixed> */
    public array $options = [];

    /**
     * @param class-string<Type<TPhpType, TDatabaseType>> $type
     * @param TNullable $nullable
     */
    public function __construct(public string $type, bool $nullable = false)
    {
        $this->nullable = $nullable;
    }

    /**
     * @return Field<TPhpType, TDatabaseType, true>
     */
    public function nullable(): self
    {
        $self = new self($this->type, true);
        $self->unique = $this->unique;
        $self->options = $this->options;

        return $self;
    }

    /**
     * @return Field<TPhpType, TDatabaseType, TNullable>
     */
    public function unique(): self
    {
        $self = clone $this;
        $self->unique = true;

        return $self;
    }

    /**
     * @param array<string, mixed> $value
     * @return Field<TPhpType, TDatabaseType, TNullable>
     */
    public function options(array $value): self
    {
        $self = clone $this;
        $self->options = $value;

        return $self;
    }
}
