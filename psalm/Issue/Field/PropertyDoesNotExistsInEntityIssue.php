<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Issue\Field;

use Psalm\CodeLocation;
use Psalm\Issue\CodeIssue;

final class PropertyDoesNotExistsInEntityIssue extends CodeIssue
{
    public function __construct(string $class, string $property, CodeLocation $location)
    {
        parent::__construct(
            message: "Entity '{$class}' has no property '{$property}'",
            code_location: $location
        );
    }
}
