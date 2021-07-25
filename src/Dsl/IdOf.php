<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Dsl;

use Doctrine\DBAL\Types\Type;
use Klimick\DoctrinePhpMapping\Field\IdField;

/**
 * @template TPhpType
 * @template TDatabaseType
 *
 * @param class-string<Type<TPhpType, TDatabaseType>> $type
 * @return IdField<TPhpType, TDatabaseType>
 */
function idOf(string $type): IdField
{
    return new IdField($type);
}
