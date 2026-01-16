<?php
// Railway uses PORT environment variable
$port = env('PORT', 8080);

return [
    'port' => $port,
];
