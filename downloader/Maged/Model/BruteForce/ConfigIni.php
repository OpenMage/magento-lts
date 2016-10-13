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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Connect
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Maged_Model_BruteForce_ConfigIni implements Maged_Model_BruteForce_ModelConfigInterface
{
    /**
     * @var array
     */
    protected $data = array();
    /**
     * @var Maged_Model_BruteForce_Resource_ResourceInterface
     */
    private $resource;

    /**
     * BruteForceConfig constructor.
     * @param Maged_Model_BruteForce_Resource_ResourceInterface $resource
     * @throws Exception
     */
    public function __construct(Maged_Model_BruteForce_Resource_ResourceInterface $resource)
    {
        if ($resource->isReadable()) {
            $this->resource = $resource;
            $this->readConfig();
        } else {
            throw new Exception("Unable to read the configuration file.");
        }
    }

    /**
     * @throws Exception
     */
    public function readConfig()
    {
        if (false === $data = parse_ini_string($this->resource->read())) {
            throw new Exception("Incorrect configuration file.");
        }
        $this->data = $data;
    }

    /**
     * @param string $name
     * @param null $defaultValue
     * @return mixed|null
     */
    public function get($name, $defaultValue = null)
    {
        return (isset($this->data[$name]) ? $this->data[$name] : $defaultValue);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     * @throws Exception
     */
    public function set($name, $value)
    {
        if (is_array($value) or is_object($value)) {
            throw new Exception ("Bad value type.");
        }
        $this->data[$name] = $value;
        return $this;
    }

    public function save()
    {
        if ($this->resource->isWritable()) {
            $res = array();
            foreach ($this->data as $key => $value) {
                $res[] = "$key = " . (is_numeric($value) ? $value : '"' . $value . '"');
            }
            $content = implode("\n", $res);
            $this->resource->write($content);
        } else {
            throw new Exception("Unable to write to the configuration file.");
        }
    }

    /**
     * @param string $name
     * @return $this
     */
    public function delete($name)
    {
        if (isset($this->data[$name])) {
            unset($this->data[$name]);
        }
        return $this;
    }

}
