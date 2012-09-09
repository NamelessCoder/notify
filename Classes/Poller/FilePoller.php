<?php
class Tx_Notify_Poller_FilePoller extends Tx_Notify_Poller_AbstractPoller implements Tx_Notify_Poller_PollerInterface {

	/**
	 * @param Tx_Notify_Domain_Model_Subscription $subscription
	 * @param boolean $rewriteChecksums
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_Notify_Domain_Model_UpdatedObject>
	 */
	public function getUpdatedObjects(Tx_Notify_Domain_Model_Subscription &$subscription, $rewriteChecksums=FALSE) {
		return t3lib_div::makeInstance('Tx_Extbase_Persistence_ObjectStorage');
	}

	/**
	 * @param Tx_Notify_Domain_Model_Subscription $subscription
	 * @param string $file
	 * @return string
	 */
	public function calculateChecksum(Tx_Notify_Domain_Model_Subscription &$subscription, $file) {
		return md5(t3lib_div::getFileAbsFileName($file));
	}

}
