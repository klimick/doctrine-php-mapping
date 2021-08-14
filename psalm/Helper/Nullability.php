<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Helper;

use Fp\Functional\Option\Option;
use Psalm\Type;

final class Nullability
{
    /**
     * @return Option<bool>
     */
    public static function fromTypelevel(Type\Union $isNullable): Option
    {
        return GetSingleAtomic::for($isNullable)
            ->flatMap(fn($atomic) => match ($atomic::class) {
                Type\Atomic\TTrue::class => Option::some(true),
                Type\Atomic\TFalse::class => Option::some(false),
                default => Option::none(),
            });
    }

    public static function makeNullableIf(bool $nullable, Type\Atomic $type): Type\Union
    {
        return $nullable
            ? new Type\Union([new Type\Atomic\TNull(), $type])
            : new Type\Union([$type]);
    }
}
