<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Issue\Field;

use PhpParser\Node;
use Psalm\Type;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\StatementsSource;

final class PropertiesIssueFactory
{
    public function __construct(private StatementsSource $source)
    {
    }

    public function indexByPropertyDoesNotExists(Node $node, string $class, string $property): void
    {
        $location = new CodeLocation($this->source, $node);
        $issue = new IndexByPropertyDoesNotExistsIssue($class, $property, $location);

        IssueBuffer::accepts($issue, $this->source->getSuppressedIssues());
    }

    public function indexByPropertyTypeIsNotAllowed(Node $node, string $class, string $property, Type\Union $actual_type): void
    {
        $location = new CodeLocation($this->source, $node);
        $issue = new IndexByPropertyTypeIsNotAllowedIssue($class, $property, $actual_type, $location);

        IssueBuffer::accepts($issue, $this->source->getSuppressedIssues());
    }

    public function propertyDoesNotExistInEntity(Node $node, string $class, string $property): void
    {
        $location = new CodeLocation($this->source, $node);
        $issue = new PropertyDoesNotExistsInEntityIssue($class, $property, $location);

        IssueBuffer::accepts($issue, $this->source->getSuppressedIssues());
    }

    public function propertyTypeMismatchIssue(Node $node, string $class, string $property, Type\Union $in_mapping, Type\Union $in_entity): void
    {
        $location = new CodeLocation($this->source, $node);
        $issue = new PropertyTypeMismatchIssue($class, $property, $in_mapping, $in_entity, $location);

        IssueBuffer::accepts($issue, $this->source->getSuppressedIssues());
    }
}
