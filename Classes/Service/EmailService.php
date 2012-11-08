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
	 * @var array
	 */
	protected $configuration = array();

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
		$settings = $this->getComponentConfiguration();
		if (!$message->getSender()) {
			$configuredSender = $settings['email']['from'];
			$message->setSender(isset($configuredSender['name']) ? array($configuredSender['email'] => $configuredSender['name']) : $configuredSender['email']);
		}
		if (!$message->getSubject()) {
			$message->setSubject($settings['email']['subject']);
		}
		if ($message->getPrepared() !== TRUE) {
			$copy = $message->prepare();
		} else {
			$copy = clone $message;
		}
		$recipient = $copy->getRfcFormattedRecipientNameAndAddress();
		$sender = $copy->getRfcFormattedSenderNameAndAddress();
		if (empty($recipient)) {
			throw new Exception('Unable to determine recipient type (data vas ' . var_export($recipient, TRUE) . ' - make sure the value is either a string, a valid $name=>$email array or an object that converts to a string using __toString(), getValue() or render() methods on the object which return an RFC valid email identity)', 1334864233);
		}
		if (empty($sender)) {
			throw new Exception('Unable to determine sender type (data vas ' . var_export($sender, TRUE) . ' - make sure the value is either a string, a valid $name=>$email array or an object that converts to a string using __toString(), getValue() or render() methods on the object which return an RFC valid email identity)', 1334864233);
		}
		$recipientParts = explode(' <', trim($recipient, '>'));
		$senderParts = explode(' <', trim($sender, '>'));
		list ($recipientName, $recipientEmail) = $recipientParts;
		list ($senderName, $senderEmail) = $senderParts;
		$mailer = $this->getMailer();
		$mailer->setSubject($copy->getSubject());
		$mailer->setFrom($senderEmail, $senderName);
		$mailer->setTo($recipientEmail, $recipientName);

			// parts:
		$mailer->setBody($copy->getBody(), 'text/html');
			// process the content body a little, plaintext emails require some trimming.
		$lines = explode("\n", trim(strip_tags($content)));
		$whiteLines = 0;
		foreach ($lines as $index => $line) {
			$line = trim($line);
			if ($line === '') {
				$whiteLines++;
				if ($whiteLines > 1) {
					unset($lines[$index]);
					continue;
				} else {
					$lines[$index] = '';
				}
			} else {
				$whiteLines = 0;
			}
			$lines[$index] = $line;
		}
		$mailer->addPart(implode(LF, $lines), 'text/plain');

		$attachments = $copy->getAttachments();
		foreach ($attachments as $attachment) {
			if ($attachment instanceof Swift_Image || $attachment instanceof Swift_EmbeddedFile) {
				$disposition = $attachment->getDisposition();
			} else {
				$disposition = 'attachment';
			}
			if ($disposition == 'inline') {
				$mailer->embed($attachment);
			} else {
				$mailer->attach($attachment);
			}
		}
		return $mailer->send();
	}

	/**
	 * @param mixed $address
	 * @return string
	 */
	protected function formatRfcAddress($address) {
		if (is_array($address) === TRUE) {
			reset($address);
			$address = current($address) . ' <' . key($address) . '>';
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

	/**
	 * @return array
	 */
	protected function getComponentConfiguration() {
		if (count($this->configuration) === 0) {
			$settings = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
			$this->configuration  = $this->typoScriptArrayToPlainArray($settings['plugin.']['tx_notify.']['settings.']);
		}
		return $this->configuration;
	}

	/**
	 * @param array $array
	 * @return array
	 */
	protected function typoScriptArrayToPlainArray(array $array) {
		$transformed = array();
		foreach ($array as $key => $member) {
			$key = trim($key, '.');
			if (is_array($member) === TRUE) {
				$member = $this->typoScriptArrayToPlainArray($member);
			}
			$transformed[$key] = $member;
		}
		return $transformed;
	}

}
