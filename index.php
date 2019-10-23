<?php

require_once __DIR__ . '/vendor/autoload.php';

$amp = new AmpClass();


// https://webhook.site/#!/36dbec3e-55ab-452e-a75e-9abea3369d02/a4495809-0027-440f-a7f0-1bbe8e8d31cb/1

$amp->addUrl("https://webhook.site/36dbec3e-55ab-452e-a75e-9abea3369d02?type=get");
$amp->addUrl("https://webhook.site/36dbec3e-55ab-452e-a75e-9abea3369d02?type=get");
$amp->addUrl("https://webhook.site/36dbec3e-55ab-452e-a75e-9abea3369d02?type=get");
$amp->addUrl("https://webhook.site/36dbec3e-55ab-452e-a75e-9abea3369d02?type=post", ['test' => 'ok']);

$time = time();

print 'Before run' . PHP_EOL;
$result = $amp->run();
print 'After run. Time: ' . (time() - $time) . ' s' . PHP_EOL;

foreach ($result as $url => $item)
{
    print 'url: ' . $url . ' - size: ' . strlen($item) . PHP_EOL;
}
print PHP_EOL . PHP_EOL;





print 'Start file_get_content: ' . PHP_EOL;
$time = time();
$url = 'https://webhook.site/36dbec3e-55ab-452e-a75e-9abea3369d02?type=get';
$response = file_get_contents($url);

print 'url: ' . $url . ' - size: ' . strlen($response) . ' with time ' . (time() - $time) . ' s' . PHP_EOL;
