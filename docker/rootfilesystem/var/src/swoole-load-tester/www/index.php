<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

return static function (string $q) {
    return json_encode(app\LoadTester::testLoadBySearchString($q));
};
