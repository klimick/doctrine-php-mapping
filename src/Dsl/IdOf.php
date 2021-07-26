<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Dsl;

use Doctrine\DBAL\Types\Type;
use Klimick\DoctrinePhpMapping\Field\IdField;

/**
 * @template TPhpType
 * @template TDatabaseType
 * @template TOptions of array<string, mixed>
 *
 * @param class-string<Type<TPhpType, TDatabaseType, TOptions>> $type
 * @return IdField<TPhpType, TDatabaseType, TOptions>
 */
function idOf(string $type): IdField
{
    return new IdField($type);
}
