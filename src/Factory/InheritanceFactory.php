<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Factory;

use Klimick\DoctrinePhpMapping\Field\Field;
use Klimick\DoctrinePhpMapping\Mapping\Inheritance\Inheritance;

/**
 * @psalm-immutable
 */
final class InheritanceFactory
{
    /**
     * @psalm-param Inheritance::TYPE_* $type
     */
    public function __construct(private string $type)
    {
    }

    /**
     * @template TPhpType of string
     * @template TDatabaseType of string
     * @template TOptions of array<string, mixed>
     *
     * @param non-empty-literal-string $name
     * @param Field<TPhpType, TDatabaseType, false, TOptions> $type
     * @return InheritanceMapFactory<TPhpType, TDatabaseType>
     */
    public function discriminator(string $name, Field $type): InheritanceMapFactory
    {
        return new InheritanceMapFactory($this->type, $type, $name);
    }
}
