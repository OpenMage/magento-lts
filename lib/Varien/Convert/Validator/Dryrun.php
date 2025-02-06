<?php

/**
 * @category   Varien
 * @package    Varien_Convert
 */

/**
 * Convert dry run validator
 *
 * Insert where you want to step profile execution if dry run flag is set
 *
 * @category   Varien
 * @package    Varien_Convert
 */
class Varien_Convert_Validator_Dryrun extends Varien_Convert_Validator_Abstract
{
    public function validate()
    {
        if ($this->getVar('dry_run') || $this->getProfile()->getDryRun()) {
            $this->addException('Dry run set, stopping execution', Varien_Convert_Exception::FATAL);
        }
        return $this;
    }
}
