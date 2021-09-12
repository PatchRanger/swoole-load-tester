<?php
declare(strict_types=1);

namespace app\dto;

use http\Client\Response;

class LoadTestResultDto extends \ArrayObject
{
    /** @var string */
    public $host;

    /** @var int */
    public $tryCount = 0;

    /** @var Response */
    public $response;
}
