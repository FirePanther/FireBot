<?php
namespace telegram;

class Reply extends Response {
	// reply to request message (setting chatId not possible/necessary, will be ignored)
	public function emit($chatId = null) {
		$request = \app\FireBot::getRequest();
		if ($request === false) {
			throw new \Exception('Can\'t find message to reply to');
		}
		
		$this->sendMessageParameters = [
			'reply_to_message_id' => $request->getMessageId()
		];
		
		parent::emit();
	}
}
