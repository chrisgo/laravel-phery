<?php

return [
    // Laravel wrapper configuration
    'prefix' => 'ajax',
    'callbacks' => [
        'before' => [],
        'after' => [],
    ],
    // Configuration options for Phery
    // https://github.com/pheryjs/phery#pheryinstance-configarray
    'csrf' => false,
    'error_reporting' => false,
    'exceptions' => true,
    'set_always_available' => true,
];
