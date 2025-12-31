<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

/**
 * Convert history
 *
 * @package    Mage_Dataflow
 *
 * @method Mage_Dataflow_Model_Resource_Profile_History _getResource()
 * @method string                                       getActionCode()
 * @method string                                       getPerformedAt()
 * @method int                                          getProfileId()
 * @method Mage_Dataflow_Model_Resource_Profile_History getResource()
 * @method int                                          getUserId()
 * @method $this                                        setActionCode(string $value)
 * @method $this                                        setPerformedAt(string $value)
 * @method $this                                        setProfileId(int $value)
 * @method $this                                        setUserId(int $value)
 */
class Mage_Dataflow_Model_Profile_History extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('dataflow/profile_history');
    }

    protected function _beforeSave()
    {
        if (!$this->getProfileId()) {
            $profile = Mage::registry('current_convert_profile');
            if ($profile) {
                $this->setProfileId($profile->getId());
            }
        }

        if (!$this->hasData('user_id')) {
            $this->setUserId(0);
        }

        parent::_beforeSave();
        return $this;
    }
}
