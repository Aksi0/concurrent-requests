<?php

use Amp\Artax\DefaultClient;
use Amp\Artax\Request;
use Amp\Artax\Response;
use Amp\Coroutine;
use Amp\Promise;
use Amp\Loop;
use function Amp\call;

/**
 * Class AmpClass
 */
class AmpHttpRequestsClass
{
    /** @var array */
    protected $urls = [];

    /** @var DefaultClient */
    protected $client;

    public function __construct()
    {
        $this->client = new DefaultClient;
        $this->client->setOption(DefaultClient::OP_TRANSFER_TIMEOUT, 15000);
    }

    /**
     * @param string $url
     * @param array|null $postBody
     */
    public function addUrl(string $url, ?array $postBody = null): void
    {
        array_push($this->urls, [
            'url' => $url,
            'body' => $postBody,
        ]);
    }

    public function run()
    {
        $result = [];
        Loop::run(function () use (&$result) {
            try {
                /** @var Coroutine[] $promises */
                $promises = [];
                foreach ($this->urls as $urlInfo) {
                    $promises[$urlInfo['url']] = call($this->getRequestHandler(), $urlInfo['url'],
                        $urlInfo['body']);
                }
                $result = yield Promise\any($promises);
                $result = $this->prepareResult($result);
            } catch (Amp\Artax\HttpException $error) {
                //echo $error;
            }
        });

        return $result;
    }

    protected function prepareResult(array $result)
    {
        $data = [];
        /** @var Exception[] $exceptions */
        $exceptions = array_shift($result);
        $responses = array_shift($result);
        foreach ($responses as $url => $response) {
            $data[$url] = [
                'status' => true,
                'data' => $response,
            ];
        }
        foreach ($exceptions as $url => $error) {
            $data[$url] = [
                'status' => false,
                'data' => $error->getMessage(),
            ];
        }

        return $data;
    }

    /**
     * @return Closure
     */
    protected function getRequestHandler(): Closure
    {
        return function (string $url, ?array $body) {
            $time = time();
            $request = new Request($url, $body ? 'POST' : 'GET');
            if (!empty($body)) {
                $request = $request->withBody(json_encode($body));
            }

            /** @var Response $response */
            $response = yield $this->client->request($request);
            print 'Finished url: ' . $url . ' with time ' . (time() - $time) . ' s' . PHP_EOL;
            return $response->getBody();
        };
    }
}
