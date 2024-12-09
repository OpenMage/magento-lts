<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Varien
 * @package    Varien_Filter
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Varien_Filter_Template_Simple
 */
class Varien_Filter_Template_Simple extends Varien_Object implements Zend_Filter_Interface
{
    /**
     * Start tag for variable in template
     *
     * @var string
     */
    protected $_startTag = '{{';

    /**
     * End tag for variable in template
     *
     * @var string
     */
    protected $_endTag = '}}';

    /**
     * Define start tag and end tag
     *
     * @param string $start
     * @param string $end
     * @return Varien_Filter_Template_Simple
     */
    public function setTags($start, $end)
    {
        $this->_startTag = $start;
        $this->_endTag = $end;
        return $this;
    }

    /**
     * Return result of getData method for matched variables
     *
     * @param array $matches
     * @return mixed
     */
    protected function _filterDataItem($matches)
    {
        return $this->getData($matches[1]);
    }

    /**
     * Insert data to template
     *
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        return preg_replace_callback(
            '#' . $this->_startTag . '(.*?)' . $this->_endTag . '#',
            [$this, '_filterDataItem'],
            $value
        );
    }
}
