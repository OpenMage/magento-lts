<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Zend html block
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
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
     * @param null $value
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
     * @return void
     */
    public function setScriptPath($dir)
    {
        $this->_view->setScriptPath($dir.DS);
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
