<?php

class okex
{
    static public $enable = false;

    public $apiTickers = "https://www.okex.com/api/v5/market/tickers?instType=SPOT";


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

        foreach ($response["tickers"]->body->data as $coin) {

            list($base, $quote) = explode("-", $coin->instId);
            $coin->symbol = $base . $quote;

            $coins[] = [
                "pair" => $coin->symbol,
                "last_price" => $coin->last,
                "ask_price" => $coin->askPx,
                "bid_price" => $coin->bidPx,
                "base" => $base,
                "quote" => $quote,
                "volume" => $coin->vol24h
            ];
        }

        return $coins;
    }
}
