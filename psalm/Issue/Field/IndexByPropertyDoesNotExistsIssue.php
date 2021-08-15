<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Issue\Field;

use Psalm\CodeLocation;
use Psalm\Issue\CodeIssue;

final class IndexByPropertyDoesNotExistsIssue extends CodeIssue
{
    public function __construct(string $class, string $property, CodeLocation $location)
    {
        parent::__construct(
            message: "IndexBy property '{$property}' does not exists in '{$class}'",
            code_location: $location
        );
    }
}
