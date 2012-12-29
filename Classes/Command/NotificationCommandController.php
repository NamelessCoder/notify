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
 * @subpackage Command
 */
class Tx_Notify_Command_NotificationCommandController extends Tx_Extbase_MVC_Controller_CommandController {

	/**
	 * @var Tx_Notify_Service_SubscriptionService
	 */
	protected $subscriptionService;

	/**
	 * @var Tx_Notify_Service_NotificationService
	 */
	protected $notificationService;

	/**
	 * @var Tx_Notify_Service_ConfigurationService
	 */
	protected $configurationService;

	/**
	 * @var Tx_Notify_Domain_Repository_SubscriptionRepository
	 */
	protected $subscriptionRepository;

	/**
	 * @param Tx_Notify_Service_SubscriptionService $subscriptionService
	 * @return void
	 */
	public function injectSubscriptionService(Tx_Notify_Service_SubscriptionService $subscriptionService) {
		$this->subscriptionService = $subscriptionService;
	}

	/**
	 * @param Tx_Notify_Service_NotificationService $notificationService
	 * @return void
	 */
	public function injectNotificationService(Tx_Notify_Service_NotificationService $notificationService) {
		$this->notificationService = $notificationService;
	}

	/**
	 * @param Tx_Notify_Service_ConfigurationService $configurationService
	 * @return void
	 */
	public function injectConfigurationService(Tx_Notify_Service_ConfigurationService $configurationService) {
		$this->configurationService = $configurationService;
	}

	/**
	 * @param Tx_Notify_Domain_Repository_SubscriptionRepository $subscriptionRepository
	 * @return void
	 */
	public function injectSubscriptionRepository(Tx_Notify_Domain_Repository_SubscriptionRepository $subscriptionRepository) {
		$this->subscriptionRepository = $subscriptionRepository;
	}

	/**
	 * Send notifications to subscribers
	 *
	 * Notifies all subscribers about changes in the
	 * content they subscribed to.
	 *
	 * @return void
	 */
	public function subscriptionsCommand() {
		$this->write('-> Collecting SourceProviders...', TRUE, TRUE);
		$providers = $this->subscriptionService->getUniqueSourceProvidersUsedByAllSubscriptions();
		$this->write('-> Analyzing SourceProviders [' . count($providers) . ' providers]:', TRUE, TRUE);
		$allSubscriptionsBySubscriber = array();
		foreach ($providers as $provider) {
			$configuration = $provider->getConfiguration();
			/** @var $subscriptions Tx_Notify_Domain_Model_Subscription[] */
			$subscriptions = $provider->getSubscriptions();
			$this->writeExportedArray($configuration['source'], get_class($provider) . ' [' . $subscriptions->count() . ' subscriptions]');
			foreach ($subscriptions as $subscription) {
				$subscriber = $subscription->getSubscriber();
				$this->write('     - ' . $subscriber, TRUE, TRUE);
				if (isset($allSubscriptionsBySubscriber[$subscriber]) === FALSE) {
					$allSubscriptionsBySubscriber[$subscriber] = new Tx_Extbase_Persistence_ObjectStorage();
				}
				if ($allSubscriptionsBySubscriber[$subscriber]->contains($subscription) === FALSE) {
					$allSubscriptionsBySubscriber[$subscriber]->attach($subscription);
				}
			}
		}
		$this->write('-> Analyzing Subscriptions for individual Subscribers [' . count($allSubscriptionsBySubscriber) . ' subscribers]:', TRUE, TRUE);
		foreach ($allSubscriptionsBySubscriber as $subscriber => $subscriptions) {
			$this->write('   * ' . $subscriber . ' [' . $subscriptions->count() . ' subscriptions]', TRUE, TRUE);
			$subscriptionsWithUpdates = new Tx_Extbase_Persistence_ObjectStorage();
			foreach ($subscriptions as $subscription) {
				$poller = $this->subscriptionService->resolvePollerForSubscription($subscription);
				$updates = $poller->getUpdatedObjects($subscription, TRUE);
				if ($updates->count() > 0) {
					$subscription->setUpdates($updates);
					$subscriptionsWithUpdates->attach($subscription);
				}
			}
			if ($subscriptionsWithUpdates->count() < 1) {
				$this->write('     There are no Subscriptions with updated content for this Subscriber', TRUE, TRUE);
				continue;
			}
			$this->write('     ' . count($subscriptionsWithUpdates) . ' Subscriptions with updates - sending notification to subscriber', TRUE, TRUE);
			$sent = $this->notificationService->sendSubscribedNotificationsToSubscriber($subscriber, $subscriptionsWithUpdates);
			$this->write(var_export($sent, TRUE));
			if ($sent === TRUE) {
				$this->write('     *** Sent', TRUE, TRUE);
			} else {
				$this->write('     !!! Error! See TYPO3 system log for details', TRUE, TRUE);
			}
		}
		return;
	}

	/**
	 * Writes one line to stdout, optionally sends output
	 *
	 * @param string $line
	 * @param boolean $linebreak
	 * @param boolean $send
	 */
	protected function write($line, $linebreak=TRUE, $send=TRUE) {
		$this->response->appendContent($line);
		if ($linebreak === TRUE) {
			$this->response->appendContent("\n");
		}
		if ($send === TRUE) {
			$this->response->send();
			$this->response->setContent('');
		}
	}

	/**
	 * @param array $array
	 * @param string $label
	 * @return void
	 */
	protected function writeExportedArray($array, $label = NULL) {
		$this->response->appendContent('   * ');
		if ($label !== NULL) {
			$this->response->appendContent($label . LF . '     ');
		}
		$index = 1;
		foreach ($array as $name => $value) {
			$this->response->appendContent($name . '=');
			$this->response->appendContent($value);
			if ($index < count($array)) {
				$this->response->appendContent(', ');
			}
			$index++;
		}
		$this->response->appendContent(LF);
		$this->response->send();
		$this->response->setContent('');
	}

}
?>