<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: mbstring.php,v 1.8 2003/11/17 00:14:57 arino Exp $
//

/*
 * PHPのmbstring extensionが使用できないときの代替関数
 *
 * 注意事項
 *
 * EUC-JP専用です。
 *
 * 使用方法
 *
 * jcode_1.34.zip (http://www.spencernetwork.org/)を入手して、
 * mbstring.phpと同じところにディレクトリ付きで展開してください。
 * 
 * -+--- mbstring.php          -r--
 *  +-+- jcode_1.34/           dr-x
 *    +--- readme.txt          -r--
 *    +--- jcode.phps          -r--
 *    +--- jcode_wrapper.php   -r--
 *    +--- code_table.ucs2jis  -r--
 *    +--- code_table.jis2ucs  -r--
 *
 */

if (is_readable('jcode_1.34/jcode_wrapper.php'))
{
	require_once('jcode_1.34/jcode_wrapper.php');
}
if (!function_exists('jcode_convert_encoding'))
{
//	die_message('Multibyte functions cannot be used. Please read "mbstring.php" for an additional installation procedure.');
	function jstrlen($str)
	{
		return strlen($str);
	}
	function jsubstr($str,$start,$length)
	{
		return substr($str,$start,$length);
	}
	function AutoDetect($str)
	{
		return 0;
	}
	function jcode_convert_encoding($str,$to_encoding,$from_encoding)
	{
		return $str;
	}
}

// mb_convert_encoding -- 文字エンコーディングを変換する
function mb_convert_encoding($str,$to_encoding,$from_encoding='')
{
	// 拡張: 配列を受けられるように
	// mb_convert_variable対策
	if (is_array($str))
	{
		foreach ($str as $key=>$value)
		{
			$str[$key] = mb_convert_encoding($value,$to_encoding,$from_encoding);
		}
		return $str;
	}
	return jcode_convert_encoding($str,$to_encoding,$from_encoding);
}

// mb_convert_variables -- 変数の文字コードを変換する
function mb_convert_variables($to_encoding,$from_encoding,&$vars)
{
	// 注: 可変長引数ではない。init.phpから呼ばれる1引数のパターンのみをサポート
	// 正直に実装するなら、可変引数をリファレンスで受ける方法が必要
	if (is_array($from_encoding) or $from_encoding == '' or $from_encoding == 'auto')
	{
		$from_encoding = mb_detect_encoding(join_array(' ',$vars));
	}   
	if ($from_encoding != 'ASCII' and $from_encoding != SOURCE_ENCODING)
	{
		$vars = mb_convert_encoding($vars,$to_encoding,$from_encoding);
	}
	return $from_encoding;
}

// 補助関数:配列を再帰的にjoinする
function join_array($glue,$pieces)
{
	$arr = array();
	foreach ($pieces as $piece)
	{
		$arr[] = is_array($piece) ? join_array($glue,$piece) : $piece;
	}
	return join($glue,$arr);
}

// mb_detect_encoding -- 文字エンコーディングを検出する
function mb_detect_encoding($str,$encoding_list='')
{
	static $codes = array(0=>'ASCII',1=>'EUC-JP',2=>'SJIS',3=>'JIS',4=>'UTF-8');
	
	// 注: $encoding_listは使用しない。
	$code = AutoDetect($str);
	if (!array_key_exists($code,$codes))
	{
		$code = 0; // oh ;(
	}
	return $codes[$code];
}

// mb_detect_order --  文字エンコーディング検出順序の設定/取得 
function mb_detect_order($encoding_list=NULL)
{
	static $list = array();
	
	// 注: 他の関数に影響を及ぼさない。呼んでも無意味。
	if ($encoding_list === NULL)
	{
		return $list;
	}
	$list = is_array($encoding_list) ? $encoding_list : explode(',',$encoding_list);
	return TRUE;
}

// mb_encode_mimeheader -- MIMEヘッダの文字列をエンコードする
function mb_encode_mimeheader($str,$charset='ISO-2022-JP',$transfer_encoding='B',$linefeed="\r\n")
{
	// 注: $transfer_encodingに関わらずbase64エンコードを返す
	$str = mb_convert_encoding($str,$charset,'auto');
	return '=?'.$charset.'?B?'.$str;
}

// mb_http_output -- HTTP出力文字エンコーディングの設定/取得
function mb_http_output($encoding='')
{
	// 注: 何もしない
	return SOURCE_ENCODING;
}

// mb_internal_encoding --  内部文字エンコーディングの設定/取得
function mb_internal_encoding($encoding='')
{
	// 注: 何もしない
	return SOURCE_ENCODING;
}

// mb_language --  カレントの言語を設定/取得 
function mb_language($language=NULL)
{
	static $mb_language = FALSE;
	if ($language === NULL)
	{
		return $mb_language;
	}
	// 注: 常にTRUEを返す
	$mb_language = $language;
	return TRUE;
}

// mb_strimwidth -- 指定した幅で文字列を丸める
function mb_strimwidth($str,$start,$width,$trimmarker='',$encoding='')
{
	if ($start == 0 and $width <= strlen($str))
	{
		return $str;
	}
	
	// 注: EUC-JP専用, $encodingを使用しない
	$chars = unpack('C*', $str);
	$substr = '';

	while (count($chars) and $start > 0)
	{
		$start--;
		if (array_shift($chars) >= 0x80)
		{
			array_shift($chars);
		}
	}
	if ($b_trimmarker = (count($chars) > $width))
	{
		$width -= strlen($trimmarker);
	}
	while (count($chars) and $width-- > 0)
	{
		$char = array_shift($chars);
		if ($char >= 0x80)
		{
			if ($width-- == 0)
			{
				break;
			}
			$substr .= chr($char);
			$char = array_shift($chars);
		}
		$substr .= chr($char);
	}
	if ($b_trimmarker)
	{
		$substr .= $trimmarker;
	}
	return $substr;
}

// mb_strlen -- 文字列の長さを得る
function mb_strlen($str,$encoding='')
{
	// 注: EUC-JP専用, $encodingを使用しない
	return jstrlen($str);
}

// mb_substr -- 文字列の一部を得る
function mb_substr($str,$start,$length=NULL,$encoding='')
{
	// 注: EUC-JP専用, $encodingを使用しない
	return jsubstr($str,$start,($length == NULL) ? jstrlen($str) : $length);
}
?>
