<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: diff.php,v 1.1 2003/01/27 05:44:11 panda Exp $
//

//衝突時に対応表を出す
define('DIFF_SHOW_TABLE',TRUE);

// 差分の作成
function do_diff($strlines1,$strlines2)
{
	$obj = new line_diff();
	return $obj->str_compare($strlines1,$strlines2);
}

// 差分の作成(更新の衝突)
function do_update_diff($pagestr,$poststr,$original)
{
//	$obj = new line_diff('+','!','');
//	$body = $obj->str_compare($oldstr,$newstr);
//	$auto = ($obj->delete_count == 0 and $obj->add_count == 0);

	$obj = new line_diff();
	
	$obj->set_str('left',$original,$pagestr);
	$obj->compare();
	$diff1 = $obj->toArray();
	
	$obj->set_str('right',$original,$poststr);
	$obj->compare();
	$diff2 = $obj->toArray();
	
	$arr = $obj->arr_compare('all',$diff1,$diff2);
	
	if (DIFF_SHOW_TABLE) {
		global $do_update_diff_table;
		$do_update_diff_table = '<p>l : base → pagedata<br />r : base → postdata</p>'."\n";
		$do_update_diff_table .= '<table border="1"><tr><th>l</th><th>r</th><th>text</th></tr>'."\n";
		foreach ($arr as $_obj) {
			$do_update_diff_table .= '<tr><td>'.$_obj->get('left').'</td><td>'.$_obj->get('right').'</td><td>'.htmlspecialchars($_obj->text()).'</td></tr>'."\n";
		}
		$do_update_diff_table .= '</table>'."\n";
	}
	
	$body = '';
	foreach ($arr as $_obj) {
		if ($_obj->get('left') != '-' and $_obj->get('right') != '-') {
			$body .= $_obj->text();
		}
	}
	
	$auto = 1;
	
	return array(rtrim($body)."\n",$auto);
}

/*
line_diffクラス

以下の情報を参考にして作成しました。

S. Wu, <A HREF="http://www.cs.arizona.edu/people/gene/vita.html">
E. Myers,</A> U. Manber, and W. Miller,
<A HREF="http://www.cs.arizona.edu/people/gene/PAPERS/np_diff.ps">
"An O(NP) Sequence Comparison Algorithm,"</A>
Information Processing Letters 35, 6 (1990), 317-323.

*/

class line_diff
{
	var $arr1,$arr2,$m,$n,$pos,$key,$plus,$minus,$equal,$reverse;
	
	function line_diff($plus='+',$minus='-',$equal=' ')
	{
		$this->plus = $plus;
		$this->minus = $minus;
		$this->equal = $equal;
	}
	function arr_compare($key,$arr1,$arr2)
	{
		$this->key = $key;
//		array_unshift($arr1,'');
//		array_unshift($arr2,'');
		$this->arr1 = $arr1;
		$this->arr2 = $arr2;
		$this->compare();
		$arr = $this->toArray();
//		array_shift($arr);
		return $arr;
	}
	function set_str($key,$str1,$str2)
	{
		$this->key = $key;
		preg_match_all("/[^\n]*\n*/",preg_replace("/\r/",'',$str1),$arr1);
		preg_match_all("/[^\n]*\n*/",preg_replace("/\r/",'',$str2),$arr2);
		
		$this->arr1 = array();
		foreach ($arr1[0] as $line) {
			$this->arr1[] = new DiffLine($line);
		}
		
		$this->arr2 = array();
		foreach ($arr2[0] as $line) {
			$this->arr2[] = new DiffLine($line);
		}
	}
	function str_compare($str1,$str2)
	{
		$this->set_str('diff',$str1,$str2);
		$this->compare();
		
		$str = '';
		foreach ($this->toArray() as $obj) {
			$str .= $obj->get('diff').$obj->text();
		}
		
		return $str;
	}
	function compare()
	{
		array_unshift($this->arr1,new DiffLine('')); //sentinel
		array_unshift($this->arr2,new DiffLine('')); //sentinel
		$this->reverse = (count($this->arr1) > count($this->arr2));
		if ($this->reverse) {
			$tmp = $this->arr1;
			$this->arr1 = $this->arr2;
			$this->arr2 = $tmp;
			unset($tmp);
		}
		$this->m = count($this->arr1) - 1;
		$this->n = count($this->arr2) - 1;
		$this->pos = array(0=>array('x'=>-1,'y'=>-1)); //sentinel
		if ($this->m <= 0) {
			$this->pos[] = array('x'=>$this->m + 1, 'y' => $this->n + 1);
			return;
		}
		$delta = $this->n - $this->m; // must be >=0;
		$fp = array();
		for ($p = -($this->m)-1; $p <= $this->n + 1; $p++) {
			$fp[$p] = -1;
		}
		
		for ($p = 0;; $p++) {
			for ($k = -$p; $k < $delta; $k++) {
				$fp[$k] = $this->snake($k, max($fp[$k - 1] + 1, $fp[$k + 1]));
			}
			
			for ($k = $delta + $p; $k > $delta; $k--) {
				$fp[$k] = $this->snake($k, max($fp[$k - 1] + 1, $fp[$k + 1]));
			}
			
			$fp[$delta] = $this->snake($delta, max($fp[$delta - 1] + 1, $fp[$delta + 1]));
			
			if ($fp[$delta] == $this->n) {
				$this->pos = array_reverse($this->pos);
				$this->pos[] = array('x'=>$this->m + 1, 'y' => $this->n + 1); // sentinel
				
				return;
			}
		}
	}
	function snake($k, $y)
	{
		$x = $y - $k;
		while (($x < $this->m) and ($y < $this->n) and $this->arr1[$x + 1]->compare($this->arr2[$y + 1])) {
			
			$x++; $y++;
			if ($x > $this->pos[0]['x'] and $y > $this->pos[0]['y']) {
				array_unshift($this->pos,array('x'=>$x,'y'=>$y));
			}
		}
		return $y;
	}
	function strcmp($str1, $str2) //ぐぅ。
	{
		return rtrim($str1) == rtrim($str2);
	}
	function toArray()
	{
		$arr = array();
		if ($this->reverse) { //姑息な…
			$_x = 'y'; $_y = 'x'; $m = $this->n; $arr1 =& $this->arr2; $arr2 =& $this->arr1;
		}
		else {
			$_x = 'x'; $_y = 'y'; $m = $this->m; $arr1 =& $this->arr1; $arr2 =& $this->arr2;
		}
		
		$x = $y = 0;
		$this->add_count = $this->delete_count = 0;
		foreach ($this->pos as $pos) {
			$this->delete_count += ($pos[$_x] - $x);
			$this->add_count += ($pos[$_y] - $y);
			
			while ($pos[$_x] > $x) {
				$arr1[$x]->set($this->key,$this->minus);
				$arr[] = $arr1[$x++];
			}
			
			while ($pos[$_y] > $y) {
				$arr2[$y]->set($this->key,$this->plus);
				$arr[] =  $arr2[$y++];
			}
			
			if ($x <= $this->m) {
				$arr1[$x]->merge($arr2[$y]);
				$arr1[$x]->set($this->key,$this->equal);
				$arr[] = $arr1[$x];
			}
			$x++; $y++;
		}
		array_shift($arr); // drop sentinel
		return $arr;
	}
}

class DiffLine
{
	var $text,$lfcount;
	var $status;
	
	function DiffLine($text)
	{
		if (preg_match("/^([^\n]*)(\n*)$/",$text,$matches)) {
			$this->text = $matches[1]; $this->lfcount = strlen($matches[2]);
		}
		else {
			$this->text = $text; $this->lfcount = 1;
		}
		$this->status = array();
	}
	function compare($obj)
	{
		return $this->text == $obj->text;
	}
	function set($key,$status)
	{
		$this->status[$key] = $status;
	}
	function get($key)
	{
		return array_key_exists($key,$this->status) ? $this->status[$key] : '';
	}
	function merge($obj)
	{
		$this->status = array_merge($this->status,$obj->status);
		$this->lfcount = max($this->lfcount,$obj->lfcount);
	}
	function text()
	{
		return $this->text.str_repeat("\n",($this->lfcount == 0) ? 1 : $this->lfcount);
	}
}
?>
