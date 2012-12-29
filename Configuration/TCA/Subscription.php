<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_notify_domain_model_subscription'] = array(
	'ctrl' => $TCA['tx_notify_domain_model_subscription']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, active, mode, subscriber, source, source_file, source_table, source_uid, source_fields, checksum, last_notification_date, url',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, active, subscriber, mode, source, source_file, source_table, source_uid, source_fields, checksum, last_notification_date, url,--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_notify_domain_model_subscription',
				'foreign_table_where' => 'AND tx_notify_domain_model_subscription.pid=###CURRENT_PID### AND tx_notify_domain_model_subscription.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'mode' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:notify/Resources/Private/Language/locallang_db.xml:tx_notify_domain_model_subscription.mode',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('LLL:EXT:notify/Resources/Private/Language/locallang_db.xml:tx_notify_domain_model_subscription.mode.0', 0),
					array('LLL:EXT:notify/Resources/Private/Language/locallang_db.xml:tx_notify_domain_model_subscription.mode.1', 1),
					array('LLL:EXT:notify/Resources/Private/Language/locallang_db.xml:tx_notify_domain_model_subscription.mode.2', 2),
				),
				'size' => 1,
				'maxitems' => 1,
				'eval' => '',
			),
		),
		'source' => array(
			'exclude' => 0,
			'config' => array(
				'type' => 'passthrough'
			)
		),
		'source_file' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:notify/Resources/Private/Language/locallang_db.xml:tx_notify_domain_model_subscription.source_file',
			'displayCond' => 'FIELD:mode:=:2',
			'config' => array(
				'type' => 'input',
				'internal_type' => 'file',
				'allowed' => '',
				'size' => 30,
				'wizards' => array(
					'_PADDING' => 0,
					'_VERTICAL' => 0,
					'link' => array(
							'type' => 'popup',
							'title' => 'Select file',
							'icon' => 'link_popup.gif',
							'script' => 'browse_links.php?mode=wizard&act=file',
							'hideParent' => 0,
							'allowedExtensions' => '',
							'JSopenParams' => 'height=500,width=800,status=0,menubar=0,scrollbars=1'
						),
					),
			),
		),
		'source_table' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:notify/Resources/Private/Language/locallang_db.xml:tx_notify_domain_model_subscription.source_table',
			'displayCond' => 'FIELD:mode:=:1',
			'config' => array(
				'type' => 'select',
				'special' => 'tables',
				'size' => 1,
				'eval' => 'trim'
			),
		),
		'source_uid' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:notify/Resources/Private/Language/locallang_db.xml:tx_notify_domain_model_subscription.source_uid',
			'displayCond' => 'FIELD:mode:<:2',
			'config' => array(
				'type' => 'select',
				'itemsProcFunc' => 'EXT:notify/Classes/UserFunction/TableRecordsItemsProcessingFunction.php:Tx_Notify_UserFunction_TableRecordsItemsProcessingFunction->itemsProcFunc',
				'size' => 1,
				'eval' => 'trim',
				'wizards' => array(
					'list' => array(
						"type" => "script",
						"title" => "List ###REC_FIELD_source_table###",
						"icon" => "list.gif",
						"params" => Array(
							"table" => "###REC_FIELD_source_table###",
							"pid" => "###CURRENT_PID###",
						),
						"script" => "wizard_list.php",
					)
				)
			),
		),
		'source_fields' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:notify/Resources/Private/Language/locallang_db.xml:tx_notify_domain_model_subscription.source_fields',
			'displayCond' => 'FIELD:mode:=:1',
			'config' => array(
				'type' => 'select',
				'itemsProcFunc' => 'EXT:notify/Classes/UserFunction/TableColumnsItemProcessingFunction.php:Tx_Notify_UserFunction_TableColumnsItemProcessingFunction->itemsProcFunc',
				'size' => 6,
				'renderMode' => 'checkbox',
				'maxitems' => 10000,
				'eval' => 'trim'
			),
		),
		'active' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:notify/Resources/Private/Language/locallang_db.xml:tx_notify_domain_model_subscription.active',
			'config' => array(
				'type' => 'check',
				'default' => 0
			),
		),
		'checksum' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:notify/Resources/Private/Language/locallang_db.xml:tx_notify_domain_model_subscription.checksum',
			'config' => array(
				'type' => 'passthrough',
			),
		),
		'last_notification_date' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:notify/Resources/Private/Language/locallang_db.xml:tx_notify_domain_model_subscription.last_notification_date',
			'config' => array(
				'type' => 'input',
				'size' => 12,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 1,
				'default' => time()
			),
		),
		'subscriber' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:notify/Resources/Private/Language/locallang_db.xml:tx_notify_domain_model_subscription.subscriber',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'url' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:notify/Resources/Private/Language/locallang_db.xml:tx_notify_domain_model_subscription.url',
			'config' => array(
				'type' => 'input',
				'size' => 70,
				'eval' => 'trim'
			),
		),
	),
);
?>