# CryptoArbitrage

Simple Application that reads the API from the most popular CryptoCurrencies exchanges and retrieves the list of the traidable Pairs.
It uses mysql Database to store the pairs along with the latest prices and volumes.

Edit the mysql Connections details in the start.php File

To create the table use the following

```
CREATE TABLE `coins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exchange` varchar(255) NOT NULL,
  `pair` varchar(255) NOT NULL,
  `base` varchar(255) NOT NULL,
  `quote` varchar(255) NOT NULL,
  `orig_pair` varchar(255) NOT NULL,
  `last_price` double NOT NULL,
  `ask_price` double NOT NULL,
  `bid_price` double NOT NULL,
  `volume` double NOT NULL,
  `lastUpdated` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `exchange` (`exchange`),
  KEY `pair` (`pair`),
  KEY `orig_pair` (`orig_pair`)
) ENGINE=InnoDB AUTO_INCREMENT=2612 DEFAULT CHARSET=latin1
```

After getting the traidable Pairs you can run a query such as

```
SELECT
          t2.pair,
          t1.volume as Volume1,
          t2.volume as Volume2,
          t1.exchange as Exchange1,
          t2.exchange as Exchange2,
          t1.ask_price as AskPrice1,
          t2.ask_price as AskPrice2,
          t1.last_price as LastPrice1,
          t2.last_price as LastPrice2,
          t1.bid_price as BidPrice1,
          t2.bid_price as BidPrice2
      FROM
          `coins` t1
      INNER JOIN coins t2 ON
          t2.exchange <> t1.exchange AND t2.pair = t1.pair AND ABS(t2.lastUpdated-t1.lastUpdated) <= 6 AND t2.id > t1.id
      WHERE t1.volume > 0 AND t2.volume > 0 
      ORDER BY ABS((t1.last_price / t2.last_price) - 1)  DESC
```

to fetch all the pairs in all different exchanges and use them to compare the prices.

* Don't forget to edit the API keys in the Binance PHP File. On the other exchanges it is possible to retrieve the traidable pairs without the need to provide an API key/secret.

# Information

You should compare the ASK price with the BID price to see if there is an arbitrage value. Use the application at your own risk
