<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field;

use Doctrine\DBAL\Types\Type;
use Klimick\DoctrinePhpMapping\Field\Common\ColumnTrait;

/**
 * @template TPhpType
 * @template TDatabaseType
 * @template TNullable of bool
 * @template TOptions of array<string, mixed>
 *
 * @psalm-immutable
 */
final class Field
{
    use ColumnTrait;

    public bool $nullable = false;
    public bool $unique = false;

    /** @var null|TOptions */
    public ?array $options = null;

    /**
     * @param class-string<Type<TPhpType, TDatabaseType, TOptions>> $type
     * @param TNullable $nullable
     */
    public function __construct(public string $type, bool $nullable = false)
    {
        $this->nullable = $nullable;
    }

    /**
     * @return Field<TPhpType, TDatabaseType, true, TOptions>
     */
    public function nullable(): self
    {
        $self = new self($this->type, true);
        $self->unique = $this->unique;
        $self->options = $this->options;

        return $self;
    }

    /**
     * @return Field<TPhpType, TDatabaseType, TNullable, TOptions>
     */
    public function unique(): self
    {
        $self = clone $this;
        $self->unique = true;

        return $self;
    }

    /**
     * @param TOptions $value
     * @return Field<TPhpType, TDatabaseType, TNullable, TOptions>
     */
    public function options(array $value): self
    {
        $self = clone $this;
        $self->options = $value;

        return $self;
    }
}
