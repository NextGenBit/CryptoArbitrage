<?php

define( 'SCRIPT_ROOT', dirname( __file__ ) . "/" );
define( 'EXCHANGE_PATH', SCRIPT_ROOT . "exchanges/" );
define( 'ALLOW_DISABLED', true );

$mysqli = new mysqli( "localhost", "coins", "password", "coins_db" );
$mysqli->query( "TRUNCATE `coins`" );

require ( SCRIPT_ROOT . "multi.php" );

$ExchangesInit = [];

foreach ( getExchanges() as $exchange ) {
    require ( EXCHANGE_PATH . $exchange );
    $ExchangeClass = basename( $exchange, '.php' );
    if ( $ExchangeClass::$enable )
        $ExchangesInit[$ExchangeClass] = new $ExchangeClass();
}

$curl = new MultiURL();

while ( true ) {
    $time = time();

    $handleResponses = [];

    foreach ( $ExchangesInit as $ExchangeName => $ExchangeObject ) {
        $urls = $ExchangeObject->getUrls();
        foreach ( $urls as $name => $request ) {
            if ( ! is_null( $request ) ) {
                $identifier = $curl->addUrl( $request );
                $handleResponses[$ExchangeName][$identifier] = $name;
            }
        }
    }

    $responses = $curl->getResponses();

    foreach ( $ExchangesInit as $ExchangeName => $ExchangeObject ) {
        $exchangeResponse = [];
        foreach ( $handleResponses[$ExchangeName] as $identifier => $name ) {
            $exchangeResponse[$name] = $responses[$identifier];
            $responses[$identifier] = null;
        }
        $coins = $ExchangeObject->process( $exchangeResponse );
        foreach ( $coins as $coin ) {
            AddCoin( $time, $coin, $ExchangeName );
        }
    }

    echo "Coins Refreshed in " . ( time() - $time ) . " Seconds!\n";
    sleep( 10 );
}

function getExchanges()
{
    $exchanges = scandir( SCRIPT_ROOT . "exchanges/" );
    return array_slice( $exchanges, 2 );
}

function AddCoin( $timeNow, $coin, $exchange )
{
    global $mysqli;

    $usd_coins = ["USDT", "USDC", "BUSD"];

    $coin["pair"] = strtoupper( $coin["pair"] );
    $coin["base"] = strtoupper( $coin["base"] );
    $coin["quote"] = strtoupper( $coin["quote"] );
    $coin["orig_pair"] = $coin["pair"];

    if ( in_array( $coin["quote"], $usd_coins ) ) {
        $coin["pair"] = $coin["base"] . "USD";
    }

    $stmt = $mysqli->prepare( "SELECT id FROM `coins` WHERE `exchange` = ? AND `orig_pair` = ?" );
    $stmt->bind_param( "ss", $exchange, $coin["orig_pair"] );
    $stmt->execute();

    $result = $stmt->get_result();

    if ( $result->num_rows > 0 ) {

        $match_row = $result->fetch_assoc();

        $stmt = $mysqli->prepare( "UPDATE `coins` SET `base` = ?,`quote` = ?,`last_price` = ?,`ask_price` = ?,`bid_price` = ?,`volume` = ?,`lastUpdated` = ? WHERE `id` = ?" );
        $stmt->bind_param( "ssddddii", $coin["base"], $coin["quote"], $coin["last_price"], $coin["ask_price"], $coin["bid_price"], $coin["volume"], $timeNow, $match_row["id"] );
        $stmt->execute();

    } else {

        $stmt = $mysqli->prepare( "INSERT INTO `coins` (`exchange`,`pair`,`orig_pair`,`base`,`quote`,`last_price`,`ask_price`,`bid_price`,`volume`) VALUES(?,?,?,?,?,?,?,?,?)" );
        $stmt->bind_param( "sssssdddd", $exchange, $coin["pair"], $coin["orig_pair"], $coin["base"], $coin["quote"], $coin["last_price"], $coin["ask_price"], $coin["bid_price"], $coin["volume"] );
        $stmt->execute();
    }
}
