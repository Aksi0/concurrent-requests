<?php

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Promise\each_limit;

require_once __DIR__ . '/vendor/autoload.php';


function ampExample()
{
    $amp = new AmpClass();
    // https://webhook.site/#!/36dbec3e-55ab-452e-a75e-9abea3369d02/a4495809-0027-440f-a7f0-1bbe8e8d31cb/1

    $amp->addUrl("https://webhook.site/36dbec3e-55ab-452e-a75e-9abea3369d02?type=get");
    $amp->addUrl("https://webhook.site/2c0df4fc-901c-4f8f-804f-37e3feb7a7db"); // error
    $amp->addUrl("https://webhook.site/36dbec3e-55ab-452e-a75e-9abea3369d02?type=post", ['test' => 'ok']);
    $amp->addUrl("https://google.com");

    $time = time();

    print 'Before run' . PHP_EOL;
    $result = $amp->run();
    print 'After run. Time: ' . (time() - $time) . ' s' . PHP_EOL;

    foreach ($result as $url => $item) {
        print 'url: ' . $url . ' - size: ' . strlen($item['data']) . ' status: ' . ($item['status'] ? 'success' : 'fail') . PHP_EOL;
    }
    print PHP_EOL . PHP_EOL;
}


##

function guzzleExample()
{
    $urls = [
        "https://webhook.site/36dbec3e-55ab-452e-a75e-9abea3369d02?type=get",
        "https://webhook.site/2c0df4fc-901c-4f8f-804f-37e3feb7a7db",
        "https://webhook.site/36dbec3e-55ab-452e-a75e-9abea3369d02?type=post",
        "https://google.com",
    ];

    $handler = HandlerStack::create();

    $iterator = function () use ($handler, $urls) {
        while (true) {
            if (empty($urls)) {
                break;
            }

            $client = new Client(['timeout' => 20, 'handler' => $handler]);
            $url = array_shift($urls);
            $request = new Request('GET', $url, []);

            echo "Queuing $url @ " . date('Y-m-d H:i:s') . PHP_EOL;

            yield $client
                ->sendAsync($request)
                ->then(function (Response $response) use ($request) {
                    return [$request, $response];
                });

        }
    };

    $promise = each_limit(
        $iterator(),
        10,  /// concurrency,
        function ($result, $index) {
            /** @var GuzzleHttp\Psr7\Request $request */
            list($request, $response) = $result;
            echo (string)$request->getUri() . ' completed ' . PHP_EOL;
        }
    );
    $promise->wait();
}

guzzleExample();