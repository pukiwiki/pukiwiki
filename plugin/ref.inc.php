<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: ref.inc.php,v 1.13 2003/03/16 13:53:29 panda Exp $
//

/*
*プラグイン ref
ページに添付されたファイルを展開する

*Usage
 #ref(filename[[,{Left|Center|Right}]|[,{Wrap|Nowrap}]|[,Around]]{}[,comments])

*パラメータ
-filename~
 添付ファイル名、あるいはURL
 'ページ名/添付ファイル名'を指定すると、そのページの添付ファイルを参照する
-Left|Center|Right~
 横の位置合わせ
-Wrap|Nowrap~
 テーブルタグで囲む/囲まない
-Around~
 テキストの回り込み

*/

// upload dir(must set end of /)
if (!defined('UPLOAD_DIR'))
{
	define('UPLOAD_DIR','./attach/');
}

// file icon image
if (!defined('FILE_ICON'))
{
	define('FILE_ICON','<img src="./image/file.png" width="20" height="20" alt="file" style="border-width:0px" />');
}

// default alignment
define('REF_DEFAULT_ALIGN','left'); // 'left','center','right'

// force wrap on default
define('REF_WRAP_TABLE',FALSE); // TRUE,FALSE

function plugin_ref_inline()
{
	global $vars;
	
	//エラーチェック
	if (!func_num_args())
	{
		return 'no argument(s).';
	}
	
	$params = plugin_ref_body(func_get_args(),$vars['page']);
	
	return ($params['_error'] != '') ? $params['_error'] : $params['_body'];
}
function plugin_ref_convert()
{
	global $vars;

	//エラーチェック
	if (!func_num_args())
	{
		return '<p>no argument(s).</p>';
	}
	
	$params = plugin_ref_body(func_get_args(),$vars['page']);
	
	if ($params['_error'] != '')
	{
		return "<p>{$params['_error']}</p>";
	}
	
	if ((REF_WRAP_TABLE and !$params['nowrap']) or $params['wrap'])
	{
		// 枠で包む
		// margin:auto Moz1=x(wrap,aroundが効かない),op6=oNN6=x(wrap,aroundが効かない)IE6=x(wrap,aroundが効かない)
		// margin:0px Moz1=x(wrapで寄せが効かない),op6=x(wrapで寄せが効かない),nn6=x(wrapで寄せが効かない),IE6=o
		$margin = ($params['around'] ? '0px' : 'auto');
		$margin_align = ($params['_align'] == 'center') ? '' : ";margin-{$params['_align']}:0px";
		$params['_body'] = <<<EOD
<table class="style_table" style="margin:$margin$margin_align">
 <tr>
  <td class="style_td">{$params['_body']}</td>
 </tr>
</table>
EOD;
	}
	// divで包む
	if ($params['around'])
	{
		$style = ($params['_align'] == 'right') ? 'float:right' : 'float:left';
	}
	else
	{
		$style = "text-align:{$params['_align']}";
	}
	return "<div class=\"img_margin\" style=\"$style\">{$params['_body']}</div>\n";
}

function plugin_ref_body($args,$page)
{
	global $script,$WikiName,$BracketName;
	
	// 戻り値
	$params = array();
	
	// 添付ファイル名を取得
	$name = array_shift($args);
	
	// 次の引数がページ名かどうか
	if (count($args) and preg_match("/^($WikiName|\[\[$BracketName\]\])$/",$args[0]))
	{
		$_page = get_fullname(strip_bracket($args[0]),$page);
		if (is_pagename($_page))
		{
			$page = $_page;
			array_shift($args);
		}
	}
	
/*
 $nameをもとに以下の変数を設定
 $url,$url2 : URL
 $title :タイトル
 $is_image : 画像のときTRUE
 $info : 画像ファイルのときgetimagesize()の'size'
         画像ファイル以外のファイルの情報
         添付ファイルのとき : ファイルの最終更新日とサイズ
         URLのとき : URLそのもの
*/
	$file = $title = $url = $url2 = $info = $title = '';
	$width = $height = 0;
	
	if (is_url($name))	//URL
	{
		$url = htmlspecialchars($name);
		$title = preg_match('/([^\/]+)$/', $name, $match) ? $match[1] : $url;
		
		$is_image = preg_match("/\.(gif|png|jpe?g)$/i",$name);
		if ($is_image and (bool)ini_get('allow_url_fopen'))
		{
			$size = @getimagesize($name);
			if (is_array($size))
			{
				$width = $size[0];
				$height = $size[1];
				$info = $size[3];
			}
		}
		else
		{
			$info = $url;
		}
	}
	else	//添付ファイル
	{
		if (!is_dir(UPLOAD_DIR))
		{
			$params['_error'] = 'no UPLOAD_DIR.';
			return $params;
		}
		
		//ページ指定のチェック
//		$page = $vars['page'];
		if (preg_match('/^(.+)\/([^\/]+)$/',$name,$matches))
		{
			if ($matches[1] == '.' or $matches[1] == '..')
			{
				$matches[1] .= '/';
			}
			$page = get_fullname($matches[1],$page);
			$name = $matches[2];
		}
		$title = $name;
		$file = UPLOAD_DIR.encode($page).'_'.encode($name);
		if (!is_file($file))
		{
			$params['_error'] = 'file not found.';
			return $params;
		}
		$size = @getimagesize($file);
		$is_image = preg_match("/\.(gif|png|jpe?g)$/i",$name);
		$width = $height = 0;
		$url = $script.'?plugin=attach&amp;openfile='.rawurlencode($name).'&amp;refer='.rawurlencode($page);
		if ($is_image)
		{
			$url2 = $url;
			$url = $file;
			if (is_array($size))
			{
				$width = $size[0];
				$height = $size[1];
			}
		}
		else
		{
			$info = get_date('Y/m/d H:i:s',filemtime($file) - LOCALZONE).' '.sprintf('%01.1f',round(filesize($file)/1000,1)).'KB';
		}
	}
	
	//パラメータ
	$params = array('left'=>FALSE,'center'=>FALSE,'right'=>FALSE,
		'wrap'=>FALSE,'nowrap'=>FALSE,'around'=>FALSE,
		'noicon'=>FALSE,'nolink'=>FALSE,
		'zoom'=>FALSE,'size'=>FALSE,'w'=>0,'h'=>0,'%'=>0,
		'_args'=>array(),'_done'=>FALSE,'_error'=>''
	);
	
	if (count($args) > 0)
	{
		array_walk($args, 'ref_check_arg', &$params);
	}
	
	//拡張パラメータをチェック
	if (count($params['_args']))
	{
		$_title = array();
		foreach ($params['_args'] as $arg)
		{
			if (preg_match('/^([0-9]+)x([0-9]+)$/',$arg,$m))
			{
				$params['size'] = TRUE;
				$params['w'] = $m[1];
				$params['h'] = $m[2];
			}
			else if (preg_match('/^([0-9.]+)%$/',$arg,$m) and $m[1] > 0)
			{
				$params['%'] = $m[1];
			}
			else
			{
				$_title[] = $arg;
			}
		}
		if (count($_title))
		{
			$title = join(',', $_title);
			$title = $is_image ? htmlspecialchars($title) : make_line_rules($title);
		}
	}
	//画像サイズ調整
	if ($is_image)
	{
		// 指定されたサイズを使用する
		if ($params['size'])
		{
			if ($width == 0 and $height == 0)
			{
				$width = $params['w'];
				$height = $params['h'];
			}
			else if ($params['zoom'])
			{
				$_w = $params['w'] ? $width / $params['w'] : 0;
				$_h = $params['h'] ? $height / $params['h'] : 0;
				$zoom = max($_w,$_h);
				if ($zoom)
				{
					$width = (int)($width / $zoom);
					$height = (int)($height / $zoom);
				}
			}
			else
			{
				$width = $params['w'] ? $params['w'] : $width;
				$height = $params['h'] ? $params['h'] : $height;
			}
		}
		if ($params['%'])
		{
			$width = (int)($width * $params['%'] / 100);
			$height = (int)($height * $params['%'] / 100);
		}
		if ($width and $height)
		{
			$info = "width=\"$width\" height=\"$height\"";
		}
	}
	
	//アラインメント判定
	if ($params['right'])
	{
		$params['_align'] = 'right';
	}
	else if ($params['left'])
	{
		$params['_align'] = 'left';
	}
	else if ($params['center'])
	{
		$params['_align'] = 'center';
	}
	else
	{
		$params['_align'] = REF_DEFAULT_ALIGN;
	}

	// ファイル種別判定
	if ($is_image)	// 画像
	{
		$_url = "<img src=\"$url\" alt=\"$title\" title=\"$title\" $info />";
		if (!$params['nolink'] and $url2)
		{
			$_url = "<a href=\"$url2\" title=\"$title\">$_url</a>";
		}
		$params['_body'] = $_url;
	}
	else	// 通常ファイル
	{
		$icon = $params['noicon'] ? '' : FILE_ICON;
		$params['_body'] = "<a href=\"$url\" title=\"$info\">$icon$title</a>\n";
	}
	return $params;
}

//-----------------------------------------------------------------------------
//オプションを解析する
function ref_check_arg($val, $_key, &$params)
{
	if ($val == '')
	{
		$params['_done'] = TRUE;
		return;
	}
	if (!$params['_done'])
	{
		foreach (array_keys($params) as $key)
		{
			if (strpos($key, strtolower($val)) === 0)
			{
				$params[$key] = TRUE;
				return;
			}
		}
		$params['_done'] = TRUE;
	}
	$params['_args'][] = $val;
}
?>
