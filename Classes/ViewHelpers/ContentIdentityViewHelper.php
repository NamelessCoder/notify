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
 * @subpackage ViewHelpers
 */
class Tx_Notify_ViewHelpers_ContentIdentityViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @param Tx_Notify_Domain_Model_Subscription $subscription
	 * @param integer $rootLineEntryLevel
	 * @return string
	 */
	public function render(Tx_Notify_Domain_Model_Subscription $subscription, $rootLineEntryLevel=0) {
		switch ($subscription->getMode()) {
			case Tx_Notify_Subscription_StandardSourceProvider::MODE_PAGE: return $this->renderPageIdentity($subscription, $rootLineEntryLevel);
			case Tx_Notify_Subscription_StandardSourceProvider::MODE_RECORD: return $this->renderRecordIdentity($subscription);
			case Tx_Notify_Subscription_StandardSourceProvider::MODE_FILE: return $this->renderResourceIdentity($subscription);
			default: break;
		}
	}

	/**
	 * @param Tx_Notify_Domain_Model_Subscription $subscription
	 * @param integer $rootLineEntryLevel
	 * @return string
	 */
	protected function renderPageIdentity(Tx_Notify_Domain_Model_Subscription $subscription, $rootLineEntryLevel) {
		$pageSelect = new t3lib_pageSelect();
		$html = array();
		foreach (array_reverse($pageSelect->getRootLine($subscription->getSourceUid())) as $level=>$page) {
			if ($level >= $rootLineEntryLevel) {
				array_push($html, $page['title']);
			}
		}
		return implode(' / ', $html);
	}

	/**
	 * @param Tx_Notify_Domain_Model_Subscription $subscription
	 * @return string
	 */
	protected function renderRecordIdentity(Tx_Notify_Domain_Model_Subscription $subscription) {
		global $TCA;
		$config = $TCA[$subscription->getSourceTable()]['ctrl'];
		$label = $config['label'] ? $config['label'] : 'uid';
		$fields = strpos($subscription->getSourceFields(), ',') ? t3lib_div::trimExplode(',', $subscription->getSourceFields()) : array('uid', $label);
		$record = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(implode(',', $fields), $subscription->getSourceTable(), "uid = '" . $subscription->getSourceUid() . "'");
		$language = new language();
		return $language->sL($config['title']) . ' "' . ($record[$label] ? $record[$label] : $record['uid']) . '"';
	}

	/**
	 * @param Tx_Notify_Domain_Model_Subscription $subscription
	 * @return string
	 */
	protected function renderResourceIdentity(Tx_Notify_Domain_Model_Subscription $subscription) {
		return $subscription->getSource();
	}

}