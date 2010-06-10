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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Directory
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Directory upgrade - adding regions of France (#6068)
 *
 * @category   Mage
 * @package    Mage_Directory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();
$installer->run("
INSERT INTO `{$installer->getTable('directory_country_region')}` (`country_id`, `code`, `default_name`)
VALUES
('FR', '01', 'Ain'),('FR', '02', 'Aisne'),('FR', '03', 'Allier'),
('FR', '04', 'Alpes-de-Haute-Provence'),('FR', '05', 'Hautes-Alpes'),('FR', '06', 'Alpes-Maritimes'),
('FR', '07', 'Ardèche'),('FR', '08', 'Ardennes'),('FR', '09', 'Ariège'),
('FR', '10', 'Aube'),('FR', '11', 'Aude'),('FR', '12', 'Aveyron'),
('FR', '13', 'Bouches-du-Rhône'),('FR', '14', 'Calvados'),('FR', '15', 'Cantal'),
('FR', '16', 'Charente'),('FR', '17', 'Charente-Maritime'),('FR', '18', 'Cher'),
('FR', '19', 'Corrèze'),('FR', '2A', 'Corse-du-Sud'),('FR', '2B', 'Haute-Corse'),
('FR', '21', 'Côte-d\\'Or'),('FR', '22', 'Côtes-d\\'Armor'),('FR', '23', 'Creuse'),
('FR', '24', 'Dordogne'),('FR', '25', 'Doubs'),('FR', '26', 'Drôme'),
('FR', '27', 'Eure'),('FR', '28', 'Eure-et-Loir'),('FR', '29', 'Finistère'),
('FR', '30', 'Gard'),('FR', '31', 'Haute-Garonne'),('FR', '32', 'Gers'),
('FR', '33', 'Gironde'),('FR', '34', 'Hérault'),('FR', '35', 'Ille-et-Vilaine'),
('FR', '36', 'Indre'),('FR', '37', 'Indre-et-Loire'),('FR', '38', 'Isère'),
('FR', '39', 'Jura'),('FR', '40', 'Landes'),('FR', '41', 'Loir-et-Cher'),
('FR', '42', 'Loire'),('FR', '43', 'Haute-Loire'),('FR', '44', 'Loire-Atlantique'),
('FR', '45', 'Loiret'),('FR', '46', 'Lot'),('FR', '47', 'Lot-et-Garonne'),
('FR', '48', 'Lozère'),('FR', '49', 'Maine-et-Loire'),('FR', '50', 'Manche'),
('FR', '51', 'Marne'),('FR', '52', 'Haute-Marne'),('FR', '53', 'Mayenne'),
('FR', '54', 'Meurthe-et-Moselle'),('FR', '55', 'Meuse'),('FR', '56', 'Morbihan'),
('FR', '57', 'Moselle'),('FR', '58', 'Nièvre'),('FR', '59', 'Nord'),
('FR', '60', 'Oise'),('FR', '61', 'Orne'),('FR', '62', 'Pas-de-Calais'),
('FR', '63', 'Puy-de-Dôme'),('FR', '64', 'Pyrénées-Atlantiques'),('FR', '65', 'Hautes-Pyrénées'),
('FR', '66', 'Pyrénées-Orientales'),('FR', '67', 'Bas-Rhin'),('FR', '68', 'Haut-Rhin'),
('FR', '69', 'Rhône'),('FR', '70', 'Haute-Saône'),('FR', '71', 'Saône-et-Loire'),
('FR', '72', 'Sarthe'),('FR', '73', 'Savoie'),('FR', '74', 'Haute-Savoie'),
('FR', '75', 'Paris'),('FR', '76', 'Seine-Maritime'),('FR', '77', 'Seine-et-Marne'),
('FR', '78', 'Yvelines'),('FR', '79', 'Deux-Sèvres'),('FR', '80', 'Somme'),
('FR', '81', 'Tarn'),('FR', '82', 'Tarn-et-Garonne'),('FR', '83', 'Var'),
('FR', '84', 'Vaucluse'),('FR', '85', 'Vendée'),('FR', '86', 'Vienne'),
('FR', '87', 'Haute-Vienne'),('FR', '88', 'Vosges'),('FR', '89', 'Yonne'),
('FR', '90', 'Territoire-de-Belfort'),('FR', '91', 'Essonne'),('FR', '92', 'Hauts-de-Seine'),
('FR', '93', 'Seine-Saint-Denis'),('FR', '94', 'Val-de-Marne'),('FR', '95', 'Val-d\\'Oise');
");
$installer->endSetup();
