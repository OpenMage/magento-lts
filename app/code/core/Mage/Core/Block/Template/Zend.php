<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Zend html block
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Block_Template_Zend extends Mage_Core_Block_Template
{
    /**
     * @var Zend_View
     */
    protected $_view = null;

    /**
     * Class constructor. Base html block
     */
    public function _construct()
    {
        parent::_construct();
        $this->_view = new Zend_View();
    }

    /**
     * @param array|string $key
     * @param array|string|null $value
     * @return $this|Mage_Core_Block_Template
     * @throws Zend_View_Exception
     */
    public function assign($key, $value = null)
    {
        if (is_array($key) && is_null($value)) {
            foreach ($key as $k => $v) {
                $this->assign($k, $v);
            }
        } elseif (!is_null($value)) {
            $this->_view->assign($key, $value);
        }
        return $this;
    }

    /**
     * @param string $dir
     * @return $this
     */
    public function setScriptPath($dir)
    {
        $this->_view->setScriptPath($dir . DS);
        return $this;
    }

    /**
     * @param string $fileName
     * @return string
     */
    public function fetchView($fileName)
    {
        return $this->_view->render($fileName);
    }
}
