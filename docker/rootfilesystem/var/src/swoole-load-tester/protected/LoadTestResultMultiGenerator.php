<?php
declare(strict_types=1);

namespace app;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Response;

class LoadTestResultMultiGenerator extends \MultipleIterator
{
    /** @var bool[] */
    private $isTestFinished;

    public function __construct(Client $client, iterable $hosts)
    {
        parent::__construct(self::MIT_NEED_ANY | self::MIT_KEYS_ASSOC);
        foreach ($hosts as $host) {
            $this->attachIterator($this->loadTestPromisesGenerator($client, $host), $host);
        }
    }

    public function current()
    {
        $promisesFlat = [];
        foreach (parent::current() as $host => $promisesByHost) {
            foreach ((array)$promisesByHost as $index => $promise) {
                $key = implode('|', [$host, $index]);
                $promisesFlat[$key] = $promise;
            }
        }

        $responses = Utils::inspectAll($promisesFlat);
        unset($promisesFlat);

        $responsesByHost = [];
        foreach ($responses as $key => $response) {
            [$host, $index] = explode('|', $key);
            $responsesByHost[$host][$index] = $response;
        }
        unset($responses);

        $result = [];
        foreach ($responsesByHost as $host => $responses) {
            $states = array_column($responses, 'state');
            if (array_count_values($states)[PromiseInterface::REJECTED] ?? 0) {
                $this->isTestFinished[$host] = true;
            }

            $result[$host] = count($responses) - 1;
        }
        unset($responsesByHost);

        return $result;
    }

    private function loadTestPromisesGenerator(Client $client, string $host): \Generator
    {
        for ($i=1; !($this->isTestFinished[$host] ?? false); $i++) {
            yield $this->loadTest($client, $host, $i);
        }
    }

    /**
     * @param Client $client
     * @param string $host
     * @param int $threadsCount
     * @return Promise[]
     */
    private function loadTest(Client $client, string $host, int $threadsCount = 1): array
    {
        $promises = [];
        for ($i=0; $i<$threadsCount; $i++) {
            $promises[] = $client->getAsync($host, ['timeout' => 5.0])->then(function (Response $response) { return $response->getStatusCode(); });
        }
        return $promises;
    }
}
