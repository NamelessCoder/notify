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
 * Email service
 *
 * Contains quick-use emailing functions.
 *
 * @package Notify
 * @subpackage Service
 */
class Tx_Notify_Service_EmailService implements t3lib_Singleton, Tx_Notify_Message_DeliveryServiceInterface {

	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Send an email. Supports any to-string-convertible parameter types
	 *
	 * @param mixed $subject
	 * @param mixed $body
	 * @param mixed $recipientEmail
	 * @param mixed $recipientName
	 * @param mixed $fromEmail
	 * @param mixed $fromName
	 * @return integer the number of recipients who were accepted for delivery
	 * @api
	 */
	public function mail($subject, $body, $recipient, $sender) {
		list ($recipientName, $recipientEmail) = explode(' <', trim($this->formatRfcAddress($recipient), '>'));
		list ($senderName, $senderEmail) = explode(' <', trim($this->formatRfcAddress($sender), '>'));
		$mail = $this->getMailer();
		$mail->setTo($recipientEmail, $recipientName);
		$mail->setFrom($senderEmail, $senderName);
		$mail->setSubject($subject);
		$mail->setBody($body);
		return $mail->send();
	}

	/**
	 * Get a mailer (SwiftMailer) object instance
	 *
	 * @return t3lib_mail_Message;
	 * @api
	 */
	public function getMailer() {
		$mailer = new t3lib_mail_Message();
		return $mailer;
	}

	/**
	 * Sends a Message-interface-implementing Message through Email routes
	 *
	 * @param Tx_Notify_Message_MessageInterface $message The message to send
	 */
	public function send(Tx_Notify_Message_MessageInterface $message) {
		if ($message->getPrepared() !== TRUE) {
			$copy = $message->prepare();
		} else {
			$copy = clone $message;
		}
		$sent = $this->mail($copy->getSubject(), $copy->getBody(), $copy->getRecipient(), $copy->getSender());
		return $sent;
	}

	/**
	 * @param mixed $address
	 * @return string
	 */
	protected function formatRfcAddress($address) {
		if (is_array($address) === TRUE) {
			reset($address);
			$address = key($address) . ' <' . current($address) . '>';
		} elseif (is_object($address) === TRUE) {
			if (method_exists($address, '__toString') === TRUE) {
				$address = (string) $address;
			} elseif (method_exists($address, 'render') === TRUE) {
				$address = $address->render();
			} elseif (method_exists($address, 'getValue') === TRUE) {
				$address = $address->getValue();
			}
		} elseif (is_string($address) === TRUE) {
			if (strpos($address, '<') === FALSE) {
				$address = $address . ' <' . $address . '>';
			}
		}
		return $address;
	}

}
