<?php
class Tx_Notify_Poller_AbstractPoller {

	/**
	 * @param array $row
	 * @return Tx_Notify_Domain_Model_UpdatedObject
	 */
	protected function getUpdatedObjectFromRecord(array $row) {
		$updatedObject = t3lib_div::makeInstance('Tx_Notify_Domain_Model_UpdatedObject');
		$updatedObject->setTitle('TITLE');
		$updatedObject->setDate(DateTime::createFromFormat('U', $row['tstamp']));
		$updatedObject->setData($row);
		return $updatedObject;
	}

}