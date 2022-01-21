<?php

class kraken
{
    static public $enable = true;

    private $apiTickers = "https://api.kraken.com/0/public/Ticker?pair={PAIRS}";
    private $apiPairsInfo = "https://api.kraken.com/0/public/AssetPairs";

    private $TradablePairs = ['AAVEAUD' => 'AAVEAUD', 'AAVEETH' => 'AAVEETH', 'AAVEEUR' => 'AAVEEUR', 'AAVEGBP' => 'AAVEGBP', 'AAVEUSD' => 'AAVEUSD', 'AAVEXBT' => 'AAVEXBT', 'ADAAUD' => 'ADAAUD', 'ADAETH' => 'ADAETH', 'ADAEUR' => 'ADAEUR', 'ADAGBP' => 'ADAGBP', 'ADAUSD' => 'ADAUSD', 'ADAUSDT' => 'ADAUSDT', 'ADAXBT' => 'ADAXBT', 'ALGOETH' => 'ALGOETH', 'ALGOEUR' => 'ALGOEUR', 'ALGOGBP' => 'ALGOGBP', 'ALGOUSD' => 'ALGOUSD', 'ALGOXBT' => 'ALGOXBT', 'ANTETH' => 'ANTETH', 'ANTEUR' => 'ANTEUR', 'ANTUSD' => 'ANTUSD', 'ANTXBT' => 'ANTXBT', 'ATOMAUD' => 'ATOMAUD', 'ATOMETH' => 'ATOMETH', 'ATOMEUR' => 'ATOMEUR', 'ATOMGBP' => 'ATOMGBP', 'ATOMUSD' => 'ATOMUSD', 'ATOMXBT' => 'ATOMXBT', 'AUDJPY' => 'AUDJPY', 'AUDUSD' => 'AUDUSD', 'BALETH' => 'BALETH', 'BALEUR' => 'BALEUR', 'BALUSD' => 'BALUSD', 'BALXBT' => 'BALXBT', 'BATETH' => 'BATETH', 'BATEUR' => 'BATEUR', 'BATUSD' => 'BATUSD', 'BATXBT' => 'BATXBT', 'BCHAUD' => 'BCHAUD', 'BCHETH' => 'BCHETH', 'BCHEUR' => 'BCHEUR', 'BCHGBP' => 'BCHGBP', 'BCHJPY' => 'BCHJPY', 'BCHUSD' => 'BCHUSD', 'BCHUSDT' => 'BCHUSDT', 'BCHXBT' => 'BCHXBT', 'COMPETH' => 'COMPETH', 'COMPEUR' => 'COMPEUR', 'COMPUSD' => 'COMPUSD', 'COMPXBT' => 'COMPXBT', 'CRVETH' => 'CRVETH', 'CRVEUR' => 'CRVEUR', 'CRVUSD' => 'CRVUSD', 'CRVXBT' => 'CRVXBT', 'DAIEUR' => 'DAIEUR', 'DAIUSD' => 'DAIUSD', 'DAIUSDT' => 'DAIUSDT', 'DASHEUR' => 'DASHEUR', 'DASHUSD' => 'DASHUSD', 'DASHXBT' => 'DASHXBT', 'DOTAUD' => 'DOTAUD', 'DOTETH' => 'DOTETH', 'DOTEUR' => 'DOTEUR', 'DOTGBP' => 'DOTGBP', 'DOTUSD' => 'DOTUSD', 'DOTUSDT' => 'DOTUSDT', 'DOTXBT' => 'DOTXBT', 'EOSETH' => 'EOSETH', 'EOSEUR' => 'EOSEUR', 'EOSUSD' => 'EOSUSD', 'EOSUSDT' => 'EOSUSDT', 'EOSXBT' => 'EOSXBT', 'ETH2.SETH' => 'ETH2.SETH', 'ETHAUD' => 'ETHAUD', 'ETHCHF' => 'ETHCHF', 'ETHDAI' => 'ETHDAI', 'ETHUSDC' => 'ETHUSDC', 'ETHUSDT' => 'ETHUSDT', 'EURAUD' => 'EURAUD', 'EURCAD' => 'EURCAD', 'EURCHF' => 'EURCHF', 'EURGBP' => 'EURGBP', 'EURJPY' => 'EURJPY', 'EWTEUR' => 'EWTEUR', 'EWTGBP' => 'EWTGBP', 'EWTUSD' => 'EWTUSD', 'EWTXBT' => 'EWTXBT', 'FILAUD' => 'FILAUD', 'FILETH' => 'FILETH', 'FILEUR' => 'FILEUR', 'FILGBP' => 'FILGBP', 'FILUSD' => 'FILUSD', 'FILXBT' => 'FILXBT', 'FLOWETH' => 'FLOWETH', 'FLOWEUR' => 'FLOWEUR', 'FLOWGBP' => 'FLOWGBP', 'FLOWUSD' => 'FLOWUSD', 'FLOWXBT' => 'FLOWXBT', 'GNOETH' => 'GNOETH', 'GNOEUR' => 'GNOEUR', 'GNOUSD' => 'GNOUSD', 'GNOXBT' => 'GNOXBT', 'GRTAUD' => 'GRTAUD', 'GRTETH' => 'GRTETH', 'GRTEUR' => 'GRTEUR', 'GRTGBP' => 'GRTGBP', 'GRTUSD' => 'GRTUSD', 'GRTXBT' => 'GRTXBT', 'ICXETH' => 'ICXETH', 'ICXEUR' => 'ICXEUR', 'ICXUSD' => 'ICXUSD', 'ICXXBT' => 'ICXXBT', 'KAVAETH' => 'KAVAETH', 'KAVAEUR' => 'KAVAEUR', 'KAVAUSD' => 'KAVAUSD', 'KAVAXBT' => 'KAVAXBT', 'KEEPETH' => 'KEEPETH', 'KEEPEUR' => 'KEEPEUR', 'KEEPUSD' => 'KEEPUSD', 'KEEPXBT' => 'KEEPXBT', 'KNCETH' => 'KNCETH', 'KNCEUR' => 'KNCEUR', 'KNCUSD' => 'KNCUSD', 'KNCXBT' => 'KNCXBT', 'KSMAUD' => 'KSMAUD', 'KSMDOT' => 'KSMDOT', 'KSMETH' => 'KSMETH', 'KSMEUR' => 'KSMEUR', 'KSMGBP' => 'KSMGBP', 'KSMUSD' => 'KSMUSD', 'KSMXBT' => 'KSMXBT', 'LINKAUD' => 'LINKAUD', 'LINKETH' => 'LINKETH', 'LINKEUR' => 'LINKEUR', 'LINKGBP' => 'LINKGBP', 'LINKUSD' => 'LINKUSD', 'LINKUSDT' => 'LINKUSDT', 'LINKXBT' => 'LINKXBT', 'LSKETH' => 'LSKETH', 'LSKEUR' => 'LSKEUR', 'LSKUSD' => 'LSKUSD', 'LSKXBT' => 'LSKXBT', 'LTCAUD' => 'LTCAUD', 'LTCETH' => 'LTCETH', 'LTCGBP' => 'LTCGBP', 'LTCUSDT' => 'LTCUSDT', 'MANAETH' => 'MANAETH', 'MANAEUR' => 'MANAEUR', 'MANAUSD' => 'MANAUSD', 'MANAXBT' => 'MANAXBT', 'NANOETH' => 'NANOETH', 'NANOEUR' => 'NANOEUR', 'NANOUSD' => 'NANOUSD', 'NANOXBT' => 'NANOXBT', 'OCEANEUR' => 'OCEANEUR', 'OCEANGBP' => 'OCEANGBP', 'OCEANUSD' => 'OCEANUSD', 'OCEANXBT' => 'OCEANXBT', 'OMGETH' => 'OMGETH', 'OMGEUR' => 'OMGEUR', 'OMGUSD' => 'OMGUSD', 'OMGXBT' => 'OMGXBT', 'OXTETH' => 'OXTETH', 'OXTEUR' => 'OXTEUR', 'OXTUSD' => 'OXTUSD', 'OXTXBT' => 'OXTXBT', 'PAXGETH' => 'PAXGETH', 'PAXGEUR' => 'PAXGEUR', 'PAXGUSD' => 'PAXGUSD', 'PAXGXBT' => 'PAXGXBT', 'QTUMETH' => 'QTUMETH', 'QTUMEUR' => 'QTUMEUR', 'QTUMUSD' => 'QTUMUSD', 'QTUMXBT' => 'QTUMXBT', 'REPV2ETH' => 'REPV2ETH', 'REPV2EUR' => 'REPV2EUR', 'REPV2USD' => 'REPV2USD', 'REPV2XBT' => 'REPV2XBT', 'SCETH' => 'SCETH', 'SCEUR' => 'SCEUR', 'SCUSD' => 'SCUSD', 'SCXBT' => 'SCXBT', 'SNXAUD' => 'SNXAUD', 'SNXETH' => 'SNXETH', 'SNXEUR' => 'SNXEUR', 'SNXGBP' => 'SNXGBP', 'SNXUSD' => 'SNXUSD', 'SNXXBT' => 'SNXXBT', 'STORJETH' => 'STORJETH', 'STORJEUR' => 'STORJEUR', 'STORJUSD' => 'STORJUSD', 'STORJXBT' => 'STORJXBT', 'TBTCETH' => 'TBTCETH', 'TBTCEUR' => 'TBTCEUR', 'TBTCUSD' => 'TBTCUSD', 'TBTCXBT' => 'TBTCXBT', 'TRXETH' => 'TRXETH', 'TRXEUR' => 'TRXEUR', 'TRXUSD' => 'TRXUSD', 'TRXXBT' => 'TRXXBT', 'UNIETH' => 'UNIETH', 'UNIEUR' => 'UNIEUR', 'UNIUSD' => 'UNIUSD', 'UNIXBT' => 'UNIXBT', 'USDCAUD' => 'USDCAUD', 'USDCEUR' => 'USDCEUR', 'USDCGBP' => 'USDCGBP', 'USDCHF' => 'USDCHF', 'USDCUSD' => 'USDCUSD', 'USDCUSDT' => 'USDCUSDT', 'USDTAUD' => 'USDTAUD', 'USDTCAD' => 'USDTCAD', 'USDTCHF' => 'USDTCHF', 'USDTEUR' => 'USDTEUR', 'USDTGBP' => 'USDTGBP', 'USDTJPY' => 'USDTJPY', 'USDTZUSD' => 'USDTUSD', 'WAVESETH' => 'WAVESETH', 'WAVESEUR' => 'WAVESEUR', 'WAVESUSD' => 'WAVESUSD', 'WAVESXBT' => 'WAVESXBT', 'XBTAUD' => 'XBTAUD', 'XBTCHF' => 'XBTCHF', 'XBTDAI' => 'XBTDAI', 'XBTUSDC' => 'XBTUSDC', 'XBTUSDT' => 'XBTUSDT', 'XDGEUR' => 'XDGEUR', 'XDGUSD' => 'XDGUSD', 'XDGUSDT' => 'XDGUSDT', 'XETCXETH' => 'ETCETH', 'XETCXXBT' => 'ETCXBT', 'XETCZEUR' => 'ETCEUR', 'XETCZUSD' => 'ETCUSD', 'XETHXXBT' => 'ETHXBT', 'XETHXXBT.d' => 'ETHXBT.d', 'XETHZCAD' => 'ETHCAD', 'XETHZCAD.d' => 'ETHCAD.d', 'XETHZEUR' => 'ETHEUR', 'XETHZEUR.d' => 'ETHEUR.d', 'XETHZGBP' => 'ETHGBP', 'XETHZGBP.d' => 'ETHGBP.d', 'XETHZJPY' => 'ETHJPY', 'XETHZJPY.d' => 'ETHJPY.d', 'XETHZUSD' => 'ETHUSD', 'XETHZUSD.d' => 'ETHUSD.d', 'XLTCXXBT' => 'LTCXBT', 'XLTCZEUR' => 'LTCEUR', 'XLTCZJPY' => 'LTCJPY', 'XLTCZUSD' => 'LTCUSD', 'XMLNXETH' => 'MLNETH', 'XMLNXXBT' => 'MLNXBT', 'XMLNZEUR' => 'MLNEUR', 'XMLNZUSD' => 'MLNUSD', 'XREPXETH' => 'REPETH', 'XREPXXBT' => 'REPXBT', 'XREPZEUR' => 'REPEUR', 'XREPZUSD' => 'REPUSD', 'XRPAUD' => 'XRPAUD', 'XRPETH' => 'XRPETH', 'XRPGBP' => 'XRPGBP', 'XRPUSDT' => 'XRPUSDT', 'XTZAUD' => 'XTZAUD', 'XTZETH' => 'XTZETH', 'XTZEUR' => 'XTZEUR', 'XTZGBP' => 'XTZGBP', 'XTZUSD' => 'XTZUSD', 'XTZXBT' => 'XTZXBT', 'XXBTZCAD' => 'XBTCAD', 'XXBTZCAD.d' => 'XBTCAD.d', 'XXBTZEUR' => 'XBTEUR', 'XXBTZEUR.d' => 'XBTEUR.d', 'XXBTZGBP' => 'XBTGBP', 'XXBTZGBP.d' => 'XBTGBP.d', 'XXBTZJPY' => 'XBTJPY', 'XXBTZJPY.d' => 'XBTJPY.d', 'XXBTZUSD' => 'XBTUSD', 'XXBTZUSD.d' => 'XBTUSD.d', 'XXDGXXBT' => 'XDGXBT', 'XXLMXXBT' => 'XLMXBT', 'XXLMZAUD' => 'XLMAUD', 'XXLMZEUR' => 'XLMEUR', 'XXLMZGBP' => 'XLMGBP', 'XXLMZUSD' => 'XLMUSD', 'XXMRXXBT' => 'XMRXBT', 'XXMRZEUR' => 'XMREUR', 'XXMRZUSD' => 'XMRUSD', 'XXRPXXBT' => 'XRPXBT', 'XXRPZCAD' => 'XRPCAD', 'XXRPZEUR' => 'XRPEUR', 'XXRPZJPY' => 'XRPJPY', 'XXRPZUSD' => 'XRPUSD', 'XZECXXBT' => 'ZECXBT', 'XZECZEUR' => 'ZECEUR', 'XZECZUSD' => 'ZECUSD', 'YFIAUD' => 'YFIAUD', 'YFIETH' => 'YFIETH', 'YFIEUR' => 'YFIEUR', 'YFIGBP' => 'YFIGBP', 'YFIUSD' => 'YFIUSD', 'YFIXBT' => 'YFIXBT', 'ZEURZUSD' => 'EURUSD', 'ZGBPZUSD' => 'GBPUSD', 'ZUSDZCAD' => 'USDCAD', 'ZUSDZJPY' => 'USDJPY'];

    function getUrls()
    {
        $apis = [];
        $apis["tickers"] = $this->apiTickersUrl();
        $apis["pairs"] = $this->apiPairsInfoUrl();

        return $apis;
    }

    function apiPairsInfoUrl()
    {
        return ["url" => $this->apiPairsInfo];
    }

    function apiTickersUrl()
    {
        return ["url" => str_replace("{PAIRS}", implode(",", $this->TradablePairs), $this->apiTickers)];
    }

    function process($response)
    {
        $coins = [];

        $PairInfo = [];
        foreach ($response["pairs"]->body->result as $pair => $pair_info) {

            $PairInfo[$pair] = ["base" => $pair_info->base, "quote" => $pair_info->quote];
        }

        foreach ($response["tickers"]->body->result as $pair => $coin) {

            $symbol = str_replace('XBT', 'BTC', $this->TradablePairs[$pair]);
            $coins[] = [
                "pair" => $symbol,
                "last_price" => $coin->c[0],
                "ask_price" => $coin->a[0],
                "bid_price" => $coin->b[0],
                "base" => $PairInfo[$pair]["base"],
                "quote" => $PairInfo[$pair]["quote"],
                "volume" => $coin->v[0]
            ];
        }

        return $coins;
    }

}
