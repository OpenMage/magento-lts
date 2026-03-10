<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

/**
 * Dataflow Batch abstract model
 *
 * @package    Mage_Dataflow
 *
 * @method Mage_Dataflow_Model_Resource_Batch_Abstract getResource()
 */
abstract class Mage_Dataflow_Model_Batch_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Set batch data
     * automatic convert to serialize data
     *
     * @param  mixed                              $data
     * @return Mage_Dataflow_Model_Batch_Abstract
     */
    public function setBatchData($data)
    {
        if ('"libiconv"' == ICONV_IMPL) {
            foreach ($data as &$value) {
                $value = iconv('utf-8', 'utf-8//IGNORE', $value);
            }
        }

        $this->setData('batch_data', serialize($data));

        return $this;
    }

    /**
     * Retrieve batch data
     * return unserialize data
     *
     * @return mixed
     */
    public function getBatchData()
    {
        $data = $this->_data['batch_data'];
        return unserialize($data, ['allowed_classes' => false]);
    }

    /**
     * Retrieve id collection
     *
     * @param  int                 $batchId
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getIdCollection($batchId = null)
    {
        if (!is_null($batchId)) {
            $this->setBatchId($batchId);
        }

        return $this->getResource()->getIdCollection($this);
    }

    /**
     * @throws Mage_Core_Exception
     */
    public function deleteCollection($batchId = null)
    {
        if (!is_null($batchId)) {
            $this->setBatchId($batchId);
        }

        return $this->getResource()->deleteCollection($this);
    }
}
