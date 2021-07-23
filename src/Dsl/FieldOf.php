<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Dsl;

use Doctrine\DBAL\Types\Type;
use Klimick\DoctrinePhpMapping\Field\Field;

/**
 * @template TPhpType
 * @template TDatabaseType
 *
 * @param class-string<Type<TPhpType, TDatabaseType>> $type
 * @return Field<TPhpType, TDatabaseType, false>
 */
function fieldOf(string $type): Field
{
    return new Field($type);
}
