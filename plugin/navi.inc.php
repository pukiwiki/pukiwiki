<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: navi.inc.php,v 1.13 2003/05/01 08:49:55 arino Exp $
//

/*

*プラグイン navi
DobBook風のナビゲーションバーを表示する

*Usage
 #navi(page)

*パラメータ
-page~
 HOMEとなるページ。省略すると自ページをHOMEとする。

*動作

-1回目の参照(HOME)~
 ページ一覧をls.inc.php風に表示する
-1回目の参照(HOME以外)
 header : ヘッダ風
  prev       next
 -----------------
-2回目の参照
 footer : フッタ風
 ------------------
  prev  home  next
  title  up  title

*/

// 除外するページ (正規表現で)
define('NAVI_EXCLUDE_PATTERN','');
#define('NAVI_EXCLUDE_PATTERN','/\/_/');

// <link>タグを出力する (TRUE|FALSE)
define('NAVI_LINK_TAGS',FALSE);

function plugin_navi_init()
{
	$messages = array(
		'_navi_messages'=>array(
			'msg_prev'=>'Prev',
			'msg_next'=>'Next',
			'msg_up'  =>'Up',
			'msg_home'  =>'Home'
		)
	);
	set_plugin_messages($messages);
}
function plugin_navi_convert()
{
	global $vars, $script, $head_tags;
	global $_navi_messages;
	static $navi = array();
	
	$home = $current = $vars['page'];
	if (func_num_args())
	{
		list($home) = func_get_args();
		$home = strip_bracket($home);
	}
	$is_home = ($home == $current);
	
	// 初回FALSE,2回目以降TRUE
	$footer = array_key_exists($home,$navi);
	if (!$footer)
	{
		$navi[$home] = array(
			'up'=>'',
			'prev'=>'',
			'prev1'=>'',
			'next'=>'',
			'next1'=>'',
			'home'=>'',
			'home1'=>'',
		);
		
		$pages = preg_grep('/^'.preg_quote($home,'/').'($|\/)/',get_existpages());
		// preg_grep(,,PREG_GREP_INVERT)が使えれば…
		if (NAVI_EXCLUDE_PATTERN != '')
		{
			$pages = array_diff($pages,preg_grep(NAVI_EXCLUDE_PATTERN,$page));
		}
		$pages[] = $current; // 番兵 :)
		$pages = array_unique($pages);
		natcasesort($pages);
		$prev = $home;
		foreach ($pages as $page)
		{
			if ($page == $current)
			{
				break;
			}
			$prev = $page;
		}
		$next = current($pages);
		
		$pos = strrpos($current, '/');
		$up = '';
		if ($pos > 0)
		{
			$up = substr($current, 0, $pos);
			$navi[$home]['up'] = make_pagelink($up,$_navi_messages['msg_up']);
		}
		if (!$is_home)
		{
			$navi[$home]['prev'] = make_pagelink($prev);
			$navi[$home]['prev1'] = make_pagelink($prev,$_navi_messages['msg_prev']);
		}
		if ($next != '')
		{
			$navi[$home]['next'] = make_pagelink($next);
			$navi[$home]['next1'] = make_pagelink($next,$_navi_messages['msg_next']);
		}
		$navi[$home]['home'] = make_pagelink($home);
		$navi[$home]['home1'] = make_pagelink($home,$_navi_messages['msg_home']);
		
		// <link>タグを生成する : start next prev(previous) parent(up)
		// 未対応 : contents(toc) search first(begin) last(end)
		if (NAVI_LINK_TAGS)
		{
			foreach (array('start'=>$home,'next'=>$next,'prev'=>$prev,'up'=>$up) as $rel=>$_page)
			{
				if ($_page != '')
				{
					$s_page = htmlspecialchars($_page);
					$r_page = rawurlencode($_page);
					$head_tags[] = " <link rel=\"$rel\" href=\"$script?$r_page\" title=\"$s_page\" />";
				}
			}
		}
	}

	$ret = '';
	if ($footer) // フッタ
	{
		$ret = <<<EOD
<hr class="full_hr" />
<ul class="navi">
 <li class="navi_left">{$navi[$home]['prev1']}<br />{$navi[$home]['prev']}</li>
 <li class="navi_right">{$navi[$home]['next1']}<br />{$navi[$home]['next']}</li>
 <li class="navi_none">{$navi[$home]['home1']}<br />{$navi[$home]['up']}</li>
</ul>
EOD;
	}
	else if ($is_home) // 目次
	{
		$ret .= '<ul>';
		foreach ($pages as $page)
		{
			if ($page != $home)
			{
				$ret .= ' <li>'.make_pagelink($page).'</li>';
			}
		}
		$ret .= '</ul>';
	}
	else // ヘッダ
	{
		$ret = <<<EOD
<ul class="navi">
 <li class="navi_left">{$navi[$home]['prev1']}</li>
 <li class="navi_right">{$navi[$home]['next1']}</li>
 <li class="navi_none">{$navi[$home]['home']}</li>
</ul>
<hr class="full_hr" />
EOD;
	}
	return $ret;
}
?>
