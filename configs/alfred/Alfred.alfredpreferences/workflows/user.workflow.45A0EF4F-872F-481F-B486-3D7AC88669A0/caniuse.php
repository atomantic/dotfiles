<?php
// ****************
//error_reporting(0);
require_once('workflows.php');
$w = new Workflows();

function getSSLPage($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function browserVersion($stats) {
    $version = 0;
    $support = '';
    foreach ($stats as $key => $val) {
        $key = floatval($key);
        if ($version < $key && $val == 'y') {
            $support = $key."+";
            break;
        } elseif ($version < $key && strpos($val, 'y x') !== false) {
            $version = $key;
            $support = $key."-px-";
        } elseif ($version < $key && strpos($val, 'a') !== false) {
            $version = $key;
            $support = $key."!pa.";
        } elseif ($version < $key && strpos($val, 'p') !== false) {
            $version = $key;
            $support = $key."w/pl";
        }
    }
    return $support ? $support : "n/a";
}

// cache
if ( filemtime("data.json") <= (time() - 86400 * 7)) {
    $data = json_decode(getSSLPage("https://raw.githubusercontent.com/Fyrd/caniuse/master/data.json"));
    $arr = array();
    foreach ($data->data as $key => $val) {
        $title = $val->title;
        $url = "http://caniuse.com/#feat=" . $key;
        $description = $val->description;
        $keywords = $val->keywords;
        $name = $key;

        $stats = array();
        foreach ($val->stats as $browser => $stat) {
            $stats[$browser] = browserVersion($val->stats->$browser);
        }

        $arr[] = array(
            "name" => $name,
            "url" => $url,
            "keywords" => $keywords,
            "title" => $title,
            "description" =>str_replace("&mdash;","-",html_entity_decode(trim(str_replace("\n"," ",strip_tags($val->description))))),
            "stats" => "[IE:{$stats['ie']}, FF:{$stats['firefox']}, GC:{$stats['chrome']}, S:{$stats['safari']}]"
        );
    }
    if (count($arr)) {
        file_put_contents("data.json", json_encode($arr));
    }
}

if (!isset($query)) { $query = urlencode( "css" ); }

$data = json_decode(file_get_contents("data.json"));

function registerResult($result) {
    global $w;
    $w->result( $result->title, $result->url, $result->title." ".$result->stats, $result->description, "icon.png" );
    return;
}

$found = array();
$query = strtolower(trim($query));

foreach ($data as $key => $result) {
    $value = strtolower(trim($result->title));
    $description = utf8_decode(strip_tags($result->description));
    $keywords = utf8_decode($result->keywords);
    $name = $result->name;

    if (strpos( $value, $query ) === 0) {
        if (!isset($found[$value])) {
            $found[$value] = true;
            registerResult($result);
        }
    }
    else if (strpos($value, $query) > 0) {
        if (!isset($found[$value])) {
            $found[$value] = true;
            registerResult($result);
        }
    }
    else if (strpos($name, $query) === 0) {
        if (!isset($found[$value])) {
            $found[$value] = true;
            registerResult($result);
        }
    }
    else if (strpos($keywords, $query) !== false) {
        if (!isset($found[$value])) {
            $found[$value] = true;
            registerResult($result);
        }
    }
    else if (strpos($description, $query) !== false) {
        if (!isset($found[$value])) {
            $found[$value] = true;
            registerResult($result);
        }
    }
}

echo $w->toxml();
// ****************
?>
