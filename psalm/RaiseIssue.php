<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping;

use Psalm\StatementsSource;

final class RaiseIssue
{
    public static function for(StatementsSource $source): IssueFactory
    {
        return new IssueFactory($source);
    }
}
