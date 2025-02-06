<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Install
 */

/**
 * Installation ending block
 *
 * @category   Mage
 * @package    Mage_Install
 */
class Mage_Install_Block_End extends Mage_Install_Block_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('install/end.phtml');
    }

    public function getEncryptionKey()
    {
        $key = $this->getData('encryption_key');
        if (is_null($key)) {
            $key = (string) Mage::getConfig()->getNode('global/crypt/key');
            $this->setData('encryption_key', $key);
        }
        return $key;
    }
}
