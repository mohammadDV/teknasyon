<?php
namespace System\Lib;

class Exp extends \Exception {
	function __construct($msg = NULL, $code = 0, Exception $previous = NULL,$userID = 0) {
        parent::__construct($msg,$code,$previous);
	}
}
