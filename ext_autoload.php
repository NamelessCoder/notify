<?php
$extensionClassesPath = t3lib_extMgm::extPath('notify') . 'Classes/';
return array(
	'tx_notify_scheduler_abstractschedulertask' => $extensionClassesPath . 'Scheduler/AbstractSchedulerTask.php',
	'tx_notify_scheduler_notificationschedulertask' => $extensionClassesPath . 'Scheduler/NotificationSchedulerTask.php',
	'tx_notify_scheduler_sharedfieldprovider' => $extensionClassesPath . 'Scheduler/SharedFieldProvider.php',
);
?>