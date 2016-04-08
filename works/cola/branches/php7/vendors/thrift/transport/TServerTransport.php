<?php
abstract class TServerTransport {

	abstract function listen();
	abstract function close();
}
