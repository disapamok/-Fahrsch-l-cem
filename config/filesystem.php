<?php

return [
    'default'   => 'storage',
    'disks'     => [
        'language'  => str_replace('config', 'lang', __DIR__),
        'storage'   => str_replace('config', 'uploads', __DIR__),
        'uploads'   => str_replace('config', 'uploads', __DIR__),
        'views'     => str_replace('config', 'views', __DIR__),
    ]
];
