<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: pgsql.php,v 1.1 2003/01/27 05:44:11 panda Exp $
//

function db_exec($sql)
{
	$conn = pg_pconnect(PG_CONNECT_STRING)
		or die_message('cannot connect db.');
	$result = pg_query($conn,$sql)
		or die_message("query '$sql' failure.");
	return $result;
}

function db_query($sql)
{
	$result = db_exec($sql);
	
	$rows = array();
	while ($row = pg_fetch_array($result)) {
		$rows[] = $row;
	}
	pg_free_result($result);
	
	return $rows;
}

/*
create table page (id serial primary key, name text not null, lastmod integer not null);
create table link (page_id integer not null, ref_id integer not null);
grant all on page,link,page_id_seq to apache;
*/
?>
