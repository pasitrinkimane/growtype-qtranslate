<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'qtranxf_dbg_log' ) ) {
    function qtranxf_dbg_log( string $msg, $var = 'novar', bool $bt = false, bool $exit = false ): void {
        global $pagenow, $wp_current_filter;

        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'] ?? '';
        if ( ! empty( $pagenow ) ) {
            $timestamp .= "($pagenow)";
        }

        if ( ! empty( $wp_current_filter ) ) {
            $current = end( $wp_current_filter );
            if ( $current ) {
                $timestamp .= "[$current";
                $previous = prev( $wp_current_filter );
                if ( $previous ) {
                    $timestamp .= ",$previous";
                }
                $timestamp .= "]";
            }
        }

        if ( $timestamp ) {
            $msg = "$timestamp: $msg";
        }

        if ( $var !== 'novar' ) {
            $msg .= var_export( $var, true );
        }

        if ( $bt ) {
            $msg .= PHP_EOL . 'backtrace:' . PHP_EOL . print_r( debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true );
        }

        $log_file = WP_CONTENT_DIR . '/debug-qtranslate.log';
        error_log( $msg . PHP_EOL, 3, $log_file );

        if ( $exit ) {
            exit;
        }
    }

    function qtranxf_dbg_echo( string $msg, $var = 'novar', bool $bt = false, bool $exit = false ): void {
        if ( $var !== 'novar' ) {
            $msg .= var_export( $var, true );
        }

        echo $msg . "<br/>\n";

        if ( $bt ) {
            debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        }

        if ( $exit ) {
            exit;
        }
    }

    function qtranxf_dbg_log_if( bool $condition, string $msg, $var = 'novar', bool $bt = false, bool $exit = false ): void {
        if ( $condition ) {
            qtranxf_dbg_log( $msg, $var, $bt, $exit );
        }
    }

    function qtranxf_dbg_echo_if( bool $condition, string $msg, $var = 'novar', bool $bt = false, bool $exit = false ): void {
        if ( $condition ) {
            qtranxf_dbg_echo( $msg, $var, $bt, $exit );
        }
    }
}
