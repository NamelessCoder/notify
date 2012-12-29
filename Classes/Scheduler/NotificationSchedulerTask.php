<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Claus Due <claus@wildside.dk>, Wildside A/S
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Notification Scheduler Task
 *
 * Sends out notifications about updated objects which
 * have been previously collected by the Polling Scheduler
 * Task or corresponding CommandController task.
 *
 * Use this as a "digest" style notification sender; the
 * electronic message that is generated will contain all
 * objects which have been updated
 *
 * @package Notify
 * @subpackage Scheduler
 */
class Tx_Notify_Scheduler_NotificationSchedulerTask extends Tx_Notify_Scheduler_AbstractSchedulerTask {

	/**
	 * CONSTRUCTOR
	 */
	public function __construct() {
		/** @var Tx_Extbase_Object_ObjectManager $objectManager */
		$objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		/** @var Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager */
		$configurationManager = $objectManager->get('Tx_Extbase_Configuration_ConfigurationManagerInterface');
		$typoScript = $configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$this->settings = $this->convertTypoScriptArrayToPlainArray($typoScript['plugin.']['tx_notify.']['settings.']);
		parent::__construct();
	}

	/**
	 * This is the main method that is called when a task is executed
	 * It MUST be implemented by all classes inheriting from this one
	 * Note that there is no error handling, errors and failures are expected
	 * to be handled and logged by the client implementations.
	 * Should return TRUE on successful execution, FALSE on error.
	 *
	 * @return boolean Returns TRUE on successful execution, FALSE on error
	 */
	public function execute() {
		/** @var Tx_Extbase_Object_ObjectManager $objectManager */
		$objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		/** @var Tx_Notify_Service_SubscriptionService $subscriptionService */
		$subscriptionService = $objectManager->get('Tx_Notify_Service_SubscriptionService');
		$subscriptionSourceProvider = $subscriptionService->getSourceProviderInstance($this->settings);

		$subscriptions = $subscriptionService->getAllActiveSubscriptions();
		$rewriteChecksum = TRUE; // if FALSE, does not update the DB with the new checksum of updated objects that the subscriber was notified about
		$updates = $subscriptionService->buildUpdatedContentObjects($subscriptions, $rewriteChecksum);
		$groupedSubscriptions = array();
		foreach ($updates as $subscription) {
			/** @var Tx_Notify_Domain_Model_Subscription $subscription */
			$subscriber = $subscription->getSubscriber();
			if (is_array($groupedSubscriptions[$subscriber]) === FALSE) {
				$groupedSubscriptions[$subscriber] = array();
			}
			array_push($groupedSubscriptions[$subscriber], $subscription);
		}
		foreach ($groupedSubscriptions as $subscriber => $subscriptions) {
			try {
				$message = $subscriptionSourceProvider->getMessageInstance($subscriber);
				$message->assign('subscriptions', $subscriptions);
				$message->send();
				foreach ($subscriptions as $subscription) {
					$now = new DateTime();
					$now->createFromFormat('U', time());
					$subscription->setLastNotificationDate($now);
					$subscriptionService->update($subscription);
				}
			} catch (Exception $error) {
				t3lib_div::sysLog($error->getMessage(), 'Notify', 2);
			}
		}
		return TRUE;
	}

	/**
	 * Return a text representation of the selected command and arguments
	 *
	 * @return string
	 */
	public function getAdditionalInformation() {
		/** @var Tx_Extbase_Object_ObjectManager $objectManager */
		$objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		/** @var Tx_Notify_Service_SubscriptionService $subscriptionService */
		$subscriptionService = $objectManager->get('Tx_Notify_Service_SubscriptionService');
		$providerName = $this->settings['source']['provider'];
		$subscriptions = $subscriptionService->getAllActiveSubscriptions();
		$numSubscriptions = $subscriptions->count();
		return $providerName . ': ' . $numSubscriptions . ' subscription' . ($numSubscriptions > 0 ? 's' : '');
	}

}