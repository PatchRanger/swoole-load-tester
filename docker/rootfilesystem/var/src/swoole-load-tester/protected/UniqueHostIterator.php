<?php
declare(strict_types=1);

namespace app;

use Iterator;

class UniqueHostIterator extends \IteratorIterator
{
    public function __construct(Iterator $iterator)
    {
        $hostsIterator = self::hostsByUrls($iterator);
        $uniqueHostsIterator = self::uniqueHosts($hostsIterator);

        parent::__construct($uniqueHostsIterator);
    }

    private static function hostsByUrls(iterable $urls): \Generator
    {
        foreach ($urls as $url) {
            yield strtolower(parse_url($url)['host']);
        }
    }

    private static function uniqueHosts(\Iterator $iterator): \Generator
    {
        yield from array_unique(iterator_to_array($iterator));
    }
}
