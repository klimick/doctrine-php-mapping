<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Dsl;

use Klimick\DoctrinePhpMapping\Factory\OwningSideFactory;

/**
 * @template TInversedBy of non-empty-literal-string
 *
 * @param TInversedBy $inversedBy
 * @return OwningSideFactory<TInversedBy>
 */
function owningSide(string $inversedBy): OwningSideFactory
{
    return new OwningSideFactory($inversedBy);
}
