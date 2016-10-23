@charset "UTF-8";

/*!
 * PukiWiki - Yet another WikiWikiWeb clone.
 * Copyright (C)
 *   2002-2016 PukiWiki Development Team
 *   2001-2002 Originally written by yu-ji
 * License: GPL v2 or (at your option) any later version
 *
 * tDiary-css-wrapper
 */

/* ------------------------------------------ */
/* PukiWiki abstruction CSS for tDiary themes */

/* <--> Expand textarea height (for editing only) */
/* <<-- textarea with not-so-long margin          */
form.update textarea {
	height: 25em;
	margin-left:   1em;
	margin-bottom: 0;
}

/* >--< Shrink textarea width (for #memo, etc) */
form textarea { width: 30em }

/* Image border = 0 */
img { border: 0 }


/* --------------------- */
/* PukiWiki original CSS */

thead td.style_td,
tfoot td.style_td {
	color:inherit;
	background-color:#D0D8E0;
}
thead th.style_th,
tfoot th.style_th {
	color:inherit;
	background-color:#E0E8F0;
}
.style_table {
	padding:0px;
	border:0px;
	margin:auto;
	text-align:left;
	color:inherit;
	background-color:#CCD5DD;
}
.style_th {
	padding:5px;
	margin:1px;
	text-align:center;
	color:inherit;
	background-color:#EEEEEE;
}
.style_td {
	padding:5px;
	margin:1px;
	color:inherit;
	background-color:#EEF5FF;
}

ul.list1 { list-style-type:disc; }
ul.list2 { list-style-type:circle; }
ul.list3 { list-style-type:square; }
ol.list1 { list-style-type:decimal; }
ol.list2 { list-style-type:lower-roman; }
ol.list3 { list-style-type:lower-alpha; }

div.ie5 { text-align:center; }

/* NoSuchPage? */
span.noexists {
	color:inherit;
	background-color:#FFFACC;
}

.small { font-size:80%; }

/* Not found, Remove? */
/*
.super_index {
	color:#DD3333;
	background-color:inherit;
	font-weight:bold;
	font-size:60%;
	vertical-align:super;
}
*/

/* for tDiary themes */
a.note_super {}

div.jumpmenu {
	font-size:60%;
	text-align:right;
}

/* for tDiary themes */
hr.full_hr {}
hr.note_hr { display:none }

span.size1 {
	font-size:xx-small;
	line-height:130%;
	text-indent:0px;
	display:inline;
}
span.size2 {
	font-size:x-small;
	line-height:130%;
	text-indent:0px;
	display:inline;
}
span.size3 {
	font-size:small;
	line-height:130%;
	text-indent:0px;
	display:inline;
}
span.size4 {
	font-size:medium;
	line-height:130%;
	text-indent:0px;
	display:inline;
}
span.size5 {
	font-size:large;
	line-height:130%;
	text-indent:0px;
	display:inline;
}
span.size6 {
	font-size:x-large;
	line-height:130%;
	text-indent:0px;
	display:inline;
}
span.size7 {
	font-size:xx-large;
	line-height:130%;
	text-indent:0px;
	display:inline;
}

/* html.php/catbody() */
strong.word0 {
	background-color:#FFFF66;
	color:black;
}
strong.word1 {
	background-color:#A0FFFF;
	color:black;
}
strong.word2 {
	background-color:#99FF99;
	color:black;
}
strong.word3 {
	background-color:#FF9999;
	color:black;
}
strong.word4 {
	background-color:#FF66FF;
	color:black;
}
strong.word5 {
	background-color:#880000;
	color:white;
}
strong.word6 {
	background-color:#00AA00;
	color:white;
}
strong.word7 {
	background-color:#886800;
	color:white;
}
strong.word8 {
	background-color:#004699;
	color:white;
}
strong.word9 {
	background-color:#990099;
	color:white;
}

/* html.php/edit_form() */
.edit_form { clear:both; }

/* pukiwiki.skin.php */
div#header {
	padding:0px;
	margin:0px;
}

div#navigator {
	clear:both;
	padding:4px 0px 0px 0px;
	margin:0px;
}

td.menubar {
	width:9em;
	vertical-align:top;
}

div#menubar {
	width:9em;
	padding:0px;
	margin:4px;
	word-break:break-all;
	font-size:90%;
	overflow:hidden;
}

div#menubar ul {
	margin:0px 0px 0px .5em;
	padding:0px 0px 0px .5em;
}

div#menubar ul li { line-height:110%; }

div#menubar h4 { font-size:110%; }

/* for tDiary themes */
div.pkwk_body { padding:0px; }

div#note {
	clear:both;
	padding:0px;
	margin:0px;
}

div#attach {
	clear:both;
	padding:0px;
	margin:0px;
}

div#toolbar {
	clear:both;
	padding:0px;
	margin:0px;
	text-align:right;
}

div#lastmodified {
	font-size:80%;
	padding:0px;
	margin:0px;
}

/* for tDiary theme */
div#related {
	font-size:80%;
	padding:0px;
	margin:0px 0px 0px 0px;
}

div#footer {
	font-size:70%;
	padding:0px;
	margin:16px 0px 0px 0px;
}

div#banner {
	float:right;
	margin-top:24px;
}

div#preview {
	color:inherit;
	background-color:#F5F8FF;
}

img#logo {
	float:left;
	margin-right:20px;
}

/* aname.inc.php */
.anchor {}
.anchor_super {
	font-size:xx-small;
	vertical-align:super;
}

/* br.inc.php */
br.spacer {}

/* calendar*.inc.php */
.style_calendar {
	padding:0px;
	border:0px;
	margin:3px;
	color:inherit;
	background-color:#CCD5DD;
	text-align:center;
}
.style_td_caltop {
	padding:5px;
	margin:1px;
	color:inherit;
	background-color:#EEF5FF;
	font-size:80%;
	text-align:center;
}
.style_td_today {
	padding:5px;
	margin:1px;
	color:inherit;
	background-color:#FFFACC;
	text-align:center;
}
.style_td_sat {
	padding:5px;
	margin:1px;
	color:inherit;
	background-color:#DDE5FF;
	text-align:center;
}
.style_td_sun {
	padding:5px;
	margin:1px;
	color:inherit;
	background-color:#FFEEEE;
	text-align:center;
}
.style_td_blank {
	padding:5px;
	margin:1px;
	color:inherit;
	background-color:#EEF5FF;
	text-align:center;
}
.style_td_day {
	padding:5px;
	margin:1px;
	color:inherit;
	background-color:#EEF5FF;
	text-align:center;
}
.style_td_week {
	padding:5px;
	margin:1px;
	color:inherit;
	background-color:#DDE5EE;
	font-size:80%;
	font-weight:bold;
	text-align:center;
}

/* calendar_viewer.inc.php */
div.calendar_viewer {
	color:inherit;
	background-color:inherit;
	margin-top:20px;
	margin-bottom:10px;
	padding-bottom:10px;
}
span.calendar_viewer_left {
	color:inherit;
	background-color:inherit;
	float:left;
}
span.calendar_viewer_right {
	color:inherit;
	background-color:inherit;
	float:right;
}

/* clear.inc.php */
.clear {
	margin:0px;
	clear:both;
}

/* counter.inc.php */
div.counter { font-size:70%; }

/* diff.inc.php */
span.diff_added {
	color:blue;
	background-color:inherit;
}

span.diff_removed {
	color:red;
	background-color:inherit;
}

/* hr.inc.php */
hr.short_line {
	text-align:center;
	width:80%;
	border-style:solid;
	border-color:#CCD5DD;
	border-width:1px 0px;
}

/* include.inc.php */
h5.side_label { text-align:center; }

/* navi.inc.php */
ul.navi {
	margin:0px;
	padding:0px;
	text-align:center;
}
li.navi_none {
	display:inline;
	float:none;
}
li.navi_left {
	display:inline;
	float:left;
	text-align:left;
}
li.navi_right {
	display:inline;
	float:right;
	text-align:right;
}

/* new.inc.php */
span.comment_date { font-size:x-small; }
span.new1 {
	color:red;
	background-color:transparent;
	font-size:x-small;
}
span.new5 {
	color:green;
	background-color:transparent;
	font-size:xx-small;
}

/* popular.inc.php */
span.counter { font-size:70%; }

/* recent.inc.php,showrss.inc.php */

/* ref.inc.php */
div.img_margin {
	margin-left:32px;
	margin-right:32px;
}

/* vote.inc.php */
td.vote_label {
	color:inherit;
	background-color:#DDE5EE;
}
td.vote_td1 {
	color:inherit;
	background-color:#FFEEEE;
}
td.vote_td2 {
	color:inherit;
	background-color:#DDE5FF;
}

@media print {
  img#logo,
  div#navigator,
  div#menubar,
  td.menubar,
  div#related,
  div#attach,
  div#toolbar {
    display: none;
  }
}
