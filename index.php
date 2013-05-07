<?php

require("vendor/autoload.php");
require("src/GuzzleApp/ApiCaller.php");

use GuzzleApp\ApiCaller;

$apiObj = new ApiCaller();

$apiObj->getContent()->getRequestBody();

print $apiObj->getHeaderInformation("Date");

$apiObj->sendPostRequest(array(
    'name' => 'matthew',
    'magazine' => 'PHP Arch'
));

$apiObj->getLastRequest();