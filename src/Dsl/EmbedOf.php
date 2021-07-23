<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Dsl;

use Klimick\DoctrinePhpMapping\EmbeddedMapping;
use Klimick\DoctrinePhpMapping\Field\EmbedField;

/**
 * @template TEmbedded of object
 *
 * @param class-string<EmbeddedMapping<TEmbedded>> $embedded
 * @return EmbedField<TEmbedded>
 */
function embedOf(string $embedded): EmbedField
{
    return new EmbedField($embedded);
}
