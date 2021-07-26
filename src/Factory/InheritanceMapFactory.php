<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Factory;

use Klimick\DoctrinePhpMapping\Field\Field;
use Klimick\DoctrinePhpMapping\Mapping\EntityMapping;
use Klimick\DoctrinePhpMapping\Mapping\Inheritance\Inheritance;

/**
 * @template TPhpType
 * @template TDatabaseType
 * @psalm-immutable
 */
final class InheritanceMapFactory
{
    /**
     * @psalm-param Inheritance::TYPE_* $inheritanceType
     * @param Field<TPhpType, TDatabaseType, false, array<string, mixed>> $discriminator
     * @param non-empty-literal-string $discriminatorName
     */
    public function __construct(
        private string $inheritanceType,
        private Field $discriminator,
        private string $discriminatorName,
    ) {}

    /**
     * @param non-empty-array<non-empty-literal-string, class-string<EntityMapping>> $map
     * @return Inheritance
     */
    public function map(array $map): Inheritance
    {
        return new Inheritance($this->inheritanceType, $this->discriminator, $this->discriminatorName, $map);
    }
}
