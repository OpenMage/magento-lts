<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
