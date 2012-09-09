<?php
interface Tx_Notify_Poller_PollerInterface {

	/**
	 * @abstract
	 * @param Tx_Notify_Domain_Model_Subscription $subscription
	 * @param boolean $rewriteChecksums
	 * @return Tx_Extbase_Persistence_ObjectStorage
	 */
	public function getUpdatedObjects(Tx_Notify_Domain_Model_Subscription &$subscription, $rewriteChecksums=FALSE);

	/**
	 * @abstract
	 * @param Tx_Notify_Domain_Model_Subscription $subscription
	 * @param array $record
	 * @return string
	 */
	public function calculateChecksum(Tx_Notify_Domain_Model_Subscription &$subscription, $record);

}
