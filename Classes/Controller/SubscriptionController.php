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
 * @subpackage Controller
 */
class Tx_Notify_Controller_SubscriptionController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * @var Tx_Notify_Service_ConfigurationService
	 */
	protected $configurationService;

	/**
	 * @var Tx_Notify_Domain_Repository_SubscriptionRepository
	 */
	protected $subscriptionRepository;

	/**
	 * @var Tx_Notify_Service_SubscriptionService
	 */
	protected $subscriptionService;

	/**
	 * @param Tx_Notify_Domain_Repository_SubscriptionRepository $subscriptionRepository
	 */
	public function injectSubscriptionRepository(Tx_Notify_Domain_Repository_SubscriptionRepository $subscriptionRepository) {
		$this->subscriptionRepository = $subscriptionRepository;
	}

	/**
	 * @param Tx_Notify_Service_SubscriptionService $subscriptionService
	 */
	public function injectSubscriptionService(Tx_Notify_Service_SubscriptionService $subscriptionService) {
		$this->subscriptionService = $subscriptionService;
	}

	/**
	 * @param Tx_Notify_Service_ConfigurationService $configurationService
	 */
	public function injectConfigurationService(Tx_Notify_Service_ConfigurationService $configurationService) {
		$this->configurationService = $configurationService;
	}

	/**
	 * Initialize action
	 */
	public function initializeAction() {
		$this->settings = $this->configurationService->getConfiguration();
	}

	/**
	 * action component
	 *
	 * @return void
	 */
	public function componentAction() {
	}

	/**
	 * action show
	 *
	 * @param $subscription
	 * @return void
	 */
	public function showAction(Tx_Notify_Domain_Model_Subscription $subscription) {
		$this->view->assign('subscription', $subscription);
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$subscriber = $this->subscriptionService->getSourceProviderInstance($this->configurationService->getConfiguration())->getSubscriber();
		if ($subscriber) {
			$subscriptions = $this->subscriptionRepository->findBySubscriber($subscriber);
			$this->view->assign('subscriptions', $subscriptions);
		}
	}

	/**
	 * action timeline
	 *
	 * @return void
	 */
	public function timelineAction() {
		$subscriber = $this->subscriptionService->getSourceProviderInstance($this->configurationService->getConfiguration())->getSubscriber();
		if ($subscriber) {
			$subscriptions = $this->subscriptionRepository->findBySubscriber($subscriber);
			$subscriptions = $this->subscriptionService->buildUpdatedContentObjects($subscriptions);
			$this->view->assign('subscriptions', $subscriptions);
		}
	}

	/**
	 * action reset
	 *
	 * @param Tx_Notify_Domain_Model_Subscription $subscription
	 * @return void
	 */
	public function resetAction(Tx_Notify_Domain_Model_Subscription $subscription=NULL) {
		$subscriber = $this->subscriptionService->getSourceProviderInstance($this->configurationService->getConfiguration())->getSubscriber();
		if ($subscriber) {
			if ($subscription) {
				$subscriptions = $this->objectManager->create('Tx_Extbase_Persistence_ObjectStorage');
				$subscriptions->attach($subscription);
			} else {
				$subscriptions = $this->subscriptionRepository->findBySubscriber($subscriber);
			}
			$this->subscriptionService->buildUpdatedContentObjects($subscriptions, TRUE);
		}
		$this->redirect('timeline');
	}

	/**
	 * action delete
	 *
	 * @param Tx_Notify_Domain_Model_Subscription $subscription
	 * @return void
	 */
	public function deleteAction(Tx_Notify_Domain_Model_Subscription $subscription) {
		$subscriber = $this->subscriptionService->getSourceProviderInstance($this->configurationService->getConfiguration())->getSubscriber();
		if ($subscriber === $subscription->getSubscriber()) {
			$this->subscriptionRepository->remove($subscription);
		}
		$this->redirect('list');
	}

}
