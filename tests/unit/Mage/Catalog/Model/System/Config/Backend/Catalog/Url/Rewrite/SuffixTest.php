<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\System\Config\Backend\Catalog\Url\Rewrite;

use Mage_Catalog_Model_System_Config_Backend_Catalog_Url_Rewrite_Suffix as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\System\Config\Backend\Catalog\Url\Rewrite\SuffixTrait;

final class SuffixTest extends OpenMageTest
{
    use SuffixTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::markTestSkipped('');
    }
}
