<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Issue\Field;

use Psalm\CodeLocation;
use Psalm\Issue\CodeIssue;
use Psalm\Type\Union;

final class IndexByPropertyTypeIsNotAllowedIssue extends CodeIssue
{
    public function __construct(string $class, string $property, Union $actual_type, CodeLocation $location)
    {
        parent::__construct(
            message: implode(' ', [
                "IndexBy property '{$property}' in '{$class}' typed with invalid type {$actual_type->getId()}.",
                "Allowed types: int | string."
            ]),
            code_location: $location
        );
    }
}
