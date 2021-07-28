<?php

declare(strict_types=1);

namespace Klimick\PsalmDoctrinePhpMapping\Helper;

use Fp\Functional\Option\Option;
use Psalm\Type;
use function Fp\Cast\asList;
use function Fp\Collection\first;
use function Fp\Collection\firstOf;
use function Fp\Evidence\proveTrue;

final class GetSingleAtomic
{
    /**
     * @template TAtomic of Type\Atomic
     *
     * @param Type\Union $union
     * @param class-string<TAtomic> $ofType
     * @return Option<TAtomic>
     */
    public static function forOf(Type\Union $union, string $ofType): Option
    {
        return Option::do(function() use ($union, $ofType) {
            $atomics = asList($union->getAtomicTypes());
            yield proveTrue(1 === count($atomics));

            return yield firstOf($atomics, $ofType, invariant: true);
        });
    }

    /**
     * @param Type\Union $union
     * @return Option<Type\Atomic>
     */
    public static function for(Type\Union $union): Option
    {
        return Option::do(function() use ($union) {
            $atomics = asList($union->getAtomicTypes());
            yield proveTrue(1 === count($atomics));

            return yield first($atomics);
        });
    }
}
