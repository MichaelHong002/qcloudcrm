<?php 
use \Tuanduimao\Mem as Mem;
use \Tuanduimao\Excp as Excp;
use \Tuanduimao\Err as Err;
use \Tuanduimao\Conf as Conf;
use \Tuanduimao\Model as Model;
use \Tuanduimao\Utils as Utils;
use \Tuanduimao\Loader\App as App;
// 有图
class Youtumodel {
	/**
	 *	appid
	 *	bucket
	 *	SecretID
	 *	SecretKey
	 *	validity
	 *	file
	 * { function_description }
	 */
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
			// "u" => "0",
			"f" =>$opt['file']
		];
		// 拼接字符串
		$orignal = "a={$s['a']}&b={$s['b']}&k={$s['k']}&e={$s['e']}&t={$s['t']}&r={$s['r']}&f={$s['f']}";
		$signTmp = hash_hmac( 'SHA1', $orignal, $SecretKey , true );
		$sign = base64_encode($signTmp.$orignal);
		return $sign;
	}
	/**
	 * 图片上传到空间
	 * [update description]
	 * @return [type] [description]
	 */

	function uploadByUrl( $imageUrl, $imageFilename=null ) {
		$imageData = file_get_contents( $imageUrl );
		return $this->upload( $imageData, $imageFilename );
	}

	function upload($imageData, $imageFilename=null){

		$appid = $this->appid;
		$bucket = $this->bucket;
		$filename = "/{$appid}/{$bucket}/{$imageFilename}";
		$key = $this->sign(["file"=>$filename]);
		$api = "http://gz.file.myqcloud.com/files/v2/{$filename}";
		$sha = sha1( $imageData );
		// 发送参数
		$data = [
			"op" => "upload",
			//是否覆盖
			"insertOnly" => "0",
			"__files" => [
				[
					//文件类型
					"mimetype" => "image/jpeg",
					"name" => "filecontent",
					// 文件名字
					"filename" => $filename,
					"data" => $imageData
				]
			],
			// "biz_attr" => "",
			"sha" => $sha
		];

		// if ($filename == "helloOCR.jpg") {
		// 	$data["insertOnly"] = "0";
		// } 
		
		$resp = Utils::Req(
			"POST",
			$api,
			[	
				"type" => "media",
				"datatype"=>"json",
				"header" => [
					"Authorization: {$key}",
				],
				"data" => $data
			]);
		return $resp;
	}
	/**
	 * 拍照
	 * @return [type] [description]
	 */
	function ocr($url){
		$appid = $this->appid;
		$bucket = $this->bucket;
		$file = $this->file;
		$filename = "/{$appid}/{$bucket}/{$file}";
		$api = "http://service.image.myqcloud.com/ocr/namecard";
		$key = $this->sign(["file"=>$filename]);
		//发送图片信息返回图片信息
		$resp = Utils::Req(
			"POST",
			$api,
			[
				"type" => "json",
				"datatype" => "json",
				"header" => [
					"Authorization: {$key}"
				],
				"data" =>[
					"appid" => $appid,
					"bucket" => $bucket,
					"ret_image" => 0,
					"url_list" => [
						$url
					]
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
	    return date(time()).$num;
	}
}
 ?>
