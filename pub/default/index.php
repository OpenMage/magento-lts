<?php

//
// This file is the entrypoint for the default OpenMage store.
// Create a separate subdirectory of pub/ to add more virtual hosts for different stores
// with different root directories and assign the run code and run type variables below as appropriate.
//

chdir(dirname(__DIR__, 2));

/* Store or website code */
if (empty($_SERVER['MAGE_RUN_CODE'])) {
    $_SERVER['MAGE_RUN_CODE'] = '';
}

/* Run store or run website */
if (empty($_SERVER['MAGE_RUN_TYPE'])) {
    $_SERVER['MAGE_RUN_TYPE'] = 'store';
}

/* Errors pages skin */
if (empty($_SERVER['MAGE_ERRORS_SKIN'])) {
    $_SERVER['MAGE_ERRORS_SKIN'] = 'default';
}

require 'index.php';
