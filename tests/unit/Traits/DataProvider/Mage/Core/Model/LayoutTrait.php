<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model;

use Generator;
use Mage_Cms_Block_Block;

trait LayoutTrait
{
    public function provideCreateBlock(): Generator
    {
        yield 'instance of Mage_Core_Block_Abstract' => [
            Mage_Cms_Block_Block::class,
            true,
            'cms/block',
            null,
            [],
        ];
        yield 'not instance of Mage_Core_Block_Abstract' => [
            false,
            false,
            'rule/conditions',
            null,
            [],
        ];
    }
}
