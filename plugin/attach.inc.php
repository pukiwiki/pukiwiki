<?
// プラグイン attach

// set PHP value to enable file upload
ini_set("file_uploads","1");

// upload dir(must set end of /)
define("UPLOAD_DIR","./attach/");

// max file size for upload on PHP(PHP default 2MB)
ini_set("upload_max_filesize","2M");

// max file size for upload on script of PukiWiki(default 1MB)
define("MAX_FILESIZE",1000000);

// file icon image
define("FILE_ICON","<img src=\"./image/file.gif\" width=\"20\" height=\"20\" border=\"0\">");

function plugin_attach_convert()
{
	global $script,$vars,$max_size;
	global $_msg_attach_filelist,$_msg_maxsize,$_msg_delete,$_btn_upload,$_btn_delete,$_msg_attachfile;
	
	$ret = "";
	$decoded_pgname = encode($vars["page"]);
	
	$icon = FILE_ICON;
	
	if ($dir = @opendir(UPLOAD_DIR))
	{
		while($file = readdir($dir))
		{
			if($file == ".." || $file == ".") continue;
			if(!preg_match("/^${decoded_pgname}_(.*)$/",$file,$match)) continue;
			
			$lastmod = date("Y/m/d H:i:s",filemtime(UPLOAD_DIR.$file));
			
			settype($dfile_size,"double");
			$dfile_size = round(filesize(UPLOAD_DIR.$file)/1000,1);
			if($dfile_size == 0) $dfile_size = 0.1;
			$file_size = sprintf("%01.1f",$dfile_size)."KB";
			
			$filename = decode($match[1]);
			$filename_url = rawurlencode($filename);
			$refername_url = rawurlencode($vars[page]);
			
			$del = "[<a href=\"$script?plugin=attach&delfile=${filename_url}&refer=${refername_url}\" title=\"".str_replace('$1',$filename,$_msg_delete)."\">$_btn_delete</a>]";
			$open = "<a href=\"$script?plugin=attach&openfile=${filename_url}&refer=${refername_url}\" title=\"$lastmod $file_size\">$icon$filename</a>\n";
			
			$into = "$open <small>$del</small>";
			
			$attach_files[$lastmod] = $into;
		}
		closedir($dir);
		@krsort($attach_files);
	}
	
	$max_size = number_format(MAX_FILESIZE/1000);
	$max_size.= "KB";
	
	$args = func_get_args();
	
	if(is_array($attach_files))
	{
		if($args[0]===FALSE) $ret.= "$_msg_attachfile: ";
		$ret.= join("\n&nbsp;&nbsp;",$attach_files)."\n";
	}
	
	if($args[0]!==FALSE)
	{
		$ret.= "<p>\n";
		
		$ret.= "<form enctype=\"multipart/form-data\" action=\"$script\" method=\"post\">\n";
		$ret.= "<input type=\"hidden\" name=\"plugin\" value=\"attach\">\n";
		$ret.= "<input type=\"hidden\" name=\"refer\" value=\"$vars[page]\">\n";
		$ret.= "<input type=\"hidden\" name=\"max_file_size\" value=\"".MAX_FILESIZE."\">\n";
		$ret.= "<small>[<a href=\"$script?plugin=attach&pcmd=list\">$_msg_attach_filelist</a>]</small><br>\n";
		$ret.= "<small>".str_replace('$1',$max_size,$_msg_maxsize)."</small><br>\n";
		$ret.= "$_msg_attachfile: <input type=\"file\" name=\"attach_file\">\n";
		$ret.= "<input type=\"submit\" value=\"$_btn_upload\"><br>\n";
		$ret.= "</form>\n";
	}
	
	return $ret;
}
function plugin_attach_action()
{
	global $vars,$script,$max_size,$HTTP_POST_FILES;
	global $_title_uploaded,$_title_file_deleted,$_title_notfound,$_msg_noparm,$_msg_already_exists,$_msg_attach_filelist,$_msg_delete,$_msg_exceed,$_btn_delete;
	global $_msg_maxsize,$_btn_upload,$_msg_attachfile,$_title_upload;
	
	$postfiles = $HTTP_POST_FILES;
	$icon = FILE_ICON;

	$vars["openfile"] = rawurldecode($vars["openfile"]);
	$vars["delfile"] = rawurldecode($vars["delfile"]);
	$vars["refer"] = rawurldecode($vars["refer"]);

	if(is_uploaded_file($postfiles["attach_file"]["tmp_name"]))
	{
		if($postfiles["attach_file"]["size"] > MAX_FILESIZE) return array("msg" => $_msg_exceed);
		if(is_freeze($vars["refer"]) || !is_editable($vars["refer"])) return array("msg" => $_msg_noparm);
		
		$filename = encode($vars["refer"])."_".encode($postfiles["attach_file"]["name"]);
		
		if(file_exists(UPLOAD_DIR.$filename)) return array("msg" => $_msg_already_exists);
		
		move_uploaded_file($postfiles["attach_file"]["tmp_name"],UPLOAD_DIR.$filename);
		
		if(file_exists(DATA_DIR.encode($vars["refer"]).".txt"))
			@touch(DATA_DIR.encode($vars["refer"]).".txt");
		
		return array("msg" => $_title_uploaded);
	}
	else if($vars["delfile"])
	{
		$filename = encode($vars["refer"])."_".encode($vars["delfile"]);
		if(is_freeze($vars["refer"]) || !is_editable($vars["refer"])) return array("msg" => $_msg_noparm);
		
		if(!file_exists(UPLOAD_DIR.$filename))
			return array("msg" => $_title_notfound);
		
		@unlink(UPLOAD_DIR.$filename);

		if(file_exists(DATA_DIR.encode($vars["refer"]).".txt"))
			@touch(DATA_DIR.encode($vars["refer"]).".txt");
		
		return array("msg" => $_title_file_deleted);
	}
	else if($vars["openfile"])
	{
		$filename = encode($vars["refer"])."_".encode($vars["openfile"]);
		
		if(!file_exists(UPLOAD_DIR.$filename))
			return array("msg" => $_title_notfound);
		
		download_file(UPLOAD_DIR.$filename,$vars["openfile"]);

		die();
	}
	else if($vars["pcmd"] == "list")
	{
		if ($dir = @opendir(UPLOAD_DIR))
		{
			$pgname_keep = "";
			$retbody = "";
			$aryret = array();
			while($file = readdir($dir))
			{
				if($file == ".." || $file == ".") continue;
				
				settype($dfile_size,"double");
				$dfile_size = round(filesize(UPLOAD_DIR.$file)/1000,1);
				if($dfile_size == 0) $dfile_size = 0.1;
				$file_size = sprintf("%01.1f",$dfile_size)."KB";
				
				preg_match("/^([^_]+)_([^_]+)$/",$file,$match);
				
				$pagename = decode($match[1]);
				$pagename_url = rawurlencode($pagename);
				$filename = decode($match[2]);
				$filename_url = rawurlencode($filename);
				$passage = get_pg_passage($pagename);
				
				$pagename = strip_bracket($pagename);
				$page = "<a href=\"$script?${pagename_url}\">$pagename</a>$passage\n";
				
				$strtmp = "";
				if($pgname_keep != $pagename)
				{
					if($pgname_keep!="")
						$strtmp .= "</ul>\n";
					
					$strtmp .= "<li>$page</li>\n";
					$strtmp .= "<ul>\n";
					$aryret[$pagename] = $strtmp;
					$pgname_keep = $pagename;
				}
				
				$lastmod = date("Y/m/d H:i:s",filemtime(UPLOAD_DIR.$file));
				
				$del = "[<a href=\"$script?plugin=attach&delfile=${filename_url}&refer=${pagename_url}\" title=\"".str_replace('$1',$filename,$_msg_delete)."\">$_btn_delete</a>]";
				
				$open = "<a href=\"$script?plugin=attach&openfile=${filename_url}&refer=${pagename_url}\" title=\"$lastmod $file_size\">$filename</a>";

				
				$into = "<li>$open <small>$del</small></li>\n";
				
				$aryret[$pagename.$filename] = $into;
			}
			closedir($dir);
			ksort($aryret);
			$retbody = join("",$aryret);
		}
		
		$retvars["msg"] = $_msg_attach_filelist;
		$retvars["body"] = "<ul>\n".$retbody."</ul>\n";
		if($retbody) $retvars["body"] .= "</ul>\n";
		
		return $retvars;
	}
	else if($vars["pcmd"] == "upload" && $vars["page"])
	{
		$vars["refer"] = $vars["page"];
		
		$max_size = number_format(MAX_FILESIZE/1000);
		$max_size.= "KB";
		
		$ret.= "<blockquote>\n";
		$ret.= "<form enctype=\"multipart/form-data\" action=\"$script\" method=\"post\">\n";
		$ret.= "<input type=\"hidden\" name=\"plugin\" value=\"attach\">\n";
		$ret.= "<input type=\"hidden\" name=\"refer\" value=\"$vars[page]\">\n";
		$ret.= "<input type=\"hidden\" name=\"max_file_size\" value=\"".MAX_FILESIZE."\">\n";
		$ret.= "<small>[<a href=\"$script?plugin=attach&pcmd=list\">$_msg_attach_filelist</a>]</small><br>\n";
		$ret.= "<small>".str_replace('$1',$max_size,$_msg_maxsize)."</small><br>\n";
		$ret.= "$_msg_attachfile: <input type=\"file\" name=\"attach_file\">\n";
		$ret.= "<input type=\"submit\" value=\"$_btn_upload\"><br>\n";
		$ret.= "</form>\n";
		$ret.= "</blockquote>\n";
		
		$retvars["msg"] = $_title_upload;
		$retvars["body"] = $ret;
		
		return $retvars;
	}
}

function attach_filelist()
{
	return plugin_attach_convert(FALSE);
}

function download_file($path_file,$filename)
{
	$content_length = filesize($path_file);

	// for japanese
	if(function_exists("mb_convert_encoding"))
		$filename = mb_convert_encoding($filename,"SJIS","auto");

	header("Content-Disposition: inline; filename=\"$filename\"");
	header("Content-Length: ".$content_length);
	header("Content-Type: application/octet-stream");

	@readfile($path_file);
}

?>
