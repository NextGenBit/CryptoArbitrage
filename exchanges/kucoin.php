<?php

class kucoin
{

    static public $enable = false;

    private $apiTickers = "https://api.kucoin.com/api/v1/market/allTickers";
    private $apiTradablePairs = "https://api.kucoin.com/api/v1/symbols";


    function getUrls()
    {
        $apis = [];
        $apis["tickers"] = $this->apiTickersUrl();
        $apis["tradable"] = $this->apiTradablePairsUrl();

        return $apis;
    }

    function apiTickersUrl()
    {
        return ["url" => $this->apiTickers];
    }

    function apiTradablePairsUrl()
    {
        return ["url" => $this->apiTradablePairs];
    }

    function process($response)
    {

        $validPairs = [];
        foreach ($response["tradable"]->body->data as $coin) {

            $coin->symbol = str_replace("-", "", strtoupper($coin->symbol));
            if ($coin->enableTrading == 1 && $coin->market != 'ETF') {
                $validPairs[$coin->symbol] = ["base" => $coin->baseCurrency, "quote" => $coin->quoteCurrency];
            }
        }


        $coins = [];
        foreach ($response["tickers"]->body->data->ticker as $coin) {

            $coin->symbol = str_replace("-", "", strtoupper($coin->symbol));

            if (array_key_exists($coin->symbol, $validPairs)) {
                $coins[] = [
                    "pair" => $coin->symbol,
                    "last_price" => $coin->last,
                    "ask_price" => $coin->sell,
                    "bid_price" => $coin->buy,
                    "base" => $validPairs[$coin->symbol]["base"],
                    "quote" => $validPairs[$coin->symbol]["quote"],
                    "volume" => $coin->vol
                ];
            }
        }

        return $coins;
    }
}
