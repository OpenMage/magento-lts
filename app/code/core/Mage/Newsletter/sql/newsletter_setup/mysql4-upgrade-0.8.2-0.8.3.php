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
 * @category   Mage
 * @package    Mage_Newsletter
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$queueTable = $installer->getTable('newsletter_queue');
$templateTable = $installer->getTable('newsletter_template');
$conn = $installer->getConnection();

$conn->addColumn($queueTable, 'newsletter_type', "int(3) default NULL AFTER `template_id`");
$conn->addColumn($queueTable, 'newsletter_text', "text AFTER `newsletter_type`");
$conn->addColumn($queueTable, 'newsletter_styles', "text AFTER `newsletter_text`");
$conn->addColumn($queueTable, 'newsletter_subject', "varchar(200) default NULL AFTER `newsletter_styles`");
$conn->addColumn($queueTable, 'newsletter_sender_name', "varchar(200) default NULL AFTER `newsletter_subject`");
$conn->addColumn(
    $queueTable,
    'newsletter_sender_email',
    "varchar(200) character set latin1 collate latin1_general_ci default NULL AFTER `newsletter_sender_name`"
);

$conn->modifyColumn($templateTable, 'template_text_preprocessed', "text comment 'deprecated since 1.4.0.1'");

$conn->beginTransaction();

try {
    $select = $conn->select()
        ->from(['main_table' => $queueTable], ['main_table.queue_id', 'main_table.template_id'])
        ->joinLeft(
            $templateTable,
            "$templateTable.template_id = main_table.template_id",
            [
                'template_type',
                'template_text',
                'template_styles',
                'template_subject',
                'template_sender_name',
                'template_sender_email'
            ]
        );
    $rows = $conn->fetchAll($select);

    if ($rows) {
        foreach ($rows as $row) {
            $whereBind = $conn
                ->quoteInto('queue_id=?', $row['queue_id']);

            $conn
                ->update(
                    $queueTable,
                    [
                        'newsletter_type'           => $row['template_type'],
                        'newsletter_text'           => $row['template_text'],
                        'newsletter_styles'         => $row['template_styles'],
                        'newsletter_subject'        => $row['template_subject'],
                        'newsletter_sender_name'    => $row['template_sender_name'],
                        'newsletter_sender_email'   => $row['template_sender_email']
                    ],
                    $whereBind
                );
        }
    }

    $conn->commit();
} catch (Exception $e) {
    $conn->rollBack();
    throw $e;
}
