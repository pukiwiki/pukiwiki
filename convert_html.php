<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: convert_html.php,v 1.28 2003/03/22 15:22:48 panda Exp $
//
function convert_html($lines)
{
	global $script,$vars,$digest;
	static $contents_id = 0;
	
	if (!is_array($lines))
	{
		$lines = explode("\n",$lines);
	}
	
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
	function debug($indent = 0)
	{
		return str_repeat(' ',$indent).get_class($this)."({$this->text})\n";
	}
}

class Inline extends Element
{ // インライン要素
	var $text;
	
	function Inline($text)
	{
		if (substr($text,0,1) == '~') { // 行頭~。パラグラフ開始
			$parent =& $this->parent;
			$this = new Paragraph(' '.substr($text,1));
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
	function debug($indent = 0)
	{
		$ret = parent::debug($indent);
		foreach (array_keys($this->elements) as $key) {
			if (is_object($this->elements[$key]))
				$ret .= $this->elements[$key]->debug($indent + 2);
			else
				$ret .= str_repeat(' ',$indent + 2).$this->elements[$key];
		}
		return $ret;
	}
}
class Paragraph extends Block
{ // 段落
	var $class;
	
	function Paragraph($text,$class='')
	{
		parent::Block();
		$this->class = $class;
		if ($text == '') {
			return;
		}
		if (substr($text,0,1) == '~') {
			$text = ' '.substr($text,1);
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
	var $level,$id,$msg_top;
	
	function Heading(&$root,$text)
	{
		parent::Block();
		if (($level = strspn($text,'*')) > 3) {
			$level = 3;
		}
		$text = ltrim(substr($text,$level));
		$this->level = ++$level;
		list($this->msg_top,$this->id) = $root->getAnchor($text,$level);
		$this->last =& $this->insert(new Inline($text));
	}
	function canContain(&$obj)
	{
		return FALSE;
	}
	function toString()
	{
		return $this->msg_top.
			$this->wrap(parent::toString(),'h'.$this->level," id=\"{$this->id}\"");
	}
}
class HRule extends Block
{ // ----
	function HRule(&$root,$text) {
		parent::Block();
	}
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
class ListContainer extends Block
{
	var $tag,$tag2,$level,$style;
	var $margin,$left_margin;
	
	function ListContainer($tag,$tag2,$level,$text)
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
		return is_a($obj, 'ListContainer') ? ($this->tag == $obj->tag and $this->level == $obj->level) : TRUE;
	}
	function setParent(&$parent)
	{
		global $_list_pad_str;

		parent::setParent($parent);
		$step = $this->level;
		if (isset($parent->parent) and is_a($parent->parent,'ListContainer')) {
			$step -= $parent->parent->level; 
		}
		$margin = $this->margin * $step;
		if ($step == $this->level) {
			$margin += $this->left_margin;
		}
		$this->style = sprintf($_list_pad_str,$this->level,$margin,$margin);
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
		return $this->wrap(parent::toString(),$this->tag,$this->style.$param);
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
		return !(is_a($obj, 'ListContainer') and ($obj->level <= $this->level));
	}
	function toString()
	{
		return $this->wrap(parent::toString(), $this->head);
	}
}
class UList extends ListContainer
{ // -
	function UList(&$root,$text)
	{
		if (($level = strspn($text,'-')) > 3) {
			$level = 3; // limitation ;(
		}
		$text = ltrim(substr($text,$level));
		parent::ListContainer('ul','li',$level,$text);
	}
}
class OList extends ListContainer
{ // +
	function OList(&$root,$text)
	{
		if (($level = strspn($text,'+')) > 3) {
			$level = 3; // limitation ;(
		}
		$text = ltrim(substr($text,$level));
		parent::ListContainer('ol','li',$level,$text);
	}
}
class DList extends ListContainer
{ // :
	function DList(&$root,$text)
	{
		if (($level = strspn($text,':')) > 3) {
			$level = 3; // limitation ;(
		}
		$out = explode('|',ltrim(substr($text,$level)),2);
		if (count($out) < 2) {
			$this = new Inline($text);
			return;
		}
		parent::ListContainer('dl','dd',$level,$out[1]);
		if ($out[0] != '') {
			array_unshift($this->elements,new Inline("\n<dt>".inline2(inline($out[0]))."</dt>\n"));
		}
	}
}
class BQuote extends Block
{ // >
	var $level;
	
	function BQuote(&$root,$text)
	{
		parent::Block();
		$head = substr($text,0,1);
		if (($level = strspn($text,$head)) > 3) {
			$level = 3; // limitation ;(
		}
		$this->level = $level;
		$text = ltrim(substr($text,$level));
		if ($head == '<') { //blockquote close
			$this->level = 0;
			$this->last =& $this->end($root,$level,$text);
		}
		else {
			$this->last =& $this->insert(new Paragraph($text, ' class="quotation"'));
		}
	}
	function canContain(&$obj)
	{
		if (!is_a($obj, get_class($this))) {
			return TRUE;
		}
		return ($obj->level >= $this->level);
	}
	function &insert(&$obj)
	{
		if (is_a($obj, 'BQuote') and $obj->level == $this->level) {
			if (is_a($this->last,'Paragraph')
				and array_key_exists(0,$obj->elements[0])
				and is_object($obj->elements[0]->elements[0])) {
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
	function &end(&$root,$level,$text)
	{
		$parent =& $root->last;
		while (is_object($parent)) {
			if (is_a($parent,'BQuote') and $parent->level == $level) {
				return $parent->parent->insert(new Inline($text));
			}
			$parent =& $parent->parent;
		}
		return $this->insert(new Inline($text));
	}
}
class TableCell extends Block
{
	var $tag = 'td'; // {td|th}
	var $colspan = 1;
	var $rowspan = 1;
	var $style; // is array('width'=>, 'align'=>...);
	
	function TableCell($text,$is_template=FALSE) {
		parent::Block();
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
		$param = " class=\"style_{$this->tag}\"";
		if ($this->rowspan > 1) {
			$param .= " rowspan=\"{$this->rowspan}\"";
		}
		if ($this->colspan > 1) {
			$param .= " colspan=\"{$this->colspan}\"";
			unset($this->style['width']);
		}
		if (count($this->style)) {
			$param .= ' style="'.join(' ',$this->style).'"';
		}
		return "\n<{$this->tag}$param>".parent::toString()."</{$this->tag}>\n";
	}
}
class Table extends Block
{ // |
	var $type,$types;
	var $col; // number of column
	
	function Table(&$root,$text)
	{
		if (!preg_match("/^\|(.+)\|([hHfFcC]?)$/",$text,$out)) {
			$this = new Inline($text);
			return;
		}
		parent::Block();
		$cells = explode('|',$out[1]);
		$this->col = count($cells);
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
		return is_a($obj, 'Table') and ($obj->col == $this->col);
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
		for ($ncol = 0; $ncol < $this->col; $ncol++) {
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
	
	function YTable(&$root,$text)
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
		return is_a($obj, 'YTable') and ($obj->col == $this->col);
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
		return <<<EOD

<div class="ie5">
 <table class="style_table" cellspacing="1" border="0">
  $rows
 </table>
</div>

EOD;
	}
}
class Pre extends Block
{ // ' '
	
	function Pre(&$root,$text)
	{
		parent::Block();
		$this->elements[] = htmlspecialchars($text);
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
	var $name,$param;
	
	function Div(&$root,$text)
	{
		if (!preg_match("/^\#([^\(]+)(?:\((.*)\))?/",$text,$out) or !exist_plugin_convert($out[1])) {
			$this = new Paragraph($text);
			return;
		}
		parent::Block();
		$this->name = $out[1];
		$this->param = array_key_exists(2,$out) ? $out[2] : '';
	}
	function canContain(&$obj)
	{
		return FALSE;
	}
	function toString()
	{
		return do_plugin_convert($this->name,$this->param);
	}
}
class Align extends Block
{ // LEFT:/CENTER:/RIGHT:
	var $align;
	
	function Align($align)
	{
		$this->align = $align;
	}
	function canContain(&$obj)
	{
		return is_a($obj,'Inline');
	}
	function toString()
	{
		return $this->wrap(parent::toString(),'div',' style="text-align:'.$this->align.'"');
	}
}
class Body extends Block
{ // Body
	var $id;
	var $count = 0;
	var $contents;
	var $contents_last;
	var $classes = array('HRule','Heading','Pre','UList','OList','DList','Table','YTable','BQuote','BQuoteEnd','Div');

	function Body($id)
	{
		$this->id = $id;
		$this->contents = new Block();
		$this->contents_last =& $this->contents;
		parent::Block();
	}
	function parse(&$lines)
	{
		$this->last =& $this;
		
		foreach ($lines as $line)
		{
			if (substr($line,0,2) == '//') //コメントは処理しない
			{
				continue;
			}
			
			$align = '';
			if (preg_match('/^(LEFT|CENTER|RIGHT):(.*)$/',$line,$matches))
			{
				$this->last =& $this->last->add(new Align(strtolower($matches[1]))); // <div style="text-align:...">
				if ($matches[2] == '')
				{
					continue;
				}
				$line = $matches[2];
			}
			
			$line = preg_replace("/[\r\n]*$/",'',$line);
			
			// 行頭文字
			$head = substr($line,0,1);
			
			if ($line == '') { // 空行
				$this->last =& $this;
			}
			else if (substr($line,0,4) == '----') { // HRule
				$this->last =& $this->insert(new HRule($this,$line));
			}
			else if ($head == '*') { // Heading
				$this->last =& $this->insert(new Heading($this,$line));
			}
			else if ($head == ' ' or $head == "\t") { // Pre
				$this->last =& $this->last->add(new Pre($this,$line));
			}
			else {
				if (substr($line,-1) == '~') {
					$line = substr($line,0,-1)."\r";
				}
				if      ($head == '-') { // UList
					$this->last =& $this->last->add(new UList($this,$line)); // inline
				}
				else if ($head == '+') { // OList
					$this->last =& $this->last->add(new OList($this,$line)); // inline
				}
				else if ($head == ':') { // DList
					$this->last =& $this->last->add(new DList($this,$line)); // inline
				}
				else if ($head == '|') { // Table
					$this->last =& $this->last->add(new Table($this,$line));
				}
				else if ($head == ',') { // Table(YukiWiki互換)
					$this->last =& $this->last->add(new YTable($this,$line));
				}
				else if ($head == '>' or $head == '<') { // BrockQuote
					$this->last =& $this->last->add(new BQuote($this,$line));
				}
				else if ($head == '#') { // Div
					$this->last =& $this->last->add(new Div($this,$line));
				}
				else { // 通常文字列
					$this->last =& $this->last->add(new Inline($line));
				}
			}
		}
	}
	function getAnchor($text,$level)
	{
		global $top;
		
		$id = "content_{$this->id}_{$this->count}";
		$this->count++;
		$this->contents_last =& $this->contents_last->add(new Contents_UList($text,$this->id,$level,$id));
		return array($this->count > 1 ? $top : '',$id);
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
		$text = preg_replace('/<p[^>]*>#contents<\/p>/',$this->getContents(),$text);
		
		// 関連するページ
		// <p>のときは行頭から、<del>のときは他の要素の子要素として存在
		$text = preg_replace('/<(p|del)>#related<\/\1>/e','make_related($vars[\'page\'],\'$1\')',$text);
		return $text;
	}
}
class Contents_UList extends ListContainer
{
	function Contents_UList($text,$id,$level,$id)
	{
		// テキストのリフォーム
		// 行頭\nで整形済みを表す ... X(
		$text = "\n<a href=\"#$id\">".strip_htmltag(inline2(inline($text,TRUE)))."</a>\n";
		parent::ListContainer('ul', 'li', --$level, $text);
	}
	function setParent(&$parent)
	{
		global $_list_pad_str;

		parent::setParent($parent);
		$step = $this->level;
		$margin = $this->left_margin;
		if (isset($parent->parent) and is_a($parent->parent,'ListContainer'))
		{
			$step -= $parent->parent->level;
			$margin = 0;
		}
		$margin += $this->margin * ($step == $this->level ? 1 : $step);
		$this->style = sprintf($_list_pad_str,$this->level,$margin,$margin);
	}
}
?>
