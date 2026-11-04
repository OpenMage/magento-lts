<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper\Purifier;

use Mage;
use Mage_Core_Block_Messages;
use Mage_Core_Helper_Purifier_DefinitionCache as Subject;
use Mage_Core_Model_Config;
use Mage_Core_Model_Message_Error;
use Mage_Core_Model_Purifier_DefinitionCache;
use OpenMage\Tests\Unit\OpenMageTest;

final class DefinitionCacheTest extends OpenMageTest
{
    private string $originalCryptKey;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalCryptKey = (string) $this->getConfig()->getNode('global/crypt/key');
    }

    protected function tearDown(): void
    {
        $this->getConfig()->setNode('global/crypt/key', $this->originalCryptKey);
        Mage::unregister('_singleton/core/purifier_message');
        parent::tearDown();
    }

    /**
     * @group Helper
     */
    public function testSignedCacheIsUsedWhenAnEncryptionKeyIsConfigured(): void
    {
        $this->getConfig()->setNode('global/crypt/key', 'definition-cache-test-key');

        self::assertSame(
            $this->getConfig()->getModelClassName('core/purifier_definitionCache'),
            (new Subject())->getCacheDefinitionImpl(),
        );
        self::assertSame(Mage_Core_Model_Purifier_DefinitionCache::class, (new Subject())->getCacheDefinitionImpl());
    }

    /**
     * @group Helper
     */
    public function testNoCacheWithoutAnEncryptionKey(): void
    {
        $this->getConfig()->setNode('global/crypt/key', '');

        self::assertNull((new Subject())->getCacheDefinitionImpl());
    }

    /**
     * The installer renders session messages before app/etc/local.xml (and so the
     * encryption key) exists; purifying them must not throw.
     *
     * @group Helper
     */
    public function testMessagesRenderWithoutAnEncryptionKey(): void
    {
        $this->getConfig()->setNode('global/crypt/key', '');
        Mage::unregister('_singleton/core/purifier_message');

        $block = new Mage_Core_Block_Messages();
        $block->addMessage(new Mage_Core_Model_Message_Error('Database connection error: <b>x</b><script>alert(1)</script>'));

        $html = $block->getGroupedHtml();

        self::assertStringContainsString('Database connection error: <b>x</b>', (string) $html);
        self::assertStringNotContainsString('<script', (string) $html);
    }

    private function getConfig(): Mage_Core_Model_Config
    {
        $config = Mage::getConfig();
        self::assertInstanceOf(Mage_Core_Model_Config::class, $config);

        return $config;
    }
}
