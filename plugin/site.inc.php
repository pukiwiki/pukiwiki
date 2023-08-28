<?php
// PLugin for site management
// site plugin (cmd=site)
// or (cmd=site&act=<act>)
// <act>= new|copy|modify|delete|list|passwd

function plugin_site_action(){
  global $vars;

  $msg  = '';
  $body = '';

  $actions = array(
    'list',   // list all wiki sites
    'new',    // create a new wiki site from scrach 
    'copy',   // create a copy of the specified wiki site
    'modify', // modify the definition of a site (except site id)  
    'delete', // delete a site (move data to trash folder)
    'passwd',  // change password of a site
  );
  $act  = 'list'; // default action
  if (isset($vars['act']) and in_array($vars['act'], $actions)){
    $act   = $vars['act'];
  }
  $data_ready = isset($vars['dataready']) ? true : false;
  $site_id   = isset($vars['site_id'])   ? $vars['site_id'] : null; 
  $body .= '<h3>' . m('manage') .'::'. m($act) . '</h3>';
  if ($data_ready) {
    $body .= plugin_site_go($act);
  }else {
    $body .= plugin_site_form($site_id, $act) ;
  }
  return array('msg'=>$msg, 'body'=>$body);
}

function plugin_site_form($site_id, $act='modify'){
  global $vars, $script;

  if ($act=='list'){
    return list_sites();
  }
  $title = $admin = $passwd = $toppage = '';
  $skin = 'default';

  if ($site_id){
    $site_config =  Symfony\Component\Yaml\Yaml::parseFile(
      WIKI_DIR . $site_id . DIRECTORY_SEPARATOR . SITE_CONFIG_FILE);
    if ($site_config){
      list(
        'title' =>$title, 
        'skin'  =>$skin, 
        'admin' =>$admin,  
        'passwd'=>$passwd, 
        'toppage'=>$toppage
      ) = $site_config;
    }
  }

  $body = <<<EOD
  <form action="$script" method="post">
  <input type="hidden" name="cmd" value="site" />
  <input type="hidden" name="act" value="$act" />
  <input type="hidden" name="dataready" value="ok"/>
  <table class="style_table">\n
EOD;
$skins = array('', 'default');
$skin_select ='<select name="skin">';
foreach ($skins as $opt_skin){
  $selected = ($opt_skin == $skin) ? ' selected' : '';
  $skin_select .= '<option value=' .p($opt_skin) . $selected . '>' . $opt_skin . '</option>';
}
$skin_select .= '</select>';

  $show_id  ='<tr><td class="style_td">' . m('site_id') . '</td><td class="style_td">'. $site_id . '</td></tr>'
    .'<input type="hidden" name="site_id" value="'. $site_id .'"/>';
  $input_id ='<tr><td class="style_td">' . m('site_id')  . '</td>'
    .'<td class="style_td"><input type="text" name="site_id" value='. p($site_id) .'size="35"/></td></tr>';
  $hidden_id ='<input type="hidden" name="old_site" value="'. $site_id .'"/>';    
  $input_pass='<tr><td class="style_td">' . m('passwd0')  . '</td>' 
    .'<td class="style_td"><input type="password" name="passwd0" size="35"/></td></tr>';
  $input_pass1='<tr><td class="style_td">' . m('passwd1')  . '</td>' 
    .'<td class="style_td"><input type="password" name="passwd1" size="35"/></td></tr>';
  $input_pass2='<tr><td class="style_td">' . m('passwd2')  . '</td>' 
    .'<td class="style_td"><input type="password" name="passwd2" size="35"/></td></tr>';
  $input_form= '
   <tr><td class="style_td">' . m('title')  . '</td>
   <td class="style_td"><input type="text" name="title" value="' . $title . '" size="60"/></td></tr>
   <tr><td class="style_td">' . m('admin')  . '</td>
   <td class="style_td"><input type="text" name="admin" value="' . $admin . '" size="35"/></td></tr>
   <tr><td class="style_td">' . m('skin')   . '</td>
   <td class="style_td">' . $skin_select . '</td> </tr>
   <tr><td class="style_td">' . m('toppage'). '</td>
   <td class="style_td"><input type="text" name="toppage" value="' . $toppage . '" size="35"/></td></tr>
';
  switch ($act){
    case 'delete' :
      $body .= $show_id  . $input_pass;
      break;
    case 'passwd' :
      $body .= $show_id . $input_pass . $input_pass1 . $input_pass2;
      break;
    case 'modify':
      $body .= $show_id . $input_form . $input_pass;
      break;
    case 'copy':
      $body .= $hidden_id . $input_id . $input_form . $input_pass;
      break;
    case 'new':
      $body .= $input_id  . $input_form . $input_pass;
      break;    
  }
  $_btn_save = m('btn_save');
  $_btn_reset = m('btn_reset');
  $body .= <<<EOD
  <tr><td class="style_td"></td ><td class="style_td">
  <input type="submit"  value="$_btn_save" />
  <input type="reset" value="$_btn_reset" /></td></tr>
  </table></form>
EOD;
  return $body;
}

function list_sites(){
  global $vars, $script;
  $msg ='';
  $body = '';
  $site_config = array();
  try{
    $files = glob(WIKI_DIR . '*' , GLOB_ONLYDIR);
    $files = array_diff($files, array(WIKI_DIR . SITE_TEMPLATE));
    foreach($files as $file){
      $site = explode('/', $file);
      $site = end($site);
      $config =  Symfony\Component\Yaml\Yaml::parseFile(
        $file . DIRECTORY_SEPARATOR . SITE_CONFIG_FILE);
      $site_config[$site] = $config;
    }
  }catch(PDOException $e){
    $msg = 'Exception : '.$e->getMessage();
    die_message($msg);
  }
  $body .= '<table class="style_table" width="90%">' ;
  $body .= '<tr>';
  foreach (array('site_id','title','admin','skin') as $item){
    $body .= '<td class="style_th">' . m($item) . '</td>';
  }
  $newsite =  _img_link('site_create.png', m('new'), '', 'new');
  $body .= '<td class="style_th" colspan=4><center>' .m('operation'). $newsite.'</center></td>';
  $body .= '<td class="style_th" colspan=2>' . m('link') . '</td>';
  $body .= '</tr>';

  foreach ($site_config as $site_id=>$config){
    $body .= '<tr>';
    $body .= '<td class="style_td">' . $site_id . '</td>';
    foreach (array('title','admin','skin') as $item){
      $body .= '<td class="style_td">' . $config[$item] . '</td>';
    }
    foreach (array('modify','copy','passwd','delete') as $item){
      $body .= '<td class="style_td">' . _img_link('site_'.$item.'.png', m($item), $site_id, $item) . '</td>';
    }
    $body .= '<td class="style_td">' . _img_link('site_inlink.png', m('open'), $site_id, 'open') . '</td>';
    $body .= '</tr>';
  }
  $body .= '</table>';
  return $body;
}

function plugin_site_go($act='modify'){
  global $vars;
  $act = $vars['act'];
  $site_id = isset($vars['site_id']) ? $vars['site_id'] : null;
  $old_site= isset($vars['old_site']) ? $vars['old_site'] : null;
  $pass0   = isset($vars['pass'])  ? trim($vars['passwd0']) : null;
  $pass1   = isset($vars['pass1']) ? trim($vars['passwd1']) : null;
  $pass2   = isset($vars['pass2']) ? trim($vars['passwd2']) : null;
  $title   = $vars['title'];
  $admin   = $vars['admin'];
  $skin    = $vars['skin'];
  $toppage = $vars['toppage'];
  $lastmod = date('Y-n-d H:i:s');
  $msg = '';
  try{
    switch ($act){
      case 'passwd' :
        if ($pass1==null or $pass2==null or $pass1!==$pass2){
          die_message(m('passwd_notmatch'));
        }
        // TODO: Update yaml config file
        $msg = $act . " Update yaml config file";
        break;
      case 'modify':
        // TODO: Update yaml config file
        $msg = $act . " Update yaml config file";
        break;
      case 'copy':
        if ($old_site){
          $ok = copy_r(WIKI_DIR . $old_site, WIKI_DIR . $site_id);
        }else{
          $msg = m('unknown_site');
        }
        break; 
      case 'new':
        $cpy_from = SITE_TEMPLATE;
        // TODO: Update yaml config file
        $msg = $act . " Copy from " . $cpy_from;
        break;
      case 'delete':
        $msg = $act . " Move site folder to trash";
        break;
      default:
        die_message(m('invalidact'));
    }
  }catch(Exception $e){
    $msg = 'Exception : '.$e->getMessage();
    die_message($msg);
  }
  return $msg;
}

// make an image link
function _img_link($img,  $title, $site, $act){
  $url = '?cmd=site&act='.$act. '&site_id='.$site;
  if ($act=="open"){
    $url = PKWK_HOME . 'site/' . $site;
  }
  if ($act=="admin"){
    $url = PKWK_HOME . 'site/' . $site;
  }
  if ($act=="new"){
    $url = '?cmd=site&act='.$act;
  }
  $link  = '<a href='  .  p($url) . '>';
  $link .= '<img src=' . p(IMAGE_DIR. $img) . ' height="20px" title='. p($title) . '/></a>' ;
  return $link;
}

// double quoting a string
function p($str){  // abc --> "abc"
  return '"' . $str . '"';
}
// single quoting a string
function q($str){  // abc --> 'abc'
  return "'" . $str . "'";
}
// get message
function m($act){
  global $_site_messages;
  if (isset($_site_messages[$act])) 
    return $_site_messages[$act];
  return '';
}
?>
