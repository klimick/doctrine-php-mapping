<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field;

use Doctrine\DBAL\Types\Type;
use Klimick\DoctrinePhpMapping\Field\Common\ColumnTrait;

/**
 * @template TPhpType
 * @template TDatabaseType
 * @template TOptions of array<string, mixed>
 *
 * @psalm-immutable
 */
final class IdField
{
    use ColumnTrait;

    /** @var null|TOptions */
    public ?array $options = null;

    /**
     * @param class-string<Type<TPhpType, TDatabaseType, TOptions>> $type
     */
    public function __construct(public string $type)
    {
    }

    /**
     * @param TOptions $value
     * @return IdField<TPhpType, TDatabaseType, TOptions>
     */
    public function options(array $value): self
    {
        $self = clone $this;
        $self->options = $value;

        return $self;
    }
}
