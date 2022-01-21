<?php

class poloniex
{
    static public $enable = true;

    private $apiTickers = "https://poloniex.com/public?command=returnTicker";
    private $apiTradablePairs = "https://poloniex.com/public?command=returnCurrencies";


    private $baseFiatCurrencies = ["USD", "USDT", "USDC", "USDJ", "TUSD", "BUSD", "DAI", "PAX"];
    private $baseCryptoCurrencies = ["BTC", "ETH", "BNB", "TRX"];

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
        foreach ($response["tradable"]->body as $base => $coin) {
            if ($coin->disabled == 0 && $coin->frozen == 0 && $coin->delisted == 0) {
                $validPairs[$base] = true;
            }
        }

       
        $coins = [];
        foreach ($response["tickers"]->body as $symbol => $coin) {

            if ($coin->isFrozen == 1 || $coin->postOnly == 1) {
                continue;
            }

            list($quote, $base) = explode("_", $symbol);

            if (!array_key_exists($base, $validPairs)) {
                continue;
            }

            $symbol = $base . $quote;

            $coins[] = [
                "pair" => $symbol,
                "last_price" => $coin->last,
                "ask_price" => $coin->lowestAsk,
                "bid_price" => $coin->highestBid,
                "volume" => $coin->quoteVolume,
                "base" => $base,
                "quote" => $quote
            ];
        }
        
        return $coins;
    }
}