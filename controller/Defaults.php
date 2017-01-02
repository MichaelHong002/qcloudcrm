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
		$we = new Wechat([
			'appid'=>'wx60ba50125346e3e6',
			'secret'=>'4b07bfa8380ac3a7630206547faaf69f'
		]);
		$data = $we->getSignature();
		Utils::out("\n", $data, "\n");
	}

	function index() {
		$hello = App::M('Hello');
		$data = $hello->select("LIMIT 20");

		App::render($data,'web','index');
		
		return [
			'js' => [
	 			"js/plugins/select2/select2.full.min.js",
	 			"js/plugins/jquery-validation/jquery.validate.min.js",
	 			"js/plugins/dropzonejs/dropzone.min.js",
	 			"js/plugins/cropper/cropper.min.js",
	 			'js/plugins/masked-inputs/jquery.maskedinput.min.js',
	 			'js/plugins/jquery-tags-input/jquery.tagsinput.min.js',
		 		"js/plugins/dropzonejs/dropzone.min.js",
		 		"js/plugins/cropper/cropper.min.js",
	    		'js/plugins/jquery-ui/jquery-ui.min.js',
        		'js/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js',
			],
			'css'=>[
	 			"js/plugins/select2/select2.min.css",
	 			"js/plugins/select2/select2-bootstrap.min.css"
	 		],
			'crumb' => [
	            "应用演示" => APP::R('defaults','index'),
	            "数据列表" =>'',
	        ]
		];
	}

	function faker() {
		$hello = App::M('Hello');
		$hello->fakerdata();
		echo json_encode($hello->select("",["COUNT(*)"]));
	}

}