<?php
declare(strict_types=1);

namespace app;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;

class LoadTester
{
    public static function testLoadBySearchString(string $q): array
    {
        $client = new Client([
            'timeout'  => 30.0,
        ]);

        $yandexResponse = (string)$client->get('https://yandex.ru/search/touch/', ['query' => [
            'service' => 'www.yandex', 'ui' => 'webmobileapp.yandex', 'numdoc' => 50, 'lr' => 213, 'p' => 0,
            'text' => $q,
        ]])->getBody();

        $urls = YandexSerpParser::parseYandexSerp($yandexResponse);
        $hosts = new UniqueHostIterator($urls);

        $resultsIterator = new UntilTimeoutIterator(new LoadTestResultMultiGenerator($client, $hosts), 25);

        // <++
        $i = 0;
        $result = null;
        foreach ($resultsIterator as $result) {
            /*if ($i > 2) {
                throw new \Exception(print_r($result, true));
            }*/
            $i += 1;
        }
        // ++>

        return $result;
    }
}
