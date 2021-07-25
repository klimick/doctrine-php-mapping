<?php

declare(strict_types=1);

namespace Klimick\DoctrinePhpMapping\Field;

use Klimick\DoctrinePhpMapping\Mapping\EmbeddedMapping;

/**
 * @template TEmbedded of object
 * @psalm-immutable
 */
final class EmbedField
{
    public null|string $prefix = null;

    /**
     * @param class-string<EmbeddedMapping<TEmbedded>> $embedded
     */
    public function __construct(public string $embedded)
    {
    }

    public function columnPrefixing(string $prefix): self
    {
        $self = clone $this;
        $self->prefix = $prefix;

        return $this;
    }
}
