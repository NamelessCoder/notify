<?php
class Tx_Notify_ViewHelpers_Message_EmbedViewHelper extends Tx_Notify_ViewHelpers_Message_AbstractAttachmentViewHelper {

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
		return 'cid:' . $attachment->getId();
	}

	/**
	 * @param string $file
	 * @return Swift_Attachment
	 */
	protected function createAttachmentObject($file) {
		$file = t3lib_div::getFileAbsFileName($file);
		$id = $this->createId($file);
		$attachment = Swift_Attachment::fromPath($file);
		$attachment->setId($id);
		$attachment->setDisposition('inline');
		return $attachment;
	}

}
