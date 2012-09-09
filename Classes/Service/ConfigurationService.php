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
 * @subpackage Service
 */
class Tx_Notify_Service_ConfigurationService extends Tx_Notify_Service_AbstractService implements t3lib_Singleton {

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @var Tx_Extbase_Service_FlexFormService
	 */
	protected $flexFormService;

	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * @param Tx_Extbase_Service_FlexFormService $flexFormService
	 */
	public function injectFlexFormService(Tx_Extbase_Service_FlexFormService $flexFormService) {
		$this->flexFormService = $flexFormService;
	}

	/**
	 * Gets configuration overlayed by plugin FlexForms respecting fallbacks.
	 *
	 * @return array
	 */
	public function getConfiguration() {
		return $this->getOverlayedSettingPath();
	}

	/**
	 * Gets a setting overlayed by a possible plugin FlexForm, observing
	 * fallbacks configured in extension configuration.
	 *
	 * @param string $path Dotted path to the setting, excuding plugin.tx_notify.settings
	 * @return mixed
	 */
	protected function getOverlayedSettingPath($path=NULL) {
		$baseSettings = (array) $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['notify']['setup'];
		$typoScriptSettings = (array) $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'notify', 'subscribe');
		if ($this->configurationManager->getContentObject() instanceof tslib_cObj) {
			$flexFormSettings = $this->flexFormService->convertFlexFormContentToArray($this->configurationManager->getContentObject()->data['pi_flexform']);
		}
		$settings = $this->mergeRecursiveIfNotEmpty($baseSettings, (array) $typoScriptSettings, (array) $flexFormSettings['settings']);
		if ($path !== NULL) {
			$settings = Tx_Extbase_Reflection_ObjectAccess::getPropertyPath($settings, $path);
		}
		return $settings;
	}

	/**
	 * @return mixed
	 */
	protected function mergeRecursiveIfNotEmpty() {
		$arguments = func_get_args();
		$result = array();
		foreach ($arguments as $array) {
			$result = $this->protectedMerge($result, $array);
		}
		return $result;
	}

	/**
	 * Merges A with B while protecting values of A if they are empty in B
	 *
	 * @param array $protectedArray
	 * @param array $overlay
	 */
	protected function protectedMerge($protectedArray, $overlay) {
		foreach ($protectedArray as $key=>$value) {
			if (isset($overlay[$key]) === TRUE) {
				if (is_array($overlay[$key])) {
					$protectedArray[$key] = $this->protectedMerge($protectedArray[$key], $overlay[$key]);
				} else if (empty($overlay[$key]) === TRUE) {
					continue;
				} else {
					$protectedArray[$key] = $overlay[$key];
				}
			}
		}
		foreach ($overlay as $key=>$value) {
			if (isset($protectedArray[$key]) === FALSE || empty($protectedArray[$key]) === TRUE) {
				$protectedArray[$key] = $value;
			}
		}
		return $protectedArray;
	}

}
