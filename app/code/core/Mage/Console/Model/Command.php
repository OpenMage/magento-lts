<?php

use Symfony\Component\Console\Command\Command;

class Mage_Console_Model_Command extends Command
{
    /**
     * @param string|null $name
     */
    public function __construct($name)
    {
        if (http_response_code() !== false) {
            throw new \Exception('You have to run it from CLI.');
        }

        if (!$name) {
            $name = null;
        }
        parent::__construct($name);
    }
}
