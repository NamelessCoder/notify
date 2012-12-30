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
 * @subpackage Domain/Model
 */
class Tx_Notify_Domain_Model_UpdatedObject extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * @var integer
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $subType;

	/**
	 * @var string
	 */
	protected $action;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @var string
	 */
	protected $link;

	/**
	 * @var DateTime
	 */
	protected $date;

	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * @return integer
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param integer $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @param string $subType
	 */
	public function setSubType($subType) {
		$this->subType = $subType;
	}

	/**
	 * @return string
	 */
	public function getSubType() {
		return $this->subType;
	}

	/**
	 * @return string
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * @param string $action
	 */
	public function setAction($action) {
		$this->action = $action;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * @param string $content
	 */
	public function setContent($content) {
		$this->content = $content;
	}

	/**
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @return string
	 */
	public function getLink() {
		return $this->link;
	}

	/**
	 * @param string $link
	 */
	public function setLink($link) {
		$this->link = $link;
	}

	/**
	 * @return DateTime
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * @param DateTime $date
	 */
	public function setDate(DateTime $date) {
		$this->date = $date;
	}

	/**
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * @param array $data
	 */
	public function setData(array $data) {
		$this->data = $data;
	}

}
