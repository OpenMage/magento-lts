<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Shell
 */

require_once 'abstract.php';

/**
 * Magento DI Test Shell Script
 *
 * @package    Mage_Shell
 */
class Mage_Shell_DiTest extends Mage_Shell_Abstract
{
    public function run()
    {
        $runner = Mage::getModel('ditest/runner');
        if ($runner) {
            echo $runner->execute() . "\n";
        } else {
            echo "Failed to resolve OpenMage_DiTest_Model_Runner from DI container.\n";
        }
    }

    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f ditest.php

  Resolves Runner via Symfony DI, which autowires Greeter,
  then prints the greeting.

USAGE;
    }
}

$shell = new Mage_Shell_DiTest();
$shell->run();
