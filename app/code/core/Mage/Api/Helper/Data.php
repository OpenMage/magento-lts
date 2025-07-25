<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * Web service api main helper
 *
 * @package    Mage_Api
 */
class Mage_Api_Helper_Data extends Mage_Core_Helper_Abstract
{
    public const XML_PATH_API_WSI = 'api/config/compliance_wsi';

    protected $_moduleName = 'Mage_Api';

    /**
     * Method to find adapter code depending on WS-I compatibility setting
     *
     * @return string
     */
    public function getV2AdapterCode()
    {
        return $this->isComplianceWSI() ? 'soap_wsi' : 'soap_v2';
    }

    /**
     * @return bool
     */
    public function isComplianceWSI()
    {
        return Mage::getStoreConfig(self::XML_PATH_API_WSI);
    }

    /**
     * Go through a WSI args array and turns it to correct state.
     *
     * @param Object $obj - Link to Object
     */
    public function wsiArrayUnpacker(&$obj)
    {
        if (is_object($obj)) {
            $modifiedKeys = $this->clearWsiFootprints($obj);

            foreach ($obj as $value) {
                if (is_object($value)) {
                    $this->wsiArrayUnpacker($value);
                }
                if (is_array($value)) {
                    foreach ($value as &$val) {
                        if (is_object($val)) {
                            $this->wsiArrayUnpacker($val);
                        }
                    }
                }
            }

            foreach ($modifiedKeys as $arrKey) {
                if ($arrKey !== 'complex_filter') {
                    $this->associativeArrayUnpack($obj->$arrKey);
                }
            }
        }
    }

    /**
     * Go through an object parameters and unpack associative object to array.
     *
     * @param Object|array $obj - Link to Object
     * @return bool
     */
    public function v2AssociativeArrayUnpacker(&$obj)
    {
        if (is_object($obj)
            && property_exists($obj, 'key')
            && property_exists($obj, 'value')
        ) {
            if (count(array_keys(get_object_vars($obj))) == 2) {
                $obj = [$obj->key => $obj->value];
                return true;
            }
        } elseif (is_array($obj)) {
            $arr = [];
            $needReplacement = true;
            foreach ($obj as &$value) {
                $isAssoc = $this->v2AssociativeArrayUnpacker($value);
                if ($isAssoc) {
                    foreach ($value as $aKey => $aVal) {
                        $arr[$aKey] = $aVal;
                    }
                } else {
                    $needReplacement = false;
                }
            }
            if ($needReplacement) {
                $obj = $arr;
            }
        } elseif (is_object($obj)) {
            $objectKeys = array_keys(get_object_vars($obj));

            foreach ($objectKeys as $key) {
                $this->v2AssociativeArrayUnpacker($obj->$key);
            }
        }
        return false;
    }

    /**
     * Go through mixed and turns it to a correct look.
     *
     * @param Mixed $mixed A link to variable that may contain associative array.
     */
    public function associativeArrayUnpack(&$mixed)
    {
        if (is_array($mixed)) {
            $tmpArr = [];
            foreach ($mixed as $key => $value) {
                if (is_object($value)) {
                    $value = get_object_vars($value);
                    if (count($value) == 2 && isset($value['key']) && isset($value['value'])) {
                        $tmpArr[$value['key']] = $value['value'];
                    }
                }
            }
            if (count($tmpArr)) {
                $mixed = $tmpArr;
            }
        }

        if (is_object($mixed)) {
            $numOfVals = count(get_object_vars($mixed));
            if ($numOfVals == 2 && isset($mixed->key) && isset($mixed->value)) {
                $mixed = get_object_vars($mixed);
                /*
                 * Processing an associative arrays.
                 * $mixed->key = '2'; $mixed->value = '3'; turns to array(2 => '3');
                 */
                $mixed = [$mixed['key'] => $mixed['value']];
            }
        }
    }

    /**
     * Corrects data representation.
     *
     * @param Object $obj - Link to Object
     * @return string[]
     */
    public function clearWsiFootprints(&$obj)
    {
        $modifiedKeys = [];

        $objectKeys = array_keys(get_object_vars($obj));

        foreach ($objectKeys as $key) {
            if (is_object($obj->$key) && isset($obj->$key->complexObjectArray)) {
                if (is_array($obj->$key->complexObjectArray)) {
                    $obj->$key = $obj->$key->complexObjectArray;
                } else { // for one element array
                    $obj->$key = [$obj->$key->complexObjectArray];
                }
                $modifiedKeys[] = $key;
            }
        }
        return $modifiedKeys;
    }

    /**
     * For the WSI, generates an response object.
     *
     * @param mixed $mixed - Link to Object
     * @return mixed
     */
    public function wsiArrayPacker($mixed)
    {
        if (is_array($mixed)) {
            $arrKeys = array_keys($mixed);
            $isDigit = false;
            foreach ($arrKeys as $key) {
                if (is_int($key)) {
                    $isDigit = true;
                    break;
                }
            }
            if ($isDigit) {
                $mixed = $this->packArrayToObject($mixed);
            } else {
                $mixed = (object) $mixed;
            }
        }
        if (is_object($mixed) && isset($mixed->complexObjectArray)) {
            foreach ($mixed->complexObjectArray as $k => $v) {
                $mixed->complexObjectArray[$k] = $this->wsiArrayPacker($v);
            }
        }
        return $mixed;
    }

    /**
     * For response to the WSI, generates an object from array.
     *
     * @param array $arr - Link to Object
     * @return stdClass
     */
    public function packArrayToObject(array $arr)
    {
        $obj = new stdClass();
        $obj->complexObjectArray = $arr;
        return $obj;
    }

    /**
     * Convert objects and arrays to array recursively
     *
     * @param array|object $data
     * @param-out array $data
     */
    public function toArray(&$data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        if (is_array($data)) {
            foreach ($data as &$value) {
                if (is_array($value) || is_object($value)) {
                    $this->toArray($value);
                }
            }
        }
    }

    /**
     * Parse filters and format them to be applicable for collection filtration
     *
     * @param null|object|array $filters
     * @param array $fieldsMap Map of field names in format: array('field_name_in_filter' => 'field_name_in_db')
     * @return array
     */
    public function parseFilters($filters, $fieldsMap = null)
    {
        // if filters are used in SOAP they must be represented in array format to be used for collection filtration
        if (is_object($filters)) {
            $parsedFilters = [];
            // parse simple filter
            if (isset($filters->filter) && is_array($filters->filter)) {
                foreach ($filters->filter as $field => $value) {
                    if (is_object($value) && isset($value->key) && isset($value->value)) {
                        $parsedFilters[$value->key] = $value->value;
                    } else {
                        $parsedFilters[$field] = $value;
                    }
                }
            }
            // parse complex filter
            if (isset($filters->complex_filter) && is_array($filters->complex_filter)) {
                $parsedFilters += $this->_parseComplexFilter($filters->complex_filter);
            }

            $filters = $parsedFilters;
        }
        // make sure that method result is always array
        if (!is_array($filters)) {
            $filters = [];
        }
        // apply fields mapping
        if (isset($fieldsMap) && is_array($fieldsMap)) {
            foreach ($filters as $field => $value) {
                if (isset($fieldsMap[$field])) {
                    unset($filters[$field]);
                    $field = $fieldsMap[$field];
                    $filters[$field] = $value;
                }
            }
        }
        return $filters;
    }

    /**
     * Parses complex filter, which may contain several nodes, e.g. when user want to fetch orders which were updated
     * between two dates.
     *
     * @param array $complexFilter
     * @return array
     */
    protected function _parseComplexFilter($complexFilter)
    {
        $parsedFilters = [];

        foreach ($complexFilter as $filter) {
            if (!isset($filter->key) || !isset($filter->value)) {
                continue;
            }
            $fieldName = $filter->key;
            $condition = $filter->value;
            $conditionName = $condition->key;
            $conditionValue = $condition->value;
            $this->formatFilterConditionValue($conditionName, $conditionValue);

            if (array_key_exists($fieldName, $parsedFilters)) {
                $parsedFilters[$fieldName] += [$conditionName => $conditionValue];
            } else {
                $parsedFilters[$fieldName] = [$conditionName => $conditionValue];
            }
        }

        return $parsedFilters;
    }

    /**
     * Convert condition value from the string into the array
     * for the condition operators that require value to be an array.
     * Condition value is changed by reference
     *
     * @param string $conditionOperator
     * @param string $conditionValue
     * @param-out string|array $conditionValue
     */
    public function formatFilterConditionValue($conditionOperator, &$conditionValue)
    {
        if (is_string($conditionOperator) && in_array($conditionOperator, ['in', 'nin', 'finset'])
            && is_string($conditionValue)
        ) {
            $delimiter = ',';
            $conditionValue = explode($delimiter, $conditionValue);
        }
    }

    /**
     * Get wsdl cache id
     *
     * @return string
     */
    public function getCacheId()
    {
        return 'wsdl_config_global_' . md5($this->getServiceUrl('*/*/*'));
    }

    /**
     * Get service url
     *
     * @param string|null $routePath
     * @param array|null $routeParams
     * @param bool $htmlSpecialChars
     * @return string
     * @throws Zend_Uri_Exception
     */
    public function getServiceUrl($routePath = null, $routeParams = null, $htmlSpecialChars = false)
    {
        $request = Mage::app()->getRequest();

        if (is_null($routeParams)) {
            $routeParams = [];
        }

        $routeParams['_nosid'] = true;

        /** @var Mage_Core_Model_Url $urlModel */
        $urlModel = Mage::getSingleton('core/url');
        $url = $urlModel->getUrl($routePath, $routeParams);
        $uri = Zend_Uri_Http::fromString($url);
        $uri->setHost($request->getHttpHost());
        if (!$urlModel->getRouteFrontName()) {
            $uri->setPath('/' . trim($request->getBasePath() . '/' . basename(getenv('SCRIPT_FILENAME')), '/'));
        } else {
            $uri->setPath($request->getBaseUrl() . $request->getPathInfo());
        }

        return $htmlSpecialChars === true ? htmlspecialchars($uri) : (string) $uri;
    }
}
