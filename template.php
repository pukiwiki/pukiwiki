<?
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: template.php,v 1.1 2002/06/21 05:21:46 masui Exp $
/////////////////////////////////////////////////

function auto_template($page)
{
  global $auto_template_rules,$auto_template_func;
  if(!$auto_template_func) return '';

  $body = '';
  foreach($auto_template_rules as $rule => $template)
    {
      if(preg_match("/$rule/",$page,$matches)) {
	$template_page = preg_replace("/$rule/",$template,$page);
	$body = join('',get_source($template_page));
	for($i=0; $i<count($matches); ++$i) {
	  $body = str_replace("\$$i",$matches[$i],$body);
	}
	break;
      }
    }
  return $body;
}
?>