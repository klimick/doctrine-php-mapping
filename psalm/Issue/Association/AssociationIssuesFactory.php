<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Issue\Association;

use PhpParser\Node;
use Psalm\CodeLocation;
use Psalm\IssueBuffer;
use Psalm\StatementsSource;

final class AssociationIssuesFactory
{
    public function __construct(private StatementsSource $source)
    {
    }

    public function noAssociationFieldAtOwningSide(Node $node, string $field, string $mapping): void
    {
        $location = new CodeLocation($this->source, $node);
        $issue = new NoAssociationFieldAtOwningSideIssue($location, $field, $mapping);

        IssueBuffer::accepts($issue, $this->source->getSuppressedIssues());
    }

    public function noAssociationFieldAtInverseSide(Node $node, string $field, string $mapping): void
    {
        $location = new CodeLocation($this->source, $node);
        $issue = new NoAssociationFieldAtInverseSideIssue($location, $field, $mapping);

        IssueBuffer::accepts($issue, $this->source->getSuppressedIssues());
    }

    public function invalidAssociationAtOwningSide(Node $node, string $field, string $mapping): void
    {
        $location = new CodeLocation($this->source, $node);
        $issue = new InvalidAssociationAtOwningSideIssue($location, $field, $mapping);

        IssueBuffer::accepts($issue, $this->source->getSuppressedIssues());
    }

    public function invalidAssociationAtInverseSide(Node $node, string $field, string $mapping): void
    {
        $location = new CodeLocation($this->source, $node);
        $issue = new InvalidAssociationAtInverseSideIssue($location, $field, $mapping);

        IssueBuffer::accepts($issue, $this->source->getSuppressedIssues());
    }
}
