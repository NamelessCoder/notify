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
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class Tx_Notify_Domain_Model_Subscription.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage Notification and subscription
 *
 * @author Claus Due <claus@wildside.dk>
 */
class Tx_Notify_Domain_Model_SubscriptionTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_Notify_Domain_Model_Subscription
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new Tx_Notify_Domain_Model_Subscription();
	}

	public function tearDown() {
		unset($this->fixture);
	}
	
	
	/**
	 * @test
	 */
	public function getModeReturnsInitialValueForInteger() { 
		$this->assertSame(
			0,
			$this->fixture->getMode()
		);
	}

	/**
	 * @test
	 */
	public function setModeForIntegerSetsMode() { 
		$this->fixture->setMode(12);

		$this->assertSame(
			12,
			$this->fixture->getMode()
		);
	}
	
	/**
	 * @test
	 */
	public function getSourceReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setSourceForStringSetsSource() { 
		$this->fixture->setSource('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getSource()
		);
	}
	
	/**
	 * @test
	 */
	public function getSourceFieldsReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setSourceFieldsForStringSetsSourceFields() { 
		$this->fixture->setSourceFields('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getSourceFields()
		);
	}
	
	/**
	 * @test
	 */
	public function getActiveReturnsInitialValueForBoolean() { 
		$this->assertSame(
			TRUE,
			$this->fixture->getActive()
		);
	}

	/**
	 * @test
	 */
	public function setActiveForBooleanSetsActive() { 
		$this->fixture->setActive(TRUE);

		$this->assertSame(
			TRUE,
			$this->fixture->getActive()
		);
	}
	
	/**
	 * @test
	 */
	public function getChecksumReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setChecksumForStringSetsChecksum() { 
		$this->fixture->setChecksum('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getChecksum()
		);
	}
	
	/**
	 * @test
	 */
	public function getLastNotificationDateReturnsInitialValueForDateTime() { }

	/**
	 * @test
	 */
	public function setLastNotificationDateForDateTimeSetsLastNotificationDate() { }
	
}
?>