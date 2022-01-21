<?php

class binance
{
    static public $enable = true;

    private $apiTickers = "https://api.binance.com/api/v3/ticker/24hr";
    private $apiTradablePairs = "https://api.binance.com/sapi/v1/capital/config/getall";
    private $exchangeInfo = "https://api.binance.com/api/v3/exchangeInfo";

    private $apiKey = "API_KEY_HERE";
    private $secretKey = "API_SECRET_HERE";
    
    function getUrls()
    {
        $apis = [];

        $apis["tickers"] = $this->apiTickersUrl();
        $apis["tradable"] = $this->apiTradablePairsUrl();
        $apis["info"] = $this->exchangeInfoUrl();

        return $apis;
    }

    function exchangeInfoUrl()
    {
        return ["url" => $this->exchangeInfo, "options" => []];
    }

    function apiTickersUrl()
    {
        return ["url" => $this->apiTickers, "options" => []];
    }

    function apiTradablePairsUrl()
    {
        $params = [];
        $params['timestamp'] = number_format(microtime(true) * 1000, 0, '.', '');
        $query = http_build_query($params, '', '&');
        $signature = hash_hmac('sha256', $query, $this->secretKey);

        return ["url" => $this->apiTradablePairs . "?{$query}&signature={$signature}", "options" => [CURLOPT_HTTPHEADER => ["X-MBX-APIKEY: {$this->apiKey}"]]];
    }

    function process($response)
    {
        $validBaseCoins = [];
        foreach ($response["tradable"]->body as $coin) {
            if ($coin->isLegalMoney == 1 || ($coin->locked == 0 && $coin->freeze == 0)) {

                if (ALLOW_DISABLED || ($coin->depositAllEnable == 1 && $coin->withdrawAllEnable == 1)) {
                    $validBaseCoins[$coin->coin] = true;
                }
            }
        }

        $validPairs = [];
        foreach ($response["info"]->body->symbols as $coin) {

            if ($coin->status == "TRADING" && array_key_exists($coin->baseAsset, $validBaseCoins)) {
                $validPairs[$coin->baseAsset . $coin->quoteAsset] = ["base" => $coin->baseAsset, "quote" => $coin->quoteAsset];
            }
        }

        $coins = [];
        foreach ($response["tickers"]->body as $coin) {
            if (array_key_exists($coin->symbol, $validPairs)) {
                $coins[] = [
                    "pair" => $coin->symbol,
                    "last_price" => $coin->lastPrice,
                    "ask_price" => $coin->askPrice,
                    "bid_price" => $coin->bidPrice,
                    "base" => $validPairs[$coin->symbol]["base"],
                    "quote" => $validPairs[$coin->symbol]["quote"],
                    "volume" => $coin->volume];
            }
        }

        return $coins;
    }

}
