<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field;

use Doctrine\DBAL\Types\Type;
use Klimick\DoctrinePhpMapping\Field\Common\ColumnTrait;

/**
 * @template TPhpType
 * @template TDatabaseType
 *
 * @psalm-immutable
 */
final class IdField
{
    use ColumnTrait;

    /** @var array<string, mixed> */
    public array $options = [];

    /**
     * @param class-string<Type<TPhpType, TDatabaseType>> $type
     */
    public function __construct(public string $type)
    {
    }

    /**
     * @param array<string, mixed> $value
     * @return IdField<TPhpType, TDatabaseType>
     */
    public function options(array $value): self
    {
        $self = clone $this;
        $self->options = $value;

        return $self;
    }
}
