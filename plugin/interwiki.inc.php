<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: interwiki.inc.php,v 1.4 2003/05/13 05:05:25 arino Exp $
//
// InterWikiNameの判別とページの表示

function plugin_interwiki_action()
{
	global $script,$vars,$interwiki,$WikiName,$InterWikiName;
	global $_title_invalidiwn,$_msg_invalidiwn;
	
	$retvars = array();
	
	if (!preg_match("/^$InterWikiName$/",$vars['page'],$match))
	{
		$retvars['msg'] = $_title_invalidiwn;
		$retvars['body'] = str_replace(
			array('$1','$2'),
			array(htmlspecialchars($name),make_pagelink('InterWikiName')),
			$_msg_invalidiwn
		);
		return $retvars;
	}
	$name = $match[2];
	$param = $match[3];
	
	$url = $opt = '';
	foreach (get_source($interwiki) as $line)
	{
		if (preg_match('/\[((?:(?:https?|ftp|news):\/\/|\.\.?\/)[!~*\'();\/?:\@&=+\$,%#\w.-]*)\s([^\]]+)\]\s?([^\s]*)/',$line,$match)
			and $match[2] == $name)
		{
			$url = $match[1];
			$opt = $match[3];
			
			if (!is_url($url))
			{
//				$url = substr($script,0,strrpos($script,'/')).substr($url,strspn($url,'.'));
				$q_name = preg_quote(basename($_SERVER['SCRIPT_NAME']));
				$url = preg_replace("/$q_name$/",'',$script).$match[1];
			}
			break;
		}
	}
	
	if ($url == '')
	{
		$retvars['msg'] = $_title_invalidiwn;
		$retvars['body'] = str_replace(
			array('$1','$2'),
			array(htmlspecialchars($name),make_pagelink('InterWikiName')),
			$_msg_invalidiwn
		);
		return $retvars;
	}
	
	$b_mb = function_exists('mb_convert_encoding');
	
	// 文字エンコーディング
	if ($opt == 'yw')
	{
		// YukiWiki系
		if (!preg_match("/$WikiName/",$param))
		{
			$param = $b_mb ? '[['.mb_convert_encoding($param,'SJIS',SOURCE_ENCODING).']]' : FALSE;
		}
	}
	else if ($opt == 'moin')
	{
		// moin系
		$param = str_replace('%','_',rawurlencode($param));
	}
	else if ($opt == '' or $opt == 'std')
	{
		// 内部文字エンコーディングのままURLエンコード
		$param = rawurlencode($param);
	}
	else if ($opt == 'asis' or $opt == 'raw')
	{
		// URLエンコードしない
		// $match[3] = $match[3];
	}
	else if ($opt != '')
	{
		// エイリアスの変換
		if ($opt == 'sjis')
		{
			$opt = 'SJIS';
		}
		else if ($opt == 'euc')
		{
			$opt = 'EUC-JP';
		}
		else if ($opt == 'utf8')
		{
			$opt = 'UTF-8';
		}

		// その他、指定された文字コードへエンコードしてURLエンコード
		$param = $b_mb ? rawurlencode(mb_convert_encoding($param,$opt,'auto')) : FALSE;
	}

	if ($param === FALSE)
	{
		$retvars['msg'] = 'Not support mb_convert_encoding.';
		$retvars['body'] = 'This server\'s PHP does not have "mb_jstring" module. Cannot convert encoding.';
		return $retvars;
	}

	if (strpos($url,'$1') !== FALSE)
	{
		$url = str_replace('$1',$param,$url);
	}
	else
	{
		$url .= $param;
	}
	
	header("Location: $url");
	die();
}
?>
