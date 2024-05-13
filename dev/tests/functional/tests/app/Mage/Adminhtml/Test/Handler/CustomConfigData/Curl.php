<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Handler\CustomConfigData;

use Mage\Core\Test\Handler\ConfigData\Curl as ConfigDataCurl;

/**
 * Curl for setting config.
 */
class Curl extends ConfigDataCurl implements CustomConfigDataInterface
{
    /**
     * {@inheritdoc}
     */
    protected function prepareConfigPath(array $input)
    {
        $resultArray = '';
        $InputValue = isset($input['value']) ? $input['value'] : null;
        $path = explode('/', str_replace($input['scope'], '', $input['path']));
        array_unshift($path, rtrim($input['scope'], '/'));
        foreach ($path as $position => $subPath) {
            if ($position === 0) {
                $resultArray .= $subPath;
                continue;
            } elseif ($position === (count($path) - 1)) {
                $resultArray .= '[fields]';
            } else {
                $resultArray .= '[groups]';
            }
            $resultArray .= '[' . $subPath . ']';
        }
        $resultArray .= '[value]';
        if (is_array($InputValue)) {
            $values = [];
            foreach ($InputValue as $key => $value) {
                $values[] = $resultArray . "[$key]=$value";
            }
            $resultArray = implode('&', $values);
        } elseif(!empty($InputValue)) {
            $resultArray .= '=' . $InputValue;
        }
        return $resultArray;
    }
}
