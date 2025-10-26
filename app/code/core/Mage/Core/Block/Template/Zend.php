<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Zend html block
 *
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
