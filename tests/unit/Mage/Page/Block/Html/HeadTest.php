<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Page\Block\Html;

use Mage;
use Mage_Core_Helper_Js;
use Mage_Page_Block_Html_Head as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use Override;
use ReflectionMethod;

final class HeadTest extends OpenMageTest
{
    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    protected function tearDown(): void
    {
        // Restore the default so one mode test does not leak into the next.
        Mage::app()->getStore()->setConfig(Mage_Core_Helper_Js::XML_PATH_PROTOTYPE_MODE, Mage_Core_Helper_Js::PROTOTYPE_MODE_SHIM);
        parent::tearDown();
    }

    /**
     * Build a Head block seeded like the layout XML does: the shim first,
     * followed by a couple of unrelated JS files, then apply prototype_mode.
     *
     * @return list<string> the resulting JS item names, in order
     */
    private function applyPrototypeMode(string $mode): array
    {
        Mage::app()->getStore()->setConfig(Mage_Core_Helper_Js::XML_PATH_PROTOTYPE_MODE, $mode);

        $head = new Subject();
        $head->addJs('prototype/prototype-shim.js');
        $head->addJs('lib/ccard.js');
        $head->addJs('varien/js.js');

        $method = new ReflectionMethod(Subject::class, '_applyPrototypeMode');
        $method->setAccessible(true);
        $method->invoke($head);

        $names = [];
        foreach ($head->getData('items') as $item) {
            if (($item['type'] ?? null) === 'js') {
                $names[] = $item['name'];
            }
        }
        return $names;
    }

    /**
     * @group Block
     */
    public function testPrototypeModeFullLoadsRealLibraries(): void
    {
        $names = $this->applyPrototypeMode(Mage_Core_Helper_Js::PROTOTYPE_MODE_FULL);

        self::assertNotContains('prototype/prototype-shim.js', $names, 'shim must be swapped out in full mode');
        self::assertContains('prototype/prototype.js', $names);
        self::assertContains('scriptaculous/effects.js', $names);
        self::assertContains('scriptaculous/dragdrop.js', $names);
        // Real Prototype must load before any dependent script.
        self::assertSame('prototype/prototype.js', $names[0]);
        self::assertContains('lib/ccard.js', $names);
        self::assertContains('varien/js.js', $names);
    }

    /**
     * @group Block
     */
    public function testPrototypeModeShimKeepsShimOnly(): void
    {
        $names = $this->applyPrototypeMode(Mage_Core_Helper_Js::PROTOTYPE_MODE_SHIM);

        self::assertContains('prototype/prototype-shim.js', $names);
        self::assertSame('prototype/prototype-shim.js', $names[0], 'shim must load first');
        self::assertNotContains('prototype/prototype.js', $names);
        self::assertNotContains('scriptaculous/effects.js', $names);
    }

    /**
     * @group Block
     */
    public function testPrototypeModeNoneDropsEverything(): void
    {
        $names = $this->applyPrototypeMode(Mage_Core_Helper_Js::PROTOTYPE_MODE_NONE);

        self::assertNotContains('prototype/prototype-shim.js', $names);
        self::assertNotContains('prototype/prototype.js', $names);
        self::assertNotContains('scriptaculous/effects.js', $names);
        // Unrelated files are untouched.
        self::assertContains('lib/ccard.js', $names);
        self::assertContains('varien/js.js', $names);
    }

    /**
     * @group Block
     */
    public function testAddCss(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->addCss('test'));
    }

    /**
     * @group Block
     */
    public function testAddJs(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->addJs('test'));
    }

    /**
     * @group Block
     */
    public function testAddCssIe(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->addCssIe('test'));
    }

    /**
     * @group Block
     */
    public function testAddJsIe(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->addJsIe('test'));
    }

    /**
     * @group Block
     */
    public function testAddLinkRel(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->addLinkRel('test', 'ref'));
    }
}
