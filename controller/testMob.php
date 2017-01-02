<?php
use \Tuanduimao\Loader\App as App;
use \Tuanduimao\Utils as Utils;
use \Tuanduimao\Tuan as Tuan;
use \Tuanduimao\Excp as Excp;
use \Tuanduimao\Conf as Conf;
use \Tuanduimao\Wechat as Wechat;

class DefaultsController extends \Tuanduimao\Loader\Controller {
	
	function __construct() {
	}

	function test() {
		echo "test_success";
		// print_r($_POST);
	}
}
?>