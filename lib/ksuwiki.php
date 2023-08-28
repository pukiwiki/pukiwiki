<?php
// Ksuwiki, transfer a relative uri to an absolute path
// relative to the 'document root'
function get_absolute_uri($uri){
	$path = realpath($_SERVER['DOCUMENT_ROOT']);
	$path = str_replace($path, '', realpath($uri));// remove prefix
	$path = str_replace('\\', '/', $path);
	if ( substr($path, 0,1) != '/') $path = '/' . $path;
	if (substr($path,-1) != '/') $path .= '/'; 
	return $path;
}

// Ksuwiki, copy directory recursively. Note: not for copying normal files
function copy_r($src, $dst){
    try{
        mkdir($dst);
        $dh = opendir($src);
        while (false !== ($entry = readdir($dh)) ) {
            if ($entry == "." || $entry == "..")  continue; //echo $entry , '<br>';
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