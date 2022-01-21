<?php

class MultiURL
{
    private $mh;
    private $curl_handlers = [];
    private $identifiers = [];


    function __construct()
    {
        $this->mh = curl_multi_init();
    }

    function getIdentifier()
    {
        return base64_encode( random_bytes( 10 ) );
    }

    function addUrl( $request )
    {
        $identifier = $this->getIdentifier();

        $ch = curl_init();

        if ( ! empty( $request["options"] ) ) {
            curl_setopt_array( $ch, $request["options"] );
        }

        curl_setopt( $ch, CURLOPT_URL, $request["url"] );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_HEADER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_ENCODING, "gzip" );
        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36" );

        $this->curl_handlers[] = $ch;
        $this->identifiers[] = $identifier;

        return $identifier;

    }

    function getResponses( $threads = null )
    {

        $threads = ( empty( $threads ) ) ? count( $this->curl_handlers ) : $threads;
        $responses = [];
        while ( count( $this->curl_handlers ) > 0 ) {
            $counter = count( $this->curl_handlers );
            for ( $i = 0; $i < $counter && $i < $threads; $i++ ) {
                curl_multi_add_handle( $this->mh, $this->curl_handlers[$i] );
            }

            $this->exec();

            for ( $i = 0; $i < $counter && $i < $threads; $i++ ) {
                $response = curl_multi_getcontent( $this->curl_handlers[$i] );
                $info = curl_getinfo( $this->curl_handlers[$i] );

                // Split the full response in its headers and body
                $header_size = $info['header_size'];
                $header = substr( $response, 0, $header_size );


                $x = new stdClass();
                $x->responseCode = intval( $info['http_code'] );
                $x->headers = $this->parseHeaders( $header );
                $x->raw_body = substr( $response, $header_size );

                $json_args = [];
                array_unshift( $json_args, $x->raw_body );

                if ( function_exists( 'json_decode' ) ) {
                    $json = call_user_func_array( 'json_decode', $json_args );
                    if ( json_last_error() === JSON_ERROR_NONE ) {
                        $x->body = $json;
                    }
                }

                $responses[$this->identifiers[$i]] = $x;
            }

            for ( $i = 0; $i < $counter && $i < $threads; $i++ ) {
                curl_multi_remove_handle( $this->mh, $this->curl_handlers[$i] );
                unset( $this->curl_handlers[$i] );
                unset( $this->identifiers[$i] );
            }

            $this->curl_handlers = array_merge( $this->curl_handlers );
            $this->identifiers = array_merge( $this->identifiers );

        }

        $this->curl_handlers = [];
        $this->identifiers = [];
        $this->mh = curl_multi_init();

        return $responses;
    }

    function exec()
    {
        do {
            curl_multi_exec( $this->mh, $running );
            curl_multi_select( $this->mh );
        } while ( $running > 0 );
    }

    function parseHeaders( $raw_headers )
    {
        if ( function_exists( 'http_parse_headers' ) ) {
            return http_parse_headers( $raw_headers );
        } else {
            $key = '';
            $headers = array();

            foreach ( explode( "\n", $raw_headers ) as $i => $h ) {
                $h = explode( ':', $h, 2 );

                if ( isset( $h[1] ) ) {
                    if ( ! isset( $headers[$h[0]] ) ) {
                        $headers[$h[0]] = trim( $h[1] );
                    } elseif ( is_array( $headers[$h[0]] ) ) {
                        $headers[$h[0]] = array_merge( $headers[$h[0]], array( trim( $h[1] ) ) );
                    } else {
                        $headers[$h[0]] = array_merge( array( $headers[$h[0]] ), array( trim( $h[1] ) ) );
                    }

                    $key = $h[0];
                } else {
                    if ( substr( $h[0], 0, 1 ) == "\t" ) {
                        $headers[$key] .= "\r\n\t" . trim( $h[0] );
                    } elseif ( ! $key ) {
                        $headers[0] = trim( $h[0] );
                    }
                }
            }

            return $headers;
        }
    }
}
