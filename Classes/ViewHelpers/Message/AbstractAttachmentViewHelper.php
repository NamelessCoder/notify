<?php


#require_once PATH_typo3 . 'contrib/swiftmailer/classes/Swift.php';
require_once(PATH_typo3 . 'contrib/swiftmailer/swift_required.php');
#Swift::registerAutoload(NULL);

class Tx_Notify_ViewHelpers_Message_AbstractAttachmentViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractTagBasedViewHelper {

	/**
	 * Embed a file (return the Content Id it receives or use custom ID)
	 *
	 * @param string $file
	 * @return string
	 */
	public function render($file = NULL) {
		if ($file === NULL) {
			$file = $this->renderChildren();
		}
		$attachment = $this->createAttachmentObject($file);
		$this->attach($attachment);
	}

	/**
	 * @param Swift_Mime_MimeEntity $file
	 * @return void
	 */
	protected function attach(Swift_Mime_MimeEntity $attachment) {
		$id = $attachment->getId();
		$media = array();
		if ($this->viewHelperVariableContainer->exists('Tx_Notify_ViewHelpers_Message_AbstractAttachmentViewHelper', 'media') === TRUE) {
			$media = $this->viewHelperVariableContainer->get('Tx_Notify_ViewHelpers_Message_AbstractAttachmentViewHelper', 'media');
		}
		$media[$id] = $attachment;
		$this->viewHelperVariableContainer->addOrUpdate('Tx_Notify_ViewHelpers_Message_AbstractAttachmentViewHelper', 'media', $media);
	}

	/**
	 * @param string $file
	 * @return Swift_Mime_MimeEntity
	 */
	protected function createAttachmentObject($file) {
		$file = t3lib_div::getFileAbsFileName($file);
		$id = $this->createId($file);
		$attachment = Swift_Attachment::fromPath($file);
			// Note: disposition is used to instruct the message compiler what to do
			// with the attachment - values are "download", "inline" and "inline-image"
		$attachment->setDisposition('download');
		$attachment->setId($id);
		return $attachment;
	}

	/**
	 * @param string $file
	 * @return string
	 */
	protected function createId($file) {
		if ($this->arguments['id']) {
			$id = $this->arguments['id'];
		} else {
			$id = md5($file);
		}
		if (strpos($id, '@') === FALSE) {
			$id = $id . '@ext.notify';
		}
		return $id;
	}

}
