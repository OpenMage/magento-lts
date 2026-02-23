<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Purifier;

use HTMLPurifier_Config;
use HTMLPurifier_DefinitionCacheFactory;
use Mage;
use Mage_Core_Model_Purifier_DefinitionCache as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use Zend_Cache;
use Zend_Cache_Backend;
use Zend_Cache_Backend_ExtendedInterface;

final class DefinitionCacheTest extends OpenMageTest
{
    private const CACHE_DEFINITION_IMPL = self::class;

    /** @var Zend_Cache_Backend&Zend_Cache_Backend_ExtendedInterface */
    private static $cacheBackend;

    /** @var null|Zend_Cache_Backend */
    private static $originalBackend;

    /** @var bool */
    private static $originalCachingOption;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$originalCachingOption = Mage::getConfig()->getCache()->getOption('caching');
        // @phpstan-ignore assign.propertyType (Doc types don't match between cache backend getter and setter)
        self::$originalBackend = Mage::getConfig()->getCache()->getBackend();

        self::$cacheBackend = new class extends Zend_Cache_Backend implements Zend_Cache_Backend_ExtendedInterface {
            /** @var array<string, array{value: string, tags: string[]}> */
            private array $entries = [];

            /**
             * Tag to ids map
             *
             * @var array<string, string[]>
             */
            private array $tags = [];

            /**
             * @return string[]
             */
            public function getIds()
            {
                return array_keys($this->entries);
            }

            /**
             * @return string[]
             */
            public function getTags()
            {
                return array_keys($this->tags);
            }

            /**
             * @param  string[] $tags
             * @return string[]
             */
            public function getIdsMatchingTags($tags = [])
            {
                $ids = [];
                foreach ($this->entries as $id => $entry) {
                    if (array_diff($tags, $entry['tags']) === []) {
                        $ids[] = $id;
                    }
                }

                return $ids;
            }

            /**
             * @param  string[] $tags
             * @return string[]
             */
            public function getIdsNotMatchingTags($tags = [])
            {
                $ids = [];
                foreach ($this->entries as $id => $entry) {
                    if (array_diff($tags, $entry['tags']) !== []) {
                        $ids[] = $id;
                    }
                }

                return $ids;
            }

            /**
             * @param  string[] $tags
             * @return string[]
             */
            public function getIdsMatchingAnyTags($tags = [])
            {
                $ids = [];
                foreach ($tags as $tag) {
                    $ids = array_merge($ids, $this->tags[$tag] ?? []);
                }

                return array_unique($ids);
            }

            /**
             * @return int
             */
            public function getFillingPercentage()
            {
                return 0;
            }

            /**
             * @param  string      $id
             * @return array|false
             */
            public function getMetadatas($id)
            {
                $entry = $this->entries[$id] ?? false;
                if ($entry === false) {
                    return false;
                }

                return [
                    'expire' => PHP_INT_MAX,
                    'tags' => $entry['tags'],
                    'mtime' => 0,
                ];
            }

            /**
             * @param  string $id
             * @param  int    $extraLifetime
             * @return bool
             */
            public function touch($id, $extraLifetime)
            {
                return array_key_exists($id, $this->entries);
            }

            /**
             * @return array
             */
            public function getCapabilities()
            {
                return [
                    'automatic_cleaning' => false,
                    'tags' => true,
                    'expired_read' => false,
                    'priority' => false,
                    'infinite_lifetime' => false,
                    'get_list' => true,
                ];
            }

            /**
             * @param array $directives
             */
            public function setDirectives($directives) {}

            /**
             * @param  string       $id
             * @param  bool         $doNotTestCacheValidity
             * @return false|string
             */
            public function load($id, $doNotTestCacheValidity = false)
            {
                $entry = $this->entries[$id] ?? false;
                if ($entry === false) {
                    return false;
                }

                return $entry['value'];
            }

            /**
             * @param  string    $id
             * @return false|int
             */
            public function test($id)
            {
                if (array_key_exists($id, $this->entries)) {
                    return 0;
                }

                return false;
            }

            /**
             * @param  string         $data
             * @param  string         $id
             * @param  string[]       $tags
             * @param  null|false|int $specificLifetime
             * @return bool
             */
            public function save($data, $id, $tags = [], $specificLifetime = false)
            {
                $oldTags = ($this->entries[$id] ?? ['tags' => []])['tags'];
                $removedTags = array_diff($oldTags, $tags);
                $addedTags = array_diff($tags, $oldTags);

                foreach ($removedTags as $removedTag) {
                    $this->tags[$removedTag] = array_diff($this->tags[$removedTag], [$id]);
                }

                foreach ($addedTags as $addedTag) {
                    $this->tags[$addedTag][] = $id;
                }

                $this->entries[$id] = [
                    'value' => $data,
                    'tags' => $tags,
                ];

                return true;
            }

            /**
             * @param  string $id
             * @return bool
             */
            public function remove($id)
            {
                $entry = $this->entries[$id] ?? false;
                if ($entry !== false) {
                    $tags = $entry['tags'];
                    foreach ($tags as $tag) {
                        $this->tags[$tag] = array_diff($this->tags[$tag], [$id]);
                    }

                    unset($this->entries[$id]);
                }

                return true;
            }

            /**
             * @param  string   $mode
             * @param  string[] $tags
             * @return bool
             */
            public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = [])
            {
                switch ($mode) {
                    case Zend_Cache::CLEANING_MODE_ALL:
                        $this->entries = [];
                        $this->tags = [];

                        break;
                    case Zend_Cache::CLEANING_MODE_OLD:
                        break;
                    case Zend_Cache::CLEANING_MODE_MATCHING_TAG:
                        foreach ($this->getIdsMatchingTags($tags) as $id) {
                            $this->remove($id);
                        }

                        break;
                    case Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG:
                        foreach ($this->getIdsNotMatchingTags($tags) as $id) {
                            $this->remove($id);
                        }

                        break;
                    case Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG:
                        foreach ($this->getIdsMatchingAnyTags($tags) as $id) {
                            $this->remove($id);
                        }

                        break;
                }

                return true;
            }
        };

        Mage::getConfig()->getCache()->setOption('caching', true);
        Mage::getConfig()->getCache()->setBackend(self::$cacheBackend);

        HTMLPurifier_DefinitionCacheFactory::instance()->register(
            self::CACHE_DEFINITION_IMPL,
            Subject::class,
        );
    }

    public static function tearDownAfterClass(): void
    {
        Mage::getConfig()->getCache()->setOption('caching', self::$originalCachingOption);
        Mage::getConfig()->getCache()->setBackend(self::$originalBackend);

        HTMLPurifier_DefinitionCacheFactory::instance()->register(
            self::CACHE_DEFINITION_IMPL,
            // @phpstan-ignore argument.type (Doc type is wrong, null is fine)
            null,
        );

        parent::tearDownAfterClass();
    }

    protected function tearDown(): void
    {
        self::$cacheBackend->clean();
    }

    /**
     * Tests that the cache is populated by calls to HTMLPurifier.
     *
     * @group Model
     */
    public function testCacheIsPopulatedByPurifier(): void
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', self::CACHE_DEFINITION_IMPL);

        $purifier = new \HTMLPurifier($config);

        self::assertEmpty(self::$cacheBackend->getIds(), 'Cache should be empty at start of test');
        self::assertEmpty(self::$cacheBackend->getTags(), 'Cache should be empty at start of test');

        $purifier->purify(
            <<<'HTML'
            <h1>Title</h1>
            <p>Here is some text.</p>
            <madeup class="foo"></madeup>
            <a href="http://localhost:8080"></a>
            HTML,
        );

        self::assertNotEmpty(self::$cacheBackend->getIds(), 'Cache should contain definition entries after purifying');
        self::assertNotEmpty(self::$cacheBackend->getTags(), 'Cache should contain tags after purifying');
    }
}
