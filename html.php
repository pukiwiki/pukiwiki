<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: html.php,v 1.45.2.7 2003/09/03 01:02:03 arino Exp $
/////////////////////////////////////////////////

// 本文をページ名から出力
function catbodyall($page,$title="",$pg="")
{
	if($title === "") $title = strip_bracket($page);
	if($pg === "") $pg = make_search($page);

	$body = join("",get_source($page));
	$body = convert_html($body);

	header_lastmod($vars["page"]);
	catbody($title,$pg,$body);
	die();
}

// 本文を出力
function catbody($title,$page,$body)
{
	global $script,$vars,$arg,$do_backup,$modifier,$modifierlink,$defaultpage,$whatsnew,$hr;
	global $date_format,$weeklabels,$time_format,$related_link;
	global $HTTP_SERVER_VARS,$cantedit;
	global $longtaketime;
	global $foot_explain, $note_hr;

	if($vars["page"] && !arg_check("backup") && $vars["page"] != $whatsnew)
	{
		$is_page = 1;
	}

 	$link_add = "$script?cmd=add&amp;page=".rawurlencode($vars["page"]);
 	$link_edit = "$script?cmd=edit&amp;page=".rawurlencode($vars["page"]);
 	$link_diff = "$script?cmd=diff&amp;page=".rawurlencode($vars["page"]);
	$link_top = "$script?".rawurlencode($defaultpage);
	$link_list = "$script?cmd=list";
	$link_filelist = "$script?cmd=filelist";
	$link_search = "$script?cmd=search";
	$link_whatsnew = "$script?".rawurlencode($whatsnew);
 	$link_backup = "$script?cmd=backup&amp;page=".rawurlencode($vars["page"]);
	$link_help = "$script?cmd=help";

	if(is_page($vars["page"]) && $is_page)
	{
		$fmt = @filemtime(get_filename(encode($vars["page"])));
	}

	if(is_page($vars["page"]) && $related_link && $is_page && !arg_check("edit") && !arg_check("freeze") && !arg_check("unfreeze"))
	{
		$related = make_related($vars["page"],FALSE);
	}

	if(is_page($vars["page"]) && !in_array($vars["page"],$cantedit) && !arg_check("backup") && !arg_check("edit") && !$vars["preview"])
	{
		$is_read = TRUE;
	}

	$longtaketime = getmicrotime() - MUTIME;
	$taketime = sprintf("%01.03f",$longtaketime);

	if ($foot_explain)
		$body .= "\n$note_hr\n".join("\n",inline2($foot_explain));

	if(!file_exists(SKIN_FILE)||!is_readable(SKIN_FILE))
	  die_message(SKIN_FILE."(skin file) is not found.");
	require(SKIN_FILE);
}

// テキスト本体をHTMLに変換する
function convert_html($string)
{
	global $hr,$script,$page,$vars,$top;
	global $note_id,$foot_explain,$digest,$note_hr;
	global $user_rules,$str_rules,$line_rules,$strip_link_wall;
	global $InterWikiName, $BracketName;

	global $content_id;
	$content_id_local = ++$content_id;
	$content_count = 0;

	$string = rtrim($string);
	$string = preg_replace("/((\x0D\x0A)|(\x0D)|(\x0A))/","\n",$string);

	$start_mtime = getmicrotime();

	$digest = md5(@join("",get_source($vars["page"])));

	$result = array();
	$saved = array();
	$arycontents = array();

	$string = preg_replace("/^#freeze\s*$/m","",$string);

	$lines = split("\n", $string);
	// 各行の行頭書式を格納
	$headform = array();
	// 現在の行数を入れておこう
	$_cnt = 0;
	// ブロックの判定フラグ
	$_p = FALSE;
	$_bq = FALSE;

	$table = 0;

	if(preg_match("/#contents/",$string))
		$top_link = "<a href=\"#contents_$content_id_local\">$top</a>";

	foreach ($lines as $line)
	{
		if(!preg_match("/^\/\/(.*)/",$line,$comment_out) && $table != 0)
		{
			if(!preg_match("/^\|(.+)\|$/",$line,$out) or
				$table != count(explode("|",$out[1])))
			{
				$table = 0;
				array_push($result, "</table></div>");
			}
		}

		$comment_out = $comment_out[1];

		// 行頭書式かどうかの判定
		$line_head = substr($line,0,1);
		if(	$line_head == ' ' || 
			$line_head == ':' || 
			$line_head == '>' || 
			$line_head == '-' || 
			$line_head == '+' || 
			$line_head == '|' || 
			$line_head == '*' || 
			$line_head == '#' || 
			$comment_out != ''
		) {
			if($headform[$_cnt-1] == '' && $_p){
				array_push($result, "</p>");
				$_p = FALSE;
			}
			if($line_head != '>' && $_bq){
				array_push($result, "</p>");
				$_bq = FALSE;
			}

			if(preg_match("/^\#([^\(]+)(.*)$/",$line,$out)){
				if(exist_plugin_convert($out[1])) {
					$result = array_merge($result,$saved); $saved = array();
					
					if($out[2]) {
						$_plugin = preg_replace("/^\#([^\(]+)\((.*)\)$/ex","do_plugin_convert('$1','$2')",$line);
					} else {
						$_plugin = preg_replace("/^\#([^\(]+)$/ex","do_plugin_convert('$1','$2')",$line);
					}
					// 先頭に空白を入れることによりとりあえずpreの扱いと同様にinline2の働きを抑える、う〜ん、無茶。
					array_push($result,"\t$_plugin");
				} else {
					array_push($result, htmlspecialchars($line));
				}
			}
			else if(preg_match("/^(\*{1,3})(.*)/",$line,$out))
			{
				$result = array_merge($result,$saved); $saved = array();
				$headform[$_cnt] = $out[1];
				$str = inline($out[2]);
				
				$level = strlen($out[1]) + 1;

				array_push($result, "<h$level><a name=\"content_{$content_id_local}_$content_count\"></a>$str $top_link</h$level>");
				$contents_str = strip_htmltag(join('',inline2(array(inline(ltrim($out[2]),TRUE)))));
				$arycontents[] = str_repeat("-",$level-1)."<a href=\"#content_{$content_id_local}_$content_count\">$contents_str</a>\n";
				$content_count++;
			}
			else if(preg_match("/^(-{1,4})(.*)/",$line,$out))
			{
				$headform[$_cnt] = $out[1];
				if(strlen($out[1]) == 4)
				{
					$result = array_merge($result,$saved); $saved = array();
					array_push($result, $hr);
				}
				else
				{
					list_push($result,$saved,'ul', strlen($out[1]));
					array_push($result, '<li>'.inline($out[2]));
				}
			}
			else if(preg_match("/^(\+{1,3})(.*)/",$line,$out))
			{
				$headform[$_cnt] = $out[1];
				list_push($result,$saved,'ol', strlen($out[1]));
				array_push($result, '<li>'.inline($out[2]));
			}
			else if (preg_match("/^:([^:]+):(.*)/",$line,$out))
			{
				$headform[$_cnt] = ':'.$out[1].':';
				back_push($result,$saved,'dl', 1);
				array_push($result, '<dt>' . inline($out[1]) . '</dt>', '<dd>' . inline($out[2]) . '</dd>');
			}
			else if(preg_match("/^(>{1,3})(.*)/",$line,$out))
			{
				$headform[$_cnt] = $out[1];
				back_push($result,$saved,'blockquote', strlen($out[1]));
				// ここのあたりで自前でback_pushかけてる感じ。無茶苦茶…
				if($headform[$_cnt-1] != $headform[$_cnt] ) {
					if(!$_bq) {
						array_push($result, "<p class=\"quotation\">");
						$_bq = TRUE;
					}
					else if(substr($headform[$_cnt-1],0,1) == '>'){
						$_level_diff = abs( strlen($out[1]) - strlen($headform[$_cnt-1]) );
						if( $_level_diff == 1 ){
							$i = array_pop($result);
							array_push($result, "</p>");
							array_push($result,$i);
							array_push($result, "<p class=\"quotation\">");
							$_bq = TRUE;
						} else {
							$i = array();
							$i[] = array_pop($result);
							$i[] = array_pop($result);
							array_push($result, "</p>");
							$result = array_merge($result,$i);
							array_push($result, "<p class=\"quotation\">");
							$_bq = TRUE;
						}
					}
				}
				array_push($result, ltrim(inline($out[2])));
			}
			else if(preg_match("/^(\s+.*)/",$line,$out))
			{
				$headform[$_cnt] = ' ';
				back_push($result,$saved,'pre', 1);
				array_push($result, htmlspecialchars($out[1],ENT_NOQUOTES));
			}
			else if(preg_match("/^\|(.+)\|$/",$line,$out))
			{
				$headform[$_cnt] = '|';
				$arytable = explode("|",$out[1]);

				if(!$table)
				{
					$result = array_merge($result,$saved); $saved = array();
					array_push($result,"<div class=\"ie5\"><table class=\"style_table\" cellspacing=\"1\" border=\"0\">");
					$table = count($arytable);
				}

				array_push($result,"<tr>");
				foreach($arytable as $td)
				{
					if (preg_match("/^(LEFT|CENTER|RIGHT):(.*)$/",$td,$tmp)) {
						$style = ' align="'.strtolower($tmp[1]).'"';
						$td = $tmp[2];
					} else {
						$style = '';
					}
					array_push($result,"<td class=\"style_td\"$style>");
					array_push($result,ltrim(inline($td)));
					array_push($result,"</td>");
				}
				array_push($result,"</tr>");

			}
			else if(strlen($comment_out) != 0)
			{
				$headform[$_cnt] = '//';
#				array_push($result," <!-- ".htmlspecialchars($comment_out)." -->");
			}

		} else {

			$headform[$_cnt] = '';
			if($headform[$_cnt-1] != $headform[$_cnt]){
				if(array_values($saved)){
					if( $_bq ){
						array_unshift($saved, "</p>");
						$_bq = FALSE;
					}
					$i = array_pop($saved);
					array_push($saved,$i);
					$result = array_merge($result,$saved); $saved = array();
				}
				if( substr($line,0,1) == '' && !$_p){
					array_push($result, "<p>");
					$_p = TRUE;
				}
				else if( substr($line,0,1) != '' && $_p){
					array_push($result, "</p>");
					$_p = FALSE;
				}
			}
			
			if (preg_match("/^(LEFT|CENTER|RIGHT):(.*)$/",$line,$tmp)) {
				if ($_p)
					array_push($result,"</p>");
				array_push($result,'<div class="p_'.strtolower($tmp[1]).'">');
				array_push($result,inline($tmp[2]));
				array_push($result,"</div>");
				$line = '';
				$_p = FALSE;
			}
			if( substr($line,0,1) == '' && $_p){
				$_tmp = array_pop($result);
				if($_tmp == "<p>") {
					$_tmp = '<p class="empty">';
				}
				array_push($result, $_tmp, "</p>");
				$_p = FALSE;
			}
			else if( substr($line,0,1) != '' && !$_p) {
				array_push($result, "<p>");
					$_p = TRUE;
			}
			if( substr($line,0,1) != '' ){
				array_push($result, inline($line));
			}

		}

		$_cnt++;
	}

	if($_p) array_push($result, "</p>");
	if($_bq) {
		array_push($result, "</p>");
	}
	if($table) array_push($result, "</table></div>");
	
	$result_last = $result = array_merge($result,$saved); $saved = array();

	if($content_count != 0)
	{
		$result = array();
		$saved = array();

		foreach($arycontents as $line)
		{
			if(preg_match("/^(-{1,3})(.*)/",$line,$out))
			{
				list_push($result,$saved,'ul', strlen($out[1]));
				array_push($result, '<li>'.$out[2]);
			}
		}
		$result = array_merge($result,$saved); $saved = array();
		
		$contents = "<a name=\"contents_$content_id_local\"></a>\n";
		$contents .= join("\n",$result);
		if($strip_link_wall)
		{
			$contents = preg_replace("/\[\[([^\]:]+):(.+)\]\]/","$1",$contents);
			$contents = preg_replace("/\[\[([^\]]+)\]\]/","$1",$contents);
		}
	}

	$result_last = inline2($result_last);
	
	$result_last = preg_replace("/^#contents/",$contents,$result_last);

	$str = join("\n", $result_last);

#	$str = preg_replace("/&((amp)|(quot)|(nbsp)|(lt)|(gt));/","&$1;",$str);

	return $str;
}

// $tagのタグを$levelレベルまで詰める。
function back_push(&$result,&$saved,$tag, $level)
{
	while (count($saved) > $level) {
		array_push($result, array_shift($saved));
	}
	if ($saved[0] != "</$tag>") {
		$result = array_merge($result,$saved); $saved = array();
	}
	while (count($saved) < $level) {
		array_unshift($saved, "</$tag>");
		array_push($result, "<$tag>");
	}
}

function list_push(&$result,&$saved,$tag,$level) {
	global $_list_left_margin, $_list_margin, $_list_pad_str;
	$cont = true;
	$open = "<$tag%s>";
	$close = "</li></$tag>";
	
	while (count($saved) > $level or
		(count($saved) > 0 and $saved[0] != $close)) {
		array_push($result, array_shift($saved));
	}
	
	$margin = $level - count($saved);
	
	while (count($saved) < ($level - 1)) {
		array_unshift($saved, ''); //count($saved)を増やすためのdummy
	}
	
	if (count($saved) < $level) {
		$cont = false;
		array_unshift($saved, $close);
		
		$left = $margin * $_list_margin;
		if ($level == $margin) $left += $_list_left_margin;
		$str = sprintf($_list_pad_str, $level, $left, $left);
		array_push($result, sprintf($open, $str));
	}
	
	if ($cont)
		array_push($result, '</li>');
}

// インライン要素のパース (注釈)
function inline($line,$remove=FALSE)
{
	$line = htmlspecialchars($line);
	
	$replace = $remove ? '' : 'make_note(\'$1\')';
	$line = preg_replace("/\(\(((?:(?!\)\)).)*)\)\)/ex",$replace,$line);

	return $line;
}

// インライン要素のパース (リンク、関連一覧、見出し一覧)
function inline2($str)
{
	global $WikiName,$BracketName,$InterWikiName,$vars,$related,$related_link,$script;
	$cnts_plain = array();
	$arykeep = array();

	for($cnt=0;$cnt<count($str);$cnt++)
	{
		if(preg_match("/^(\s)/",$str[$cnt]))
		{
			$arykeep[$cnt] = $str[$cnt];
			$str[$cnt] = "";
			$cnts_plain[] = $cnt;
		}
	}

	$str = make_link($str);
	$str = preg_replace("/#related/e",'make_related($vars["page"],TRUE)',$str);
	$str = make_user_rules($str);

	$tmp = $str;
	$str = preg_replace("/^#norelated$/","",$str);
	if($tmp != $str)
		$related_link = 0;

	foreach($cnts_plain as $cnt)
		$str[$cnt] = $arykeep[$cnt];

	return $str;
}

// 一覧の取得
function get_list($withfilename)
{
	global $script,$list_index,$top,$non_list,$whatsnew;
	global $_msg_symbol,$_msg_other;
	
	$retval = array();
	$files = get_existpages();
	foreach($files as $page) {
		if(preg_match("/$non_list/",$page) && !$withfilename) continue;
		if($page == $whatsnew) continue;
		$page_url = rawurlencode($page);
		$page2 = strip_bracket($page);
		$pg_passage = get_pg_passage($page);
		$file = encode($page).".txt";
		$retval[$page2] .= "<li><a href=\"$script?$page_url\">".htmlspecialchars($page2,ENT_QUOTES)."</a>$pg_passage";
		if($withfilename)
		{
			$retval[$page2] .= "<ul><li>$file</li></ul>\n";
		}
		$retval[$page2] .= "</li>\n";
	}
	
	$retval = list_sort($retval);
	
	if($list_index)
	{
		$head_str = "";
		$etc_sw = 0;
		$symbol_sw = 0;
		$top_link = "";
		$link_counter = 0;
		foreach($retval as $page => $link)
		{
			$head = substr($page,0,1);
			if($head_str != $head && !$etc_sw)
			{
				$retval2[$page] = "";
				
				if(preg_match("/([A-Z])|([a-z])/",$head,$match))
				{
					if($match[1])
						$head_nm = "High_$head";
					else
						$head_nm = "Low_$head";
					
					if($head_str != '') $retval2[$page] = "</ul></li>\n";
					$retval2[$page] .= "<li><a href=\"#top_$head_nm\" name=\"$head_nm\"><strong>$head</strong></a>\n<ul>\n";
					$head_str = $head;
					if($link_counter) $top_link .= "|";
					$link_counter = $link_counter + 1;
					$top_link .= "<a href=\"#$head_nm\" name=\"top_$head_nm\"><strong>&nbsp;".$head."&nbsp;</strong></a>";
					if($link_counter==16) {
					        $top_link .= "<br />";
						$link_counter = 0;
					}
				}
				else if(preg_match("/[ -~]/",$head))
				{
					if(!$symbol_sw)
					{
						if($head_str != '') $retval2[$page] = "</ul></li>\n";
						$retval2[$page] .= "<li><a href=\"#top_symbol\" name=\"symbol\"><strong>$_msg_symbol</strong></a>\n<ul>\n";
						$head_str = $head;
						if($link_counter) $top_link .= "|";
						$link_counter = $link_counter + 1;
						$top_link .= "<a href=\"#symbol\" name=\"top_symbol\"><strong>$_msg_symbol</strong></a>";
						$symbol_sw = 1;
					}
				}
				else
				{
					if($head_str != '') $retval2[$page] = "</ul></li>\n";
					$retval2[$page] .= "<li><a href=\"#top_etc\" name=\"etc\"><strong>$_msg_other</strong></a>\n<ul>\n";
					$etc_sw = 1;
					if($link_counter) $top_link .= "|";
					$link_counter = $link_counter + 1;
					$top_link .= "<a href=\"#etc\" name=\"top_etc\"><strong>$_msg_other</strong></a>";
				}
			}
			$retval2[$page] .= $link;
		}
		$retval2[] = "</ul></li>\n";
		
		$top_link = "<div style=\"text-align:center\"><a name=\"top\"></a>$top_link</div><br />\n<ul>";
		
		array_unshift($retval2,$top_link);
	}
	else
	{
		$retval2 = $retval;
		
		$top_link = "<ul>";
		
		array_unshift($retval2,$top_link);
	}
	
	return join("",$retval2)."</ul>";
}

// 編集フォームの表示
function edit_form($postdata,$page,$add=0)
{
	global $script,$rows,$cols,$hr,$vars,$function_freeze;
	global $_btn_addtop,$_btn_preview,$_btn_update,$_btn_freeze,$_msg_help,$_btn_notchangetimestamp;
	global $whatsnew,$_btn_template,$_btn_load,$non_list,$load_template_func;

	$digest = md5(@join("",get_source($page)));

	if($add)
	{
		$addtag = '<input type="hidden" name="add" value="true" />';
		$add_top = '<input type="checkbox" name="add_top" value="true" /><span class="small">'.$_btn_addtop.'</span>';
	}

	if($vars["help"] == "true")
		$help = $hr.catrule();
	else
 		$help = "<br />\n<ul><li><a href=\"$script?cmd=edit&amp;help=true&amp;page=".rawurlencode($page)."\">$_msg_help</a></ul></li>\n";

	if($function_freeze)
		$str_freeze = '<input type="submit" name="freeze" value="'.$_btn_freeze.'" accesskey="f" />';

	if($load_template_func)
	{
		$vals = array();

		$files = get_existpages();
		foreach($files as $pg_org) {
			if($pg_org == $whatsnew) continue;
			if(preg_match("/$non_list/",$pg_org)) continue;
			$name = strip_bracket($pg_org);
			$s_name = htmlspecialchars($name);
			$s_org = htmlspecialchars($pg_org);
			$vals[$name] = "    <option value=\"$s_org\">$s_name</option>";
		}
		@ksort($vals);
		
		$template = "   <select name=\"template_page\">\n"
			   ."    <option value=\"\">-- $_btn_template --</option>\n"
			   .join("\n",$vals)
			   ."   </select>\n"
			   ."   <input type=\"submit\" name=\"template\" value=\"$_btn_load\" accesskey=\"r\" /><br />\n";

		if($vars["refer"]) $refer = $vars["refer"]."\n\n";
	}

return '
<form action="'.$script.'" method="post">
'.$addtag.'
<table cellspacing="3" cellpadding="0" border="0">
 <tr>
  <td align="right">
'.$template.'
  </td>
 </tr>
 <tr>
  <td align="right">
   <input type="hidden" name="page" value="'.htmlspecialchars($page).'" />
   <input type="hidden" name="digest" value="'.htmlspecialchars($digest).'" />
   <textarea name="msg" rows="'.$rows.'" cols="'.$cols.'" wrap="virtual">
'.htmlspecialchars($refer.$postdata).'</textarea>
  </td>
 </tr>
 <tr>
  <td>
   <input type="submit" name="preview" value="'.$_btn_preview.'" accesskey="p" />
   <input type="submit" name="write" value="'.$_btn_update.'" accesskey="s" />
   '.$add_top.'
   <input type="checkbox" name="notimestamp" value="true" /><span style="small">'.$_btn_notchangetimestamp.'</span>
  </td>
 </tr>
</table>
</form>

<form action="'.$script.'?cmd=freeze" method="post">
<div>
<input type="hidden" name="page" value="'.htmlspecialchars($vars["page"]).'" />
'.$str_freeze.'
</div>
</form>

' . $help;
}

// 関連するページ
function make_related($page,$_isrule)
{
	global $related_str,$rule_related_str,$related,$_make_related,$vars;

	$page_name = strip_bracket($vars["page"]);

	if(!is_array($_make_related))
	{
		$aryrelated = do_search($page,"OR",1);

		if(is_array($aryrelated))
		{
			foreach($aryrelated as $key => $val)
			{
				$new_arylerated[$key.md5($val)] = $val;
			}
		}

		if(is_array($related))
		{
			foreach($related as $key => $val)
			{
				$new_arylerated[$key.md5($val)] = $val;
			}
		}

		@krsort($new_arylerated);
		$_make_related = @array_unique($new_arylerated);
	}

	if($_isrule)
	{
		if(is_array($_make_related))
		{
			foreach($_make_related as $str)
			{
				preg_match("/<a\shref=\"([^\"]+)\">([^<]+)<\/a>(.*)/",$str,$out);
				
				if($out[3]) $title = " title=\"$out[2] $out[3]\"";
				
				$aryret[$out[2]] = "<a href=\"$out[1]\"$title>$out[2]</a>";
			}
			@ksort($aryret);
		}
	}
	else
	{
		$aryret = $_make_related;
	}

	if($_isrule) $str = $rule_related_str;
	else         $str = $related_str;

	return @join($str,$aryret);
}

// 注釈処理
function make_note($str)
{
	global $note_id,$foot_explain;

	$str = preg_replace("/^\(\(/","",$str);
	$str = preg_replace("/\s*\)\)$/","",$str);

	$str= str_replace("\\'","'",$str);

	$str = make_user_rules($str);

	$foot_explain[] = "<a name=\"notefoot_$note_id\" href=\"#notetext_$note_id\" class=\"note_super\">*$note_id</a> <span class=\"small\">$str</span><br />\n";
	$note =  "<a name=\"notetext_$note_id\" href=\"#notefoot_$note_id\" class=\"note_super\">*$note_id</a>";
	$note_id++;

	return $note;
}

// リンクを付加する
function make_link($name,$page = '')
{
	return p_make_link($name,$page);
}

// ユーザ定義ルール(ソースを置換する)
function user_rules_str($str)
{
	global $str_rules;

	$arystr = split("\n",$str);

	// 日付・時刻置換処理
	foreach($arystr as $str)
	{
		if(substr($str,0,1) != " ")
		{
			foreach($str_rules as $rule => $replace)
			{
				$str = preg_replace("/$rule/",$replace,$str);
			}
		}
		$retvars[] = $str;
	}

	return join("\n",$retvars);
}

// ユーザ定義ルール(ソースは置換せずコンバート)
function make_user_rules($str)
{
	global $user_rules;

	foreach($user_rules as $rule => $replace)
	{
		$str = preg_replace("/$rule/",$replace,$str);
	}

	return $str;
}

// HTMLタグを取り除く
function strip_htmltag($str)
{
	//$str = preg_replace("/<a[^>]+>\?<\/a>/","",$str);
	return preg_replace("/<[^>]+>/","",$str);
}

// ページ名からページ名を検索するリンクを作成
function make_search($page)
{
	global $script,$WikiName;

	$name = strip_bracket($page);
	$url = rawurlencode($page);

	//WikiWikiWeb like...
	//if(preg_match("/^$WikiName$/",$page))
	//	$name = preg_replace("/([A-Z][a-z]+)/","$1 ",$name);

 	return "<a href=\"$script?cmd=search&amp;word=$url\">".htmlspecialchars($name)."</a> ";
}

?>
