<?php

class huobi
{
    static public $enable = false;

    private $apiTickers = "https://api.huobi.pro/market/tickers";
    private $apiTradablePairs = "https://api.huobi.pro/v1/common/symbols";

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
            $last2type = substr($coin->{'base-currency'}, -2);

            if ($last2type == "3l" || $last2type == "3s") {
                continue;
            }

            if ($coin->state == "online") {
                $validPairs[$coin->symbol] = ["base" => $coin->{'base-currency'}, "quote" => $coin->{'quote-currency'}];
            }
        }

        $coins = [];
        foreach ($response["tickers"]->body->data as $coin) {
            if (array_key_exists($coin->symbol, $validPairs)) {
                $coins[] = [
                    "pair" => $coin->symbol,
                    "base" => $validPairs[$coin->symbol]["base"],
                    "quote" => $validPairs[$coin->symbol]["quote"],
                    "last_price" => $coin->close,
                    "ask_price" => $coin->ask,
                    "bid_price" => $coin->bid,
                    "volume" => $coin->vol
                ];
            }
        }

        return $coins;
    }
}
