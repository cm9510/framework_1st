<?php
define('APP_NAME', 'Framework');

require_once __DIR__.'/../Core/Functions.php';
require_once __DIR__.'/../Core/Loader.php';
require_once __DIR__.'/../Route/web.php';
require_once __DIR__.'/../Core/Start.php';


Start::app()->launch();

