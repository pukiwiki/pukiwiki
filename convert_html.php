<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: convert_html.php,v 1.11 2003/02/03 12:46:56 panda Exp $
//

function &convert_html(&$lines)
{
	global $script,$vars,$digest;
	static $contents_id = 0;
	
	$digest = md5(join('',get_source($vars['page'])));
	
	$body = new Body(++$contents_id);
	$body->parse($lines);
	$ret = $body->toString();
	
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
	var $level,$top,$id;
	
	function Heading($text, &$contents)
	{
		parent::Block();
		preg_match("/^(\*{1,3})\s*(.*)$/",$text,$out) or die("Heading");
		$this->level = strlen($out[1]) + 1;
		list($this->top,$this->id) = $contents->getAnchor($out[2], $this->level);
		$this->last =& $this->insert(new Inline($out[2]));
	}
	function canContain(&$obj)
	{
		return FALSE;
	}
	function toString()
	{
		return $this->wrap(parent::toString().$this->top,'h'.$this->level," id=\"{$this->id}\"");
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
		$obj =& new ListElement($obj, $this->level, $this->tag2); // wrap
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
	function ListElement(&$obj,$level,$head)
	{
		parent::Block();
		$this->level = $level;
		$this->head = $head;
		$this->insert($obj);
		$this->last = NULL;
		if (isset($obj->last) and is_object($obj->last)) {
			$this->last =& $obj->last;
		}
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
			array_unshift($this->elements,new Inline("\n<dt>".inline2(inline($out[2]))."</dt>\n"));
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
		$this->last =& $this->insert(new Paragraph($this->text, ' class="quotation"'));
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
			if (is_a($this->last,'Paragraph')) {
				$this->last->insert($obj->elements[0]->elements[0]);
			} else {
				$this->last =& $this->insert($obj->elements[0]);
			}
			return $this->last;
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

class TableCell extends Block
{
	var $tag; // {td|th}
	var $colspan;
	var $rowspan;
	var $style; // is array('width'=>, 'align'=>...);
	
	function TableCell($text,$is_template=FALSE) {
		parent::Block();
		$this->tag = 'td';
		$this->colspan = 1;
		$this->rowspan = 1;
		$this->style = array();
		
		if (preg_match("/^(LEFT|CENTER|RIGHT):(.*)$/",$text,$out)) {
			$this->style['align'] = 'text-align:'.strtolower($out[1]).';';
			$text = $out[2];
		}
		if ($is_template) {
			if (is_numeric($text)) {
				$this->style['width'] = "width:{$text}px;";
			}
		}
		if ($text == '>') {
			$this->colspan = 0;
		}
		else if ($text == '~') {
			$this->rowspan = 0;
		}
		else if (substr($text,0,1) == '~') {
			$this->tag = 'th';
			$text = substr($text,1);
		}
		$this->last =& $this->insert(new Inline($text));
	}
	function setStyle(&$style) {
		foreach ($style as $key=>$value) {
			if (!array_key_exists($key,$this->style)) {
				$this->style[$key] = $value;
			}
		}
	}
	function toString() {
		if ($this->rowspan == 0 or $this->colspan == 0) {
			return '';
		}
		$param = array();
		if ($this->rowspan > 1) {
			$param[] = 'rowspan="'.$this->rowspan.'"';
		}
		if ($this->colspan > 1) {
			$param[] = 'colspan="'.$this->colspan.'"';
			unset($this->style['width']);
		}
		if (count($this->style)) {
			$param[] = 'style="'.join(' ',$this->style).'"';
		}
		return $this->wrap(parent::toString(),$this->tag,
			" class=\"style_{$this->tag}\" ".join(' ',$param));
	}
}
class Table extends Block
{ // |
	var $type,$types;
	var $level;
	
	function Table($text)
	{
		parent::Block();
		if (!preg_match("/^\|(.+)\|([hHfFcC]?)$/",$text,$out)) {
			$this = new Inline($text);
			return;
		}
		$cells = explode('|',$out[1]);
		$this->level = count($cells);
		$this->type = strtolower($out[2]);
		$this->types = array($this->type);
		$is_template = ($this->type == 'c');
		$row = array();
		foreach ($cells as $cell) {
			$row[] = new TableCell($cell,$is_template);
		}
		$this->elements[] = $row;
		$this->last =& $this;
	}
	function canContain(&$obj)
	{
		return is_a($obj, 'Table') and ($obj->level == $this->level);
	}
	function &insert(&$obj)
	{
		$this->elements[] = $obj->elements[0];
		$this->types[] = $obj->type;
		return $this;
	}
	function toString()
	{
		// rowspanを設定(下から上へ)
		for ($ncol = 0; $ncol < $this->level; $ncol++) {
			$rowspan = 1;
			foreach (array_reverse(array_keys($this->elements)) as $nrow) {
				$row =& $this->elements[$nrow];
				if ($row[$ncol]->rowspan == 0) {
					$rowspan++;
				}
				else {
					$row[$ncol]->rowspan = $rowspan;
					while (--$rowspan) { // 行種別を継承する
						$this->types[$nrow + $rowspan] = $this->types[$nrow];
					}
					$rowspan = 1;
				}
			}
		}
		// colspan,styleを設定
		$stylerow = NULL;
		foreach (array_keys($this->elements) as $nrow) {
			$row =& $this->elements[$nrow];
			if ($this->types[$nrow] == 'c') {
				$stylerow =& $row;
			}
			$colspan = 1;
			foreach (array_keys($row) as $ncol) {
				if ($row[$ncol]->colspan == 0) {
					$colspan++;
				}
				else {
					$row[$ncol]->colspan = $colspan;
					if ($stylerow !== NULL) {
						$row[$ncol]->setStyle($stylerow[$ncol]->style);
						while (--$colspan) { // 列スタイルを継承する
							$row[$ncol - $colspan]->setStyle($stylerow[$ncol]->style);
						}
					}
					$colspan = 1;
				}
			}
		}
		// テキスト化
		$string = '';
		$parts = array('h'=>'thead',''=>'tbody','f'=>'tfoot');
		foreach ($parts as $type=>$part) {
			$part_string = '';
			foreach (array_keys($this->elements) as $nrow) {
				if ($this->types[$nrow] != $type) {
					continue;
				}
				$row =& $this->elements[$nrow];
				$row_string = '';
				foreach (array_keys($row) as $ncol) {
					$row_string .= $row[$ncol]->toString();
				}
				$part_string .= $this->wrap($row_string,'tr');
			}
			$string .= $this->wrap($part_string,$part);
		}
		return <<<EOD
<div class="ie5">
 <table class="style_table" cellspacing="1" border="0">
  $string
 </table>
</div>
EOD;
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
class Align extends Block
{ // LEFT:/CENTER:/RIGHT:
	var $align;
	
	function Align($align)
	{
		$this->align = $align;
	}
	function &insert(&$obj)
	{
		if (is_a($obj,'Inline')) {
			$obj =& $obj->toPara();
		}
		return parent::insert($obj);
	}
	function toString()
	{
		$string = parent::toString();
		if ($string != '') {
			if (preg_match('/^(\s*<[^>]+style=")(.+)$"/',$string,$matches)) {
				$string = $matches[1]."text-align:{$this->align};".$matches[2];
			}
			else {
				$string = preg_replace('/^(\s*<[a-z]+)/', '$1 style="text-align:'.$this->align.';"',$string);
			}
		}
		return $string;
	}
}
class Body extends Block
{ // Body
	var $id,$count,$top,$contents,$last;
	
	function Body($id)
	{
		global $top;
		
		$this->id = $id;
		$this->count = 0;
		$this->top = "<a href=\"#contents_$id\">$top</a>";
		$this->contents = new Block();
		$this->last =& $this->contents;
		parent::Block();
	}
	function parse(&$lines)
	{
		$last =& $this;
		
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
				$last =& $this;
			}
			else if (substr($line,0,4) == '----') { // HRule
				$last =& $this->insert(new HRule());
			}
			else if ($head == '*') { // Heading
				$last =& $this->insert(new Heading($line, $this));
			}
			else if ($head == ' ' or $head == "\t") { // Pre
				$last =& $last->add(new Pre($line));
			}
			else {
				if (substr($line,-1) == '~') {
					$line .= "\r";
				}
				if      ($head == '-') { // UList
					$last =& $last->add(new UList($line)); // inline
				}
				else if ($head == '+') { // OList
					$last =& $last->add(new OList($line)); // inline
				}
				else if ($head == ':') { // DList
					$last =& $last->add(new DList($line)); // inline
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
	}
	function getAnchor($text,$level)
	{
		$id = "content_{$this->id}_{$this->count}";
		$this->count++;
		$this->last =& $this->last->add(new Contents_UList($text,$this->id,$level,$id));
		return array(&$this->top,$id);
	}
	function getContents()
	{
		$contents  = "<a id=\"contents_{$this->id}\"></a>";
		$contents .= $this->contents->toString();
		return $contents;
	}
	function &insert(&$obj)
	{
		if (is_a($obj,'Inline')) {
			$obj =& $obj->toPara();
		}
		return parent::insert($obj);
	}
	function toString()
	{
		global $vars;
		
		$text = parent::toString();
		
		// #contents
		$text = preg_replace("/<p>#contents<\/p>/",$this->getContents(),$text);
		
		// 関連するページ
		// <p>のときは行頭から、<del>のときは他の要素の子要素として存在
		$text = preg_replace('/<(p|del)>#related<\/\1>/e','make_related($vars[\'page\'],\'$1\')',$text);
		
		return $text;
	}
}
class Contents_UList extends _List
{
	function Contents_UList($text,$id,$level,$id)
	{
		// テキストのリフォーム
		// 行頭\nで整形済みを表す ... X(
		$text = "\n<a href=\"#$id\">".strip_htmltag(inline2(inline($text,TRUE)))."</a>\n";
		parent::_List('ul', 'li', --$level, $text);
	}
}
?>
