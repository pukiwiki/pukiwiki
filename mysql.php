<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: mysql.php,v 1.2 2003/03/10 11:30:50 panda Exp $
//

function db_exec($sql)
{
	$conn = mysql_pconnect(MYSQL_HOST,MYSQL_USER,MYSQL_PASS)
		or die_message('cannot connect db.');
	mysql_select_db(MYSQL_DB,$conn)
		or die_message('cannot select db.');
	$result = mysql_query($sql,$conn)
		or die_message("query '$sql' failure.\n".mysql_error($conn));
	return $result;
}


function db_query($sql)
{
	$result = db_exec($sql);
	
	$rows = array();
	while ($row = mysql_fetch_array($result)) {
		$rows[] = $row;
	}
	mysql_free_result($result);

	return $rows;
}

/*
create table page (id integer auto_increment primary key, name text not null, lastmod integer not null);
create table link (page_id integer not null, ref_id integer not null);
*/
?>
