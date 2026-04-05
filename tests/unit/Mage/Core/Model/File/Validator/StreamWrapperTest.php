<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\File\Validator;

use Mage;
use Mage_Core_Model_File_Validator_StreamWrapper as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class StreamWrapperTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('core/file_validator_streamwrapper');
    }
}
