<?php

require_once __DIR__.'/router.php';

get('/', 'views/index.php');
get('/$id', 'views/index.php');

any('/404','views/404.php');
