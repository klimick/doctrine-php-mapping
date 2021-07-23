<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Dsl;

use Klimick\DoctrinePhpMapping\Factory\InverseSideFactory;

/**
 * @template TMappedBy of non-empty-literal-string
 *
 * @param TMappedBy $mappedBy
 * @return InverseSideFactory<TMappedBy>
 */
function inverseSide(string $mappedBy): InverseSideFactory
{
    return new InverseSideFactory($mappedBy);
}
