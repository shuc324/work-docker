<?php

class TNonblockingServer extends TServer {

	public function __construct($processor, $transport) {
		parent::__construct($processor, $transport);
		$this->transport_->setCallback(array($this, 'handleRequest'));
	}
	
	public function serve() {
		$this->transport_->listen();
	}
	
	public function listen() {
		$this->transport_->listen(false);
	}
	
	public function loop($block_once = false) {
		$this->transport_->loop($block_once);
	}

	public function stop() {
		$this->transport_->close();
	}

	public function handleRequest(TTransport $transport) {
		$protocol = new TBinaryProtocol($transport, true, true);
		$this->processor_->process($protocol, $protocol);
	}

}

