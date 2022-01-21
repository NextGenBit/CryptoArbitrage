<?php

class gate_io
{
    static public $enable = false;

    private $apiTickers = "https://api.gateio.ws/api/v4/spot/tickers";
    private $apiTradableCurrencies = "https://api.gateio.ws/api/v4/spot/currencies";
    private $apiTradablePairs = "https://api.gateio.ws/api/v4/spot/currency_pairs";

    private $TradablePairs = [];

    function getUrls()
    {
        $apis = [];
        $apis["tickers"] = $this->apiTickersUrl();
        $apis["tradable_pairs"] = $this->apiTradablePairsUrl();
        $apis["tradable_currencies"] = $this->apiTradableCurrenciesUrl();

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

    function apiTradableCurrenciesUrl()
    {
        return ["url" => $this->apiTradableCurrencies];
    }


    function process($response)
    {

        $validCurrencies = [];
        foreach ($response["tradable_currencies"]->body as $coin) {

            if (!$coin->delisted && !$coin->withdraw_disabled && !$coin->deposit_disabled && !$coin->trade_disabled) {
                $validCurrencies[$coin->currency] = true;
            }
        }


        $validPairs = [];
        foreach ($response["tradable_pairs"]->body as $coin) {

            $last2type = strtolower(substr($coin->base, -2));
            if ($last2type == "3l" || $last2type == "3s") {
                continue;
            }

            list($base, $quote) = explode("_", $coin->id);

            if ($coin->trade_status == "tradable" && array_key_exists($base, $validCurrencies)) {
                $validPairs[$coin->id] = true;
            }
        }

        $coins = [];
        foreach ($response["tickers"]->body as $coin) {

            list($base, $quote) = explode("_", $coin->currency_pair);

            $symbol = $base . $quote;

            if (array_key_exists($coin->currency_pair, $validPairs)) {
                $coins[] = ["pair" => $symbol, "last_price" => $coin->last, "ask_price" => $coin->lowest_ask, "bid_price" => $coin->highest_bid, "base" => $base, "quote" => $quote, "volume" => $coin->base_volume];
            }
        }


        return $coins;

    }


}
