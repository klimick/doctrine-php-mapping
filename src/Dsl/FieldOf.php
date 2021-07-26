<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Dsl;

use Doctrine\DBAL\Types\Type;
use Klimick\DoctrinePhpMapping\Field\Field;

/**
 * @template TPhpType
 * @template TDatabaseType
 * @template TOptions of array<string, mixed>
 *
 * @param class-string<Type<TPhpType, TDatabaseType, TOptions>> $type
 * @return Field<TPhpType, TDatabaseType, false, TOptions>
 */
function fieldOf(string $type): Field
{
    return new Field($type);
}
