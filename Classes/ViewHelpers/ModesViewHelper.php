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
class Tx_Notify_ViewHelpers_ModesViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var Tx_Notify_Service_ConfigurationService
	 */
	protected $configurationService;

	/**
	 * @param Tx_Notify_Service_ConfigurationService $configurationService
	 */
	public function injectConfigurationService(Tx_Notify_Service_ConfigurationService $configurationService) {
		$this->configurationService = $configurationService;
	}

	/**
	 * @return string
	 */
	public function render() {
		$settings = $this->configurationService->getConfiguration();
		$modes = explode(',', $settings['source']['modes']);
		$labels = array();
		foreach ($modes as $index=>$mode) {
			array_push($labels, Tx_Extbase_Utility_Localization::translate('source.modes.' . $mode, 'notify'));
			if ($index < count($modes) - 2) {
				array_push($labels, ', ');
			} else if ($index == count($modes) - 2) {
				array_push($labels, ' or ');
			}
		}
		return implode('', $labels);
	}

}