<?php
interface Tx_Notify_Message_DeliveryServiceInterface {

	/**
	 * @abstract
	 * @param Tx_Notify_Message_MessageInterface $message The Message interface implementing class containing the Message to be sent
	 * @return boolean TRUE on success, Exeption with error details if errors are encountered while sending
	 */
	public function send(Tx_Notify_Message_MessageInterface $message);

}
