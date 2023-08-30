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
    'new',    // create a new wiki site from template 
    'copy',   // create a copy of the specified wiki site
    'modify', // modify the definition of a site (except site id)  
    'delete', // delete a site, move it to trash folder
    'passwd', // change site password , using md5() hash
    'login',  // login
    'logout', // logout
  );
  $act = 'list';
  if (isset($vars['act']) and in_array($vars['act'], $actions)){
    $act = $vars['act'];
  }

  if($act == 'list'){
    return array('msg'=>$msg, 'body'=> list_sites());
  }

  if ($act == 'logout' and defined('SITE_URL')){
    site_logout();
    $page = !empty($vars['page']) ? '/?page=' . $vars['page'] : '';  
    header('Location:'. SITE_URL . $page);
  }
  
  $data_ready = isset($vars['dataready']) ? true : false;
  $site_id   = isset($vars['site_id'])   ? $vars['site_id'] : null; 
  $body .= '<h3>' . m('manage') .'::'. m($act) . '</h3>';
  if ($data_ready) {
    $body .= _site_save($act);
  }else {
    $body .= _site_form($site_id, $act) ;
  }
  return array('msg'=>$msg,'body'=>$body);
}

function _site_form($site_id, $act='modify'){
  global $vars, $script;

  $title = $admin = $passwd = $toppage = '';
  $skin = 'default';
 
  if ($site_id){
    $site_config =  _site_config($site_id);
    if ($site_config){
      list(
        'title' =>$title, 
        'skin'  =>$skin, 
        'admin' =>$admin, 
        'toppage'=>$toppage,
        'readonly'=>$readonly,  
      ) = $site_config;
    }
  }
  $action = PKWK_HOME . 'site/' . SITE_ID;
  $body = <<<EOD
  <form action="$action" method="post">
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

  $show_id  ='<tr><td class="style_td">' . m('site_id') 
    . '</td><td class="style_td">'. $site_id . '</td></tr>'
    .'<input type="hidden" name="site_id" value='. p($site_id) .'/>';

  $input_id ='<tr><td class="style_td">' . m('site_id')  . '</td>'
    .'<td class="style_td"><input type="text" name="site_id" value='. p($site_id) .'size="35"/></td></tr>';

  $hidden_id ='<input type="hidden" name="orig_site" value='. p($site_id) .'/>'; 

  $input_pass='<tr><td class="style_td">' . m('passwd0')  . '</td>' 
    .'<td class="style_td"><input type="password" name="passwd" size="35"/></td></tr>';

  $input_pass1='<tr><td class="style_td">' . m('passwd1')  . '</td>' 
    .'<td class="style_td"><input type="password" name="passwd1" size="35"/></td></tr>';

  $input_pass2='<tr><td class="style_td">' . m('passwd2')  . '</td>' 
    .'<td class="style_td"><input type="password" name="passwd2" size="35"/></td></tr>';

  $writeable='';
  foreach(array( 0=>'No', 1=>'Yes') as $key=>$value){
    $checked = $key==$readonly ? 'checked' : '';
    $writeable .= '<input type="radio" name="readonly" value=' . p($key). ' '.$checked.'>' . $value . '&nbsp;';
  }
  $input_form= '
   <tr><td class="style_td">' . m('title')  . '</td>
   <td class="style_td"><input type="text" name="title" value=' . p($title) . ' size="35"/></td></tr>
   <tr><td class="style_td">' . m('admin')  . '</td>
   <td class="style_td"><input type="text" name="admin" value=' . p($admin) . ' size="35"/></td></tr>
   <tr><td class="style_td">' . m('skin')   . '</td>
   <td class="style_td">' . $skin_select . '</td> </tr>
   <tr><td class="style_td">' . m('toppage'). '</td>
   <td class="style_td"><input type="text" name="toppage" value=' . p($toppage). ' size="35"/></td></tr>
   <tr><td class="style_td">' . m('readonly'). '</td>
   <td class="style_td">' . $writeable . '</td></tr>
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
      $body .= $hidden_id . $input_id . $input_form . $input_pass1 . $input_pass2;
      break;
    case 'new':
      $body .= $input_id . $input_form . $input_pass1 . $input_pass2;
      break;  
    case 'login':
      $body .= $input_pass;
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

function _site_config($site){
  return Symfony\Component\Yaml\Yaml::parseFile(
    WIKI_DIR .'sites/' . $site . '/' . SITE_CONFIG_FILE
  );
}

function list_sites(){
  $msg ='';
  $body = '';
  $site_config = array();
  try{
    $files = glob(WIKI_DIR .'sites/*' , GLOB_ONLYDIR);
    foreach($files as $file){
      $site = explode('/', $file);
      $site = end($site);
      $site_config[$site] = _site_config($site);
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

function _site_save($act='modify'){
  global $vars;
  foreach (['act', 'title', 'admin','skin','toppage','readonly'] as $item){
    $$item = $vars[$item];   
  }
  foreach (['site_id', 'orig_site', 'passwd','passwd1','passwd2'] as $item){
    $$item = isset($vars[$item]) ? $vars[$item] : null; 
  }

  $msg = '';
  try{
    switch ($act){
      case 'passwd' :
      case 'modify':
        $config = _site_config($site_id);
        if ($act=='passwd'){
          if  ($passwd1==null or $passwd2==null or $passwd1!==$passwd2){
            die_message(m('passwd_notmatch'));
          }
          $config['passwd'] = md5($passwd1);
        }
        if ($act=='modify'){
          if (md5($passwd) !== $config['passwd']){
            die_message('Password incorrect!');
          }
          foreach (['title', 'admin','skin','toppage','readonly'] as $item){
            $config[$item] = $$item;   
          }
        }
        $yaml = Symfony\Component\Yaml\Yaml::dump($config);
        $file = WIKI_DIR .'sites/' . $site_id . '/' . SITE_CONFIG_FILE;
        file_put_contents($file, $yaml);
        $msg = "Successfully updated site ". $site_id;      
        break;
  
      case 'new':
      case 'copy':
        if ($passwd1 !== $passwd2){
          die_message('Passwords not matched !');
        }
        foreach (['title', 'admin','skin','toppage','readonly'] as $item){
          $config[$item] = $$item;   
        }
        $config['passwd'] = md5($passwd1);

        if ($act=='new'){
          $origin = WIKI_DIR . SITE_TEMPLATE;
        }
        if ($act=='copy' and $orig_site){
          $origin = WIKI_DIR .'sites/' . $orig_site ;
        }
        $target = WIKI_DIR .'sites/'. $site_id;

        $ok = site_copy($origin, $target);
        if ($ok and is_dir($target)){
          $yaml = Symfony\Component\Yaml\Yaml::dump($config);
          $file = $target . '/' . SITE_CONFIG_FILE;
          file_put_contents($file, $yaml);
        }
        break;
      case 'delete':
        $config = _site_config($site_id);
        if ($config and $config['passwd']==md5($passwd)){
          $target = WIKI_DIR .'sites/'. $site_id;
          $trash = WIKI_DIR . 'trash/' . $site_id. '_BAK';
          if (rename($target, $trash)){
            $msg = 'Successfully removed the site ' . $site_id;
          }else{
            $msg = 'Failed to remove the site ' . $site_id;
          }
        }
        break;
      case 'login':
        if (defined('SITE_ID') and  defined('SITE_URL')){
          $ok = site_auth(SITE_ID, $passwd);
          if ($ok) { 
            $page = !empty($vars['page']) ? '/?page=' . $vars['page'] : '';  
            header('Location:'. SITE_URL . $page);
          }else{
            sleep(2);       // Blocking brute force attack
          }  
        }
        break;
      default:
        die_message(m('msg_invalidact'));
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
  if ($act == 'open'){
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
