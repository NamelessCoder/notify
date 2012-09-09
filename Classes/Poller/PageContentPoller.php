<?php
class Tx_Notify_Poller_PageContentPoller extends Tx_Notify_Poller_AbstractPoller implements Tx_Notify_Poller_PollerInterface {

	/**
	 * @param Tx_Notify_Domain_Model_Subscription $subscription
	 * @param boolean $rewriteChecksums
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_Notify_Domain_Model_UpdatedObject>
	 */
	public function getUpdatedObjects(Tx_Notify_Domain_Model_Subscription &$subscription, $rewriteChecksums=FALSE) {
		$objectStorage = t3lib_div::makeInstance('Tx_Extbase_Persistence_ObjectStorage');
		$pageUid = $subscription->getSourceUid();
		$contentElements = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'tt_content', "pid = '" . $pageUid . "'");
		$checksumParts = array();
		$updatedElements = array();
		foreach ($contentElements as $contentElement) {
			$contentChecksum = $this->calculateChecksum($subscription, $contentElement);
			array_push($checksumParts, $contentChecksum);
			if ($contentElement['tstamp'] > $subscription->getLastNotificationDate()->format('U')) {
				$updatedObject = $this->getUpdatedObjectFromRecord($contentElement);
				array_push($updatedElements, $updatedObject);
			}
		}
		$currentChecksum = md5(implode('', $checksumParts));
		if ($subscription->getChecksum() !== $currentChecksum) {
			foreach ($updatedElements as $updatedObject) {
				$objectStorage->attach($updatedObject);
			}
		}
		if ($rewriteChecksums === TRUE) {
			$subscription->setChecksum($currentChecksum);
		}
		return $objectStorage;
	}

	/**
	 * @param Tx_Notify_Domain_Model_Subscription $subscription
	 * @param array $record
	 * @return string
	 */
	public function calculateChecksum(Tx_Notify_Domain_Model_Subscription &$subscription, $record) {
		$fields = t3lib_div::trimExplode(',', $subscription->getSourceFields());
		if ($subscription->getSourceFields() !== '' && count($fields) > 0) {
			$values = array();
			foreach ($fields as $field) {
				$values[$field] = $record[$field];
			}
			$currentChecksum = md5(implode('', $values));
		} else {
			$currentChecksum = md5(implode('', $record));
		}
		return $currentChecksum;
	}
	
}
