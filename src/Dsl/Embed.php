<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Dsl;

use Klimick\DoctrinePhpMapping\Mapping\EmbeddedMapping;
use Klimick\DoctrinePhpMapping\Field\EmbedField;

/**
 * @template TEmbedded of object
 *
 * @param class-string<EmbeddedMapping<TEmbedded>> $embedded
 * @return EmbedField<TEmbedded>
 */
function embed(string $embedded): EmbedField
{
    return new EmbedField($embedded);
}
