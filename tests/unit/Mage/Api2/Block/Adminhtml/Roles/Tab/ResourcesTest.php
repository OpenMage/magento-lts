<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Api2\Block\Adminhtml\Roles\Tab;

use Mage_Api2_Block_Adminhtml_Roles_Tab_Resources as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Api2\Block\Adminhtml\Roles\Tab\ResourcesTrait;

final class ResourcesTest extends OpenMageTest
{
    use ResourcesTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::markTestSkipped('');
    }
}
