<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Varien
 * @package    Varien_Crypt
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Openssl plugin
 *
 * @category   Varien
 * @package    Varien_Crypt
 */
class Varien_Crypt_Openssl extends Varien_Crypt_Abstract
{

    private $_key;

    /**
     * Initialize encryption module
     *
     * @param string $key cipher private key
     * @return $this
     */
    public function init($key)
    {
        $this->_key = $key;

        return $this;
    }

    /**
     * Encrypt data
     *
     * @param string $data source string
     * @return string
     */
    public function encrypt($data)
    {
        return openssl_encrypt($data, 'bf-ecb', $this->_key, OPENSSL_RAW_DATA);
    }

    /**
     * Decrypt data
     *
     * @param string $data encrypted string
     * @return string
     */
    public function decrypt($data)
    {
        return openssl_decrypt($data, 'bf-ecb', $this->_key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
    }

}
