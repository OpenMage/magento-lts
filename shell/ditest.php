<?php

require_once 'abstract.php';

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
