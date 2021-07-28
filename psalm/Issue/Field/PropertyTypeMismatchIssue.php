<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Issue\Field;

use Psalm\CodeLocation;
use Psalm\Issue\CodeIssue;
use Psalm\Type;

final class PropertyTypeMismatchIssue extends CodeIssue
{
    public function __construct(string $class, string $property, Type\Union $in_mapping, Type\Union $in_entity, CodeLocation $location)
    {
        parent::__construct(
            message: implode(' ', [
                "Property type '{$property}' of entity '{$class}' is incompatible with mapping.",
                "\nType in mapping: {$in_mapping->getId()}",
                "\nType in entity: {$in_entity->getId()}"
            ]),
            code_location: $location
        );
    }
}
