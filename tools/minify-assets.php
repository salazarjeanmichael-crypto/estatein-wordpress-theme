<?php
/**
 * Generate the minified CSS and JS the theme serves in production.
 *
 * Hand-written rather than pulled from a build chain, so the theme still needs
 * no npm install: edit the source, run this, commit both files.
 *
 * @package Estatein
 */

if ( 'cli' !== php_sapi_name() ) {
	exit( 'This script runs from the command line only.' );
}

/**
 * Minify CSS without touching anything inside quotes.
 *
 * Quoted runs are copied verbatim because data URIs carry spaces and slashes
 * that mean something, and calc() breaks if the spaces around + and - are lost.
 *
 * @param string $css Source.
 * @return string
 */
function estatein_minify_css( $css ) {
	$out = '';
	$len = strlen( $css );

	for ( $i = 0; $i < $len; $i++ ) {
		$ch = $css[ $i ];

		// Copy a quoted string through untouched, including its escapes.
		if ( '"' === $ch || "'" === $ch ) {
			$quote = $ch;
			$out  .= $ch;

			while ( ++$i < $len ) {
				$out .= $css[ $i ];

				if ( '\\' === $css[ $i ] && $i + 1 < $len ) {
					$out .= $css[ ++$i ];
					continue;
				}

				if ( $css[ $i ] === $quote ) {
					break;
				}
			}
			continue;
		}

		// Drop comments entirely.
		if ( '/' === $ch && $i + 1 < $len && '*' === $css[ $i + 1 ] ) {
			$end = strpos( $css, '*/', $i + 2 );
			$i   = ( false === $end ) ? $len : $end + 1;
			continue;
		}

		// Collapse any whitespace run to a single space; the tidy-up below
		// decides whether that space is needed at all.
		if ( preg_match( '/\s/', $ch ) ) {
			if ( '' !== $out && ' ' !== substr( $out, -1 ) ) {
				$out .= ' ';
			}
			continue;
		}

		$out .= $ch;
	}

	// Only structural punctuation loses its surrounding space. Arithmetic
	// operators are deliberately left alone so calc() survives.
	$out = preg_replace( '/\s*([{};,:>])\s*/', '$1', $out );

	// A selector combinator still needs its space: "a>b" is fine, "a b" is not.
	$out = str_replace( array( '{ ', ' }' ), array( '{', '}' ), $out );
	$out = preg_replace( '/;}/', '}', $out );

	return trim( $out );
}

/**
 * Minify JavaScript conservatively.
 *
 * Comments and indentation go; line breaks stay, because removing them is
 * where automatic semicolon insertion turns working code into a syntax error.
 *
 * @param string $js Source.
 * @return string
 */
function estatein_minify_js( $js ) {
	$out = '';
	$len = strlen( $js );

	for ( $i = 0; $i < $len; $i++ ) {
		$ch = $js[ $i ];

		if ( '"' === $ch || "'" === $ch ) {
			$quote = $ch;
			$out  .= $ch;

			while ( ++$i < $len ) {
				$out .= $js[ $i ];

				if ( '\\' === $js[ $i ] && $i + 1 < $len ) {
					$out .= $js[ ++$i ];
					continue;
				}

				if ( $js[ $i ] === $quote ) {
					break;
				}
			}
			continue;
		}

		if ( '/' === $ch && $i + 1 < $len ) {
			if ( '*' === $js[ $i + 1 ] ) {
				$end = strpos( $js, '*/', $i + 2 );
				$i   = ( false === $end ) ? $len : $end + 1;
				continue;
			}

			if ( '/' === $js[ $i + 1 ] ) {
				$end = strpos( $js, "\n", $i );
				$i   = ( false === $end ) ? $len : $end - 1;
				continue;
			}
		}

		$out .= $ch;
	}

	$lines = array();

	foreach ( explode( "\n", $out ) as $line ) {
		$line = rtrim( $line );
		$line = preg_replace( '/^\s+/', '', $line );

		if ( '' !== $line ) {
			$lines[] = $line;
		}
	}

	return implode( "\n", $lines );
}

$dir = dirname( __DIR__ );

$jobs = array(
	'/assets/css/main.css' => array( '/assets/css/main.min.css', 'estatein_minify_css' ),
	'/assets/js/main.js'   => array( '/assets/js/main.min.js',  'estatein_minify_js' ),
);

foreach ( $jobs as $source => $job ) {
	list( $target, $fn ) = $job;

	if ( ! file_exists( $dir . $source ) ) {
		echo "  ! missing {$source}\n";
		continue;
	}

	$raw = file_get_contents( $dir . $source );
	$min = call_user_func( $fn, $raw );

	file_put_contents( $dir . $target, $min );

	printf(
		"  %-22s %6d -> %6d bytes  (-%d%%)\n",
		basename( $target ),
		strlen( $raw ),
		strlen( $min ),
		round( ( 1 - strlen( $min ) / max( 1, strlen( $raw ) ) ) * 100 )
	);
}

echo "\nDone. Commit the .min files alongside the sources.\n";
