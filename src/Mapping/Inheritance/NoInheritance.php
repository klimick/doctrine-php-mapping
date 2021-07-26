<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Mapping\Inheritance;

/**
 * @psalm-immutable
 */
final class NoInheritance implements InheritanceInterface
{
    private function __construct()
    {
    }

    public static function instance(): self
    {
        /** @var NoInheritance|null $self */
        static $self = null;

        if (null === $self) {
            $self = new self();
        }

        return $self;
    }
}
