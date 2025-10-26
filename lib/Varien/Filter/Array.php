<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Filter
 */

class Varien_Filter_Array extends Zend_Filter
{
    /**
     * @var array
     */
    protected $_columnFilters = [];

    /**
     * @param string $column
     * @return $this
     */
    public function addFilter(Zend_Filter_Interface $filter, $column = '')
    {
        if ('' === $column) {
            parent::addFilter($filter);
        } else {
            if (!isset($this->_columnFilters[$column])) {
                $this->_columnFilters[$column] = new Zend_Filter();
            }

            $this->_columnFilters[$column]->addFilter($filter);
        }

        return $this;
    }

    /**
     * @param array $array
     * @return array
     */
    public function filter($array)
    {
        $out = [];
        foreach ($array as $column => $value) {
            $value = parent::filter($value);
            if (isset($this->_columnFilters[$column])) {
                $value = $this->_columnFilters[$column]->filter($value);
            }

            $out[$column] = $value;
        }

        return $out;
    }
}
