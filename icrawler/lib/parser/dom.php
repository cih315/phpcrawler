<?php
class DOM {
	public $html = null;
	private $_dom = null;
	private $_xpath = null;
	public function __construct() {
	}
	public function init($html, $pointer = null) {
		if(!$html or !is_string($html)) $html = "<html></html>";
		if(is_null($pointer)) $pointer = new self();
		$pointer -> html = $html;
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom -> preserveWhiteSpace = false;
		libxml_use_internal_errors(true);
		$dom -> loadHTML('<?xml encoding="utf-8" ?>' . $html);
		libxml_clear_errors();
		$pointer -> _dom = $dom;
		$pointer -> _xpath = new DOMXpath($pointer -> _dom);
		return $pointer;
	}
	public function attr($attr) { return $this -> find("//@{$attr}", 0); }
	public function text() { return $this -> find("//text()", 0); }
	public function count($xpath) { return count($this -> find($xpath)); }
	public function find($xpath, $index = null, $delete = false) {
		if(strpos($xpath, '/') !== 0) {
			trigger_error("XPATH IS WRONG ({$xpath})", E_USER_WARNING);
			if(is_null($index)) return array();
			return null;
		}
		$xpath = preg_replace(
			'~class\((?P<class>.*?)\)~i',
			'contains(concat(" ",normalize-space(@class)," ")," $1 ")',
			$xpath
		);
		$xpath = preg_replace(
			'~lower-case\((?P<lower>.*?)\)~i',
			'translate($1,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")',
			$xpath
		);
		if(!$this -> _xpath) return array();
		$list = $this -> _xpath -> query($xpath);
		if($list === false) {
			$array = array();
		} elseif($delete) {
			foreach($list as $elem) $elem -> parentNode -> removeChild($elem);
			return call_user_func_array(array($this, 'find'), array('/*', 0));
		} else $array = $this -> toArray($list, 0);
		if(is_null($index)) return $array;
		return @ $array[$index];
	}
	public function delete($xpath) {
		return call_user_func_array(array($this, 'find'), array($xpath, null, true));
	}
	public function __invoke($xpath, $index = null, $delete = false) {
		return call_user_func_array(array($this, 'find'), array($xpath, $index, $delete));
	}
	private function toArray($node, $level) {
		$array = array();
		if(!$node) return array();
		if($node instanceof DOMAttr) return $node -> value;
		if($node instanceof DOMNodeList) {
			foreach($node as $n) $array[] = $this -> toArray($n, $level);
			return $array;
		}
		if($node -> nodeType == XML_TEXT_NODE) {
			if($level)
				return esc(trim($node -> nodeValue));
			else return trim($node -> nodeValue);
		}
		if($node -> nodeType == XML_COMMENT_NODE) return '<!--' . $node -> nodeValue . '-->';
		@ $tag = $node -> tagName;
		if(!$tag) return '';
		$collector = "<{$tag}%s>%s</{$tag}>";
		$closed = "<{$tag}%s />";
		$attr = array();
		$inner = array();
		if($node -> hasAttributes())
			foreach($node -> attributes as $a)
				$attr[] = ' ' . sprintf('%s="%s"', $a -> nodeName, fesc($a -> nodeValue));
		if($node -> hasChildNodes())
			foreach($node -> childNodes as $childNode) {
				$t = $this -> toArray($childNode, $level + 1);
				if($t or $t == 0) $inner[] = $t;
			}
		$attr = implode('', $attr);
		$inner = implode('', $inner);
		if(!$inner and in_array($tag, explode(',', 'br,img,hr,param')))
			return sprintf($closed, $attr);
		return sprintf($collector, $attr, $inner);
	}
}
