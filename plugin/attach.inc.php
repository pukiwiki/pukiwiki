<?php
// プラグイン attach

// changed by Y.MASUI <masui@hisec.co.jp> http://masui.net/pukiwiki/
// $Id: attach.inc.php,v 1.11 2003/01/27 05:38:44 panda Exp $

// modified by PANDA <panda@arino.jp> http://home.arino.jp/
// Last-Update:2002-12-08 rev.8

// upload dir(must set end of /)
if (!defined('UPLOAD_DIR')) {
	define('UPLOAD_DIR','./attach/');
}

// max file size for upload on PHP(PHP default 2MB)
ini_set('upload_max_filesize','2M');

// max file size for upload on script of PukiWiki(default 1MB)
define('MAX_FILESIZE',1000000);

//管理者だけが添付ファイルをアップロードできるようにする
define('ATTACH_UPLOAD_ADMIN_ONLY',FALSE); // FALSE or TRUE
//管理者だけが添付ファイルを削除できるようにする
define('ATTACH_DELETE_ADMIN_ONLY',FALSE); // FALSE or TRUE

//アップロード/削除時にパスワードを要求する(ADMIN_ONLYが優先)
define('ATTACH_PASSWORD_REQUIRE',FALSE); // FALSE or TRUE

// file icon image
if (!defined('FILE_ICON'))
	define('FILE_ICON','<img src="./image/file.png" width="20" height="20" alt="file" style="border-width:0px" />');

// status index(編集不要)
define('ATTACH_STATUS_COUNT' ,0);
define('ATTACH_STATUS_BACKUP',1);
define('ATTACH_STATUS_PASS'  ,2);
define('ATTACH_STATUS_FREEZE',3);
define('ATTACH_STATUS_TERM'  ,4); //必ず最後に

//-------- init
function plugin_attach_init()
{
	$messages = array('_attach_messages'=>array(
		'msg_uploaded' => '$1 にアップロードしました',
		'msg_deleted'  => '$1 からファイルを削除しました',
		'msg_freezed'  => '添付ファイルを凍結しました。',
		'msg_unfreezed'=> '添付ファイルを凍結解除しました。',
		'msg_upload'   => '$1 への添付',
		'msg_info'     => '添付ファイルの情報',
		'msg_confirm'  => '<p>%s を削除します。</p>',
		'msg_list'     => '添付ファイル一覧',
		'msg_listpage' => '$1 の添付ファイル一覧',
		'msg_listall'  => '全ページの添付ファイル一覧',
		'msg_file'     => '添付ファイル',
		'msg_maxsize'  => 'アップロード可能最大ファイルサイズは %s です。',
		'msg_count'    => ' <span class="small">%s件</span>',
		'msg_password' => 'パスワード',
		'msg_adminpass'=> '管理者パスワード',
		'msg_delete'   => 'このファイルを削除します。',
		'msg_freeze'   => 'このファイルを凍結します。',
		'msg_unfreeze' => 'このファイルを凍結解除します。',
		'msg_isfreeze' => 'このファイルは凍結されています。',
		'msg_require'  => '(管理者パスワードが必要です)',
		'msg_filesize' => 'サイズ',
		'msg_date'     => '登録日時',
		'msg_dlcount'  => 'アクセス数',
		'err_noparm'   => '$1 へはアップロード・削除はできません',
		'err_exceed'   => '$1 へのファイルサイズが大きすぎます',
		'err_exists'   => '$1 に同じファイル名が存在します',
		'err_notfound' => '$1 にそのファイルは見つかりません',
		'err_noexist'  => '添付ファイルがありません。',
		'err_password' => 'パスワードが一致しません。',
		'err_adminpass'=> '管理者パスワードが一致しません。',
		'btn_upload'   => 'アップロード',
		'btn_info'     => '詳細',
		'btn_submit'   => '実行',
	));
	set_plugin_messages($messages);
}

//-------- convert
function plugin_attach_convert()
{
	global $vars;
	
	if (!ini_get('file_uploads')) {
		return 'file_uploads disabled';
	}
	
	$nolist = $noform = FALSE;
	
	if (func_num_args() > 0) {
		$args = func_get_args();
		$tmp = array();
		foreach ($args as $arg) {
			$tmp[] = strtolower($arg);
		}
		$nolist = in_array('nolist',$tmp);
		$noform = in_array('noform',$tmp);
	}
	$ret = '';
	if (!$nolist) {
		$ret .= attach_getlist($vars['page']);
	}
	if (!$noform) {
		$ret .= attach_form($vars['page']);
	}
	
	return $ret;
}

//-------- action
function plugin_attach_action()
{
	global $vars,$HTTP_POST_FILES;
	
	if (array_key_exists('openfile',$vars)) {
		$vars['pcmd'] = 'open';
		$vars['file'] = $vars['openfile'];
	}
	if (array_key_exists('delfile',$vars)) {
		$vars['pcmd'] = 'delete';
		$vars['file'] = $vars['delfile'];
	}
	if (array_key_exists('attach_file',$HTTP_POST_FILES) and
		is_uploaded_file($HTTP_POST_FILES['attach_file']['tmp_name']))
	{
		return attach_upload();
	}
	
	$age = array_key_exists('age',$vars) ? $vars['age'] : '';
	$pcmd = array_key_exists('pcmd',$vars) ? $vars['pcmd'] : '';
	
	switch ($pcmd) {
		case 'info':    return attach_info();
		case 'delete':  return attach_delete();
		case 'open':    return attach_open($vars['refer'],$vars['file'],$age);
		case 'list':    return attach_list();
		case 'freeze':  return attach_freeze(TRUE);
		case 'unfreeze':return attach_freeze(FALSE);
		case 'upload':   return attach_showform();
	}
	if ($vars['page'] == '' or !is_page($vars['page'])) {
		return attach_list();
	}
	
	return attach_showform();
}
//-------- call from skin
function attach_filelist()
{
	global $_attach_messages;
	
	plugin_attach_init();
	
	$ret = attach_getlist();
	return ($ret == '') ? '' : $_attach_messages['msg_file'].': '.$ret."\n";
}
//-------- 実体
//ファイルアップロード
function attach_upload()
{
	global $vars,$adminpass,$HTTP_POST_FILES;
	global $_attach_messages;
	
	if ($HTTP_POST_FILES['attach_file']['size'] > MAX_FILESIZE) {
		return array('msg'=>$_attach_messages['err_exceed']);
	}
	if (is_freeze($vars['refer']) || !is_editable($vars['refer'])) {
		return array('msg'=>$_attach_messages['err_noparm']);
	}
	if (ATTACH_UPLOAD_ADMIN_ONLY and md5($vars['pass']) != $adminpass) {
		return array('msg'=>$_attach_messages['err_adminpass']);
	}
	
	$file = encode($vars['refer']).'_'.encode($HTTP_POST_FILES['attach_file']['name']);
	if (file_exists(UPLOAD_DIR.$file)) {
		return array('msg'=>$_attach_messages['err_exists']);
	}
	move_uploaded_file($HTTP_POST_FILES['attach_file']['tmp_name'],UPLOAD_DIR.$file);
	
	if (is_page($vars['refer'])) {
		touch(DATA_DIR.encode($vars['refer']).'.txt');
	}
	
	$status = attach_getstatus($file);
	$status[ATTACH_STATUS_PASS] = array_key_exists('pass',$vars) ? md5($vars['pass']) : '';
	attach_putstatus($file,$status);

	return array('msg'=>$_attach_messages['msg_uploaded']);
}
//詳細フォームを表示
function attach_info($err='')
{
	global $script,$vars;
	global $_attach_messages;
	
	$msg_error = ($err == '') ? '' : '<p>'.$_attach_messages[$err].'</p>';
	
	$s_file = htmlspecialchars($vars['file']);
	$s_refer = htmlspecialchars($vars['refer']);
	$r_refer = rawurlencode($vars['refer']);
	$navi = <<<EOD
  <span class="small">
   [<a href="$script?plugin=attach&amp;pcmd=list&amp;refer=$r_refer">{$_attach_messages['msg_list']}</a>]
   [<a href="$script?plugin=attach&amp;pcmd=list">{$_attach_messages['msg_listall']}</a>]
  </span><br />
EOD;
	
	$obj = new AttachFile($vars['refer'],$vars['file']);
	if ($obj->status[ATTACH_STATUS_FREEZE]) {
		$msg_freezed = '<dd>'.$_attach_messages['msg_isfreeze'].'</dd>';
		$msg_delete = '';
		$msg_freeze  = '<input type="hidden" name="pcmd" value="unfreeze" />'.$_attach_messages['msg_unfreeze'];
	}
	else {
		$msg_freezed = '';
		$msg_delete = '<input type="radio" name="pcmd" value="delete" />'.$_attach_messages['msg_delete'];
		if (ATTACH_DELETE_ADMIN_ONLY) {
			$msg_delete .= $_attach_messages['msg_require'];
		}
		$msg_delete .= '<br />';
		$msg_freeze = '<input type="radio" name="pcmd" value="freeze" />'.$_attach_messages['msg_freeze'];
	}
	$info = $obj->getinfo(TRUE,FALSE);
	$type = attach_mime_content_type(UPLOAD_DIR.$obj->file);
	$body = <<< EOD
<dl>
 <dt>$info</dt>
 <dd>{$_attach_messages['msg_filesize']}:{$obj->sizestr} ({$obj->size} bytes)</dd>
 <dd>Content-type:$type</dd>
 <dd>{$_attach_messages['msg_date']}:{$obj->timestr}</dd>
 <dd>{$_attach_messages['msg_dlcount']}:{$obj->status[ATTACH_STATUS_COUNT][0]}</dd>
  $msg_freezed
</dl>
<hr>
<form action="$script" method="post">
 <div>
  <input type="hidden" name="plugin" value="attach" />
  <input type="hidden" name="refer" value="$s_refer" />
  <input type="hidden" name="file" value="$s_file" />
  $msg_delete
  $msg_freeze{$_attach_messages['msg_require']}<br />
  {$_attach_messages['msg_password']}: <input type="password" name="pass" size="8" />
  <input type="submit" value="{$_attach_messages['btn_submit']}" />
 </div>
</form>
EOD;
	return array(
		'msg'=>sprintf($_attach_messages['msg_info'],$s_file),
		'body'=>$msg_error.$navi.$body
	);
}
//削除
function attach_delete()
{
	global $vars,$adminpass;
	global $_attach_messages;
	
	if (is_freeze($vars['refer']) or !is_editable($vars['refer'])) {
		return array('msg' => $_attach_messages['err_noparm']);
	}
	
	$file = encode($vars['refer']).'_'.encode($vars['file']);
	
	if (!file_exists(UPLOAD_DIR.$file)) {
		return array('msg' => $_attach_messages['err_notfound']);
	}
	
	$status = attach_getstatus($file);
	
	if ($status[ATTACH_STATUS_FREEZE]) {
		return attach_info('msg_isfreeze');
	}
	
	if (md5($vars['pass']) != $adminpass) {
		if (ATTACH_DELETE_ADMIN_ONLY) {
			return attach_info('err_adminpass');
		}
		else if (ATTACH_PASSWORD_REQUIRE and md5($vars['pass']) != $status[ATTACH_STATUS_PASS]) {
			return attach_info('err_password');
		}
	}
	//バックアップ
	do {
		$backup = ++$status[ATTACH_STATUS_BACKUP];
	} while (file_exists(UPLOAD_DIR.$file.'.'.$backup));
	rename(UPLOAD_DIR.$file,UPLOAD_DIR.$file.'.'.$backup);
	$status[ATTACH_STATUS_COUNT][$backup] = $status[ATTACH_STATUS_COUNT][0];
	$status[ATTACH_STATUS_COUNT][0] = 0;
	attach_putstatus($file,$status);
	
	if (is_page($vars['refer'])) {
		touch(DATA_DIR.encode($vars['refer']).'.txt');
	}
	
	return array('msg' => $_attach_messages['msg_deleted']);
}
//凍結
function attach_freeze($freeze)
{
	global $vars,$adminpass;
	global $_attach_messages;
	
	if (is_freeze($vars['refer']) or !is_editable($vars['refer'])) {
		return array('msg' => $_attach_messages['err_noparm']);
	}
	
	$file = encode($vars['refer']).'_'.encode($vars['file']);
	
	if (!file_exists(UPLOAD_DIR.$file)) {
		return array('msg' => $_attach_messages['err_notfound']);
	}
	
	if (md5($vars['pass']) != $adminpass) {
		return attach_info('err_adminpass');
	}
	
	$status = attach_getstatus($file);
	$status[ATTACH_STATUS_FREEZE] = $freeze;
	attach_putstatus($file,$status);
	
	return array('msg' => $_attach_messages[$freeze ? 'msg_freezed' : 'msg_unfreezed']);
}
//ダウンロード
function attach_open($page,$name,$age = 0)
{
	global $_attach_messages;
	
	$file = encode($page).'_'.encode($name);
	$ext = $age  ? '.'.$age : '';
	
	if (!file_exists(UPLOAD_DIR.$file.$ext)) {
		return array('msg' => $_attach_messages['err_notfound']);
	}
	
	$length = filesize(UPLOAD_DIR.$file.$ext);
	$status = attach_getstatus($file);
	$status[ATTACH_STATUS_COUNT][0+$age]++;
	attach_putstatus($file,$status);
	
	$type = attach_mime_content_type(UPLOAD_DIR.$file.$ext);
	
	// for japanese
	if (function_exists('mb_convert_encoding')) {
		$name = mb_convert_encoding($name,'SJIS','auto');
	}
	
	header('Content-Disposition: inline; filename="'.$name.'"');
	header('Content-Length: '.$length);
	header('Content-Type: '.$type);
	
	@readfile(UPLOAD_DIR.$file.$ext);
	die(); 
}
//一覧取得
function attach_list()
{
	global $vars;
	global $_attach_messages;
	
	$refer = array_key_exists('refer',$vars) ? $vars['refer'] : '';
	
	$msg = $_attach_messages[$refer == '' ? 'msg_listall' : 'msg_listpage'];
	$obj = new AttachFiles($refer);
	$body = $obj->getlist(TRUE);
	if ($body == '') {
		$body = $_attach_messages['err_noexist'];
	}
	return array('msg'=>$msg,'body'=>$body);
}
//アップロードフォームを表示
function attach_showform()
{
	global $vars;
	global $_attach_messages;
	
	$vars['refer'] = $vars['page'];
	$body = ini_get('file_uploads') ? attach_form($vars['page']) : 'file_uploads disabled.';
	
	return array('msg'=>$_attach_messages['msg_upload'],'body'=>$body);
}
function attach_getlist()
{
	global $vars;
	
	$obj = new AttachFiles($vars['page']);
	$arr = $obj->get($vars['page']);
	$ret = array();
	
	foreach ($arr as $file) {
		if (is_object($file)) {
			$ret[] = $file->getinfo(TRUE);
		}
	}
	
	return join("\n&nbsp;&nbsp;",$ret);
}
//-------- サービス
//ステータス取得
function attach_getstatus($file)
{
	$data = file_exists(UPLOAD_DIR.$file.'.log') ?
		file(UPLOAD_DIR.$file.'.log') : array_fill(0,ATTACH_STATUS_TERM,0);
	foreach ($data as $line) {
		$status[] = chop($line);
	}
	$status[ATTACH_STATUS_COUNT] = explode(',',$status[ATTACH_STATUS_COUNT]);
	return $status;
}
//ステータス保存
function attach_putstatus($file,$status)
{
	$status[ATTACH_STATUS_COUNT] = join(',',$status[ATTACH_STATUS_COUNT]);
	$fp = fopen(UPLOAD_DIR.$file.'.log','wb');
	for ($n = 0; $n < ATTACH_STATUS_TERM; $n++) {
		fwrite($fp,$status[$n]."\n");
	}
	fclose($fp);
}
//mime-typeの決定
function attach_mime_content_type($filename)
{
	$type = 'application/octet-stream'; //default
	$config = ':config/plugin/attach/mime-type';
	
	$size = getimagesize($filename);
	if (is_array($size)) {
		switch ($size[2]) {
		case 1:
			return 'image/gif';
		case 2:
			return 'image/jpeg';
		case 3:
			return 'image/png';
		case 4:
			return 'application/x-shockwave-flash';
		}
	}
	
	if (!is_page($config)) {
		return $type;
	}
	
	foreach (get_source($config) as $line) {
		$cells = explode('|',$line);
		$_type = trim($cells[1]);
		$exts = preg_split('/\s+|,/',trim($cells[2]),-1,PREG_SPLIT_NO_EMPTY);
		
		foreach ($exts as $ext) {
			if (preg_match("/\.$ext$/i",$filename)) {
				return $_type;
			}
		}
	}
	
	return $type;
}
//アップロードフォーム
function attach_form($page)
{
	global $script,$vars;
	global $_attach_messages;
	
	$r_page = rawurlencode($page);
	$s_page = htmlspecialchars($page);
	$navi = <<<EOD
  <span class="small">
   [<a href="$script?plugin=attach&amp;pcmd=list&amp;refer=$r_page">{$_attach_messages['msg_list']}</a>]
   [<a href="$script?plugin=attach&amp;pcmd=list">{$_attach_messages['msg_listall']}</a>]
  </span><br />
EOD;

	if (!(bool)ini_get('file_uploads')) {
		return $navi;
	}
	
	$maxsize = MAX_FILESIZE;
	$msg_maxsize = sprintf($_attach_messages['msg_maxsize'],number_format($maxsize/1000)."KB");

	$pass = '';
	if (ATTACH_PASSWORD_REQUIRE or ATTACH_UPLOAD_ADMIN_ONLY) {
		$title = $_attach_messages[ATTACH_UPLOAD_ADMIN_ONLY ? 'msg_adminpass' : 'msg_password'];
		$pass = '<br />'.$title.': <input type="password" name="pass" size="8" />';
	}
	return <<<EOD
<form enctype="multipart/form-data" action="$script" method="post">
 <div>
  <input type="hidden" name="plugin" value="attach" />
  <input type="hidden" name="pcmd" value="post" />
  <input type="hidden" name="refer" value="$s_page" />
  <input type="hidden" name="max_file_size" value="$maxsize" />
  $navi
  <span class="small">
   $msg_maxsize
  </span><br />
  {$_attach_messages['msg_file']}: <input type="file" name="attach_file" />
  $pass
  <input type="submit" value="{$_attach_messages['btn_upload']}" />
 </div>
</form>
EOD;
}
//-------- クラス
//ファイル
class AttachFile
{
	var $page,$rawpage,$name,$rawname,$age,$file,$time,$timestr,$size,$sizestr,$status;
	
	function AttachFile($page,$name,$age=0)
	{
		$this->page = $page;
		$this->rawpage = rawurlencode($page);
		$this->name = $name;
		$this->rawname = rawurlencode($name);
		$this->age = $age;
		
		$this->file = encode($page).'_'.encode($name).($age ? '.'.$age : '');
		$this->time = filemtime(UPLOAD_DIR.$this->file) - LOCALZONE;
		$this->timestr = get_date('Y/m/d H:i:s',$this->time);
		$this->size = filesize(UPLOAD_DIR.$this->file);
		$this->sizestr = sprintf('%01.1f',round($this->size)/1000,1).'KB';
		$this->status = attach_getstatus($this->file);
	}
	function datecomp($a,$b)
	{
		return ($a->time == $b->time) ? 0 : (($a->time > $b->time) ? -1 : 1);
	}
	function getinfo($icon = FALSE,$info = TRUE)
	{
		global $script,$date_format,$time_format,$weeklabels;
		global $_attach_messages;
		
		$param = '&amp;file='.$this->rawname.'&amp;refer='.$this->rawpage;
		$infostr = $counter = '';
		if ($this->age) {
			$param .= '&amp;age='.$this->age;
			$label = $this->age.format_date($this->time,TRUE);
			$counter = $delete = '';
		}
		else {
			$icon = ($icon) ? FILE_ICON : '';
			$label = "$icon$this->name";
			if ($info) {
				$counter = ($icon and !empty($this->status[ATTACH_STATUS_COUNT][0])) ?
					sprintf($_attach_messages['msg_count'],$this->status[ATTACH_STATUS_COUNT][0]) : '';
				$info_title = str_replace('$1',$this->rawname,$_attach_messages['msg_info']);
				$infostr = "\n<span class=\"small\">[<a href=\"$script?plugin=attach&amp;pcmd=info$param\" title=\"$info_title\">{$_attach_messages['btn_info']}</a>]</span>";
			}
		}
		return "<a href=\"$script?plugin=attach&amp;pcmd=open$param\" title=\"{$this->timestr} {$this->sizestr}\">$label</a>$counter$infostr";
	}
}
//コンテナ
class AttachFiles
{
	var $files,$backups,$count;
	
	function AttachFiles($page = '')
	{
		$this->count = 0;
		$this->files = array();
		if (!$dir = @opendir(UPLOAD_DIR)) {
			return;
		}
		while ($file = readdir($dir)) {
			if ($file == '.' or $file == '..' or strstr($file,'.log') !== FALSE) {
				continue;
			}
			if (!preg_match('/^([0-9A-F]+)_([0-9A-F]+)(?:\.([0-9]+))?$/',$file,$matches)) {
				continue;
			}
			$_page = decode($matches[1]);
			if ($page != '' and $page != $_page) {
				continue;
			}
			$_age = isset($matches[3]) ? $matches[3] : 0;
			$obj =& new AttachFile($_page,decode($matches[2]),0 + $_age);
			if ($obj->age == 0) {
				$this->count++;
				$this->files[$obj->page][$obj->name] =& $obj;
			}
			else {
				if (!isset($obj->name,$this->files[$obj->page][$obj->name]) or !is_object($this->files[$obj->page][$obj->name])) {
					$this->files[$obj->page][$obj->name] = $obj->name; // ダミー
				}
				$this->backups[$obj->page][$obj->name][$obj->age] =& $obj;
			}
		}
		closedir($dir);
		foreach ($this->files as $key=>$tmp) {
			uasort($this->files[$key], array('AttachFile','datecomp'));
		}
	}
	function &get($page)
	{
		return array_key_exists($page,$this->files) ? $this->files[$page] : array();
	}
	function getlist($backup = FALSE)
	{
		global $script;
		
		$ret = '';
		$keys = array_keys($this->files);
		sort($keys);
		foreach ($keys as $page) {
			if (!$backup and $this->files[$page]->count == 0) {
				continue;
			}
			$strip = strip_bracket($page);
			$raw = rawurlencode($page);
			$passage = get_pg_passage($page);
			$ret .= "<ul>\n <li><a href=\"$script?$raw\">$strip</a>$passage\n  <ul>\n";
			
			foreach ($this->files[$page] as $obj) {
				if (is_object($obj)) {
					$ret .= '   <li>'.$obj->getinfo(FALSE)."\n";
					$name = $obj->name;
				}
				else {
					$ret .= '   <li>'.$obj."\n";
					$name = $obj;
				}
				if ($backup and isset($this->backups[$page][$name]) and is_array($this->backups[$page][$name])) {
					$ret .= "    <ul>\n";
					foreach ($this->backups[$page][$name] as $obj) {
						$ret .= '     <li>'.$obj->getinfo(FALSE)."</li>\n";
					}
					$ret .= "    </ul>\n";
				}
				$ret .= "   </li>\n";
			}
			$ret .= "  </ul>\n </li>\n</ul>\n";
		}
		return $ret;
	}
}
?>
