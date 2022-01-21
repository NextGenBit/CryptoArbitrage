<?php

class bitfinex
{
    static public $enable = true;

    public $apiTickers = "https://api-pub.bitfinex.com/v2/tickers?symbols=ALL";


    function getUrls()
    {
        $apis = [];
        $apis["tickers"] = $this->apiTickersUrl();

        return $apis;
    }

    function apiTickersUrl()
    {
        return ["url" => $this->apiTickers];
    }

    function process($response)
    {
        $coins = [];

        foreach ($response["tickers"]->body as $coin) {

            $type = substr($coin[0], 0, 1);

            if ($type == 't') {

                $pair = substr($coin[0], 1);

                if (strpos($pair, ":") !== false) {
                    list($base, $quote) = explode(":", $pair);
                } else {
                    list($base, $quote) = str_split($pair, 3);
                }

                $coins[] = [
                    "pair" => $base . $quote,
                    "last_price" => $coin[7],
                    "ask_price" => $coin[3],
                    "bid_price" => $coin[1],
                    "base" => $base,
                    "quote" => $quote,
                    "volume" => $coin[8]
                ];
            }
        }

        return $coins;
    }
}
