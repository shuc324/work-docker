<?php
abstract class TServer {

	/**
	 * Underlying transport
	 *
	 * @var TTransport
	 */
	protected $transport_;

	/**
	 * Underlying processor
	 *
	 * @var processor
	 */
	protected $processor_;

	/**
	 * Constructor
	 */
	public function __construct($processor, $transport) {
		$this->processor_ = $processor;
		$this->transport_ = $transport;
	}

	/**
	 * The run method fires up the server and gets things going.
	 */
	abstract function serve();

	/**
	 * Stop the server. This is optional on a per-implementation basis. Not
	 * all servers are required to be cleanly stoppable.
	 */
	public function stop() {

	}

}

