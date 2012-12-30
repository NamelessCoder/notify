<?php
class Tx_Notify_Poller_AbstractPoller {

	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @param array $row
	 * @param string $table
	 * @return Tx_Notify_Domain_Model_UpdatedObject
	 */
	protected function getUpdatedObjectFromRecord(array $row, $table = NULL) {
		/** @var $updatedObject Tx_Notify_Domain_Model_UpdatedObject */
		$updatedObject = $this->objectManager->create('Tx_Notify_Domain_Model_UpdatedObject');
		if ($table !== NULL && isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['Tx_Notify_Extraction'][$table])) {
			$extractorClassName = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['Tx_Notify_Extraction'][$table];
			/** @var $extractor Tx_Notify_Extraction_ExtractorInterface */
			$extractor = $this->objectManager->create($extractorClassName);
			$title = $extractor->extractTitleFromRecord($row);
			$content = $extractor->extractContentFromRecord($row);
			$dateTime = $extractor->extractDateTimeFromRecord($row);
		} else {
			$title = $row[$GLOBALS['TCA'][$table]['ctrl']['label']];
			$dateTime = DateTime::createFromFormat('U', $row[$GLOBALS['TCA'][$table]['ctrl']['tstamp']]);
			$content = isset($row[$GLOBALS['TCA'][$table]['ctrl']['label_alt']]) ? $row[$GLOBALS['TCA'][$table]['ctrl']['label_alt']] : $title;
		}
		$updatedObject->setSubType($GLOBALS['TCA'][$table]['ctrl']['title']);
		$updatedObject->setTitle($title);
		$updatedObject->setDate($dateTime);
		$updatedObject->setData($row);
		$updatedObject->setContent($content);
		return $updatedObject;
	}

}