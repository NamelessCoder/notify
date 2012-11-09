<?php
class Tx_Notify_ViewHelpers_Message_ImageViewHelper extends Tx_Notify_ViewHelpers_Message_AbstractAttachmentViewHelper {

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
		return $attachment->getId();
	}

	/**
	 * @param string $file
	 * @return Swift_Attachment
	 */
	protected function createAttachmentObject($file) {
		$file = t3lib_div::getFileAbsFileName($file);
		$fileInfo = new finfo(FILEINFO_MIME);
		$contentType = $fileInfo->file($file);
		$id = $this->createId($file);
		$attachment = new Swift_Image(file_get_contents($file), basename($file), $contentType);
		$attachment->setId($id);
		$attachment->setDisposition('inline');
		return $attachment;
	}

}
