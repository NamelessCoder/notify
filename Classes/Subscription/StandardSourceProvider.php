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
 * @subpackage Subscription
 */
class Tx_Notify_Subscription_StandardSourceProvider implements Tx_Notify_Subscription_SourceProviderInterface {

	const MODE_PAGE = 0;
	const MODE_RECORD = 1;
	const MODE_FILE = 2;

	/**
	 * @var Tx_Notify_Domain_Repository_SubscriptionRepository
	 */
	protected $subscriptionRepository;

	/**
	 * @var Tx_Notify_Service_ConfigurationService
	 */
	protected $configurationService;

	/**
	 * The currently loaded configuration
	 *
	 * @var array
	 */
	protected $configuration;

	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @var string
	 */
	protected $providerIdentity = 'Tx_Notify_Subscription_StandardSourceProvider';

	/**
	 * @param Tx_Notify_Domain_Repository_SubscriptionRepository $subscriptionRepository
	 */
	public function injectSubscriptionRepository(Tx_Notify_Domain_Repository_SubscriptionRepository $subscriptionRepository) {
		$this->subscriptionRepository = $subscriptionRepository;
	}

	/**
	 * @param Tx_Notify_Service_ConfigurationService $configurationService
	 */
	public function injectConfigurationServuce(Tx_Notify_Service_ConfigurationService $configurationService) {
		$this->configurationService = $configurationService;
	}

	/**
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * Initialize this object
	 */
	public function initializeObject() {
		$this->setConfiguration($this->configurationService->getConfiguration());
	}

	/**
	 * Get an array of possible modes, indexed by their label (if any).
	 * If the array is numerically indexed then the value is used as option.
	 *
	 * @return array
	 */
	public function getModes() {
		return t3lib_div::trimExplode(',', $this->configuration['source']['modes']);
	}

	/**
	 * Gets the currently selected mode. Usually this is left out in implementers,
	 * delegating this to the built-in detection routine whith is:
	 * 1) Plugin FlexForm
	 * 2) TS
	 * 3) Fallback (basic configuration possible in Extension Configuration)
	 *
	 * @return string
	 */
	public function getMode() {
		return $this->configuration['source']['mode'];
	}

	/**
	 * Returns a string identifier of a subscriber entity - for example
	 * Tx_Extbase_Domain_Model_FrontendUser:123, tt_address:123, a fully
	 * qualified email address such as "Claus Due <claus@wildside.dk>",
	 * a Twitter name such as @NamelessCoder, a phone number such as
	 * +451234567890 for SMS notification - or another value that can
	 * be recognized as an elecronic address capable of receiving messages.
	 *
	 * For the StandardSourceProvider the mode is considered when deciding
	 * the Subscriber - for some types and/or cases for example if no user
	 * is specified, the Subscriber may be provided through an FE form field.
	 *
	 * @return string
	 */
	public function getSubscriber() {
		if (isset($GLOBALS['TSFE']->fe_user->user['email'])) {
			return $GLOBALS['TSFE']->fe_user->user['email'];
		} elseif (isset($_SESSION['tx_notify_subscriber'])) {
			return $_SESSION['tx_notify_subscriber'];
		} elseif (isset($_COOKIE['tx_notify_subscriber'])) {
			return $_COOKIE['tx_notify_subscriber'];
		} elseif (isset($_COOKIE['dialog_poster_identifier'])) {
			if (class_exists('Tx_Dialog_Domain_Repository_PosterRepository')) {
				$identifier = $_COOKIE['dialog_poster_identifier'];
				$repository = $this->objectManager->get('Tx_Dialog_Domain_Repository_PosterRepository');
				$poster = $repository->findOneByIdentifier($identifier);
				if ($poster) {
					return $poster->getEmail();
				}
			}
		}
		return NULL;
	}

	/**
	 * Get all Subscriptions that apply to this Provider
	 *
	 * @param boolean $includeInactive If TRUE, includes inactive subscriptions
	 * @return Tx_Extbase_Persistence_QueryResultInterface
	 */
	public function getSubscriptions($includeInactive=FALSE) {
		$query = $this->subscriptionRepository->createQuery();
		$constraints = array(
			$query->equals('mode', $this->configuration['source']['mode']),
			$query->equals('source', $this->configuration['source']['source']),
			$query->equals('source_table', $this->configuration['source']['source_table']),
			$query->equals('source_fields', $this->configuration['source']['source_fields']),
			$query->equals('source_uid', $this->configuration['source']['source_uid']),
		);
		if ($includeInactive === FALSE) {
			array_push($constraints, $query->equals('active', TRUE));
		}
		return $query->matching($query->logicalAnd($constraints))->execute();
	}

	/**
	 * Set the configuration this instance should use. We are a singleton, so
	 * remember that configuration gets stored to be used by the next plugin
	 * instance unless you manually reset the configuration.
	 *
	 * @abstract
	 * @param array $configuration
	 */
	public function setConfiguration(array $configuration) {
		$this->configuration = $configuration;
	}

	/**
	 * Creates a new Subscription for $subscriber
	 *
	 * @param string $subscriber
	 * @return Tx_Notify_Domain_Model_Subscription
	 */
	public function createSubscription($subscriber) {
		/** @var Tx_Notify_Domain_Model_Subscription $subscription */
		$subscription = $this->objectManager->create('Tx_Notify_Domain_Model_Subscription');
		$subscription->setMode($this->configuration['source']['mode']);
		$subscription->setSubscriber($subscriber);
		$subscription->setActive(TRUE);
		$subscription->setLastNotificationDate(DateTime::createFromFormat('U', time()));
		switch ($this->getMode()) {
			case self::MODE_PAGE:
				$subscription->setSourceTable('pages');
				$subscription->setSourceUid($this->configuration['source']['uid']);
				break;
			case self::MODE_RECORD:
				$subscription->setSourceTable($this->configuration['source']['table']);
				$subscription->setSourceFields($this->configuration['source']['fields']);
				$subscription->setSourceUid($this->configuration['source']['uid']);
				break;
			case self::MODE_FILE:
				$subscription->setSource($this->configuration['source']['resource']);
				break;
			default: break;
		}
		return $subscription;
	}

	/**
	 * Gets an existing Subscription for $subscriber
	 *
	 * @param string $subscriber
	 * @return Tx_Notify_Domain_Model_Subscription
	 */
	public function getSubscription($subscriber) {
		$query = $this->subscriptionRepository->createQuery();
		$constraints = array(
			$query->equals('mode', $this->configuration['source']['mode']),
			$query->equals('subscriber', $subscriber)
		);
		switch ($this->configuration['source']['mode']) {
			case self::MODE_FILE:
				array_push($constraints, $query->equals('source', $this->configuration['source']['resource']));
				break;
			case self::MODE_RECORD:
				array_push($constraints, $query->equals('source_table', (string) $this->configuration['source']['table']));
				array_push($constraints, $query->equals('source_fields', (string) $this->configuration['source']['fields']));
				array_push($constraints, $query->equals('source_uid', intval($this->configuration['source']['uid'])));
				break;
			case self::MODE_PAGE:
			default:
				array_push($constraints, $query->equals('source_table', 'pages'));
				array_push($constraints, $query->equals('source_uid', intval($this->configuration['source']['uid'])));
				break;
		}
		return $query->matching($query->logicalAnd($constraints))->execute()->getFirst();
	}

	/**
	 * Returns a preconfigured MessageInterface implementation
	 * according to the configuration requested by the system and
	 * subscriber's local specifications.
	 *
	 * @param string $subscriber
	 * @return Tx_Notify_Message_MessageInterface
	 */
	public function getMessageInstance($subscriber) {
		/** @var Tx_Notify_Message_FluidEmail $message */
		$message = $this->objectManager->create('Tx_Notify_Message_FluidEmail');
		$message->setRecipient($subscriber);
		$message->setSender($this->configuration['email']['from']['name'] . ' <' . $this->configuration['email']['from']['email'] . '>');
		$message->setSubject($this->configuration['email']['subject']);
		$message->setBody($this->configuration['email']['template']['templatePathAndFilename'], TRUE);
		$message->assign('subject', $this->configuration['email']['subject']);
		$message->assign('section', $this->configuration['email']['template']['section']);
		$message->assign('subscriber', $subscriber);
		$message->assign('settings', $this->configuration);
		$message->assign('lllPrefix', 'LLL:EXT:notify/Resources/Private/Language/locallang.xml:tx_notify_domain_model_subscription');
		foreach ((array) $this->configuration['email']['template']['variables'] as $name => $value) {
			$message->assign($name, $value);
		}
		return $message;
	}
}
