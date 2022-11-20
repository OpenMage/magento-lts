<?php

// DO NOT RUN DIRECTLY IN YOUR PRODUCTION ENVIRONMENT!
// This script is distributed in the hope that it will be useful, but without any warranty.

chdir(dirname(__DIR__, 1));
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (PHP_SAPI != 'cli')
    exit(0);

// Max number of lines to check in each file, "@copyright" line shouldn't be further than 50 lines down
define('MAX_SCAN_LINES', 50);

// Get all commits in format "HASH YYYY-MM-DD COMMIT_MSG"
$commits = explode("\n", shell_exec("git log --pretty='%H %cs %s'"));

// Keep track of upstream Magento and OpenMage commits for debugging
$commits_magento = array();
$commits_openmage = array();

// Filter only commits by OpenMage contributors and reduce to assoc array with format:
// array(
//     'HASH' => 'YEAR',
// );

$commits = array_reduce(
    $commits,
    function($acc, $cur) use(&$commits_magento, &$commits_openmage) {
        $cur = explode(' ', $cur, 3);
        if (count($cur) !== 3) {
            return $acc;
        }
        list($hash, $date, $msg) = $cur;
        $msg = trim($msg);

        // Push into upstream magento commits for now. If we don't return early we'll move to openmage commits
        $commits_magento[] = implode(' ', array($hash, $date, $msg));

        // Ignore any "Updated Copyright" type commits
        if (preg_match('/^(Add(ed)?|Update(d)?).*?Copyright/i', $msg)) {
            return $acc;
        }

        // Ignore any "Fixed Whitespace" type commits
        if (preg_match('/^(Fix(ed)?).*?Whitespace/i', $msg)) {
            return $acc;
        }

        // Ignore any "Import Magento" type commits
        if (preg_match('/^(Import(ed)?|Update(d)?|Upgrade(d)?)( to)? Magento/i', $msg)) {
            return $acc;
        }

        // Ignore some merge commits
        if (stripos($msg, 'Merge') === 0) {
            if (stripos($msg, 'Merge tag') === 0) {
                return $acc;
            }
            if (stripos($msg, 'magento-1.') !== false) {
                return $acc;
            }
            if (stripos($msg, 'upstream-') !== false) {
                return $acc;
            }
            if (stripos($msg, 'import-') !== false) {
                return $acc;
            }
            if (stripos($msg, 'magento-mirror') !== false) {
                return $acc;
            }
            if (stripos($msg, 'release-merge-') !== false) {
                return $acc;
            }
        }

        // Ignore any commits with "SUPEE", except for a few whitelisted ones
        if (stripos($msg, 'SUPEE') !== false) {
            switch ($hash) {
            case 'c0c5342b4ce6da45e1a7b12a48571f2d937f5b5c': // 2020-10-18 Adds missing meta tags to prevent SUPEE-11295 warnings (#1236)
            case '951c0c79d75a13ed1f4a67b629c3de8bae7dcf0a': // 2020-07-06 Fix broken file upload for downloadables caused by PATCH SUPEE-11314 (#1048)
                break;
            default:
                return $acc;
            }
        }

        // Other commits not caught by rules above
        switch ($hash) {
        case 'e1e0826b969b736e58b2bc6cf5daa5ea653c8971': // 2009-12-31 Reverting magento-1.4.0.0-alpha1 thru magento-1.4.0.0-rc1 commits
        case '88e2a6b3c1c77f89801991c1e1a5c349e87335ad': // 2011-02-09 Fixing missing files
        case 'd0b97bacb84d061d98f7f765b9edf932d7ac6da8': // 2013-09-25 Magento 1.8.0.0, excluding copyright changes from 2012 to 2013
        case 'b9c3c45f9fcb78d133064d2cb2b607a1c7b47379': // 2013-09-25 Copyright change from 2012 to 2013
        case '170cfbaee735f40a3772b367f621ad4c43cf56c5': // 2017-02-10 Imported 1.9.3.2 sources
        case 'c401b73c3957ee89a21c4c79ee0aee7070279810': // 2017-03-03 Merge branch '1.9.3.x' into 1.9.3.0
        case '0e7f77ea8756e69df59746c9191f45a1d7fbad3d': // 2018-03-09 Merge var directory from 1.9.3.8.
        case '2f09902201f5ae2b2063cdde4c354484f72d5396': // 2018-03-09 Update Copyright notices for 1.9.3.8.
        case 'd92a24aa27360439a6f6ed68ba9260685b2f9e54': // 2018-03-09 Fix 1.9.3.x upstream merges
        case 'a5ad2ee47599400ef0066562315b300c7f581938': // 2017-11-28 1.9.3.7
        case '577122d5a50acb7ae585a817fa50faee848c9673': // 2018-02-28 Merge pull request #61 from JeroenBoersma/magento-1.9.3.8
        case '9e443ccb2167cfec26aedc6446600a079b8caee1': // 2020-01-29 Magento 1.9.4.4 code changes
        case '76ca350b1533f1e2a357d247a807fb558276f1b4': // 2020-01-29 Magento 1.9.4.4 copyright only
        case '16dc8f76f1f6bbbaf18005e88ae08df218d60aac': // 2020-05-05 Updated to pristine copy of 1.9.4.5 from magento.com (#944)
        case 'e6f08517af7e8ce1b166905737874da721e4427e': // Remove DISCLAIMER and change Magento -> OpenMage in header (#2297)
            return $acc;
        }

        $commits_openmage[] = array_pop($commits_magento);
        $acc[$hash] = substr($date, 0, 4);

        return $acc;
    },
    array()
);

// If `php update-copyright.php dump` then stop here
if ($argv[1] ?? '' === 'dump') {
    echo "Upstream Magento Commits:\n" . str_repeat('=', 80) . "\n" . implode("\n", $commits_magento) . "\n\n";
    echo "OpenMage Commits:\n" . str_repeat('=', 80) . "\n" . implode("\n", $commits_openmage) . "\n\n";
    exit;
}

// Grep for all files that have "@copyright Magento", excluding .git
$files = array_filter(explode("\n", shell_exec("grep -Erl --exclude-dir='.git' '@copyright(.*)Magento' .")));

// Stats
$files_updated = 0;

foreach ($files as $file) {

    // Ignore this file
    if ($file === './shell/update-copyright.php') {
        continue;
    }

    // Get the commit hashes for this file
    $commits_file = array_filter(explode("\n", shell_exec("git log --pretty='%H' '$file'")));

    // Find the years this file has been modified from our filtered $commits assoc array
    $copyright_years = array();
    foreach ($commits_file as $hash) {
        if (array_key_exists($hash, $commits) && !in_array($commits[$hash], $copyright_years)) {
            $copyright_years[] = $commits[$hash];
        }
    }

    // Check if we did not find anything
    if (count($copyright_years) === 0) {
        continue;
    }

    // Format as a string "2020-2022" or just "2022"
    $copyright_years_str = count($copyright_years) > 1 ? min($copyright_years) . '-' . max($copyright_years) : $copyright_years[0];

    // Get file, "@copyright" line shouldn't be further than MAX_SCAN_LINES lines down
    $contents = explode("\n", file_get_contents($file), MAX_SCAN_LINES);

    // Keep track if we need to update this file
    $update = false;

    // Find copyright line
    foreach ($contents as $line_no => &$line) {

        // Check that this is a copyright line
        if (!preg_match('/^.*@copyright.*Magento.*$/', $line)) {
            continue;
        }

        // Check if we have an existing Copyright OpenMage line and update
        if (preg_match('/.*?@copyright.*?(\d{4}(?:\s*-\s*\d{4})?).*?OpenMage.*/', $contents[$line_no+1], $match)) {
            if ($match[1] !== $copyright_years_str) {
                $contents[$line_no+1] = str_replace($match[1], $copyright_years_str, $match[0]);
                $update = true;
            }
            break;
        }

        // Determine info about formatting:
        // What comment character we are using (including spaces) Possible values are #, *, or //
        // How many spaces after the @copyright tag
        preg_match('/^(\s*\S*\s)\S*(\s*)/', $line, $matches);

        // Make sure we matched the string
        if (count($matches) !== 3) {
            echo "Something wrong with the formatting of file: $file\n";
            break;
        }

        // Otherwise add a new copyright line
        $line_new = "{$matches[1]}@copyright{$matches[2]}Copyright (c) $copyright_years_str The OpenMage Contributors (https://www.openmage.org)";

        // Add into the file after the current @copyright line
        array_splice($contents, $line_no + 1, 0, $line_new);

        $update = true;
        break;
    }

    // Write file
    if ($update) {
        file_put_contents($file, implode("\n", $contents));
        $files_updated++;
    }
}

echo "Updated $files_updated file(s)\n";
