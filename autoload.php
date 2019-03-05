<?php

/*
 * Bear CMS Universal
 * https://github.com/bearcms/universal
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

$classes = [
    'BearCMS\Universal' => 'src/Universal.php',
    'BearCMS\Universal\Response' => 'src/Universal/Response.php',
];

spl_autoload_register(function ($class) use ($classes) {
    if (isset($classes[$class])) {
        require __DIR__ . '/' . $classes[$class];
    }
}, true);
