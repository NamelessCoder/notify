<?php
class Tx_Notify_Message_FluidEmail extends Tx_Notify_Message_AbstractMessage implements Tx_Notify_Message_MessageInterface {

	/**
	 * @return string
	 */
	public function getRecipient() {
		return $this->getRfcFormattedRecipientNameAndAddress();
	}

	/**
	 * @return string
	 */
	public function getSender() {
		return $this->getRfcFormattedSenderNameAndAddress();
	}

}
