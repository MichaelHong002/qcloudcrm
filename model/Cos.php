<?php 

//test in local

use \Tuanduimao\Mem as Mem;
use \Tuanduimao\Excp as Excp;
use \Tuanduimao\Err as Err;
use \Tuanduimao\Conf as Conf;
use \Tuanduimao\Model as Model;
use \Tuanduimao\Utils as Utils;
use \Tuanduimao\Loader\App as App;
/**
 * cos对象存储
 */
class  CosModel {


	function __construct( $opt=[] ) {
		$this->appid = isset($opt['appid']) ? $opt['appid'] : "";
		$this->bucket = isset($opt['bucket']) ? $opt['bucket'] : "";
		$this->SecretID = isset($opt['SecretID']) ? $opt['SecretID'] : "";
		$this->SecretKey = isset($opt['SecretKey']) ? $opt['SecretKey'] : "";
	}
	/**
	 * 签名
	 * @param  array  $opt [description]
	 * @return [type]      [description]
	 */
	function sign( $opt=[] ) {
		// 生成10位随机数
		$randstr= $this->generateId(10);

		// 参数处理
		// $t = isset($opt['e']) ? time()+86400 : 0;
		$a = isset($opt['appid']) ? $opt['appid'] : $this->appid;
		$b = isset($opt['bucket']) ? $opt['bucket'] : $this->bucket;
		$k = isset($opt['SecretID']) ? $opt['SecretID'] : $this->SecretID;
		$e = isset($opt['e']) ? $opt['e'] : time()+3600;
		$SecretKey = isset($opt['SecretKey']) ? $opt['SecretKey'] : $this->SecretKey;
		$s = [
			"a" => $a,
			"b" => $b,
			"k" => $k,
			"e" => $e,
			"t" => time(),
			"r" => $randstr,
			"f" =>$opt['file'],
		];
		// 拼接字符串
		$orignal = "a={$s['a']}&b={$s['b']}&k={$s['k']}&e={$s['e']}&t={$s['t']}&r={$s['r']}&f={$s['f']}";
		$signTmp = hash_hmac( 'SHA1', $orignal, $SecretKey , true );
		$sign = base64_encode($signTmp.$orignal);
		return $sign;
	}

	/**
	 * 文件转为字符串
	 * @param  [type] $imageUrl [description]
	 * @param  array  $opt      [description]
	 * @return [type]           [description]
	 */
	function uploadByUrl( $imageUrl, $opt=[] ) {
		$imageData = file_get_contents( $imageUrl );
		return $this->upload( $imageData, $opt );
	}


	/**
	 * 文件上传
	 * @param  [type] $imageData [description]
	 * @param  array  $opt       [description]
	 * @return [type]            [description]S
	 */
	function upload( $imageData, $opt=[] ){
		// $config = App::M('config');
		$appid = isset($opt["appid"]) ? $opt["appid"] : $this->appid;
		$bucket = isset($opt['bucket']) ? $opt['bucket'] : $this->bucket;
		// 生成随机数
		// 生成随机数避免文件重复	
		$num = $this->generateId(10);
		$filetype  = isset($opt['filetype']) ? $opt['filetype'] : 'jpg';
		$file =  $num.".".$opt["filetype"];
		$file = isset($opt["file"]) ? $opt["file"] : $file;
		$filename  ="/".$appid."/".$bucket."/test_file/".$file;
		// $filename  ="/".$appid."/".$bucket."/test_file/".$num.".".$opt["filetype"];

		$opt["attr"] = isset($opt["attr"]) ? $opt["attr"] : "";
		$opt["insertOnly"] = isset($opt["insertOnly"]) ? $opt["insertOnly"] : "0";
		$opt["mimetype"] = isset($opt["mimetype"]) ? $opt["mimetype"] : "image/jpeg";
		$api = "http://sh.file.myqcloud.com/files/v2/{$filename}";

		// 文件名字
		$signStr = $this->sign(["file"=>$filename]);
		// 文件流进行转码
		$sha = sha1( $imageData );
		// 发送参数
		$data = [
			"op" => "upload",
			//是否覆盖
			"insertOnly" => $opt["insertOnly"],

			"__files" => [
				[
					//文件类型
					"mimetype"=>$opt["mimetype"],
					"name"=>"filecontent",
					// 文件名字
					"filename"=>$filename,
					"data" => $imageData,
				]
			],
			"sha" => $sha
		];
		
		// 文件属性
		if ( $opt["attr"] != "" ) {
			$data["biz_attr"] = $opt["attr"];
		}
		$resp = Utils::Req(
			"POST",
			$api,
			[	
				"type" => "media",
				"datatype"=>"json",
				"header" => [
					"Authorization:{$signStr}",
				],
				"data" => $data
			]);
		return $resp;
	}



	/**
	 * 删除方法
	 * @param  [type] $file [description]
	 * @return [type]       [description]
	 */
	function remove( $file ){
		// $config = App::M('config');
		// $appid = $config->getvalue('cos')['appid'];
		// $bucket = $config->getvalue('cos')['bucket'];
		$appid = $this->appid;
		$bucket = $this->bucket;
		$api = "http://sh.file.myqcloud.com/files/v2/{$appid}/{$bucket}/test_file/{$file}";

		$filename  ="/".$appid."/".$bucket."/test_file/".$file;
		$signStr = $this->sign(['file'=>$filename,'e'=>'0']);
		$resp = Utils::Req(
			"POST",
			$api,
			[	
				"datatype"=>"json",
				"header" => [
					"Authorization:$signStr",
				],
				"data" =>[
					"op" => "delete"
				]
			]
		);
		return $resp;
	}



	/**
	 *随机数
	 * @return [type] [description]
	 */
	function generateId( $length ) {
    	// 随机数字符集，可任意添加你需要的字符
    	$chars = '1234567890';
	    $num = '';
	    for ( $i = 0; $i < $length; $i++ ) 
	    {
	      
	        $num .= $chars[ mt_rand(0, strlen($chars) - 1) ];
	    }

	    return $num;
	}
}

 ?>