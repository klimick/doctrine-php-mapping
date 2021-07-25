<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field\Common;

/**
 * @psalm-immutable
 */
final class OrderBy
{
    public const ASC = 'ASC';
    public const DESC = 'DESC';

    private function __construct()
    {
    }
}
