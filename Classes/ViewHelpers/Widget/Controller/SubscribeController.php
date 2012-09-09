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
 * @subpackage ViewHelpers/Widget/Controller
 */
class Tx_Notify_ViewHelpers_Widget_Controller_SubscribeController extends Tx_Fluid_Core_Widget_AbstractWidgetController {

	/**
	 * @var Tx_Notify_Service_SubscriptionService
	 */
	protected $subscriptionService;

	/**
	 * @var Tx_Notify_Domain_Repository_SubscriptionRepository
	 */
	protected $subscriptionRepository;

	/**
	 * @var Tx_Notify_Subscription_SourceProviderInterface
	 */
	protected $source;

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
	 * Initialize actions
	 */
	public function initializeAction() {
		if (session_id() == '') {
			session_start();
		}
		if (isset($this->widgetConfiguration['subscription'])) {
			$this->widgetConfiguration['settings']['source']['mode'] = $this->widgetConfiguration['subscription']->getMode();
			$this->widgetConfiguration['settings']['source']['table'] = $this->widgetConfiguration['subscription']->getSourceTable();
			$this->widgetConfiguration['settings']['source']['uid'] = $this->widgetConfiguration['subscription']->getSourceUid();
			$this->widgetConfiguration['settings']['source']['fields'] = $this->widgetConfiguration['subscription']->getSourceFields();
			$this->widgetConfiguration['settings']['source']['resource'] = $this->widgetConfiguration['subscription']->getSource();
		} elseif (isset($this->widgetConfiguration['mode'])) {
			$this->widgetConfiguration['settings']['source']['mode'] = $this->widgetConfiguration['mode'];
			switch ($this->widgetConfiguration['mode']) {
				case Tx_Notify_Subscription_StandardSourceProvider::MODE_FILE:
					$this->widgetConfiguration['settings']['source']['resource'] = $this->widgetConfiguration['resource'];
					break;
				case Tx_Notify_Subscription_StandardSourceProvider::MODE_RECORD:
					if ($this->widgetConfiguration['object']) {
						$this->widgetConfiguration['settings']['source']['table'] = $this->widgetConfiguration['object'] ? strtolower(get_class($this->widgetConfiguration['object'])) : $this->widgetConfiguration['settings']['source']['table'];
						$this->widgetConfiguration['settings']['source']['uid'] = $this->widgetConfiguration['object']->getUid();
						$this->widgetConfiguration['settings']['source']['fields'] = $this->widgetConfiguration['fields'] ? $this->widgetConfiguration['fields'] : $this->widgetConfiguration['settings']['source']['fields'];
					} else {
						$this->widgetConfiguration['settings']['source']['table'] = $this->widgetConfiguration['table'] ? $this->widgetConfiguration['table'] : $this->widgetConfiguration['settings']['source']['table'];
						$this->widgetConfiguration['settings']['source']['uid'] = $this->widgetConfiguration['uid'] ? $this->widgetConfiguration['uid'] : $this->widgetConfiguration['settings']['source']['uid'];
					}
					break;
				case Tx_Notify_Subscription_StandardSourceProvider::MODE_PAGE:
					$this->widgetConfiguration['settings']['source']['table'] = 'pages';
					$this->widgetConfiguration['settings']['source']['uid'] = ($this->widgetConfiguration['settings']['source']['uid'] ? $this->widgetConfiguration['settings']['source']['uid'] : ($this->widgetConfiguration['pageUid'] ? $this->widgetConfiguration['pageUid'] : ($this->widgetConfiguration['uid'] ? $this->widgetConfiguration['uid'] : $GLOBALS['TSFE']->id)));
				default:
					break;
			}
		}
		if (isset($this->widgetConfiguration['displayMode'])) {
			$this->widgetConfiguration['settings']['display']['mode'] = $this->widgetConfiguration['displayMode'];
		}
		if ($this->widgetConfiguration['settings']['source']['mode'] == Tx_Notify_Subscription_StandardSourceProvider::MODE_PAGE) {
			if ($this->widgetConfiguration['settings']['source']['uid'] < 1) {
				$this->widgetConfiguration['settings']['source']['uid'] = $GLOBALS['TSFE']->id;
			}
		}
		$this->source = $this->subscriptionService->getSourceProviderInstance($this->widgetConfiguration['settings']);
	}

	/**
	 * @param Tx_Notify_Domain_Model_Subscription $subscription
	 * @param string $subscriber
	 * @return string
	 */
	public function subscribeAction(Tx_Notify_Domain_Model_Subscription $subscription=NULL, $subscriber=NULL) {
		if ($subscription) {
			$this->widgetConfiguration['subscription'] = $subscription;
			$this->initializeAction();
		}
		if ($subscriber === NULL) {
			$subscriber = $this->source->getSubscriber();
		}
		if ($subscription && $subscription->getSubscriber() != $subscriber) {
			return $this->indexAction();
		}
		if ($this->subscriptionService->isSubscribed($this->source, $subscriber) === FALSE) {
			$this->subscriptionService->subscribe($this->source, $subscriber, $this->widgetConfiguration['url']);
		}
		return $this->indexAction();
	}

	/**
	 * @param Tx_Notify_Domain_Model_Subscription $subscription
	 * @param string $subscriber
	 * @return string
	 */
	public function unsubscribeAction(Tx_Notify_Domain_Model_Subscription $subscription=NULL, $subscriber=NULL) {
		if ($subscription) {
			$this->widgetConfiguration['subscription'] = $subscription;
			$this->initializeAction();
		}
		if ($subscriber === NULL) {
			$subscriber = $this->source->getSubscriber();
		}
		if ($subscription && $subscription->getSubscriber() != $subscriber) {
			return $this->indexAction();
		}
		if ($this->subscriptionService->isSubscribed($this->source, $subscriber)) {
			$this->subscriptionService->unsubscribe($this->source, $subscriber);
		}
		return $this->indexAction();
	}

	/**
	 * @param Tx_Notify_Domain_Model_Subscription $subscription
	 * @param string $subscriber
	 * @return string
	 */
	public function toggleAction(Tx_Notify_Domain_Model_Subscription $subscription=NULL, $subscriber=NULL) {
		if ($subscription) {
			$this->widgetConfiguration['subscription'] = $subscription;
			$this->initializeAction();
		}
		if ($subscriber === NULL) {
			$subscriber = $this->source->getSubscriber();
		}
		if ($subscription && $subscription->getSubscriber() != $subscriber) {
			return $this->indexAction();
		}
		if ($this->subscriptionService->isSubscribed($this->source, $subscriber)) {
			$this->subscriptionService->unsubscribe($this->source, $subscriber);
		} else {
			$this->subscriptionService->subscribe($this->source, $subscriber, $this->widgetConfiguration['url']);
		}
		return $this->indexAction();
	}

	/**
	 * @return string
	 */
	public function indexAction() {
		$isSubscribed = $this->subscriptionService->isSubscribed($this->source, $this->source->getSubscriber());
		$linkTexts = $this->widgetConfiguration['settings']['display']['link'];
		$images = $this->widgetConfiguration['settings']['display']['image'];
		$this->view->assign('subscriber', $this->source->getSubscriber());
		$this->view->assign('isSubscribed', $isSubscribed);
		$this->view->assign('subscription', $this->widgetConfiguration['subscription']);
		$this->view->assign('source', $this->source);
		$this->view->assign('settings', $this->widgetConfiguration['settings']);
		$this->view->assign('modes', explode(',', $this->widgetConfiguration['settings']['source']['modes']));
		$this->view->assign('radioName', t3lib_div::getRandomHexString(8));
		$this->view->assign('toggleImage', $isSubscribed ? $images['subscribed'] : $images['unsubscribed']);
		$this->view->assign('toggleImageAlt', $isSubscribed ? $images['unsubscribed'] : $images['subscribed']);
		$this->view->assign('toggleLinkText', $isSubscribed ? $linkTexts['subscribed'] : $linkTexts['unsubscribed']);
		$this->view->assign('toggleLinkTextAlt', $isSubscribed ? $linkTexts['unsubscribed'] : $linkTexts['subscribed']);
		$this->view->assign('role', $isSubscribed ? 'subscribe' : 'unsubscribe');
		$this->view->assign('url', $this->widgetConfiguration['url']);
		return '<span class="subscribe-parent">' . $this->view->render('index') . '</span>';
	}

}
