<?php

chdir(dirname(__DIR__, 2));

if (file_exists('app/etc/local.xml')) {
    require 'errors/404.php';
} else {
    require 'install.php';
}
