<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: proxy.php,v 1.6 2004/07/31 03:09:19 henoheno Exp $
//

/*
 * http_request($url)
 *   HTTPリクエストを発行し、データを取得する
 * $url     : http://から始まるURL(http://user:pass@host:port/path?query)
 * $method  : GET, POST, HEADのいずれか(デフォルトはGET)
 * $headers : 任意の追加ヘッダ
 * $post    : POSTの時に送信するデータを格納した配列('変数名'=>'値')
 * $redirect_max : HTTP redirectの回数制限
*/

// リダイレクト回数制限の初期値
define('HTTP_REQUEST_URL_REDIRECT_MAX',10);

function http_request($url,$method='GET',$headers='',$post=array(),
	$redirect_max=HTTP_REQUEST_URL_REDIRECT_MAX)
{
	global $proxy_host, $proxy_port;
	global $need_proxy_auth, $proxy_auth_user, $proxy_auth_pass;

	$rc = array();
	$arr = parse_url($url);

	$via_proxy = via_proxy($arr['host']);

	// query
	$arr['query'] = isset($arr['query']) ? '?'.$arr['query'] : '';
	// port
	$arr['port'] = isset($arr['port']) ? $arr['port'] : 80;

	$url_base = $arr['scheme'].'://'.$arr['host'].':'.$arr['port'];
	$url_path = isset($arr['path']) ? $arr['path'] : '/';
	$url = ($via_proxy ? $url_base : '').$url_path.$arr['query'];

	$query = $method.' '.$url." HTTP/1.0\r\n";
	$query .= "Host: ".$arr['host']."\r\n";
	$query .= "User-Agent: PukiWiki/".S_VERSION."\r\n";

	// proxyのBasic認証
	if ($need_proxy_auth and isset($proxy_auth_user) and isset($proxy_auth_pass))
	{
		$query .= 'Proxy-Authorization: Basic '.
			base64_encode($proxy_auth_user.':'.$proxy_auth_pass)."\r\n";
	}
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

	$errno = 0; $errstr = '';
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
		$response .= fread($fp,4096);
	}
	fclose($fp);

	$resp = explode("\r\n\r\n",$response,2);
	$rccd = explode(' ',$resp[0],3); // array('HTTP/1.1','200','OK\r\n...')
	$rc = (integer)$rccd[1];

	// Redirect
	$matches = array();
	switch ($rc)
	{
		case 302: // Moved Temporarily
		case 301: // Moved Permanently
			if (preg_match('/^Location: (.+)$/m',$resp[0],$matches)
				and --$redirect_max > 0)
			{
				$url = trim($matches[1]);
				if (!preg_match('/^https?:\//',$url)) // no scheme
				{
					if ($url{0} != '/') // Relative path
					{
						// to Absolute path
						$url = substr($url_path,0,strrpos($url_path,'/')).'/'.$url;
					}
					// add sheme,host
					$url = $url_base.$url;
				}
				return http_request($url,$method,$headers,$post,$redirect_max);
			}
	}

	return array(
		'query'  => $query,   // Query String
		'rc'     => $rc,      // Response Code
		'header' => $resp[0], // Header
		'data'   => $resp[1]  // Data
	);
}

// プロキシを経由する必要があるかどうか判定
function via_proxy($host)
{
	global $use_proxy, $no_proxy;
	static $ip_pattern = '/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})(?:\/(.+))?$/';

	if (!$use_proxy)
	{
		return FALSE;
	}
	$ip = gethostbyname($host);
	$l_ip = ip2long($ip);
	$valid = (is_long($l_ip) and long2ip($l_ip) == $ip); // valid ip address

	$matches = array();
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
