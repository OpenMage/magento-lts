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
 * @category    Mage
 * @package     Mage_Shell
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'abstract.php';
chdir(dirname(__DIR__, 1));

/**
 * OpenMage Translation Helper Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      The OpenMage Contributors
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
     */
    protected function getFiles()
    {
        $files = [];
        $fh = fopen('php://stdin', 'r');
        stream_set_blocking($fh, false);

        while (($line = fgets($fh)) !== false) {
            $files[] = $line;
        }
        if (count($files)) {
            $this->_stdin = true;
        } else {
            $files = array_merge(
                // Grep for all files that might call the __ function
                explode("\n", shell_exec("grep -Frl --exclude-dir='.git' --include=*.php --include=*.phtml '__' .")),

                // Grep for all XML files that might use the translate attribute
                explode("\n", shell_exec("grep -Frl --exclude-dir='.git' --include=*.xml 'translate=' ."))
            );
        }
        return array_filter($files);
    }

    /**
     * Get all defined translation strings per file from app/locale/$CODE/*.csv
     *
     */
    protected function getDefinedStrings()
    {
        $map = [];
        $lang = $this->getArg('lang') ?: 'en_US';
        foreach (glob("app/locale/$lang/*.csv") as $file) {
            $parser = new Varien_File_Csv();
            $parser->setDelimiter(',');
            $data = $parser->getDataPairs($file);
            $map[$file] = array_keys($data);
        }
        return $map;
    }

    /**
     * Get all used translation strings per file from all php, phtml, and xml files
     *
     */
    protected function getUsedStrings()
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
                $re_dq = '/__\s?\(\s*"([^"\\\\]*(?:\\\\.[^"\\\\]*)*\s*)"/s';
                $re_sq = "/__\s?\(\s*'([^'\\\\]*(?:\\\\.[^'\\\\]*)*\s*)'/s";

                if (preg_match_all($re_dq, $contents, $_matches)) {
                    $matches = array_merge($matches, str_replace('\"', '"', $_matches[1]));
                }
                if (preg_match_all($re_sq, $contents, $_matches)) {
                    $matches = array_merge($matches, str_replace("\'", "'", $_matches[1]));
                }
            } else if ($ext === 'xml') {
                $xml = new SimpleXMLElement($contents);
                // Get all nodes with translate="" attribute
                $nodes = $xml->xpath('//*[@translate]');
                foreach ($nodes as $node) {
                    // Which children should we translate?
                    $translate_children = array_map('trim', explode(' ', $node['translate']));
                    foreach ($node->children() as $child) {
                        if (in_array($child->getName(), $translate_children)) {
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
     * Run script
     *
     */
    public function run()
    {
        if ($this->getArg('missing')) {

            $defined_file_map = $this->getDefinedStrings();
            $used_file_map = $this->getUsedStrings();

            $defined_flat = array_unique(array_merge(...array_values($defined_file_map)));
            $used_flat = array_unique(array_merge(...array_values($used_file_map)));

            if ($this->getArg('verbose')) {
                foreach ($used_file_map as $file => $used) {
                    $missing = array_diff($used, $defined_flat);
                    if (count($missing)) {
                        echo "$file\n    " . implode("\n    ", $missing) . "\n\n";
                    }
                }
            } else {
                $missing = array_diff($used_flat, $defined_flat);
                sort($missing);
                echo implode("\n", $missing) . "\n";
            }

        } else if ($this->getArg('unused')) {

            $defined_file_map = $this->getDefinedStrings();
            $used_file_map = $this->getUsedStrings();

            $defined_flat = array_unique(array_merge(...array_values($defined_file_map)));
            $used_flat = array_unique(array_merge(...array_values($used_file_map)));

            if ($this->_stdin) {
                echo "stdin file list cannot be used with the 'unused' mode.\n";
                exit;
            }
            if ($this->getArg('verbose')) {
                foreach ($defined_file_map as $file => $defined) {
                    $unused = array_diff($defined, $used_flat);
                    if (count($unused)) {
                        echo "$file\n    " . implode("\n    ", $unused) . "\n\n";
                    }
                }
            } else {
                $unused = array_diff($defined_flat, $used_flat);
                sort($unused);
                echo implode("\n", $unused) . "\n";
            }

        } else {
            echo $this->usageHelp();
        }
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f translations.php -- [options]
        php -f translations.php -- missing --verbose

  missing           Display used translations strings that are missing from csv files
  unused            Display defined translations strings that are not used in templates
  --verbose         Include filename with output
  --lang <lang>     Specify which language pack to check in app/locale. Default is en_US
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
