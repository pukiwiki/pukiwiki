<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: navi.inc.php,v 1.8 2003/03/03 07:07:28 panda Exp $
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

1ページで2回呼ばれることを考慮して、関連変数はスタティックに持つ。

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
function plugin_navi_init()
{
	$messages = array('_navi_messages'=>array(
		'msg_prev'=>'Prev',
		'msg_next'=>'Next',
		'msg_up'  =>'Up',
		'msg_home'  =>'Home',
	));
  set_plugin_messages($messages);
}
function plugin_navi_convert()
{
	global $vars, $script;
	global $_navi_messages;
	static $_navi_pages;
	
	$home = $vars['page'];
	if (func_num_args())
	{
		list($home) = func_get_args();
	}
	$is_home = ($home == $vars['page']);
	$current = $vars['page'];
	
	if (!$footer = isset($_navi_pages))
	{
		$pages = array($current=>strip_bracket($current));
		$_pages = preg_grep('/^(\[\[)?'.strip_bracket($home).'\//',get_existpages());
		foreach ($_pages as $_page)
		{
			$pages[$_page] = strip_bracket($_page);
		}
		natcasesort($pages);
		$pages = array_keys($pages);
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
		
		$_navi_pages = array(
			'up'=>'',
			'prev'=>'',
			'prev1'=>'',
			'next'=>'',
			'next1'=>'',
			'home'=>'',
			'home1'=>'',
		);

		$pos = strrpos($current, '/');
		if ($pos > 0)
		{
			$up = substr($current, 0, $pos).(substr($current,0,2)=='[[' ? ']]' : '');
			$_navi_pages['up'] = navi_make_link($up,'none',$_navi_messages['msg_up']);
		}
		if (!$is_home)
		{
			$_navi_pages['prev'] = navi_make_link($prev,'left');
			$_navi_pages['prev1'] = navi_make_link($prev,'left',$_navi_messages['msg_prev']);
		}
		if ($next != '')
		{
			$_navi_pages['next'] = navi_make_link($next,'right');
			$_navi_pages['next1'] = navi_make_link($next,'right',$_navi_messages['msg_next']);
		}
		$_navi_pages['home'] = navi_make_link($home,'none');
		$_navi_pages['home1'] = navi_make_link($home,'none',$_navi_messages['msg_home']);
	}

	$ret = '';
	if ($footer) //フッタ
	{
		$ret = <<<EOD
<hr class="full_hr" />
<ul class="navi">
 <li class="navi_left">{$_navi_pages['prev1']}<br />{$_navi_pages['prev']}</li>
 <li class="navi_right">{$_navi_pages['next1']}<br />{$_navi_pages['next']}</li>
 <li class="navi_none">{$_navi_pages['home1']}<br />{$_navi_pages['up']}</li>
</ul>
EOD;
	}
	else if ($is_home) //目次
	{
		$ret .= '<ul>';
		foreach ($pages as $page) {
			if (strip_bracket($page) == strip_bracket($home))
			{
				continue;
			}
			$ret .= '<li>'.make_link("[[$page]]").'</li>';
		}
		$ret .= '</ul>';
	}
	else
	{
		$ret = <<<EOD
<ul class="navi">
  <li class="navi_left">{$_navi_pages['prev1']}</li>
  <li class="navi_right">{$_navi_pages['next1']}</li>
  <li class="navi_none">{$_navi_pages['home']}</li>
</ul>
<hr class="full_hr" />
EOD;
	}
	return $ret;
}
function navi_make_link($page,$align,$name='')
{
	global $script;
	
	$r_page = rawurlencode($page);
	$s_name = htmlspecialchars(strip_bracket($page));
	$name = ($name == '') ? $s_name : htmlspecialchars($name);
	$title = $s_name . get_pg_passage($page,FALSE);
	
	return "<a href=\"$script?{$r_page}\" title=\"$title\">$name</a>";
}
?>
