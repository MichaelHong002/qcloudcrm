<?php 
define('SEROOT', getenv('SEROOT') );
define('TROOT', getenv('TROOT') );
define('CWD', getenv('CWD') );
define('APP_ROOT', getenv('APP_ROOT') );

require_once( SEROOT . "/loader/Autoload.php" );

use \Tuanduimao\Loader\App as App;
use \Tuanduimao\Mem as Mem;
use \Tuanduimao\Excp as Excp;
use \Tuanduimao\Err as Err;
use \Tuanduimao\Conf as Conf;
use \Tuanduimao\Model as Model;
use \Tuanduimao\Utils as Utils;
/**
 * youtu测试	
 */
class Testyoutu extends PHPUnit_Framework_TestCase {
	/**
	 * 上传测试
	 * @return [type] [description]
	 */
	function testUpload() {
		// $config = App::M('config');

		$yt = App::M("Youtu",
			[
				'appid'=>'1252666445',
				'bucket'=>'yoututest',
				'SecretID'=>'AKIDkJhssgYkARXVRCO8JI7lDTYR8dxFCUM2',
				'SecretKey'=>'3vc9YqvqbBYklfNtDSWO7MTO7f2CGAbx',
				'file'=>'helloworld.jpg'
			]);
		// try {
			$resp = $yt->uploadByUrl( "http://4493bz.1985t.com/uploads/allimg/150127/4-15012G52133.jpg", "helloworld.jpg");
		// } catch(Excp $e) {
		// 	print_r($e->toArray());
		// }
		Utils::out("upload1\n", $resp, "\n");
		$this->assertEquals($resp['code'],0);
	}
	/**
	 * 名片识别测试
	 * @return [type] [description]
	 * 图片必须为名片并且大小不得超过500m
	 */
	function testOcr() {
		$yt = App::M("Youtu",
			[
				'appid'=>'1252666445',
				'bucket'=>'yoututest',
				'SecretID'=>'AKIDkJhssgYkARXVRCO8JI7lDTYR8dxFCUM2',
				'SecretKey'=>'3vc9YqvqbBYklfNtDSWO7MTO7f2CGAbx',
				'file'=>'helloOCR.jpg'
			]);
		// try {
			$resp = $yt->uploadByUrl("http://pic.58pic.com/58pic/12/49/04/80k58PICzYP.jpg", "helloOCR.jpg");
		// } catch(Excp $e) {
		// 	print_r($e->toArray());
		// }
		sleep(2);
		Utils::out("upload2\n", $resp, "\n");
		// try {
			$namecard = $yt->ocr($resp['data']['source_url']);
		// } catch(Excp $e) {
		// 	print_r($e->toArray());
		// }
		Utils::out( "ocr\n", $namecard , "\n");
		$this->assertEquals($namecard['result_list']['code'],0);
	}
}
?>