<?php 

use \Tuanduimao\Loader\App as App;
use \Tuanduimao\Utils as Utils;
use \Tuanduimao\Tuan as Tuan;
use \Tuanduimao\Excp as Excp;
use \Tuanduimao\Conf as Conf;
use \Tuanduimao\Wechat as Wechat;
/**
 * 客户面板
 */
class MobCustomerPanelController extends \Tuanduimao\Loader\Controller {

	function __construct() {

	}

	/**
	 * 详情页入口
	 * @return [type] [description]
	 */
	function index(){

		$data=['title'=>'客户管理'];
		// html代码载入
		App::render($data,'mobile/customer','search.index');

	}

	/**
	 * 创建客户表单
	 * @return [type] [description]
	 */
	function create(){

		$data=['title'=>'客户管理'];
		// html代码载入
		App::render($data,'mobile/customer','panel.create');

	}

	/**
	 * 修改客户表单
	 * @return [type] [description]
	 */
	function  modify(){
		// html代码载入
		App::render($data,'mobile/customer','panel.modify');
	}

	/**
	 * 微信页载入
	 * @return [type] [description]
	 */
	function wechat(){

		// $we = new Wechat([
		// 	'appid'=>'wx60ba50125346e3e6',
		// 	'secret'=>'4b07bfa8380ac3a7630206547faaf69f'
		// ]);
		// $data = $we->getSignature();

		// $accessToken = Utils::req(
		// 	"GET",
		// 	"https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx60ba50125346e3e6&secret=4b07bfa8380ac3a7630206547faaf69f"
		// );
		// // Utils::out("\n", $accessToken, "\n");
		// $jsapiTicket = Utils::req(
		// 	"GET",
		// 	"https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$accessToken["access_token"]}&type=jsapi"
		// );
		$jsapiTicket = $this->getJsApiTicket();
		// Utils::out("\n", $jsapiTicket, "\n");
		
		$url = "http://hzh.appcook.cn/i/crm/Mobcustomerpanel/wechat";
		// 注意 URL 一定要动态获取，不能 hardcode.
	    // $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	    // $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		$timestamp = time();
		$nonceStr = $this->createNonceStr();

		// $string1 = "jsapi_ticket={$jsapiTicket["Ticket"]}&noncestr={$nonceStr}&timestamp={$timeStamp}&url={$urlPath}";
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
		$signature = sha1($string);

		$data = array(
			"appId" => "wx60ba50125346e3e6",
			"timestamp" => $timestamp,
			"nonceStr" => $nonceStr,
			"url" => $url,
			"signature" => $signature,
			"rawString" => $string
		);
		// Utils::out("\n", $data, "\n");
		App::render($data,'mobile/customer','panel.wechat');
	}

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  private function getJsApiTicket() {
    // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode($this->get_php_file("jsapi_ticket.php"));
    if ($data->expire_time < time()) {
      $accessToken = $this->getAccessToken();
      // 如果是企业号用以下 URL 获取 ticket
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
      $res = json_decode($this->httpGet($url));
      $ticket = $res->ticket;
      if ($ticket) {
        $data->expire_time = time() + 7000;
        $data->jsapi_ticket = $ticket;
        $this->set_php_file("jsapi_ticket.php", json_encode($data));
      }
    } else {
      $ticket = $data->jsapi_ticket;
    }

    return $ticket;
  }

  private function getAccessToken() {
    // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode($this->get_php_file("access_token.php"));
    if ($data->expire_time < time()) {
      // 如果是企业号用以下URL获取access_token
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx60ba50125346e3e6&secret=4b07bfa8380ac3a7630206547faaf69f";
      $res = json_decode($this->httpGet($url));
      $access_token = $res->access_token;
      if ($access_token) {
        $data->expire_time = time() + 7000;
        $data->access_token = $access_token;
        $this->set_php_file("access_token.php", json_encode($data));
      }
    } else {
      $access_token = $data->access_token;
    }
    return $access_token;
  }

  private function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
    // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
  }

  private function get_php_file($filename) {
    return trim(substr(file_get_contents($filename), 15));
  }
  private function set_php_file($filename, $content) {
    $fp = fopen($filename, "w");
    fwrite($fp, "<?php exit();?>" . $content);
    fclose($fp);
  }
	
	/**
	 * 查看客户页面（客户详情页）
	 * @return [type] [description]
	 */
	function  read(){

		$_id = $_GET['_id'];
		// 实例化
		$Customer = App::M('Customer');
		// 查询这条数据的数值
		$data= $Customer->getLine( "where _id=:id", [],['id'=>$_id ] );
		$data  = ['data'=>$data];

		// html代码载入
		App::render($data,'mobile/customer','panel.read');
		
	}


}


 ?>