<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: related.inc.php,v 1.2 2005/01/15 14:11:12 henoheno Exp $
//
// Related plugin: Show Backlinks for the page

// TODO: move '#related' here
//function plugin_related_convert()
//{
//	global $related_link;
//	$related_link = 0;
//	// Do
//}

// Show Backlinks: via related caches for the page
function plugin_related_action()
{
	global $vars, $script, $non_list, $defaultpage, $whatsnew;

	$_page = isset($vars['page']) ? $vars['page'] : '';
	if ($_page == '') $_page = $defaultpage;

	// Get related from cache
	$data = links_get_related_db($_page);
	$non_list_pattern = '/' . $non_list . '/';
	foreach(array_keys($data) as $page)
		if ($page == $whatsnew || preg_match($non_list_pattern, $page))
			unset($data[$page]);

	// Result
	$s_word = htmlspecialchars($_page);
	$msg = 'Backlinks for: ' . $s_word;
	$retval  = '<a href="' . $script . '?' . $s_word . '">' .
		'Return ' . $s_word .'</a><br/>'. "\n";

	if (empty($data)) {
		$retval .= '<ul><li>No related pages found.</li></ul>' . "\n";	
	} else {
		// Show count($data)?
		ksort($data);
		$retval .= '<ul>' . "\n";
		foreach ($data as $page=>$time) {
			$r_page  = rawurlencode($page);
			$s_page  = htmlspecialchars($page);
			$passage = get_passage($time);
			$retval .= ' <li><a href="' . $script . '?' . $r_page . '">' . $s_page .
				'</a> ' . $passage . '</li>' . "\n";
		}
		$retval .= '</ul>' . "\n";
	}
	return array('msg'=>$msg, 'body'=>$retval);
}
?>
