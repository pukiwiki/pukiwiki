<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: convert_html.php,v 1.2 2003/01/31 01:49:35 panda Exp $
//

function &convert_html(&$lines)
{
	global $script,$vars,$digest;
	static $contents_id = 0;

	$digest = md5(join('',get_source($vars['page'])));

	$contents = new Contents(++$contents_id);

	$body = new Body();
	$last =& $body;

	foreach ($lines as $line) {
		if (substr($line,0,2) == '//') { //コメントは処理しない
			continue;
		}
		
		$line = rtrim($line);
		
		$align = '';
		if (preg_match('/^(LEFT|CENTER|RIGHT):(.*)$/',$line,$matches)) {
			$last =& $last->add(new Align(strtolower($matches[1]))); // <div style="text-align:...">
			if ($matches[2] == '') {
				continue;
			}
			$line = $matches[2];
		}
		
		// 行頭文字
		$head = substr($line,0,1);
		
		if ($line == '') { // 空行
			$last =& $body;
		}
		else if (substr($line,0,4) == '----') { // HRule
			$last =& $body->insert(new HRule());
		}
		else if ($head == ' ' or $head == "\t") { // Pre
			$last =& $last->add(new Pre($line));
		}
		else if ($head == '*') { // Heading
			$last =& $body->insert(new Heading($line, $contents));
		}
		else {
			if (substr($line,-1) == '~') {
				$line .= "\r";
			}
			if      ($head == '-') { // UList
				$last =& $last->add(new UList($line));
			}
			else if ($head == '+') { // OList
				$last =& $last->add(new OList($line));
			}
			else if ($head == ':') { // DList
				$last =& $last->add(new DList($line));
			}
			else if ($head == '|') { // Table
				$last =& $last->add(new Table($line));
			}
			else if ($head == ',') { // Table(YukiWiki互換)
				$last =& $last->add(new YTable($line));
			}
			else if ($head == '>') { // BrockQuote
				$last =& $last->add(new BQuote($line));
			}
			else if ($head == '<') { // BlockQuote end
				$last =& bq_end($last, $line);
			}
			else if ($head == '#') { // Div
				$last =& $last->add(new Div($line));
			}
			else { // 通常文字列
				$last =& $last->add(new Inline($line));
			}
		}
	}
	$ret = $body->toString();
	
	// #contents
	$ret = preg_replace("/<p>#contents<\/p>/",$contents->getContents(),$ret);
	
	// 関連するページ
	$ret = preg_replace('/#related/e','make_related($vars[\'page\'],TRUE)',$ret);
	
	return $ret;
}

class Element
{
	var $parent;
	
	function setParent(&$parent)
	{
		$this->parent =& $parent;
	}
}

class Inline extends Element
{ // インライン要素
	var $text;
	
	function Inline($text)
	{
		if (substr($text,0,1) == '~') { // 行頭~。パラグラフ開始
			$parent =& $this->parent;
			$this = new Paragraph(substr($text,1));
			$this->setParent($parent);
		}
		else {
			$this->text = (substr($text,0,1) == "\n") ? $text : inline2(inline($text));
		}
	}
	function &add(&$obj)
	{
		return $this->insert($obj);
	}
	function &insert(&$obj)
	{
		return $this->parent->add($obj);
	}
	function toString()
	{
		return $this->text;
	}
	function toPara($class = '')
	{
		$obj = new Paragraph('',$class);
		$obj->insert($this);
		$this->setParent($obj);
		return $obj;
	}
}
class Block extends Element
{ // ブロック要素
	var $elements; // 要素の配列
	
	function Block() {
		$this->elements = array();
	}
	
	function &add(&$obj) // エレメントを追加
	{
		if ($this->canContain($obj)) {
			return $this->insert($obj);
		}
		return $this->parent->add($obj);
	}
	function &insert(&$obj)
	{
		$obj->setParent($this);
		$this->elements[] =& $obj;
		if (isset($obj->last) and is_object($obj->last)) {
			return $obj->last;
		}
		return $obj;
	}
	function canContain($obj)
	{
		return TRUE;
	}
	function toString()
	{
		$ret = '';
		if (isset($this->elements) and count($this->elements) > 0) {
			foreach ($this->elements as $obj) {
				$ret .= $obj->toString();
			}
		}
		return $ret;
	}
	function wrap($string, $tag, $param = '')
	{
		return  ($string == '') ? '' : "\n<$tag$param>$string</$tag>\n";
	}
}
class Body extends Block
{ // Body
	
	function &insert(&$obj)
	{
		if (is_a($obj,'Inline')) {
			$obj =& $obj->toPara();
		}
		return parent::insert($obj);
	}
}
class Paragraph extends Block
{ // 段落
	var $class;
	
	function Paragraph($text, $class = '')
	{
		parent::Block();
		$this->class = $class;
		if ($text == '') {
			return;
		}
		if (substr($text,0,1) == '~') {
			$text = substr($text,1);
		}
		$this->elements[] =& new Inline($text);
	}
	function canContain($obj)
	{
		return is_a($obj,'Inline');
	}
	function toString()
	{
		return $this->wrap(parent::toString(), 'p', $this->class);
	}
}

class Heading extends Block
{ // *
	var $level;
	function Heading($text, &$contents)
	{
		parent::Block();
		preg_match("/^(\*{1,3})\s*(.*)$/",$text,$out) or die("Heading");
		$this->level = strlen($out[1]) + 1;
		list($this->text,$this->contents_str) = $contents->getAnchor($out[2], $this->level);
	}
	function canContain(&$obj)
	{
		return FALSE;
	}
	function toString()
	{
		return $this->wrap($this->text,'h'.$this->level, $this->contents_str);
	}
}
class HRule extends Block
{ // ----
	function canContain(&$obj)
	{
		return FALSE;
	}
	function toString()
	{
		global $hr;
		
		return $hr;
	}
}
class _List extends Block
{
	var $level;
	var $step, $margin, $left_margin;
	
	function _List($tag, $tag2, $level, $text)
	{
		parent::Block();
		//マージンを取得
		$var_margin = "_{$tag}_margin";
		$var_left_margin = "_{$tag}_left_margin";
		global $$var_margin, $$var_left_margin;
		$this->margin = $$var_margin;
		$this->left_margin = $$var_left_margin;

		//初期化
		$this->tag = $tag;
		$this->tag2 = $tag2;
		$this->level = $level;
		
		if ($text != '') {
			$this->insert(new Inline($text));
		}
	}

	function canContain(&$obj)
	{
		return is_a($obj, '_List') ? ($this->tag == $obj->tag and $this->level == $obj->level) : TRUE;
	}
	function setParent(&$parent)
	{
		parent::setParent($parent);
		$this->step = $this->level;
		if (isset($parent->parent) and is_a($parent->parent,'_List')) {
			$this->step -= $parent->parent->level; 
		}
	}
	function &insert(&$obj)
	{
		if (is_a($obj, get_class($this))) {
			for ($n = 0; $n < count($obj->elements); $n++) {
				$this->last =& parent::insert($obj->elements[$n]);
			}
			return $this->last;
		}
		else {
			$obj =& new ListElement($obj, $this->level, $this->tag2); // wrap
		}
		$this->last =& $obj;
		return parent::insert($obj);
	}
	function toString($param='')
	{
		global $_list_left_margin, $_list_margin, $_list_pad_str;
		
		$margin = $_list_margin * $this->step;
		if ($this->level == $this->step) {
			$margin += $_list_left_margin;
		}
		$style = sprintf($_list_pad_str,$this->level,$margin,$margin);
		return $this->wrap(Block::toString(),$this->tag,$style.$param);
	}
}
class ListElement extends Block
{
	function ListElement($obj,$level,$head)
	{
		parent::Block();
		$this->level = $level;
		$this->head = $head;
		$this->insert($obj);
	}
	function canContain(&$obj)
	{
		return !(is_a($obj, '_List') and ($obj->level <= $this->level));
	}
	function toString()
	{
		return $this->wrap(parent::toString(), $this->head);
	}
}
class UList extends _List
{ // -
	function UList($text)
	{
		preg_match("/^(\-{1,3})([\n]?.*)$/",$text,$out) or die("UList $text");
		parent::_List('ul', 'li', strlen($out[1]), $out[2]);
	}
}
class OList extends _List
{ // +
	function OList($text)
	{
		preg_match("/^(\+{1,3})(.*)$/",$text,$out) or die("OList");
		parent::_List('ol', 'li', strlen($out[1]), $out[2]);
	}
}
class DList extends _List
{ // :
	function DList($text)
	{
		if (!preg_match("/^(:{1,3})(.*)\|(.*)$/",$text,$out)) {
			$this = new Inline($text);
			return;
		}
		parent::_List('dl', 'dd', strlen($out[1]), $out[3]);
		if ($out[2] != '') {
			array_unshift($this->elements,new Inline("\n<dt>".inline($out[2])."</dt>\n"));
		}
	}
}
class BQuote extends Block
{ // >
	var $level;
	
	function BQuote($text)
	{
		parent::Block();
		preg_match("/^(\>{1,3})(.*)$/",$text,$out) or die("BQuote");
		$this->level = strlen($out[1]);
		$this->text = $out[2];
		$this->insert(new Paragraph($this->text, ' class="quotation"'));
	}
	function canContain(&$obj)
	{
		if (!is_a($obj, get_class($this))) {
			return TRUE;
		}
		return ($this->level <= $obj->level);
	}
	function &insert(&$obj)
	{
		if (is_a($obj, 'BQuote') and $obj->level == $this->level) {
			$obj =& $obj->elements[0];
		}
		else if (is_a($obj,'Inline')) {
			$obj = $obj->toPara('quotation');
		}
		$this->last =& $obj;
		return parent::insert($obj);
	}
	function toString()
	{
		return $this->wrap(parent::toString(),'blockquote');
	}
}
function &bq_end(&$last, $text)
{
	preg_match("/^(\<{1,3})(.*)$/",$text,$out) or die("bq_end");
	$level = strlen($out[1]);
	$parent =& $last;
	while (is_object($parent)) {
		if (is_a($parent,'BQuote') and $parent->level == $level) {
			return $parent->parent->insert(new Inline($out[2]));
		}
		$parent =& $parent->parent;
	}
	return $last->insert(new Inline($text));
}
class Table extends Block
{ // |
	var $col,$head,$foot;
	var $level;
	
	function Table($text)
	{
		parent::Block();
		if (!preg_match("/^\|(.+)\|([hHfFcC]?)$/",$text,$out)) {
			$this = new Inline($text);
			return;
		}
		$this->elements = array();
		$cells = explode('|',$out[1]);
		$this->level = count($cells);
		$char = strtolower($out[2]);
		if ($char == 'c') {
			$this->col =& new Col($cells);
		}
		else {
			$this->insert(new Row($cells,($char == 'h' ? 0 : ($char == 'f' ? 1 : 2))));
		}
	}
	function canContain(&$obj)
	{
		return is_a($obj, 'Table') and $obj->level == $this->level;
	}
	function &insert(&$obj)
	{
		if (is_a($obj, 'Table')) {
			if (isset($obj->col) and is_object($obj->col)) {
				$this->col = $obj->col;
				return $this;
			}
			$obj =& $obj->elements[0];
			$last = count($this->elements) - 1;
			for ($n = 0; $n < count($obj->elements); $n++) {
				if ($obj->elements[$n] != '~') {
					continue;
				}
				$obj->type = $this->elements[$last]->type;
				for ($m = $last; $m >= 0; $m--) {
					if ($this->elements[$m]->elements[$n] == '~') {
						continue;
					}
					$this->elements[$m]->row[$n]++;
					break;
				}
			}
		}
		$this->elements[] = $obj;
		return $this;
	}
	function toString()
	{
		$col = NULL;
		if (isset($this->col) and is_object($this->col)) {
			$col =& $this->col;
		}
		$string = $col ? $this->col->toString() : '';
		$part = array(0=>'thead',1=>'tfoot',2=>'tbody');
		foreach ($part as $type=>$str) {
			$tmp = '';
			foreach ($this->elements as $row) {
				if ($row->type != $type) {
					continue;
				}
				$tmp .= $row->toString($col);
			}
			if ($tmp != '') {
				$string .= $this->wrap($tmp,$str);
			}
		}
		if ($string != '') {
			$string = <<<EOD

<div class="ie5">
 <table class="style_table" cellspacing="1" border="0">
  $string
 </table>
</div>
EOD;
		}
		return $string;
	}
}
class Row extends Block
{
	var $col,$row,$type;
	
	function Row($cells,$type='')
	{
		parent::Block();
		$this->elements = $cells;
		$this->type = $type;
		$span = 1;
		for ($n = 0; $n < count($cells); $n++) {
			$this->row[$n] = 1;
			if ($cells[$n] == '>') {
				$this->col[$n] = 0;
				$span++;
			}
			else {
				$this->col[$n] = $span;
				$span = 1;
			}
		}
	}
	function toString($obj)
	{
		$cells = '';
		for ($n = 0; $n < count($this->elements); $n++) {
			$cell = $this->elements[$n];
			if ($cell == '>' or $cell == '~') {
				continue;
			}
			$row = $col = '';
			if ($this->row[$n] > 1) {
				$row = " rowspan=\"{$this->row[$n]}\"";
			}
			if ($this->col[$n] > 1) {
				$col = " colspan=\"{$this->col[$n]}\"";
			}
			$align = $width = '';
			if (is_object($obj)) {
				$align = $obj->align[$n];
				if ($this->col[$n] == 1) {
					$width = $obj->width[$n];
				}
			}
			if (preg_match("/^(LEFT|CENTER|RIGHT):(.*)$/",$cell,$out)) {
				$align = strtolower($out[1]);
				$cell = $out[2];
			}
			if (preg_match('/^~(.+)$/',$cell,$matches)) {
				$tag = 'th'; $cell = $matches[1];
			}
			else {
				$tag = 'td';
			}
			$style = $width == '' ? '' : 'width:'.$width.'px;';
			$style.= $align == '' ? '' : 'text-align:'.$align.';';
			$style = $style == '' ? '' : ' style="'.$style.'"';
			$cells .= "\n<$tag class=\"style_$tag\"$style$row$col>".inline2(inline($cell))."</$tag>\n";
		}
		return $this->wrap($cells,'tr');
	}
}
class Col extends Row
{
	var $width,$align;
	
	function Col($cells)
	{
		parent::Row($cells);
		$align = $width = '';
		for ($n = count($this->elements) - 1; $n >= 0; $n--) {
			if ($cells[$n] == '') {
				$align = $width = '';
			}
			else if ($cells[$n] != '>') {
				if (preg_match("/^(LEFT|CENTER|RIGHT):(.*)$/",$cells[$n],$out)) {
					$align = strtolower($out[1]);
					$width = htmlspecialchars($out[2]);
				}
			}
			$this->align[$n] = $align;
			$this->width[$n] = $width;
		}
	}
	function toString()
	{
		$cells = '';
		for ($n = 0; $n < count($this->elements); $n++) {
			$cell = $this->elements[$n];
			if ($cell == '>') {
				continue;
			}
			$span = " span=\"{$this->col[$n]}\"";
			$align = $this->align[$n] == '' ? '' : ' align="'.$this->align[$n].'"';
			$width = $this->width[$n] == '' ? '' : ' width="'.$this->width[$n].'"';
			$cells .= "\n<colgroup$span$align$width></colgroup>\n";
		}
		return $cells;
	}
}
class YTable extends Block
{ // ,
	var $col;
	
	function YTable($text)
	{
		parent::Block();
		if (!preg_match_all('/("[^"]*(?:""[^"]*)*"|[^,]*),/',"$text,",$out)) {
			$this = new Inline($text);
			return;
		}
		array_shift($out[1]);
		$_value = array();
		foreach ($out[1] as $val) {
			$_value[] = preg_match('/^"(.*)"$/',$val,$matches) ? str_replace('""','"',$matches[1]) : $val;
		}
		$align = array();
		$value = array();
		foreach($_value as $val) {
			if (preg_match('/^(\s+)?(.+?)(\s+)?$/',$val,$matches)) {
				$align[] =($matches[1] != '') ?
					((array_key_exists(3,$matches) and $matches[3] != '') ? ' style="text-align:center"' : ' style="text-align:right"') : '';
				$value[] = $matches[2];
			}
			else {
				$align[] = '';
				$value[] = $val;
			}
		}
		$this->col = count($value);
		$colspan = array();
		foreach ($value as $val) {
			$colspan[] = ($val == '==') ? 0 : 1;
		}
		$str = '';
		for ($i = 0; $i < count($value); $i++) {
			if ($colspan[$i]) {
				while ($i + $colspan[$i] < count($value) and $value[$i + $colspan[$i]] == '==') {
					$colspan[$i]++;
				}
				$colspan[$i] = ($colspan[$i] > 1) ? " colspan=\"{$colspan[$i]}\"" : '';
				$str .= "<td class=\"style_td\"{$align[$i]}{$colspan[$i]}>".inline2(inline($value[$i])).'</td>';
			}
		}
		$this->elements[] = $str;
	}
	function canContain(&$obj)
	{
		return is_a($obj, 'YTable') and $obj->col == $this->col;
	}
	function &insert(&$obj)
	{
		$this->elements[] = $obj->elements[0];
		return $this;
	}
	function toString()
	{
		$rows = '';
		foreach ($this->elements as $str) {
			$rows .= "\n<tr class=\"style_tr\">$str</tr>\n";
		}
		$string = <<<EOD

<div class="ie5">
 <table class="style_table" cellspacing="1" border="0">
  $rows
 </table>
</div>

EOD;
		return $string;
	}
}
class Pre extends Block
{ // ' '
	
	function Pre($text)
	{
		parent::Block();
		$tab = 8;
		while (preg_match('/^([^\t]*)(\t+)(.+)$/',$text,$m)) {
			$text = $m[1].str_repeat(' ',strlen($m[2]) * $tab - strlen($m[1]) % $tab).$m[3];
		}
		$this->elements[] = htmlspecialchars($text,ENT_NOQUOTES);
	}
	function canContain(&$obj)
	{
		return is_a($obj, 'Pre');
	}
	function &insert(&$obj)
	{
		$this->elements[] = $obj->elements[0];
		return $this;
	}
	function toString()
	{
		return $this->wrap(join("\n",$this->elements),'pre');
	}
}
class Div extends Block
{ // #
	var $text;
	
	function Div($text)
	{
		parent::Block();
		$this->text = $text;
	}
	function canContain(&$obj)
	{
		return FALSE;
	}
	function toString()
	{
		if (preg_match("/^\#([^\(]+)(.*)$/",$this->text,$out) and exist_plugin_convert($out[1])) {
			if ($out[2]) {
				$_plugin = preg_replace("/^\#([^\(]+)\((.*)\)$/ex","do_plugin_convert('$1','$2')",$this->text);
			}
			else {
				$_plugin = preg_replace("/^\#([^\(]+)$/ex","do_plugin_convert('$1','$2')",$this->text);
			}
			$text = "\t$_plugin";
		}
		else {
			$text = "\n<p>".htmlspecialchars($this->text)."</p>\n";
		}
		return $text;
	}
}
class Align extends Body
{ // LEFT:/CENTER:/RIGHT:
	var $align;
	
	function Align($align)
	{
		$this->align = $align;
	}
	function toString()
	{
		$string = parent::toString();
		if ($string != '') {
			if (preg_match('/^(.+)style="(.+)$/',$string,$matches)) {
				$string = $matches[1].'style="text-align:'.$this->align.'; '.$matches[2];
			}
			else {
				$string = preg_replace('/(<[a-z]+)/', '$1 style="text-align:'.$this->align.';"',$string);
			}
		}
		return $string;
	}
}
//見出しの一覧関係
class Contents
{
	var $id,$count,$top,$contents,$last;
	
	function Contents($id)
	{
		global $top;
		
		$this->id = $id;
		$this->count = 0;
		$this->top = "<a href=\"#contents_$id\">$top</a>";
		$this->contents =& new Block();
		$this->last =& $this->contents;
	}
	function getAnchor($text,$level)
	{
		$content_str = "content_{$this->id}_{$this->count}";
		$this->last =& $this->last->add(new Contents_UList($text,$this->id,$level,$content_str));
		$this->count++;
		return array(inline2(inline($text)).$this->top," id=\"{$content_str}\"");
	}
	function getContents()
	{
		global $strip_link_wall;
		
		$contents  = "<a id=\"contents_{$this->id}\"></a>";
		$contents .= $this->contents->toString();
		if ($strip_link_wall) {
			$contents = preg_replace("/\[\[([^\]]+)\]\]/","$1",$contents);
		}
		return $contents;
	}
}
class Contents_UList extends _List
{ // -
	var $id;
	
	function Contents_UList($text,$id,$level,$content_str)
	{
		$this->id = $id;
		// テキストのリフォーム
		// 行頭\nで整形済みを表す ... X(
		$text = "\n<a href=\"#{$content_str}\">".
			strip_htmltag(make_user_rules(inline($text,TRUE))).
			"</a>\n";
		parent::_List('ul', 'li', --$level, $text);
	}
}
?>
