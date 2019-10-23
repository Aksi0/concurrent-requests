<?php

require_once __DIR__ . '/vendor/autoload.php';

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
