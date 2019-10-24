<?php

use function Amp\ParallelFunctions\parallelMap;
use function Amp\Promise\wait;

/**
 * Class AmpThreadsClass
 */
class AmpParallelClass
{
    public function run()
    {
        $promise = parallelMap([
            "https://webhook.site/36dbec3e-55ab-452e-a75e-9abea3369d02?type=get",
            "https://webhook.site/2c0df4fc-901c-4f8f-804f-37e3feb7a7db",
            "https://webhook.site/36dbec3e-55ab-452e-a75e-9abea3369d02?type=get2",
        ], function ($url) {
            $time = time();
            $response = @file_get_contents($url);
            print 'Finished url: ' . $url . ' with time ' . (time() - $time) . ' s' . PHP_EOL;
            return [
                $url => [
                    'data' => $response,
                    'status' => (bool)$response,
                ]
            ];
        });

        $result = wait($promise);

        $result = call_user_func_array('array_merge', $result);

        return $result;
    }
}