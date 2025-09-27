<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

/**
 * Convert dry run validator
 *
 * Insert where you want to step profile execution if dry run flag is set
 *
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Convert_Validator_Dryrun extends Mage_Dataflow_Model_Convert_Validator_Abstract
{
    public function validate()
    {
        if ($this->getVar('dry_run') || $this->getProfile()->getDryRun()) {
            $this->addException(Mage::helper('dataflow')->__('Dry run set, stopping execution.'), Mage_Dataflow_Model_Convert_Exception::FATAL);
        }
        return $this;
    }
}
