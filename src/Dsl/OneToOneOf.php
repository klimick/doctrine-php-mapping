<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Dsl;

use Klimick\DoctrinePhpMapping\EntityMapping;
use Klimick\DoctrinePhpMapping\Field\OneToOneField;

/**
 * @template TEntity of object
 *
 * @param class-string<EntityMapping<TEntity>> $mapping
 * @return OneToOneField<TEntity, false>
 */
function oneToOneOf(string $mapping): OneToOneField
{
    return new OneToOneField($mapping);
}
