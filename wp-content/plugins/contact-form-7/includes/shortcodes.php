<?php

class WPCF7_ShortcodeManager {

	var $shortcode_tags = array();

	// Taggs scanned at the last time of do_shortcode()
	var $scanned_tags = null;

	// Executing shortcodes (true) or just scanning (false)
	var $exec = true;

	function add_shortcode( $tag, $func, $has_name = false ) {
		if ( ! is_callable( $func ) )
			return;

		$tags = array_filter( array_unique( (array) $tag ) );

		foreach ( $tags as $tag ) {
			$this->shortcode_tags[$tag] = array(
				'function' => $func,
				'has_name' => (boolean) $has_name );
		}
	}

	function remove_shortcode( $tag ) {
		unset( $this->shortcode_tags[$tag] );
	}

	function normalize_shortcode( $content ) {
		if ( empty( $this->shortcode_tags ) || ! is_array( $this->shortcode_tags ) )
			return $content;

		$pattern = $this->get_shortcode_regex();
		return preg_replace_callback( '/' . $pattern . '/s',
			array( &$this, 'normalize_space_cb' ), $content );
	}

	function normalize_space_cb( $m ) {
		// allow [[foo]] syntax for escaping a tag
		if ( $m[1] == '[' && $m[6] == ']' )
			return $m[0];

		$tag = $m[2];
		$attr = trim( preg_replace( '/[\r\n\t ]+/', ' ', $m[3] ) );
		$content = trim( $m[5] );

		$content = str_replace( "\n", '<WPPreserveNewline />', $content );

		$result = $m[1] . '[' . $tag
			. ( $attr ? ' ' . $attr : '' )
			. ( $m[4] ? ' ' . $m[4] : '' )
			. ']'
			. ( $content ? $content . '[/' . $tag . ']' : '' )
			. $m[6];

		return $result;
	}

	function do_shortcode( $content, $exec = true ) {
		$this->exec = (bool) $exec;
		$this->scanned_tags = array();

		if ( empty( $this->shortcode_tags ) || ! is_array( $this->shortcode_tags ) )
			return $content;

		$pattern = $this->get_shortcode_regex();
		return preg_replace_callback( '/' . $pattern . '/s',
			array( &$this, 'do_shortcode_tag' ), $content );
	}

	function scan_shortcode( $content ) {
		$this->do_shortcode( $content, false );
		return $this->scanned_tags;
	}

	function get_shortcode_regex() {
		$tagnames = array_keys( $this->shortcode_tags );
		$tagregexp = join( '|', array_map( 'preg_quote', $tagnames ) );

		return '(\[?)'
			. '\[(' . $tagregexp . ')(?:[\r\n\t ](.*?))?(?:[\r\n\t ](\/))?\]'
			. '(?:([^[]*?)\[\/\2\])?'
			. '(\]?)';
	}

	function do_shortcode_tag( $m ) {
		// allow [[foo]] syntax for escaping a tag
		if ( $m[1] == '[' && $m[6] == ']' ) {
			return substr( $m[0], 1, -1 );
		}

		$tag = $m[2];
		$attr = $this->shortcode_parse_atts( $m[3] );

		$scanned_tag = array(
			'type' => $tag,
			'basetype' => trim( $tag, '*' ),
			'name' => '',
			'options' => array(),
			'raw_values' => array(),
			'values' => array(),
			'pipes' => null,
			'labels' => array(),
			'attr' => '',
			'content' => '' );

		if ( is_array( $attr ) ) {
			if ( is_array( $attr['options'] ) ) {
				if ( $this->shortcode_tags[$tag]['has_name'] && ! empty( $attr['options'] ) ) {
					$scanned_tag['name'] = array_shift( $attr['options'] );

					if ( ! wpcf7_is_name( $scanned_tag['name'] ) )
						return $m[0]; // Invalid name is used. Ignore this tag.
				}

				$scanned_tag['options'] = (array) $attr['options'];
			}

			$scanned_tag['raw_values'] = (array) $attr['values'];

			if ( WPCF7_USE_PIPE ) {
				$pipes = new WPCF7_Pipes( $scanned_tag['raw_values'] );
				$scanned_tag['values'] = $pipes->collect_befores();
				$scanned_tag['pipes'] = $pipes;
			} else {
				$scanned_tag['values'] = $scanned_tag['raw_values'];
			}

			$scanned_tag['labels'] = $scanned_tag['values'];

		} else {
			$scanned_tag['attr'] = $attr;
		}

		$scanned_tag['values'] = array_map( 'trim', $scanned_tag['values'] );
		$scanned_tag['labels'] = array_map( 'trim', $scanned_tag['labels'] );

		$content = trim( $m[5] );
		$content = preg_replace( "/<br[\r\n\t ]*\/?>$/m", '', $content );
		$scanned_tag['content'] = $content;

		$scanned_tag = apply_filters( 'wpcf7_form_tag', $scanned_tag, $this->exec );

		$this->scanned_tags[] = $scanned_tag;

		if ( $this->exec ) {
			$func = $this->shortcode_tags[$tag]['function'];
			return $m[1] . call_user_func( $func, $scanned_tag ) . $m[6];
		} else {
			return $m[0];
		}
	}

	function shortcode_parse_atts( $text ) {
		$atts = array( 'options' => array(), 'values' => array() );
		$text = preg_replace( "/[\x{00a0}\x{200b}]+/u", " ", $text );
		$text = stripcslashes( trim( $text ) );

		$pattern = '%^([-+*=0-9a-zA-Z:.!?#$&@_/|\%\r\n\t ]*?)((?:[\r\n\t ]*"[^"]*"|[\r\n\t ]*\'[^\']*\')*)$%';

		if ( preg_match( $pattern, $text, $match ) ) {
			if ( ! empty( $match[1] ) ) {
				$atts['options'] = preg_split( '/[\r\n\t ]+/', trim( $match[1] ) );
			}
			if ( ! empty( $match[2] ) ) {
				preg_match_all( '/"[^"]*"|\'[^\']*\'/', $match[2], $matched_values );
				$atts['values'] = wpcf7_strip_quote_deep( $matched_values[0] );
			}
		} else {
			$atts = $text;
		}

		return $atts;
	}

}

function wpcf7_add_shortcode( $tag, $func, $has_name = false ) {
	global $wpcf7_shortcode_manager;

	if ( is_a( $wpcf7_shortcode_manager, 'WPCF7_ShortcodeManager' ) )
		return $wpcf7_shortcode_manager->add_shortcode( $tag, $func, $has_name );
}

function wpcf7_remove_shortcode( $tag ) {
	global $wpcf7_shortcode_manager;

	if ( is_a( $wpcf7_shortcode_manager, 'WPCF7_ShortcodeManager' ) )
		return $wpcf7_shortcode_manager->remove_shortcode( $tag );
}

function wpcf7_do_shortcode( $content ) {
	global $wpcf7_shortcode_manager;

	if ( is_a( $wpcf7_shortcode_manager, 'WPCF7_ShortcodeManager' ) )
		return $wpcf7_shortcode_manager->do_shortcode( $content );
}

function wpcf7_get_shortcode_regex() {
	global $wpcf7_shortcode_manager;

	if ( is_a( $wpcf7_shortcode_manager, 'WPCF7_ShortcodeManager' ) )
		return $wpcf7_shortcode_manager->get_shortcode_regex();
}

class WPCF7_Shortcode {

	public $type;
	public $basetype;
	public $name = '';
	public $options = array();
	public $raw_values = array();
	public $values = array();
	public $pipes;
	public $labels = array();
	public $attr = '';
	public $content = '';

	public function __construct( $tag ) {
		foreach ( $tag as $key => $value ) {
			if ( property_exists( __CLASS__, $key ) )
				$this->{$key} = $value;
		}
	}

	public function is_required() {
		return ( '*' == substr( $this->type, -1 ) );
	}

	public function has_option( $opt ) {
		$pattern = sprintf( '/^%s(:.+)?$/i', preg_quote( $opt, '/' ) );
		return (bool) preg_grep( $pattern, $this->options );
	}

	public function get_option( $opt, $pattern = '', $single = false ) {
		$preset_patterns = array(
			'date' => '[0-9]{4}-[0-9]{2}-[0-9]{2}',
			'int' => '[0-9]+',
			'signed_int' => '-?[0-9]+',
			'class' => '[-0-9a-zA-Z_]+',
			'id' => '[-0-9a-zA-Z_]+' );

		if ( isset( $preset_patterns[$pattern] ) )
			$pattern = $preset_patterns[$pattern];

		if ( '' == $pattern )
			$pattern = '.+';

		$pattern = sprintf( '/^%s:%s$/i', preg_quote( $opt, '/' ), $pattern );

		if ( $single ) {
			$matches = $this->get_first_match_option( $pattern );

			if ( ! $matches )
				return false;

			return substr( $matches[0], strlen( $opt ) + 1 );
		} else {
			$matches_a = $this->get_all_match_options( $pattern );

			if ( ! $matches_a )
				return false;

			$results = array();

			foreach ( $matches_a as $matches )
				$results[] = substr( $matches[0], strlen( $opt ) + 1 );

			return $results;
		}
	}

	public function get_class_option( $default = '' ) {
		if ( is_string( $default ) )
			$default = explode( ' ', $default );

		$options = array_merge(
			(array) $default,
			(array) $this->get_option( 'class', 'class' ) );

		$options = array_filter( array_unique( $options ) );

		return implode( ' ', $options );
	}

	public function get_size_option( $default = '' ) {
		$matches_a = $this->get_all_match_options( '%^([0-9]*)/[0-9]*$%' );

		foreach ( (array) $matches_a as $matches ) {
			if ( isset( $matches[1] ) && '' !== $matches[1] )
				return $matches[1];
		}

		return $default;
	}

	public function get_maxlength_option( $default = '' ) {
		$matches_a = $this->get_all_match_options(
			'%^(?:[0-9]*x?[0-9]*)?/([0-9]+)$%' );

		foreach ( (array) $matches_a as $matches ) {
			if ( isset( $matches[1] ) && '' !== $matches[1] )
				return $matches[1];
		}

		return $default;
	}

	public function get_cols_option( $default = '' ) {
		$matches_a = $this->get_all_match_options(
			'%^([0-9]*)x([0-9]*)(?:/[0-9]+)?$%' );

		foreach ( (array) $matches_a as $matches ) {
			if ( isset( $matches[1] ) && '' !== $matches[1] )
				return $matches[1];
		}

		return $default;
	}

	public function get_rows_option( $default = '' ) {
		$matches_a = $this->get_all_match_options(
			'%^([0-9]*)x([0-9]*)(?:/[0-9]+)?$%' );

		foreach ( (array) $matches_a as $matches ) {
			if ( isset( $matches[2] ) && '' !== $matches[2] )
				return $matches[2];
		}

		return $default;
	}

	public function get_first_match_option( $pattern ) {
		foreach( (array) $this->options as $option ) {
			if ( preg_match( $pattern, $option, $matches ) )
				return $matches;
		}

		return false;
	}

	public function get_all_match_options( $pattern ) {
		$result = array();

		foreach( (array) $this->options as $option ) {
			if ( preg_match( $pattern, $option, $matches ) )
				$result[] = $matches;
		}

		return $result;
	}
}

?>