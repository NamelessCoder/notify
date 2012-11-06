<?php

class Tx_Notify_ViewHelpers_Message_EmailAttachmentViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @param string $file
	 * @return string
	 */
	public function render($file) {
		$md5 = md5($file);
		$media = array();
		if ($this->viewHelperVariableContainer->exists('Tx_Notify_ViewHelpers_Message_EmailAttachmentViewHelper', 'media') === TRUE) {
			$media = $this->viewHelperVariableContainer->get('Tx_Notify_ViewHelpers_Message_EmailAttachmentViewHelper', 'media');
		}
		$media[$md5] = $file;
		$this->viewHelperVariableContainer->addOrUpdate('Tx_Notify_ViewHelpers_Message_EmailAttachmentViewHelper', 'media', $media);
		return $md5;
	}

}