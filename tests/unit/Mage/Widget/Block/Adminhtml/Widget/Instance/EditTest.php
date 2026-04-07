<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Widget\Block\Adminhtml\Widget\Instance;

use Mage_Widget_Block_Adminhtml_Widget_Instance_Edit as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Widget\Block\Adminhtml\Widget\Instance\EditTrait;

final class EditTest extends OpenMageTest
{
    use EditTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::markTestSkipped('');
    }
}
