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
 * Table record list item processing function
 *
 * @package Notify
 * @subpackage UserFunction
 */
class Tx_Notify_UserFunction_TableRecordsItemsProcessingFunction {

	/**
	 * ItemsProcFunc - adds items to tt_content.colPos selector
	 *
	 * @param array $params
	 */
	public function itemsProcFunc(&$params) {
		if ($params['row']['mode'] == 0) {
			$params['row']['source_table'] = 'pages';
		}
		global $TCA;
		$control = $TCA[$params['row']['source_table']]['ctrl'];
		$dynamicConfigurationFile = t3lib_div::getFileAbsFileName($control['dynamicConfigFile']);
		if (file_exists($dynamicConfigurationFile)) {
			require_once $dynamicConfigurationFile;
		}
		$label = $control['label'];
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,' . $label, $params['row']['source_table'], '1');
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$params['items'][] = array(
				$row[$label],
				$row['uid']
			);
		}
	}
}
?>