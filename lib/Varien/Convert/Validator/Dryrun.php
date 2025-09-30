<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Convert
 */

/**
 * Convert dry run validator
 *
 * Insert where you want to step profile execution if dry run flag is set
 *
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
