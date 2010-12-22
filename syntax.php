<?php
/**
 * Escapes tags in <html> with security concerns
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_htmlsafe extends DokuWiki_Syntax_Plugin {

    function getType() { return 'protected';}
    function getPType() { return 'normal';}
    function getSort() { return 189; }

    /**
     * Connect pattern to lexer
     */
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('<html>.*?</html>', $mode, 'plugin_htmlsafe');
        $this->Lexer->addSpecialPattern('<HTML>.*?</HTML>', $mode, 'plugin_htmlsafe');
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, &$handler){
		return array( substr($match,1,4), substr($match,6,-7) );
    }
    /**
     * Create output
     */
    function render($format, &$renderer, $data) {
        if($format == 'xhtml'){
            list($tag,$content) = $data;
			global $conf;
			$wrapper = ($tag === "HTML") ? "pre" : "code";
			if($conf['htmlok']){
				$strict = strtolower(str_replace(',',' ',$this->getConf('filter')));
				$strict = array_unique(array_filter(explode(' ',$strict)));
				$strict = implode( "|", $strict );
				$renderer->doc .= preg_replace( "/<(\/?)($strict)(\s|>)/i", "&lt;$1$2$3", $content );
			}
			else {
			  $renderer->doc .= p_xhtml_cached_geshi($content, 'html4strict', $wrapper);
			}
            return true;
        }
        return false;
    }
}
