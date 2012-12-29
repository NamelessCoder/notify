<?php
class Tx_Notify_Message_AbstractMessage {

	/**
	 * @var integer
	 */
	protected $type = Tx_Notify_Message_MessageInterface::TYPE_TEXT;

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
	 * @var mixed
	 */
	protected $alternative;

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
	 * @param string $subject
	 */
	public function setSubject($subject) {
		$this->subject = trim($subject);
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
		$this->body = trim($body);
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
	 * @param mixed $attachment String or string-convertible object containing TYPO3-keyworded or simple path to attachment, siteroot-relative and absolute supported
	 * @param string $description A string description of the attachment, rendered as label for the file (or if you choose, rendered any way you like in a dynamic template)
	 */
	public function addAttachment($attachment) {
		if (in_array($attachment, $this->attachments) === FALSE) {
			array_push($this->attachments, $attachment);
		}
	}

	/**
	 * @param mixed $attachment String or string-convertible object containing TYPO3-keyworded or simple path to attachment, siteroot-relative and absolute supported
	 */
	public function removeAttachment($attachment) {
		if (in_array($attachment, $this->attachments) === TRUE) {
			$index = array_search($attachment, $this->attachments);
			unset($this->attachments[$index]);
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
		$sent = FALSE;
		try {
			$sent = $this->emailService->send($copy);
		} catch (Exception $e) {
			$newException = new Exception('Errors while sending Message - see previous exception attached to this Exception. Message was: ' . $e->getMessage(), 1334867135, $e);
			throw $newException;
		}
		return $sent;
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
			$paths = Tx_Fed_Utility_Path::translatePath($typoScriptSettings['plugin.']['tx_notify.']['settings.']['email.']['view.']);

			$className = get_class($this);
			if (strpos($className, '\\') !== FALSE) {
				$classNameSegments = explode('\\', $className);
			} else {
				$classNameSegments = explode('_', $className);
			}
			$extensionName = $classNameSegments[1];
			/** @var $request Tx_Extbase_MVC_Web_Request */
			$request = $this->objectManager->create('Tx_Extbase_MVC_Web_Request');
			$request->setControllerExtensionName($extensionName);
			/** @var $controllerContext Tx_Extbase_MVC_Controller_ControllerContext */
			$controllerContext = $this->objectManager->create('Tx_Extbase_MVC_Controller_ControllerContext');
			$controllerContext->setRequest($request);
			/** @var $template Tx_Flux_MVC_View_ExposedStandaloneView */
			$this->variables['attachments'] = $this->attachments;
			$this->variables['recipient'] = $this->recipient;
			$template = $this->objectManager->create('Tx_Flux_MVC_View_ExposedStandaloneView');
			$template->setControllerContext($controllerContext);
			$template->setFormat('eml');
			$template->assignMultiple($this->variables);
			$template->setTemplateSource($content);
			$template->setLayoutRootPath($paths['layoutRootPath']);
			$template->setPartialRootPath($paths['partialRootPath']);

				// extract media added in the template
			try {
				$media = (array) $template->getStoredVariable('Tx_Notify_ViewHelpers_Message_AbstractAttachmentViewHelper', 'media');
				foreach ($media as $attachmentContentId => $attachment) {
					$this->addAttachment($attachment, $attachmentContentId);
				}
			} catch (exception $e) {
				// avoid error if no attachment
				if ($e->getCode() != 1243325768) {
					throw $e;

				}
			}

			$content = $template->render();
		}
		$content = trim($content);

			// NOTE: we clone this current Message to preserve the current object-type variables that have been set.
			// The EmailService requires the variables to be proper strings and cannot perform transformations.
			// ...which means: if you create a custom Message type make sure it also follows this behavior (or returns
			// data types that your custom DeliveryServiceInterface implementing class understands or can transform)
		$this->setPrepared(TRUE);
		$copy = clone $this;
		$copy->setBody($content, FALSE);
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

	/**
	 * @param integer $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @return integer
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param mixed $alternative
	 */
	public function setAlternative($alternative) {
		$this->alternative = $alternative;
	}

	/**
	 * @return mixed
	 */
	public function getAlternative() {
		if (!$this->alternative && $this->type === Tx_Notify_Message_MessageInterface::TYPE_HTML) {
			// process the content body a little, plaintext emails require some trimming.
			$lines = explode("\n", trim(strip_tags($this->getBody())));
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
			return implode(LF, $lines);
		}
		return $this->alternative;
	}

}
