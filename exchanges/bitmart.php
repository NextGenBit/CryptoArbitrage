<?php

class bitmart
{
    static public $enable = false;

    private $apiTickers = "https://api-cloud.bitmart.com/spot/v1/ticker";
    private $apiTradablePairs = "https://api-cloud.bitmart.com/spot/v1/currencies";

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

        foreach ($response["tradable"]->body->data->currencies as $coin) {
            if (ALLOW_DISABLED || ($coin->withdraw_enabled == 1 && $coin->deposit_enabled == 1)) {
                $validPairs[$coin->id] = true;
            }
        }

        foreach ($response["tickers"]->body->data->tickers as $coin) {


            list($base, $quote) = explode("_", $coin->symbol);

            if (!array_key_exists($base, $validPairs)) {
                continue;
            }

            $symbol = $base . $quote;
            
            $coins[] = [
                "pair" => $symbol,
                "last_price" => $coin->last_price,
                "ask_price" => $coin->best_ask,
                "bid_price" => $coin->best_bid,
                "volume" => $coin->base_volume_24h,
                "base" => $base,
                "quote" => $quote
            ];
        }

        return $coins;
    }
}