<?php
interface Tx_Notify_Extraction_ExtractorInterface {

	/**
	 * @param array $record
	 * @return string
	 */
	public function extractContentFromRecord(array $record);

	/**
	 * @param array $record
	 * @return string
	 */
	public function extractTitleFromRecord(array $record);

	/**
	 * @param array $record
	 * @return DateTime
	 */
	public function extractDateTimeFromRecord(array $record);

}
