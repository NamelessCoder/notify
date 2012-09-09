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
 * @subpackage Domain/Model
 */
class Tx_Notify_Domain_Model_Subscription extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * Subscription mode
	 *
	 * @var integer
	 */
	protected $mode;

	/**
	 * Source of subscribed content
	 *
	 * @var string
	 */
	protected $source;

	/**
	 * List of fields in source type which when changed trigger a notification
	 *
	 * @var string
	 */
	protected $sourceFields;

	/**
	 * @var string
	 */
	protected $sourceTable;

	/**
	 * @var integer
	 */
	protected $sourceUid;

	/**
	 * Active subscription
	 *
	 * @var boolean
	 */
	protected $active;

	/**
	 * Checksum of last value field sets. Used to confirm if a
	 * change has occurred.
	 *
	 * @var string
	 */
	protected $checksum;

	/**
	 * Last date of notification
	 *
	 * @var DateTime
	 */
	protected $lastNotificationDate;

	/**
	 * Identification of the subscribing entity - supports various formats
	 * such as email address or Class:UID object identities
	 *
	 * @var string
	 */
	protected $subscriber;

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_Notify_Domain_Model_UpdatedObject>
	 */
	protected $updates;

	/**
	 * @return void
	 */
	public function __construct() {
		$this->updates = t3lib_div::makeInstance('Tx_Extbase_Persistence_ObjectStorage');
	}

	/**
	 * @return integer $mode
	 */
	public function getMode() {
		return $this->mode;
	}

	/**
	 * @param integer $mode
	 * @return void
	 */
	public function setMode($mode) {
		$this->mode = $mode;
	}

	/**
	 * @return string $source
	 */
	public function getSource() {
		return $this->source;
	}

	/**
	 * @param string $source
	 * @return void
	 */
	public function setSource($source) {
		$this->source = $source;
	}

	/**
	 * @return string $sourceFields
	 */
	public function getSourceFields() {
		return $this->sourceFields;
	}

	/**
	 * @param string $sourceFields
	 * @return void
	 */
	public function setSourceFields($sourceFields) {
		$this->sourceFields = $sourceFields;
	}

	/**
	 * @return boolean $active
	 */
	public function getActive() {
		return $this->active;
	}

	/**
	 * @param boolean $active
	 * @return void
	 */
	public function setActive($active) {
		$this->active = $active;
	}

	/**
	 * @return boolean
	 */
	public function isActive() {
		return $this->getActive();
	}

	/**
	 * @return string $checksum
	 */
	public function getChecksum() {
		return $this->checksum;
	}

	/**
	 * @param string $checksum
	 * @return void
	 */
	public function setChecksum($checksum) {
		$this->checksum = $checksum;
	}

	/**
	 * @return DateTime $lastNotificationDate
	 */
	public function getLastNotificationDate() {
		return $this->lastNotificationDate;
	}

	/**
	 * @param DateTime $lastNotificationDate
	 * @return void
	 */
	public function setLastNotificationDate($lastNotificationDate) {
		$this->lastNotificationDate = $lastNotificationDate;
	}

	/**
	 * @return string
	 */
	public function getSubscriber() {
		return $this->subscriber;
	}

	/**
	 * @param string $subscriber
	 */
	public function setSubscriber($subscriber) {
		$this->subscriber = $subscriber;
	}

	/**
	 * @return string
	 */
	public function getSourceTable() {
		return $this->sourceTable;
	}

	/**
	 * @param string $sourceTable
	 */
	public function setSourceTable($sourceTable) {
		$this->sourceTable = $sourceTable;
	}

	/**
	 * @return integer
	 */
	public function getSourceUid() {
		return $this->sourceUid;
	}

	/**
	 * @param integer $sourceUid
	 */
	public function setSourceUid($sourceUid) {
		$this->sourceUid = $sourceUid;
	}

	/**
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_Notify_Domain_Model_UpdatedObject>
	 */
	public function getUpdates() {
		return $this->updates;
	}

	/**
	 * @param Tx_Extbase_Persistence_ObjectStorage<Tx_Notify_Domain_Model_UpdatedObject> $updates
	 */
	public function setUpdates(Tx_Extbase_Persistence_ObjectStorage $updates) {
		$this->updates = $updates;
	}

	/**
	 * @param Tx_Notify_Domain_Model_UpdatedObject $update
	 */
	public function addUpdate(Tx_Notify_Domain_Model_UpdatedObject $update) {
		$this->updates->attach($update);
	}

	/**
	 * @param Tx_Notify_Domain_Model_UpdatedObject $update
	 */
	public function removeUpdate(Tx_Notify_Domain_Model_UpdatedObject $update) {
		$this->updates->detach($update);
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

}
