<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Shell
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

// DO NOT RUN DIRECTLY IN YOUR PRODUCTION ENVIRONMENT!
// This script is distributed in the hope that it will be useful, but without any warranty.

require_once 'abstract.php';
chdir(dirname(__DIR__, 1));

/**
 * OpenMage Translation Helper Shell Script
 *
 * @category   Mage
 * @package    Mage_Shell
 * @author     The OpenMage Contributors
 */
class Mage_Shell_Translation extends Mage_Shell_Abstract
{
    /**
     * Remember if we used stdin for file list
     *
     * @var boolean
     */
    protected $_stdin;

    /**
     * Get a list of files to scan for translated strings
     *
     * @return array<int, string>
     */
    protected function getFiles(): array
    {
        $files = [];
        $fh = fopen('php://stdin', 'r');

        if ($fh === false) {
            return $files;
        }

        stream_set_blocking($fh, false);

        while (($line = fgets($fh)) !== false) {
            $files[] = $line;
        }
        if (count($files)) {
            $this->_stdin = true;
        } else {
            $files = array_merge(
                // Grep for all files that might call the __ function
                explode("\n", (string)shell_exec("grep -Frl --exclude-dir='.git' --include=*.php --include=*.phtml '__' .")),
                // Grep for all XML files that might use the translate attribute
                explode("\n", (string)shell_exec("grep -Frl --exclude-dir='.git' --include=*.xml 'translate=' ."))
            );
        }
        return array_filter(array_map('trim', $files));
    }

    /**
     * Get all defined translation strings per file from app/locale/$CODE/*.csv
     *
     * @return array<string, array<int, string>>
     */
    protected function getDefinedStrings(): array
    {
        $map = [];
        $lang = $this->getArg('lang');

        if (!is_string($lang)) {
            $lang = 'en_US';
        }

        $files = glob("app/locale/$lang/*.csv");
        if (!is_array($files)) {
            return $map;
        }

        $parser = new Varien_File_Csv();
        $parser->setDelimiter(',');
        foreach ($files as $file) {
            $data = $parser->getDataPairs($file);
            $map[$file] = array_keys($data);
        }

        return $map;
    }

    /**
     * Get all used translation strings per file from all php, phtml, and xml files
     *
     * @return array<string, array<int, string>>
     */
    protected function getUsedStrings(): array
    {
        $map = [];
        $files = $this->getFiles();
        foreach ($files as $file) {
            // Ignore this file
            if ($file === './shell/translations.php') {
                continue;
            }

            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $contents = file_get_contents($file);

            if ($contents === false) {
                echo "ERROR: File not found $file\n";
                continue;
            }

            $matches = [];

            if ($ext === 'php' || $ext === 'phtml') {
                // Regex to get first argument of __ function
                // https://stackoverflow.com/a/5696141
                $re_dq = '/__\s*\(\s*"([^"\\\\]*(?:\\\\.[^"\\\\]*)*\s*)"/s';
                $re_sq = "/__\s*\(\s*'([^'\\\\]*(?:\\\\.[^'\\\\]*)*\s*)'/s";

                if (preg_match_all($re_dq, $contents, $_matches)) {
                    $matches = array_merge($matches, str_replace('\"', '"', $_matches[1]));
                }
                if (preg_match_all($re_sq, $contents, $_matches)) {
                    $matches = array_merge($matches, str_replace("\'", "'", $_matches[1]));
                }
            } elseif ($ext === 'xml') {
                $xml = new SimpleXMLElement($contents);
                // Get all nodes with translate="" attribute
                $nodes = $xml->xpath('//*[@translate]');
                foreach ($nodes as $node) {
                    // Which children should we translate?
                    $translateNode = $node['translate'];
                    if (!$translateNode instanceof SimpleXMLElement) {
                        continue;
                    }
                    $translateChildren = array_map('trim', explode(' ', $translateNode->__toString()));
                    foreach ($node->children() as $child) {
                        if (in_array($child->getName(), $translateChildren)) {
                            $matches[] = $child->__toString();
                        }
                    }
                }
            }

            $matches = array_filter(array_unique($matches));
            if (count($matches)) {
                $map[$file] = $matches;
            }
        }
        return $map;
    }

    /**
     * Find deprecated usage of global __ function
     *
     * @return void
     */
    protected function findDeprecated(): void
    {
        $files = $this->getFiles();
        foreach ($files as $file) {
            // Ignore this file
            if ($file === './shell/translations.php') {
                continue;
            }

            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $contents = file_get_contents($file);

            if ($contents === false) {
                echo "ERROR: File not found $file\n";
                continue;
            }

            if ($ext === 'php' || $ext === 'phtml') {
                // Capture what precedes a __() call
                $re = '/(\S*\s*)(__\s*\()/';

                $found = false; // If we found deprecated usage in this file
                $insert = "Mage::helper('core')->"; // String to insert before global __ usage
                $offset = 0; // Keep track of extra offset from adding strings

                if (preg_match_all($re, $contents, $matches, PREG_OFFSET_CAPTURE)) {
                    for ($i = 0; $i < count($matches[0]); $i++) {
                        $word = trim($matches[1][$i][0]);
                        if (substr($word, -2) !== '->' && substr($word, -2) !== '::' && $word !== 'function') {
                            $found = true;
                            if ($this->getArg('fix')) {
                                $contents = substr_replace($contents, $insert, $matches[2][$i][1] + $offset, 0);
                                $offset += strlen($insert);
                            }
                        }
                    }
                }
                if ($found) {
                    if ($this->getArg('fix')) {
                        echo "DEPRECATED: Global __ function fixed in: $file\n";
                        file_put_contents($file, $contents);
                    } else {
                        echo "DEPRECATED: Global __ function found in: $file\n";
                    }
                }
            }
        }
    }

    /**
     * Run script
     *
     * @return void
     */
    public function run()
    {
        if ($this->getArg('missing')) {
            $definedFileMap = $this->getDefinedStrings();
            $usedFileMap = $this->getUsedStrings();

            $definedFlat = array_unique(array_merge(...array_values($definedFileMap)));
            $usedFlat = array_unique(array_merge(...array_values($usedFileMap)));

            if ($this->getArg('verbose')) {
                foreach ($usedFileMap as $file => $used) {
                    $missing = array_diff($used, $definedFlat);
                    if (count($missing)) {
                        echo "$file\n    " . implode("\n    ", $missing) . "\n\n";
                    }
                }
            } else {
                $missing = array_diff($usedFlat, $definedFlat);
                sort($missing);
                echo implode("\n", $missing) . "\n";
            }
        } elseif ($this->getArg('unused')) {
            $definedFileMap = $this->getDefinedStrings();
            $usedFileMap = $this->getUsedStrings();

            $definedFlat = array_unique(array_merge(...array_values($definedFileMap)));
            $usedFlat = array_unique(array_merge(...array_values($usedFileMap)));

            if ($this->_stdin) {
                echo "stdin file list cannot be used with the 'unused' mode.\n";
                exit;
            }
            if ($this->getArg('verbose')) {
                foreach ($definedFileMap as $file => $defined) {
                    $unused = array_diff($defined, $usedFlat);
                    if (count($unused)) {
                        echo "$file\n    " . implode("\n    ", $unused) . "\n\n";
                    }
                }
            } else {
                $unused = array_diff($definedFlat, $usedFlat);
                sort($unused);
                echo implode("\n", $unused) . "\n";
            }
        } elseif ($this->getArg('deprecated')) {
            $this->findDeprecated();
        } else {
            echo $this->usageHelp();
        }
    }

    /**
     * Retrieve Usage Help Message
     *
     * @return string
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f translations.php -- [options]
        php -f translations.php -- missing --verbose

  missing           Display used translations strings that are missing from csv files
  unused            Display defined translations strings that are not used in templates
  --verbose         Include filename with output
  --lang <lang>     Specify which language pack to check in app/locale, default is en_US
  deprecated        Find deprecated usage of the global __() function
  --fix             Overwrite files to fix deprecated usage DO NOT RUN IN PRODUCTION!
  help              This help

Note: By default, this script will check all files in this repository. However,
      you can pipe a list of files to check for missing translations strings.
      This is useful for checking a specific commit. For example:

      # Check if last two commits may have introduced missing translations
      git diff --name-only HEAD~2 | php -f translations.php -- missing


USAGE;
    }
}

$shell = new Mage_Shell_Translation();
$shell->run();
