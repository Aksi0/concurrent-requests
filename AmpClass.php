<?php

use Amp\Artax\DefaultClient;
use Amp\Artax\Request;
use Amp\Artax\Response;
use Amp\Loop;

/**
 * Class AmpClass
 */
class AmpClass
{
    /** @var array */
    protected $urls = [];

    /** @var DefaultClient */
    protected $client;

    protected $result = [];

    public function __construct()
    {
        $this->client = new DefaultClient;
        $this->client->setOption(DefaultClient::OP_TRANSFER_TIMEOUT, 90000);
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
        Loop::run(function () {
            try {
                $promises = [];
                foreach ($this->urls as $urlInfo) {
                    $promises[$urlInfo['url']] = Amp\call($this->getRequestHandler(), $urlInfo['url'],
                        $urlInfo['body']);
                }
                $this->result = yield $promises;
            } catch (Amp\Artax\HttpException $error) {
                echo $error;
            }
        });

        return $this->result;
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
