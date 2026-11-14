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
     * array of its children with encrypted values already decrypted.
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

        foreach ($configModel->getEncryptedNodeEntriesPaths() as $encryptedPath) {
            if ($encryptedPath === $path || str_starts_with($encryptedPath, $path . '/')) {
                return true;
            }
        }

        return false;
    }
}
