<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

declare(strict_types=1);

/**
 * HTMLPurifier definition cache implementation using Magento's cache system.
 *
 * This allows HTMLPurifier to cache parsed definitions using whatever cache
 * backend Magento is configured to use (File, Memcached, APC, Redis, etc.),
 * rather than requiring a separate file-based cache.
 *
 * This Model class is not intended to be instantiated by Magento. Its
 * fully-qualified class name is registered to the HTMLPurifier library, which
 * will instantiate instance(s) as it sees fit. That said, the constructor *is*
 * compatible with the {@link Mage::getModel()} factory.
 *
 * @package Mage_Core
 */
class Mage_Core_Model_Purifier_DefinitionCache extends HTMLPurifier_DefinitionCache
{
    /**
     * Cache tag for all HTMLPurifier entries, enabling bulk invalidation.
     */
    public const MAGE_CACHE_TAG = 'HTMLPURIFIER';

    /**
     * Cache ID prefix to namespace HTMLPurifier entries.
     */
    protected const MAGE_CACHE_ID_PREFIX = 'htmlpurifier_def';

    /**
     * Cache lifetime in seconds (1 week). Definitions rarely change.
     */
    protected const MAGE_CACHE_LIFETIME = 604800;

    protected string $signingKey;

    /**
     * @param array{type: string}|string $type          type of definition objects this instance of
     *                                                  the cache will handle
     * @param null|string                $encryptionKey Magento's core encryption key (or a suitable
     *                                                  substitute). Used to derive a key to
     *                                                  authenticate cache entries.
     */
    public function __construct($type = '', $encryptionKey = null)
    {
        if (!is_string($type)) {
            $type = ((array) $type)['type'] ?? '';
        }

        parent::__construct($type);

        $encryptionKey ??= (string) Mage::getConfig()->getNode('global/crypt/key');
        if ($encryptionKey === '') {
            throw new RuntimeException('Missing Magento encryption key');
        }

        $this->signingKey = hash_hkdf('sha256', $encryptionKey, info: 'htmlpurifier-cache-signing');
    }

    /**
     * Generates a unique identifier for a particular configuration
     *
     * Note: If you must override this method, then you likely need to also
     * override {@link static::isOld()}.
     *
     * @param  HTMLPurifier_Config $config Instance of HTMLPurifier_Config
     * @return string
     */
    public function generateKey($config)
    {
        $version = str_replace('.', '_', $config->version);
        $hash = $config->getBatchSerial($this->type);
        $revision = $config->get($this->type . '.DefinitionRev');
        return self::MAGE_CACHE_ID_PREFIX . "__{$version}__{$hash}__$revision";
    }

    /**
     * Tests whether or not a key is old with respect to the configuration's
     * version and revision number.
     *
     * Note: If you must override this method, then you likely need to also
     * override {@link static::generateKey()}.
     *
     * @param  string              $key    Key to test
     * @param  HTMLPurifier_Config $config Instance of HTMLPurifier_Config to
     *                                     test against
     * @return bool
     */
    public function isOld($key, $config)
    {
        $parts = explode('__', $key, 5);
        if (count($parts) !== 4) {
            return true;
        }

        [, $version, $hash, $revision] = $parts;

        // Be conservative: any version mismatch counts as "old"
        if (version_compare($version, $config->version) !== 0) {
            return true;
        }

        if ($hash !== $config->getBatchSerial($this->type)) {
            return true;
        }

        // Revisions are ints, but string comparison might be future-proof
        if ($revision < $config->get($this->type . '.DefinitionRev')) {
            return true;
        }

        return false;
    }

    /**
     * @param  HTMLPurifier_Definition $def
     * @param  HTMLPurifier_Config     $config
     * @return bool
     */
    public function add($def, $config)
    {
        if (!$this->checkDefType($def)) {
            return false;
        }

        $cache = $this->getMageCache();

        $id = $this->generateKey($config);

        // Only add if not already cached
        if ($cache->test($id) !== false) {
            return false;
        }

        return $cache->save(
            $this->encode($def),
            $id,
            $this->getMageCacheTags(),
            self::MAGE_CACHE_LIFETIME,
        );
    }

    /**
     * @param  HTMLPurifier_Definition $def
     * @param  HTMLPurifier_Config     $config
     * @return bool
     */
    public function set($def, $config)
    {
        if (!$this->checkDefType($def)) {
            return false;
        }

        $cache = $this->getMageCache();

        $id = $this->generateKey($config);

        return $cache->save(
            $this->encode($def),
            $id,
            $this->getMageCacheTags(),
            self::MAGE_CACHE_LIFETIME,
        );
    }

    /**
     * @param  HTMLPurifier_Definition $def
     * @param  HTMLPurifier_Config     $config
     * @return bool
     */
    public function replace($def, $config)
    {
        if (!$this->checkDefType($def)) {
            return false;
        }

        $cache = $this->getMageCache();

        $id = $this->generateKey($config);

        // Only replace if already cached
        if ($cache->test($id) === false) {
            return false;
        }

        return $cache->save(
            $this->encode($def),
            $id,
            $this->getMageCacheTags(),
            self::MAGE_CACHE_LIFETIME,
        );
    }

    /**
     * @param  HTMLPurifier_Config           $config
     * @return false|HTMLPurifier_Definition
     */
    public function get($config)
    {
        $id = $this->generateKey($config);

        $data = $this->getMageCache()->load($id);
        if (!is_string($data)) {
            return false;
        }

        /*
         * NOTE: Upstream's Serializer DefinitionCache does NOT check for
         * staleness in its implementation. Presumably for performance reasons.
         */

        return $this->decode($data);
    }

    /**
     * @param  HTMLPurifier_Config $config
     * @return bool
     */
    public function remove($config)
    {
        $id = $this->generateKey($config);
        return $this->getMageCache()->remove($id);
    }

    /**
     * Clears all HTMLPurifier definition cache entries.
     *
     * @param  HTMLPurifier_Config $config
     * @return bool
     */
    public function flush($config)
    {
        return $this->getMageCache()->clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            $this->getMageCacheTags(),
        );
    }

    /**
     * Clears old and invalid/corrupted entries.
     *
     * @param  HTMLPurifier_Config $config
     * @return bool
     */
    public function cleanup($config)
    {
        $cache = $this->getMageCache();
        $ids = $cache->getIdsMatchingTags($this->getMageCacheTags());
        foreach ($ids as $id) {
            $data = $cache->load($id);

            // No longer in cache
            if ($data === false) {
                continue;
            }

            // Corrupted
            if (!is_string($data)) {
                $cache->remove($id);
                continue;
            }

            $def = $this->decode($data);

            // Corrupted
            if ($def === false) {
                $cache->remove($id);
                continue;
            }

            // Old/stale
            if ($this->isOld($id, $config)) {
                $cache->remove($id);
            }
        }

        return true;
    }

    /**
     * Encodes the definition object to be stored in cache.
     *
     * Note: If you must override this method, then you likely need to also
     * override {@link static::decode()}.
     *
     * @param  HTMLPurifier_Definition $def
     * @return string
     */
    protected function encode($def)
    {
        $serialized = $this->serialize($def);
        $signature = $this->generateSignature($serialized);
        return "$signature:$serialized";
    }

    /**
     * Decodes the definition object from the encoded value.
     *
     * Note: If you must override this method, then you likely need to also
     * override {@link static::encode()}.
     *
     * @param  string                        $encoded
     * @return false|HTMLPurifier_Definition
     */
    protected function decode($encoded)
    {
        $parts = explode(':', $encoded, 2);
        if (count($parts) !== 2) {
            return false;
        }

        [$signature, $serialized] = $parts;

        if (!$this->signatureMatches($signature, $serialized)) {
            return false;
        }

        return $this->deserialize($serialized);
    }

    /**
     * Serializes the definition object to be stored in cache.
     *
     * Note: If you must override this method, then you likely need to also
     * override {@link static::deserialize()}.
     *
     * @param  HTMLPurifier_Definition $def
     * @return string
     */
    protected function serialize($def)
    {
        return serialize($def);
    }

    /**
     * Deserializes the definition object to be stored in cache.
     *
     * **WARNING:** The base implementation, {@link self::deserialize()}, calls
     * {@link \unserialize()} without an explicit 'allowed_classes' option.
     * Therefore, {@link self::deserialize()} should *never* be called on data
     * that has not been authenticated (i.e., by confirming that
     * {@link static::signatureMatches()} returns `true`).
     *
     * Note: If you must override this method, then you likely need to also
     * override {@link static::serialize()}.
     *
     * @param  string                        $serialized
     * @return false|HTMLPurifier_Definition
     */
    protected function deserialize($serialized)
    {
        $def = unserialize($serialized);
        if ($def instanceof HTMLPurifier_Definition) {
            return $def;
        }

        return false;
    }

    /**
     * Generates cryptographic signature for the serialized data.
     *
     * Note: If you must override this method, then you likely need to also
     * override {@link static::signatureMatches()}.
     *
     * @param  string $serialized
     * @return string
     * @see static::signatureMatches()
     */
    protected function generateSignature($serialized)
    {
        return hash_hmac('sha256', $serialized, $this->signingKey);
    }

    /**
     * Check cryptographic signature for the serialized data.
     *
     * Note: If you must override this method, then you likely need to also
     * override {@link static::generateSignature()}.
     *
     * @param  string $signature
     * @param  string $serialized
     * @return bool
     * @see static::generateSignature()
     */
    protected function signatureMatches($signature, $serialized)
    {
        return hash_equals($signature, $this->generateSignature($serialized));
    }

    /**
     * @return string[]
     */
    protected function getMageCacheTags()
    {
        return [self::MAGE_CACHE_TAG, $this->type];
    }

    /**
     * Gets the Magento cache instance.
     *
     * @return Zend_Cache_Core
     */
    protected function getMageCache()
    {
        return Mage::app()->getCache();
    }
}
