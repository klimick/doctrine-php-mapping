<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Mapping\Inheritance;

use Klimick\DoctrinePhpMapping\Field\Field;
use Klimick\DoctrinePhpMapping\Mapping\EntityMapping;

/**
 * @template TPhpType
 * @template TDatabaseType
 * @psalm-immutable
 */
final class Inheritance implements InheritanceInterface
{
    public const TYPE_SINGLE_TABLE = 'SINGLE_TABLE';
    public const TYPE_JOINED = 'JOINED';

    /**
     * @psalm-param Inheritance::TYPE_* $inheritanceType
     * @param Field<TPhpType, TDatabaseType, false, array<string, mixed>> $discriminator
     * @param non-empty-literal-string $discriminatorName
     * @param non-empty-array<non-empty-literal-string, class-string<EntityMapping>> $discriminatorMap
     */
    public function __construct(
        public string $inheritanceType,
        public Field $discriminator,
        public string $discriminatorName,
        public array $discriminatorMap
    ) {}
}
