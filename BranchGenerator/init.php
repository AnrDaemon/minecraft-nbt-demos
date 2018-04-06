<?php

$_GET['seed'] = 3810;

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once 'libs/classloader.php';
require_once 'BranchGenerator.php';

$offset = AnrDaemon\Math\Point::fromCartesian(0, 0, 0);
