<?php
class Tx_Notify_Poller_RecordPoller extends Tx_Notify_Poller_AbstractPoller implements Tx_Notify_Poller_PollerInterface {

	/**
	 * @param Tx_Notify_Domain_Model_Subscription $subscription
	 * @param boolean $rewriteChecksums
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_Notify_Domain_Model_UpdatedObject>
	 */
	public function getUpdatedObjects(Tx_Notify_Domain_Model_Subscription &$subscription, $rewriteChecksums=FALSE) {
		$objectStorage = t3lib_div::makeInstance('Tx_Extbase_Persistence_ObjectStorage');
		$table = $subscription->getSourceTable();
		$lastChecksum = $subscription->getChecksum();
		$uid = $subscription->getSourceUid();
		if ($uid > 0) {
			$row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', $table, "uid = '" . $uid . "'");
			$currentChecksum = $this->calculateChecksum($subscription, $row);
			if ($currentChecksum !== $lastChecksum) {
				$updatedObject = $this->getUpdatedObjectFromRecord($row, $table);
				$objectStorage->attach($updatedObject);
			}
		} else {
			$records = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', $table, "tstamp < '" . $subscription->getLastNotificationDate()->format('U') . "' AND hidden = 0 AND deleted = 0");
			$currentChecksum = md5(implode('', array_keys($records)));
			if (count($records) > 0 && $currentChecksum !== $lastChecksum) {
				foreach ($records as $record) {
					$updatedObject = $this->getUpdatedObjectFromRecord($record, $table);
					$objectStorage->attach($updatedObject);
				}
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
