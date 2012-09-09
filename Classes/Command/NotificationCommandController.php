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
	 * @var Tx_Notify_Domain_Repository_SubscriptionRepository
	 */
	protected $subscriptionRepository;

	/**
	 * @param Tx_Notify_Service_SubscriptionService $subscriptionService
	 */
	public function injectSubscriptionService(Tx_Notify_Service_SubscriptionService $subscriptionService) {
		$this->subscriptionService = $subscriptionService;
	}

	/**
	 * @param Tx_Notify_Domain_Repository_SubscriptionRepository $subscriptionRepository
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
		$this->write('-> Collecting subscriptions... ', FALSE, TRUE);
		$subscriptions = $this->subscriptionService->getAllActiveSubscriptions();
		sleep(3);
		$this->write(count($subscriptions) . ' active subscription(s) loaded.');

		$this->write('-> Building polling tasks... ', FALSE, TRUE);
		$tasks = $this->subscriptionService->buildUpdateCheckTaskList($subscriptions);
		sleep(3);
		$this->write(count($tasks) . ' polling tasks built.');

		$this->write('-> Running polling tasks... ', FALSE, TRUE);
		sleep(3);
		$this->write('done.');

		sleep(3);
		$this->write('-> Subscription notification job completed');
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

}
?>