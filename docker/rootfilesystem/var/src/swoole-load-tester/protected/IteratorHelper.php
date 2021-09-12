<?php
declare(strict_types=1);

namespace app;

/**
 * @link https://gist.github.com/PatchRanger/6854629cff6ac8d123226aef81164c6d
 */
class IteratorHelper
{
    public static function iteratorChunk(\Iterator $i, int $size = 100): \Generator
    {
        for ($i = static::remaining($i); $i->valid(); $i = static::remaining($i)) {
            yield new \LimitIterator($i, 0, $size);
        }
    }

    /**
     * Converts iterator (whether started or not) to new (not started) generator.
     * It is required because "foreach" uses "rewind" internally - which breaks any started generator.
     *
     * @link https://stackoverflow.com/a/40352724
     */
    public static function remaining(\Iterator $i): \Generator
    {
        // Check is required as "yield from" will break for closed iterators.
        if ($i->valid()) {
            yield from $i;
        }
    }
}
