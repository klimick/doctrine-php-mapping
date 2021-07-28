<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Issue;

use Psalm\StatementsSource;

final class RaiseIssue
{
    public static function for(StatementsSource $source): IssueAccept
    {
        return new IssueAccept($source);
    }
}
