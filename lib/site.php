<?php
/////////////////////////////////
// New for Ksuwiki, transfer a relative uri to an absolute one
// By absolute uri, here we mean a path relative to the 'document root'
function get_absolute_uri($uri){
	$path = realpath($_SERVER['DOCUMENT_ROOT']);
	$path = str_replace($path, '', realpath($uri));// remove prefix
	$path = str_replace('\\', '/', $path);
	if ( $path[0] != '/') $path = '/' . $path;
	if (substr($path,-1) != '/') $path .= '/'; //die ($path);
	return $path;
}

///////////////////////////////
// New for Ksuwiki, copy directory recursively
// Note: not for copying normal files
//
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


// Check  whether an ip matches a subnet
// e.g. $ip='123.234.235.2', $range='123.234.235.0/16';
////.BEGIN
function cidr_match($ip, $range){
	list ($subnet, $bits) = explode('/', $range);
	if (!is_ip($ip || !is_ip($subnet) )) {
		return false;
	}
	if (empty($bits)) {
		$bits = 32;
	}
	$ip = substr(ip2bin($ip),0,$bits) ;
	$subnet = substr(ip2bin($subnet),0,$bits) ;
	return ($ip == $subnet) ;
}
function is_ip($ip){
	return filter_var($ip, FILTER_VALIDATE_IP);
}
function ip2bin($ip){
	$ips = explode(".", $ip) ;
	$ipbin = '';
	foreach ($ips as $iptmp){
		$ipbin .= sprintf("%08b",$iptmp) ;
	}
	return $ipbin ;
}
////.END