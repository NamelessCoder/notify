<?php
interface Tx_Notify_Message_MessageInterface {

	/**
	 * @abstract
	 * @param mixed $recipient Either an array of $name=>$email, a simple email address or a "Name <email@dom.tld>" string or a string-convertible object which returns any of the beforementioned address types
	 */
	public function setRecipient($recipient);

	/**
	 * @abstract
	 * @return mixed
	 */
	public function getRecipient();

	/**
	 * @abstract
	 * @param mixed $sender Either an array of $name=>$email, a simple email address or a "Name <email@dom.tld>" string or a string-convertible object which returns any of the beforementioned address types
	 */
	public function setSender($sender);

	/**
	 * @abstract
	 * @return mixed
	 */
	public function getSender();

	/**
	 * @abstract
	 * @param string $subject
	 */
	public function setSubject($subject);

	/**
	 * @abstract
	 * @return string
	 */
	public function getSubject();

	/**
	 * @abstract
	 * @param mixed $body Any type of recognized body format: string, string-convertible object, Fluid template or path to .html or .txt file (simple oldschool template markers are supported for template variables only)
	 * @param boolean $isFilePathAndFilename
	 */
	public function setBody($body, $isFilePathAndFilename=FALSE);

	/**
	 * @abstract
	 * @return mixed
	 */
	public function getBody();

	/**
	 * @abstract
	 * @param Swift_Mime_MimeEntity[] $attachments
	 */
	public function setAttachments(array $attachments);

	/**
	 * @abstract
	 * @return Swift_Mime_MimeEntity[]
	 */
	public function getAttachments();

	/**
	 * @abstract
	 * @param mixed $attachmentPathAndFilename String or string-convertible object containing TYPO3-keyworded or simple path to attachment, siteroot-relative and absolute supported
	 * @param string $description A string description of the attachment, rendered as label for the file (or if you choose, rendered any way you like in a dynamic template)
	 */
	public function addAttachment($attachmentPathAndFilename, $description);

	/**
	 * @abstract
	 * @param mixed $attachmentPathAndFilename String or string-convertible object containing TYPO3-keyworded or simple path to attachment, siteroot-relative and absolute supported
	 */
	public function removeAttachment($attachmentPathAndFilename);

	/**
	 * @abstract
	 * @param string $name The name of the variable to register for rendering (Fluid variable or oldschool marker name in UPPERCASE_UNDERSCORED, the first is only supported if body is a Fluid template file and the latter is only supported if your template is a standard .html or .txt file)
	 * @param mixed $value The value to assign when rendering the template
	 */
	public function assign($name, $value);

	/**
	 * @abstract
	 * @param string $name The name of the variable whose existence must be checked, returns TRUE if a variable is already assigned as $name
	 */
	public function exists($name);

	/**
	 * Finally send the Message - usually handled by a base class such as FluidEmail, uses the appropriate Service to deliver the Message and the appropriate logic to validate and render the message (and template, if any)
	 *
	 * @abstract
	 * @return boolean TRUE on success
	 * @throws Exception
	 */
	public function send();

	/**
	 * Prepare the Message - collect attachment, render body etc.
	 *
	 * @abstract
	 * @return Tx_Notify_Message_MessageInterface
	 * @throws Exception
	 */
	public function prepare();

	/**
	 * @abstract
	 * @param boolean $prepared
	 */
	public function setPrepared($prepared);

	/**
	 * @abstract
	 * @return boolean
	 */
	public function getPrepared();

}