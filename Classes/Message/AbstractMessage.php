<?php
class Tx_Notify_Message_AbstractMessage {

	/**
	 * @var mixed
	 */
	protected $recipient;

	/**
	 * @var array
	 */
	protected $carbonCopies = array();

	/**
	 * @var array
	 */
	protected $blindCarbonCopies = array();

	/**
	 * @var mixed
	 */
	protected $sender;

	/**
	 * @var string
	 */
	protected $subject;

	/**
	 * @var mixed
	 */
	protected $body;

	/**
	 * @var boolean
	 */
	protected $bodyIsFilePathAndFilename = FALSE;

	/**
	 * @var array
	 */
	protected $attachments = array();

	/**
	 * @var boolean
	 */
	protected $prepared = FALSE;

	/**
	 * Template variable storage. RESERVED NAMES are the current values. These values are set internally when sending
	 *
	 * @var array
	 */
	protected $variables = array(
		'recipient' => NULL,
		'attachments' => NULL,
	);

	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @var Tx_Notify_Service_EmailService
	 */
	protected $emailService;

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
	 * @param Tx_Notify_Service_EmailService $emailService
	 */
	public function injectEmailService(Tx_Notify_Service_EmailService $emailService) {
		$this->emailService = $emailService;
	}

	/**
	 * @param mixed $recipient Either an array of $name=>$email, a simple email address or a "Name <email@dom.tld>" string or a string-convertible object which returns any of the beforementioned address types
	 */
	public function setRecipient($recipient) {
		$this->recipient = $recipient;
	}

	/**
	 * @return mixed
	 */
	public function getRecipient() {
		return $this->recipient;
	}

	/**
	 * @param mixed $sender Either an array of $name=>$email, a simple email address or a "Name <email@dom.tld>" string or a string-convertible object which returns any of the beforementioned address types
	 */
	public function setSender($sender) {
		$this->sender = $sender;
	}

	/**
	 * @return mixed
	 */
	public function getSender() {
		return $this->sender;
	}

	/**
	 * Returns the recipient as a valid RFC formatted (Name Surname <)email@domain.tld(>) address
	 *
	 * @return string
	 */
	public function getRfcFormattedRecipientNameAndAddress() {
		return $this->formatRfcAddress($this->recipient);
	}

	/**
	 * Returns the sender as a valid RFC formatted (Name Surname <)email@domain.tld(>) address
	 *
	 * @return string
	 */
	public function getRfcFormattedSenderNameAndAddress() {
		return $this->formatRfcAddress($this->sender);
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

	/**
	 * @param string $subject
	 */
	public function setSubject($subject) {
		$this->subject = $subject;
	}

	/**
	 * @return string
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * @param mixed $body Any type of recognized body format: string, string-convertible object, Fluid template or path to .html or .txt file (simple oldschool template markers are supported for template variables only)
	 * @param boolean $isFilePathAndFilename
	 */
	public function setBody($body, $isFilePathAndFilename=FALSE) {
		$this->body = $body;
		$this->bodyIsFilePathAndFilename = (boolean) $isFilePathAndFilename;
	}

	/**
	 * @return mixed
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * @param array $attachments
	 */
	public function setAttachments(array $attachments) {
		$this->attachments = $attachments;
	}

	/**
	 * @return array
	 */
	public function getAttachments() {
		return $this->attachments;
	}

	/**
	 * @param mixed $attachmentPathAndFilename String or string-convertible object containing TYPO3-keyworded or simple path to attachment, siteroot-relative and absolute supported
	 * @param string $description A string description of the attachment, rendered as label for the file (or if you choose, rendered any way you like in a dynamic template)
	 */
	public function addAttachment($attachmentPathAndFilename, $description) {
		$this->attachments[$attachmentPathAndFilename] = $description;
	}

	/**
	 * @param mixed $attachmentPathAndFilename String or string-convertible object containing TYPO3-keyworded or simple path to attachment, siteroot-relative and absolute supported
	 */
	public function removeAttachment($attachmentPathAndFilename) {
		if (isset($this->attachments[$attachmentPathAndFilename])) {
			unset($this->attachments[$attachmentPathAndFilename]);
		}
	}

	/**
	 * @param string $name The name of the variable to register for rendering (Fluid variable or oldschool marker name in UPPERCASE_UNDERSCORED, the first is only supported if body is a Fluid template file and the latter is only supported if your template is a standard .html or .txt file)
	 * @param mixed $value The value to assign when rendering the template
	 */
	public function assign($name, $value) {
		if (isset($this->variables[$name]) === TRUE) {
			throw new Exception('Requested variable ' . $name . ' is already stored, please use another name', 1334863859);
		}
		$this->variables[$name] = $value;
	}

	/**
	 * @param string $name The name of the variable whose existence must be checked, returns TRUE if a variable is already assigned as $name
	 */
	public function exists($name) {
		return isset($this->variables[$name]);
	}

	/**
	 * Finally send the Message - usually handled by a base class such as FluidEmail,
	 * uses the appropriate Service to deliver the Message and the appropriate logic
	 * to validate and render the message (and template, if any)
	 *
	 * @return boolean TRUE on success
	 * @throws Exception
	 */
	public function send() {
		$copy = $this->prepare();
		try {
			return $this->emailService->send($copy);
		} catch (Exception $e) {
			$newException = new Exception('Errors while sending Message - see previous exception attached to this Exception. Message was: ' . $e->getMessage(), 1334867135);
			throw $newException;
		}
	}

	/**
	 * Prepare the data used in the email - body, subject, attachemts etc. - and return
	 * a copy of this object.
	 *
	 * @return Tx_Notify_Message_MessageInterface
	 * @throws Exception
	 */
	public function prepare() {
		$body = $this->body;
		if ($this->bodyIsFilePathAndFilename === TRUE) {
			$templatePathAndFilename = t3lib_div::getFileAbsFileName($body);
			if (file_exists($templatePathAndFilename) === FALSE) {
				throw new Exception('Email template file "' . $templatePathAndFilename . '" not found - file does not exist', 1334865912);
			}
			$content = file_get_contents($templatePathAndFilename);
		} else {
			$content = $body;
		}
		$isFluidTemplate = strpos($content, '{namespace') !== FALSE;
		if ($isFluidTemplate === FALSE) {
			foreach ($this->variables as $name=>$value) {
				$content = str_replace('###' . $name . '###' , $value, $content);
			}
		} else {
			$typoScriptSettings = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
			$paths = Tx_Fed_Utility_Path::translatePath($typoScriptSettings['plugin.']['tx_notify.']['settings.']['email.']['template.']['view.']);
			/** @var $template Tx_Flux_MVC_View_ExposedStandaloneView */
			$template = $this->objectManager->create('Tx_Flux_MVC_View_ExposedStandaloneView');
			$this->variables['attachments'] = $this->attachments;
			$this->variables['recipient'] = $this->recipient;
			$template->assignMultiple($this->variables);
			$template->setTemplateSource($content);
			$template->setLayoutRootPath($paths['layoutRootPath']);
			$template->setPartialRootPath($paths['partialRootPath']);

				// extract media added in the template
			$media = (array) $template->getStoredVariable('Tx_Notify_ViewHelpers_Message_EmailAttachmentViewHelper', 'media');
			foreach ($media as $md5 => $attachmentFilePathAndFilename) {
				$this->addAttachment($attachmentFilePathAndFilename, $md5);
			}

			$content = $template->render();
		}

			// process the content body a little, plaintext emails require some trimming.
		$lines = explode("\n", trim($content));
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
		$content = implode("\n", $lines);

			// NOTE: we clone this current Message to preserve the current object-type variables that have been set.
			// The EmailService requires the variables to be proper strings and cannot perform transformations.
			// ...which means: if you create a custom Message type make sure it also follows this behavior (or returns
			// data types that your custom DeliveryServiceInterface implementing class understands or can transform)
		$this->setPrepared(TRUE);
		$copy = clone $this;
		$copy->setBody($content, $this->bodyIsFilePathAndFilename);
		$copy->setRecipient($recipient);
		$copy->setSender($sender);
		return $copy;
	}

	/**
	 * @param array $carbonCopies
	 */
	public function setCarbonCopies($carbonCopies) {
		$this->carbonCopies = $carbonCopies;
	}

	/**
	 * @return array
	 */
	public function getCarbonCopies() {
		return $this->carbonCopies;
	}

	/**
	 * @param mixed $recipient
	 */
	public function addCarbonCopy($recipient) {
		if (in_array($recipient, $this->carbonCopies) === FALSE) {
			array_push($this->carbonCopies, $recipient);
		}
	}

	/**
	 * @param mixed $recipient
	 */
	public function removeCarbonCopy($recipient) {
		if (in_array($recipient, $this->carbonCopies) === TRUE) {
			$index = array_search($recipient, $this->carbonCopies);
			unset($this->carbonCopies[$index]);
		}
	}

	/**
	 * @param array $blindCarbonCopies
	 */
	public function setBlindCarbonCopies($blindCarbonCopies) {
		$this->blindCarbonCopies = $blindCarbonCopies;
	}

	/**
	 * @return array
	 */
	public function getBlindCarbonCopies() {
		return $this->blindCarbonCopies;
	}

	/**
	 * @param mixed $recipient
	 */
	public function addBlindCarbonCopy($recipient) {
		if (in_array($recipient, $this->blindCarbonCopies) === FALSE) {
			array_push($this->blindCarbonCopies, $recipient);
		}
	}

	/**
	 * @param mixed $recipient
	 */
	public function removeBlindCarbonCopy($recipient) {
		if (in_array($recipient, $this->blindCarbonCopies) === TRUE) {
			$index = array_search($recipient, $this->blindCarbonCopies);
			unset($this->blindCarbonCopies[$index]);
		}
	}

	/**
	 * @param boolean $prepared
	 */
	public function setPrepared($prepared) {
		$this->prepared = $prepared;
	}

	/**
	 * @return boolean
	 */
	public function getPrepared() {
		return $this->prepared;
	}

}
