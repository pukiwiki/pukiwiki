<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: proxy.php,v 1.2 2003/08/04 01:54:19 arino Exp $
//

/*
 * http_request($url)
 *   HTTPリクエストを発行し、データを取得する
 * $url     : http://から始まるURL(http://user:pass@host:port/path?query)
 * $method  : GET, POST, HEADのいずれか(デフォルトはGET)
 * $headers : 任意の追加ヘッダ
 * $post    : POSTの時に送信するデータを格納した配列('変数名'=>'値')
*/

function http_request($url,$method='GET',$headers='',$post=array())
{
	global $use_proxy,$proxy_host,$proxy_port;
	
	$rc = array();
	$arr = parse_url($url);
	
	$via_proxy = $use_proxy and via_proxy($arr['host']);
	
	// query
	$arr['query'] = isset($arr['query']) ? '?'.$arr['query'] : '';
	// port
	$arr['port'] = isset($arr['port']) ? $arr['port'] : 80;
	
	$url = $via_proxy ? $arr['scheme'].'://'.$arr['host'].':'.$arr['port'] : '';
	$url .= isset($arr['path']) ? $arr['path'] : '/';
	$url .= $arr['query'];
	
	$query = $method.' '.$url." HTTP/1.0\r\n";
	$query .= "Host: ".$arr['host']."\r\n";
	$query .= "User-Agent: PukiWiki/".S_VERSION."\r\n";

	// Basic 認証用
	if (isset($arr['user']) and isset($arr['pass']))
	{
		$query .= 'Authorization: Basic '.
			base64_encode($arr['user'].':'.$arr['pass'])."\r\n";
	}
	
	$query .= $headers;
	
	// POST 時は、urlencode したデータとする
	if (strtoupper($method) == 'POST')
	{
		$POST = array();
		foreach ($post as $name=>$val)
		{
			$POST[] = $name.'='.urlencode($val);
		}
		$data = join('&',$POST);
		$query .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$query .= 'Content-Length: '.strlen($data)."\r\n";
		$query .= "\r\n";
		$query .= $data;
	}
	else
	{
		$query .= "\r\n";
	}
	
	$fp = fsockopen(
		$via_proxy ? $proxy_host : $arr['host'],
		$via_proxy ? $proxy_port : $arr['port'],
		$errno,$errstr,30);
	if (!$fp)
	{
		return array(
			'query'  => $query, // Query String
			'rc'     => $errno, // エラー番号
			'header' => '',     // Header
			'data'   => $errstr // エラーメッセージ
		);
	}
	
	fputs($fp, $query);
	
	$response = '';
	while (!feof($fp))
	{
		$response .= fgets($fp,4096);
	}
	fclose($fp);
	
	$resp = explode("\r\n\r\n",$response,2);
	$rccd = explode(' ',$resp[0],3); // array('HTTP/1.1','200','OK\r\n...')
	return array(
		'query'  => $query,             // Query String
		'rc'     => (integer)$rccd[1], // Response Code
		'header' => $resp[0],           // Header
		'data'   => $resp[1]            // Data
	);
}
// プロキシを経由する必要があるかどうか判定
function via_proxy($host)
{
	global $use_proxy,$no_proxy;
	static $ip_pattern = '/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(?:\/(.+))?$/';
	
	if (!$use_proxy)
	{
		return FALSE;
	}
	$ip = gethostbyname($host);
	$l_ip = ip2long($ip);
	$valid = (is_long($l_ip) and long2ip($l_ip) == $ip); // valid ip address
	
	foreach ($no_proxy as $network)
	{
		if ($valid and preg_match($ip_pattern,$network,$matches))
		{
			$l_net = ip2long($matches[1]);
			$mask = array_key_exists(2,$matches) ? $matches[2] : 32;
			$mask = is_numeric($mask) ?
				pow(2,32) - pow(2,32 - $mask) : // "10.0.0.0/8"
				ip2long($mask);                 // "10.0.0.0/255.0.0.0"
			if (($l_ip & $mask) == $l_net)
			{
				return FALSE;
			}
		}
		else
		{
			if (preg_match('/'.preg_quote($network,'/').'/',$host))
			{
				return FALSE;
			}
		}
	}
	return TRUE;
}
?>
