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
 * @package    Varien_Object
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Maged_Model_Config extends Maged_Model
{
    public function saveConfigPost($p)
    {
        $this->set('preferred_state', $p['preferred_state']);
        //$this->set('mage_dir', $p['mage_dir']);
        $this->save();
        return $this;
    }

    public function getFilename()
    {
        return $this->controller()->filepath('config.ini');
    }

    public function load()
    {
        if (!file_exists($this->getFilename())) {
            return $this;
        }
        $rows = file($this->getFilename());
        if (!$rows) {
            return $this;
        }
        foreach ($rows as $row) {
            $arr = explode('=', $row, 2);
            if (count($arr)!==2) {
                continue;
            }
            $key = trim($arr[0]);
            $value = trim($arr[1], " \t\"'");
            if (!$key || $key[0]=='#' || $key[0]==';') {
                continue;
            }
            $this->set($key, $value);
        }
        return $this;
    }

    public function save()
    {
        if (!is_writable($this->getFilename())) {
            $this->controller()->session()
                ->addMessage('error', 'Invalid file permissions, could not save configuration.');
            return $this;
        }
        $fp = fopen($this->getFilename(), 'w');
        foreach ($this->_data as $k=>$v) {
            fwrite($fp, $k.'='.$v."\n");
        }
        fclose($fp);
        return $this;
    }
}
