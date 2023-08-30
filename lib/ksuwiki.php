<?php
// Ksuwiki, 
// site_copy, copy directory recursively. Note: not for copying normal files
function site_copy($src, $dst){
    try{
        mkdir($dst);
        $dh = opendir($src);
        while (false !== ($entry = readdir($dh)) ) {
            if ($entry == "." || $entry == "..")  continue; 
            $src_entry = $src . DIRECTORY_SEPARATOR . $entry;
            $dst_entry = $dst . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($src_entry)){ 
                copy_r($src_entry, $dst_entry);
            }else{ 
                copy($src_entry, $dst_entry);
            }
        }
        closedir($dh);
    }catch(Exception $e){
        return 'Exception : '.$e->getMessage();
    }
  	return true;
}

function site_login($site, $password){
    $config = Symfony\Component\Yaml\Yaml::parseFile(
        WIKI_DIR .'sites/' . $site . '/' . SITE_CONFIG_FILE
    );
    if (md5($password) === $config['passwd']) {
        return true;
    }
    return false;
}

function site_auth($site, $password)
{
    if (site_login($site, $password)){
        session_start();
        session_regenerate_id(true); // require: PHP5.1+
        $_SESSION['authenticated_site'] = $site;
        return true;
    }
	return false;
}

function site_authed($site)
{
    session_start();
    return isset($_SESSION['authenticated_site']) and $_SESSION['authenticated_site']==$site;
}
function site_logout()
{
    $_SESSION = array();
    session_regenerate_id(true); // require: PHP5.1+
    session_destroy();
}