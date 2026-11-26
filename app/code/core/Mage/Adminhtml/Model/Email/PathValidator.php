<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Validator for Email Template
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_Email_PathValidator
{
    /**
     * A config path the validator can reason about: segments of letters, digits, underscore, dot
     * and hyphen. Varien_Simplexml_Element::descend() gives other syntax a meaning of its own (a
     * `node@attribute=value` segment selects by attribute, and `@x=` matches any node), so a path
     * that is not plain could resolve to an encrypted node while comparing unequal to its path.
     */
    public const PLAIN_PATH_PATTERN = '#^[A-Za-z0-9_.\-]+(?:/[A-Za-z0-9_.\-]+)*$#';

    /**
     * Returns true if and only if $value meets the validation requirements
     * If $value fails validation, then this method returns false
     *
     * @param  mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $pathNode = is_array($value) ? array_shift($value) : $value;

        return $this->isEncryptedNodePath($pathNode);
    }

    /**
     * Return bool after checking the encrypted model in the path to config node
     *
     * A parent of an encrypted path counts as encrypted too: getConfig() on a group returns an
     * array of its children with encrypted values already decrypted. A path that is not plain (see
     * PLAIN_PATH_PATTERN) counts as encrypted, because a string comparison cannot tell which node
     * it resolves to.
     *
     * @param  string $path
     * @return bool
     */
    protected function isEncryptedNodePath($path)
    {
        /** @var Mage_Adminhtml_Model_Config $configModel */
        $configModel = Mage::getSingleton('adminhtml/config');

        $path = trim((string) $path, '/');
        if ($path === '') {
            return false;
        }

        if (preg_match(self::PLAIN_PATH_PATTERN, $path) !== 1) {
            return true;
        }

        foreach ($configModel->getEncryptedNodeEntriesPaths() as $encryptedPath) {
            if ($encryptedPath === $path || str_starts_with($encryptedPath, $path . '/')) {
                return true;
            }
        }

        return false;
    }
}
