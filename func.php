<?
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: func.php,v 1.3 2002/06/28 10:39:57 masui Exp $
/////////////////////////////////////////////////

// 検索
function do_search($word,$type="AND",$non_format=0)
{
	global $script,$whatsnew,$vars;
	global $_msg_andresult,$_msg_orresult,$_msg_notfoundresult;
	
	$database = array();
	$retval = array();
	$cnt = 0;

	$files = get_existpages();
	foreach($files as $page) {
		$cnt++;
		if($page == $whatsnew) continue;
		if($page == $vars["page"] && $non_format) continue;
		$data[$page] = get_source($page);
	}
	
	$arywords = explode(" ",$word);
	$result_word = $word;
	
	foreach($data as $name => $lines)
	{
		$line = join("\n",$lines);
		
		$hit = 0;
		if(strpos($result_word," ") !== FALSE)
		{
			foreach($arywords as $word)
			{
				if($type=="AND")
				{
					if(strpos($line,$word) === FALSE)
					{
						$hit = 0;
						break;
					}
					else
					{
						$hit = 1;
					}
				}
				else if($type=="OR")
				{
					if(strpos($line,$word) !== FALSE)
						$hit = 1;
				}
			}
			if($hit==1 || strpos($name,$word)!==FALSE)
			{
				$page_url = rawurlencode($name);
				$word_url = rawurlencode($word);
				$name2 = strip_bracket($name);
				$str = get_pg_passage($name);
				$retval[$name2] = "<li><a href=\"$script?$page_url\">$name2</a>$str</li>";
			}
		}
		else
		{
			if(stristr($line,$word) || stristr($name,$word))
			{
				$page_url = rawurlencode($name);
				$word_url = rawurlencode($word);
				$name2 = strip_bracket($name);
				$link_tag = "<a href=\"$script?$page_url\">$name2</a>";
				$link_tag .= get_pg_passage($name,false);
				if($non_format)
				{
					$tm = @filemtime(get_filename(encode($name)));
					$retval[$tm] = $link_tag;
				}
				else
				{
					$retval[$name2] = "<li>$link_tag</li>";
				}
			}
		}
	}

	if($non_format)
		return $retval;

	$retval = list_sort($retval);

	if(count($retval) && !$non_format)
	{
		$retvals = "<ul>\n" . join("\n",$retval) . "</ul>\n<br>\n";
		
		if($type=="AND")
			$retvals.= str_replace('$1',htmlspecialchars($result_word),str_replace('$2',count($retval),str_replace('$3',$cnt,$_msg_andresult)));
		else
			$retvals.= str_replace('$1',htmlspecialchars($result_word),str_replace('$2',count($retval),str_replace('$3',$cnt,$_msg_orresult)));

	}
	else
		$retvals .= str_replace('$1',htmlspecialchars($result_word),$_msg_notfoundresult);
	return $retvals;
}

// プログラムへの引数のチェック
function arg_check($str)
{
	global $arg,$vars;

	return preg_match("/^".$str."/",$vars["cmd"]);
}

// ページリストのソート
function list_sort($values)
{
	if(!is_array($values)) return array();
	
	// ksortのみだと、[[日本語]]、[[英文字]]、英文字のみ、に順に並べ替えられる
	ksort($values);

	$vals1 = array();
	$vals2 = array();
	$vals3 = array();

	// 英文字のみ、[[英文字]]、[[日本語]]、の順に並べ替える
	foreach($values as $key => $val)
	{
		if(preg_match("/\[\[[^\w]+\]\]/",$key))
			$vals3[$key] = $val;
		else if(preg_match("/\[\[[\W]+\]\]/",$key))
			$vals2[$key] = $val;
		else
			$vals1[$key] = $val;
	}
	return array_merge($vals1,$vals2,$vals3);
}

// ページ名のエンコード
function encode($key)
{
	$enkey = '';
	$arych = preg_split("//", $key, -1, PREG_SPLIT_NO_EMPTY);
	
	foreach($arych as $ch)
	{
		$enkey .= sprintf("%02X", ord($ch));
	}

	return $enkey;
}

// ファイル名のデコード
function decode($key)
{
	$dekey = '';
	
	for($i=0;$i<strlen($key);$i+=2)
	{
		$ch = substr($key,$i,2);
		$dekey .= chr(intval("0x".$ch,16));
	}
	return $dekey;
}

// InterWikiName List の解釈(返値:２次元配列)
function open_interwikiname_list()
{
	global $interwiki;
	
	$retval = array();
	$aryinterwikiname = get_source($interwiki);

	$cnt = 0;
	foreach($aryinterwikiname as $line)
	{
		if(preg_match("/\[((https?|ftp|news)(\:\/\/[[:alnum:]\+\$\;\?\.%,!#~\*\/\:@&=_\-]+))\s([^\]]+)\]\s?([^\s]*)/",$line,$match))
		{
			$retval[$match[4]]["url"] = $match[1];
			$retval[$match[4]]["opt"] = $match[5];
		}
	}

	return $retval;
}

// [[ ]] を取り除く
function strip_bracket($str)
{
	global $strip_link_wall;
	
	if($strip_link_wall)
	{
		preg_match("/^\[\[(.*)\]\]$/",$str,$match);
		if($match[1])
			$str = $match[1];
	}
	return $str;
}

// テキスト整形ルールを表示する
function catrule()
{
	global $rule_body;
	return $rule_body;
}

// エラーメッセージを表示する
function die_message($msg)
{
	$title = $page = "Runtime error";

	$body = "<h3>Runtime error</h3>\n";
	$body .= "<b>Error message : $msg</b>\n";

	catbody($title,$page,$body);

	die();
}

// 現在時刻をマイクロ秒で取得
function getmicrotime()
{
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$sec + (float)$usec);
}

// 差分の作成
function do_diff($strlines1,$strlines2)
{
	$lines1 = split("\n",$strlines1);
	$lines2 = split("\n",$strlines2);
	
	$same_lines = $diff_lines = $del_lines = $add_lines = $retdiff = array();
	
	if(count($lines1) > count($lines2)) { $max_line = count($lines1)+2; }
	else                                { $max_line = count($lines2)+2; }

	//$same_lines = array_intersect($lines1,$lines2);

	$diff_lines2 = array_diff($lines2,$lines1);
	$diff_lines = array_merge($diff_lines2,array_diff($lines1,$lines2));

	foreach($diff_lines as $line)
	{
		$index = array_search($line,$lines1);
		if($index > -1)
		{
			$del_lines[$index] = $line;
		}
		
		//$index = array_search($line,$lines2);
		//if($index > -1)
		//{
		//	$add_lines[$index] = $line;
		//}
	}

	$cnt=0;
	foreach($lines2 as $line)
	{
		$line = rtrim($line);
		
		while($del_lines[$cnt])
		{
			$retdiff[] = "- ".$del_lines[$cnt];
			$del_lines[$cnt] = "";
			$cnt++;
		}
		
		if(in_array($line,$diff_lines))
		{
			$retdiff[] = "+ $line";
		}
		else
		{
			$retdiff[] = "  $line";
		}		

		$cnt++;
	}
	
	foreach($del_lines as $line)
	{
		if(trim($line))
			$retdiff[] = "- $line";
	}

	return join("\n",$retdiff);
}


// 差分の作成
function do_update_diff($oldstr,$newstr)
{
	$oldlines = split("\n",$oldstr);
	$newlines = split("\n",$newstr);
	
	$retdiff = $props = array();
	$auto = true;
	
	foreach($newlines as $newline) {
	  $flg = false;
	  $cnt = 0;
	  foreach($oldlines as $oldline) {
	    if($oldline == $newline) {
	      if($cnt>0) {
		for($i=0; $i<$cnt; ++$i) {
		  array_push($retdiff,array_shift($oldlines));
		  array_push($props,'! ');
		  $auto = false;
		}
	      }
	      array_push($retdiff,array_shift($oldlines));
	      array_push($props,'');
	      $flg = true;
	      break;
	    }
	    $cnt++;
	  }
	  if(!$flg) {
	    array_push($retdiff,$newline);
	    array_push($props,'+ ');
	  }
	}
	foreach($oldlines as $oldline) {
	  array_push($retdiff,$oldline);
	  array_push($props,'! ');
	  $auto = false;
	}
	if($auto) {
	  return array(join("\n",$retdiff),$auto);
	}

	$ret = '';
	foreach($retdiff as $line) {
	  $ret .= array_shift($props) . $line . "\n";
	}
	return array($ret,$auto);
}
?>
