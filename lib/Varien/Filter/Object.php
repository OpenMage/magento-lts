<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Filter
 */

class Varien_Filter_Object extends Zend_Filter
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
     * @param Varien_Object $object
     * @return mixed
     * @throws Exception
     */
    public function filter($object)
    {
        if (!$object instanceof Varien_Object) {
            throw new Exception('Expecting an instance of Varien_Object');
        }
        $class = get_class($object);
        $out = new $class();
        foreach ($object->getData() as $column => $value) {
            $value = parent::filter($value);
            if (isset($this->_columnFilters[$column])) {
                $value = $this->_columnFilters[$column]->filter($value);
            }
            $out->setData($column, $value);
        }
        return $out;
    }
}
