<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Subscribe',
	array(
		'Subscription' => 'component',
		
	),
	array(
		'Subscription' => 'component',
		
	)
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Subscriptions',
	array(
		'Subscription' => 'list,delete',

	),
	array(
		'Subscription' => 'list,delete',

	)
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Timeline',
	array(
		'Subscription' => 'timeline,reset',

	),
	array(
		'Subscription' => 'timeline,reset',

	)
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'Tx_Notify_Command_NotificationCommandController';

?>