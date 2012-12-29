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
interface Tx_Notify_Subscription_SourceProviderInterface {

	/**
	 * Get an array of possible modes, indexed by their label (if any).
	 * If the array is numerically indexed then the value is used as option.
	 *
	 * @abstract
	 * @return array
	 */
	public function getModes();

	/**
	 * Gets the currently selected mode. Usually this is left out in implementers,
	 * delegating this to the built-in detection routine whith is:
	 * 1) Plugin FlexForm
	 * 2) TS
	 * 3) Fallback (basic configuration possible in Extension Configuration)
	 *
	 * @abstract
	 * @return string
	 */
	public function getMode();

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
	 * @abstract
	 * @return string
	 */
	public function getSubscriber();

	/**
	 * Get all Subscriptions that apply to this Provider
	 *
	 * @abstract
	 * @return Tx_Extbase_Persistence_QueryResultInterface
	 */
	public function getSubscriptions();

	/**
	 * Set the configuration this instance should use. We are a singleton, so
	 * remember that configuration gets stored to be used by the next plugin
	 * instance unless you manually reset the configuration.
	 *
	 * @abstract
	 * @param array $configuration
	 */
	public function setConfiguration(array $configuration);

	/**
	 * @return array
	 */
	public function getConfiguration();

	/**
	 * Creates a new Subscription for $subscriber
	 *
	 * @abstract
	 * @param string $subscriber
	 */
	public function createSubscription($subscriber);

	/**
	 * Gets an existing Subscription for $subscriber
	 *
	 * @abstract
	 * @param string $subscriber
	 * @return Tx_Notify_Domain_Model_Subscription
	 */
	public function getSubscription($subscriber);

	/**
	 * Returns a preconfigured MessageInterface implementation
	 * according to the configuration requested by the system and
	 * subscriber's local specifications.
	 *
	 * @abstract
	 * @param string $subscriber
	 * @return Tx_Notify_Message_MessageInterface
	 */
	public function getMessageInstance($subscriber);

	/**
	 * Returns output of serialize($this)
	 *
	 * @return string
	 */
	public function __toString();

}
