<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Model\System\Config\Backend;

use Mage_Core_Model_Config_Data;
use Mage_Paypal_Model_System_Config_Backend_RetryStatusCodes as Subject;

final class RetryStatusCodesTestSubject extends Subject
{
    public function beforeSaveForTest(): Mage_Core_Model_Config_Data
    {
        return $this->_beforeSave();
    }
}
