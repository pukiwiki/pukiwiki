<?
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: backup.php,v 1.2 2002/06/21 12:33:29 masui Exp $
/////////////////////////////////////////////////

// バックアップデータを作成する
function make_backup($filename,$body,$oldtime)
{
	global $splitter,$cycle,$maxage;
	$aryages = array();
	$arystrout = array();

	if(function_exists(gzfile))
		$filename = str_replace(".txt",".gz",$filename);

	$realfilename = BACKUP_DIR.$filename;

	if(time() - @filemtime($realfilename) > (60 * 60 * $cycle))
	{
		$aryages = read_backup($filename);
		if(count($aryages) >= $maxage)
		{
			array_shift($aryages);
		}
		
		foreach($aryages as $lines)
		{
			foreach($lines as $key => $line)
			{
				if($key && $key == "timestamp")
				{
					$arystrout[] = "$splitter " . rtrim($line);
				}
				else
				{
					$arystrout[] = rtrim($line);
				}
			}
		}

		$strout = join("\n",$arystrout);
		if(!preg_match("/\n$/",$strout) && trim($strout)) $strout .= "\n";

		$body = "$splitter " . $oldtime . "\n" . $body;
		if(!preg_match("/\n$/",$body)) $body .= "\n";

		$fp = backup_fopen($realfilename,"w");
		backup_fputs($fp,$strout);
		backup_fputs($fp,$body);
		backup_fclose($fp);
	}
	
	return true;
}

// 特定の世代のバックアップデータを取得
function get_backup($age,$filename)
{
	$aryages = read_backup($filename);
	
	foreach($aryages as $key => $lines)
	{
		if($key != $age) continue;
		foreach($lines as $key => $line)
		{
			if($key && $key == "timestamp") continue;
			$retvars[] = $line;
		}
	}

	return $retvars;
}

// バックアップ情報を返す
function get_backup_info($filename)
{
	global $splitter;
	$lines = array();
	$retvars = array();
	$lines = backup_file(BACKUP_DIR.$filename);

	if(!is_array($lines)) return array();

	$age = 0;
	foreach($lines as $line)
	{
		preg_match("/^$splitter\s(\d+)$/",trim($line),$match);
		if($match[1])
		{
			$age++;
			$retvars[$age] = $match[1];
		}
	}
	
	return $retvars;
}

// バックアップデータ全体を取得
function read_backup($filename)
{
	global $splitter;
	$lines = array();
	$lines = backup_file(BACKUP_DIR.$filename);

	if(!is_array($lines)) return array();

	$age = 0;
	foreach($lines as $line)
	{
		preg_match("/^$splitter\s(\d+)$/",trim($line),$match);
		if($match[1])
		{
			$age++;
			$retvars[$age]["timestamp"] = $match[1] . "\n";
		}
		else
		{
			$retvars[$age][] = $line;
		}
	}

	return $retvars;
}

// バックアップ一覧の取得
function get_backup_list($_page="")
{
	global $script,$date_format,$time_format,$weeklabels,$cantedit;
	global $_msg_backuplist,$_msg_diff,$_msg_nowdiff,$_msg_source;

	$ins_date = date($date_format,$val);
	$ins_time = date($time_format,$val);
	$ins_week = "(".$weeklabels[date("w",$val)].")";
	$ins = "$ins_date $ins_week $ins_time";

	if (($dir = @opendir(BACKUP_DIR)) && !$_page)
	{
		while($file = readdir($dir))
		{
			if(function_exists(gzopen))
				$file = str_replace(".txt",".gz",$file);

			if($file == ".." || $file == ".") continue;
			$page = decode(trim(preg_replace("/(\.txt)|(\.gz)$/"," ",$file)));
			if(in_array($page,$cantedit)) continue;
			$page_url = rawurlencode($page);
			$name = $page;
			$name = strip_bracket($name);
			if(is_page($page))
				$vals[$name]["link"] = "<li><a href=\"$script?$page_url\">$name</a></li>";
			else
				$vals[$name]["link"] = "<li>$name</li>";
			$vals[$name]["name"] = $page;
		}
		closedir($dir);
		$vals = list_sort($vals);
		$retvars[] = "<ul>";
	}
	else
	{
		$page_url = rawurlencode($_page);
		$name = strip_bracket($_page);
		$vals[$name]["link"] = "";
		$vals[$name]["name"] = $_page;
		$retvars[] = "<ul>";
		$retvars[] .= "<li><a href=\"$script?cmd=backup\">$_msg_backuplist</a></li>\n";
	}
	
	
	foreach($vals as $page => $line)
	{
		$arybackups = get_backup_info(encode($line["name"]).".txt");
		$page_url = rawurlencode($line["name"]);
		if(count($arybackups)) $line["link"] .= "\n<ul>\n";
		foreach($arybackups as $key => $val)
		{
			$ins_date = date($date_format,$val);
			$ins_time = date($time_format,$val);
			$ins_week = "(".$weeklabels[date("w",$val)].")";
			$backupdate = "($ins_date $ins_week $ins_time)";
			if(!$_page)
			{
 				$line["link"] .= "<li><a href=\"$script?cmd=backup&amp;page=$page_url&amp;age=$key\">$key $backupdate</a></li>\n";
			}
			else
			{
 				$line["link"] .= "<li><a href=\"$script?cmd=backup&amp;page=$page_url&amp;age=$key\">$key $backupdate</a> [ <a href=\"$script?cmd=backup_diff&amp;page=$page_url&amp;age=$key\">$_msg_diff</a> | <a href=\"$script?cmd=backup_nowdiff&amp;page=$page_url&amp;age=$key\">$_msg_nowdiff</a> | <a href=\"$script?cmd=backup_source&amp;page=$page_url&amp;age=$key\">$_msg_source</a> ]</li>\n";
			}
		}
		if(count($arybackups)) $line["link"] .= "</ul>";
		$retvars[] = $line["link"];
	}
	$retvars[] = "</ul>";
	
	return join("\n",$retvars);
}

// zlib関数が使用できれば、圧縮して使用するためのファイルシステム関数
function backup_fopen($filename,$mode)
{
	if(function_exists(gzopen))
		return gzopen(str_replace(".txt",".gz",$filename),$mode);
	else
		return fopen($filename,$mode);
}

function backup_fputs($zp,$str)
{
	if(function_exists(gzputs))
		return gzputs($zp,$str);
	else
		return fputs($zp,$str);
}

function backup_fclose($zp)
{
	if(function_exists(gzclose))
		return gzclose($zp);
	else
		return fclose($zp);
}

function backup_file($filename)
{
	if(function_exists(gzfile))
		return @gzfile(str_replace(".txt",".gz",$filename));
	else
		return @file($filename);
}

function backup_delete($filename)
{
	if(function_exists(gzopen))
		return @unlink(str_replace(".txt",".gz",$filename));
	else
		return @unlink($filename);
}
?>
