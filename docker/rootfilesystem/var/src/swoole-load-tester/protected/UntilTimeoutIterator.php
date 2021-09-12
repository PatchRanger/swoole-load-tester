<?php
declare(strict_types=1);

namespace app;

use Traversable;

class UntilTimeoutIterator extends \IteratorIterator
{
    /** @var int */
    private $timestamp;
    /** @var int */
    private $timeoutInSec;

    public function __construct(Traversable $iterator, int $timeoutInSec)
    {
        $this->timestamp = time();
        $this->timeoutInSec = $timeoutInSec;
        parent::__construct($iterator);
    }

    public function valid()
    {
        if (time() >= $this->timestamp + $this->timeoutInSec) {
            return false;
        }
        return parent::valid();
    }
}
