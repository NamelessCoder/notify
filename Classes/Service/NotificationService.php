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
 * @package Notify
 * @subpackage Service
 */
class Tx_Notify_Service_NotificationService extends Tx_Notify_Service_AbstractService implements t3lib_Singleton {

	/**
	 * @var Tx_Notify_Service_SubscriptionService
	 */
	protected $subscriptionService;

	/**
	 * @param Tx_Notify_Service_SubscriptionService $subscriptionService
	 */
	public function injectSubscriptionService(Tx_Notify_Service_SubscriptionService $subscriptionService) {
		$this->subscriptionService = $subscriptionService;
	}

	/**
	 * @param $subscriber
	 * @param Traversable $subscriptions
	 * @return boolean
	 */
	public function sendSubscribedNotificationsToSubscriber($subscriber, Traversable $subscriptions) {
		$typoScriptSettings = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$typoScriptSettings = $typoScriptSettings['plugin.']['tx_notify.']['settings.'];
		$messageType = isset($typoScriptSettings['email.']['class']) ? $typoScriptSettings['email.']['class'] : 'Tx_Notify_Message_FluidEmail';
		try {
			/** @var $message Tx_Notify_Message_FluidEmail */
			$message = $this->objectManager->create($messageType);
			$message->setRecipient($subscriber);
			$message->setBody($typoScriptSettings['email.']['template.']['templatePathAndFilename'], TRUE);
			$message->assign('subscriptions', $subscriptions);
			$message->assign('subscriber', $subscriber);
			return $message->send();
		} catch (Exception $error) {
			t3lib_div::sysLog($error->getMessage(), 'notify', t3lib_div::SYSLOG_SEVERITY_ERROR);
			return FALSE;
		}
	}

	/**
	 * Send all notifications to stored subscriptions as triggered by $sourceProvider
	 *
	 * @param Tx_Notify_Subscription_SourceProviderInterface $sourceProvider
	 * @return boolean
	 */
	public function sendNotifications(Tx_Notify_Subscription_SourceProviderInterface $sourceProvider) {
		$typoScriptSettings = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$typoScriptSettings = $typoScriptSettings['plugin.']['tx_notify.']['settings.'];
		$subscriptions = $sourceProvider->getSubscriptions();
		$triggeredSubscriptions = $this->subscriptionService->buildUpdatedContentObjects($subscriptions, TRUE);
		$total = $triggeredSubscriptions->count();
		$sent = 0;
		$messageType = isset($typoScriptSettings['email.']['class']) ? $typoScriptSettings['email.']['class'] : 'Tx_Notify_Message_FluidEmail';
		foreach ($triggeredSubscriptions as $subscription) {
			try {
				/** @var $subscription Tx_Notify_Domain_Model_Subscription */
				$subscriber = $subscription->getSubscriber();
				/** @var $message Tx_Notify_Message_FluidEmail */
				$message = $this->objectManager->create($messageType);
				$message->setRecipient($subscriber);
				$message->assign('subscriptions', array($subscription));
				$message->assign('subscriber', $subscriber);
				$message->setBody($typoScriptSettings['email.']['template.']['templatePathAndFilename'], TRUE);
				$message->send();
				$sent++;
			} catch (Exception $error) {
				t3lib_div::sysLog($error->getMessage(), 'Notify', t3lib_div::SYSLOG_SEVERITY_ERROR);
			}
		}
		return $sent === $total;
	}

}
