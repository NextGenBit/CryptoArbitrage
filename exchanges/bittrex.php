<?php

class bittrex
{
    static public $enable = false;

    private $apiTickers = "https://api.bittrex.com/api/v1.1/public/getmarketsummaries";
    private $apiTradableCurrencies = "https://api.bittrex.com/v3/currencies";
    private $apiTradablePairs = "https://api.bittrex.com/v3/markets";

    private $TradablePairs = [];

    private $baseFiatCurrencies = ["USD", "USDT", "EUR"];
    private $baseCryptoCurrencies = ["BTC", "ETH"];


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


    function process( $response )
    {


        $validCurrencies = [];
        foreach ( $response["tradable_currencies"]->body as $coin ) {
            if ( $coin->status == "ONLINE" ) {
                $validCurrencies[$coin->symbol] = true;
            }
        }


        $validPairs = [];
        foreach ( $response["tradable_pairs"]->body as $coin ) {

            if ( in_array( $coin->baseCurrencySymbol, $this->baseFiatCurrencies ) ) {
                if ( ! in_array( $coin->quoteCurrencySymbol, $this->baseCryptoCurrencies ) && ! in_array( $coin->quoteCurrencySymbol, $this->baseFiatCurrencies ) ) {
                    $coin->symbol = $coin->quoteCurrencySymbol . "-" . $coin->baseCurrencySymbol;
                }
            }


            if ( $coin->status == "ONLINE" ) {
                list( $base, $quote ) = explode( "-", $coin->symbol );

                if ( array_key_exists( $base, $validCurrencies ) ) {
                    $validPairs[$base . $quote] = true;
                }
            }

        }

        $coins = [];

        foreach ( $response["tickers"]->body->result as $coin ) {
            list( $coinA, $coinB ) = explode( "-", $coin->MarketName );

            if ( array_key_exists( $coinA . $coinB, $validPairs ) ) {
                $symbol = $coinA . $coinB;
                $base = $coinA;
                $quote = $coinB;
            } elseif ( array_key_exists( $coinB . $coinA, $validPairs ) ) {
                $symbol = $coinB . $coinA;
                $base = $coinB;
                $quote = $coinA;
            } else {
                continue;
            }


            $coins[] = ["pair" => $symbol, "last_price" => $coin->Last, "ask_price" => $coin->Ask, "bid_price" => $coin->Bid, "base" => $base, "quote" => $quote, "volume" => $coin->BaseVolume];
        }


        return $coins;

    }
}
