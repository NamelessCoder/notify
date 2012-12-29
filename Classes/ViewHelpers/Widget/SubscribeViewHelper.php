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
 * @subpackage ViewHelpers/Widget
 */
class Tx_Notify_ViewHelpers_Widget_SubscribeViewHelper extends Tx_Fluidwidget_Core_Widget_AbstractWidgetViewHelper {

	/**
	 * @var bool
	 */
	protected $ajaxWidget = TRUE;

	/**
	 * @var Tx_Notify_ViewHelpers_Widget_Controller_SubscribeController
	 */
	protected $controller;

	/**
	 * @param Tx_Notify_ViewHelpers_Widget_Controller_SubscribeController $controller
	 */
	public function injectController(Tx_Notify_ViewHelpers_Widget_Controller_SubscribeController $controller) {
		$this->controller = $controller;
	}

	/**
	 * Initialize all actions
	 */
	public function initializeAction() {
		$this->ajaxWidget = (bool) Tx_Extbase_Reflection_ObjectAccess::getPropertyPath($this->arguments['settings'], 'settings.display.ajax');
	}

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		$this->registerArgument('subscription', 'Tx_Notify_Domain_Model_Subscription', 'If set, overrides all source definitions instead getting these values from the provided Subscription', FALSE);
		$this->registerArgument('settings', 'array', 'Settings for this Widget - same structure as TS plugin.tx_notify.settings');
		$this->registerArgument('mode', 'integer', 'Mode of operation - 0=page, 1=record, 2=file/dir', FALSE);
		$this->registerArgument('pageUid', 'integer', 'If mode=0 (page), uid of page that is to be subscribed. If empty and mode is page, current page id is used', FALSE);
		$this->registerArgument('object', 'Tx_Extbase_DomainObject_DomainObjectInterface', 'If mode=1 (record) the table name and uid of this object is used for the subscription', FALSE);
		$this->registerArgument('table', 'string', 'If mode=1 (record) and you did not specify an object and/or need to override the table name, specify it here', FALSE);
		$this->registerArgument('fields', 'string', 'If mode=1 (record) and you only want to monitor specific fields for changes, fill it here. Note: this value makes a subscription unique, meaning that you can create multiple subscriptions for individual fields or sets of fields in a record - in short, you can bind this Widget to individual properties', FALSE);
		$this->registerArgument('uid', 'integer', 'UID of the record that is to be subscribed to. Note: if both pageUid and uid is specified, pageUid takes precedence if mode=0 (page)', FALSE);
		$this->registerArgument('resource', 'string', 'File, filename wildcard or folder path to subscribe to. If file modification times changed the subscription is triggered', FALSE);
		$this->registerArgument('displayMode', 'string', 'Display mode - button,link,image', FALSE);
		$this->registerArgument('url', 'string', 'Optional URL to use whenever links to this subscription target are rendered', FALSE, NULL);
	}

	/**
	 * @return string
	 */
	public function render() {
		return $this->initiateSubRequest();
	}

}
