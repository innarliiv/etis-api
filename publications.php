<?PHP
// 10.07.2016 Innar Liiv
// Fetching publication list from ETIS
$count=json_decode(file_get_contents("https://www.etis.ee:7443/api/publication/getcount?Format=json&SearchType=3&Take=1&Skip=0"),true);
$result_size=100;
$max_query=(2+(int)($count["Count"]/$result_size));
echo "Fetching ".$count["Count"]." publications in $max_query queries.\n";

for ($m=0;$m<$max_query;$m++) {
  $skip=$m*$result_size;
  if (file_exists("publications.$skip.csv")) {
    echo "Skipping m=$m / publications.$skip.csv\n";
  } else {
    $query_url="https://www.etis.ee:7443/api/publication/getitems?Format=json&SearchType=3&Take=$result_size&Skip=$skip";
    echo "$m:".$query_url."\n";
    $result=file_get_contents($query_url);
    sleep(1);
    if ($result!="") {
      $file = fopen("publications.$skip.csv","w");
      fwrite($file,$result);
      fclose($file);
    } else { echo "Re-query ".($m*$result_size)."\n";sleep(10);$m--; } // if json was empty, re-query
  }
}
?>

