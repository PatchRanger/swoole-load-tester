#!/usr/bin/env php
<?php

declare(strict_types=1);

$http = new Swoole\Http\Server("0.0.0.0", 9501);
$http->on(
    "request",
    function (Swoole\Http\Request $request, Swoole\Http\Response $response) {
        try {
            $q = $request->get['search'] ?? '';
            if (strlen($q) < 3) {
                throw new \InvalidArgumentException('Too short search query');
            }
            $result = (require __DIR__ . '/../src/swoole-load-tester/www/index.php')($q);
        } catch (\InvalidArgumentException $e) {
            $response->status(400, $e->getMessage());
            $result = $e->getMessage();
        }
        $response->end(
            <<<EOT
                <pre>
                $result
                </pre>
            EOT
        );
    }
);
$http->start();
