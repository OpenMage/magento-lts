<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
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
