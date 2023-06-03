<?php
// local에서 Timescale 에서 정보 추출
error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING );
@ini_set("display_errors", 1);

$dsn = "pgsql:dbname=test_db;port=5432 host=localhost";
try {
    $db = new PDO($dsn, "postgres", "super@ingglobal");
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
    echo $e->getMessage();
}

$sql = "  SELECT time_bucket('5 minutes', event_time) AS five_min, avg(lower_heat)
            FROM test1
            GROUP BY five_min
            ORDER BY five_min DESC LIMIT 10
";
$stmt = $db->query($sql);
for ($i=0; $row=sql_fetch_array_pg($result); $i++) {
    var_dump($row);
    echo '<br>';
    echo $row['five_min'].'<br>';
    echo $row['avg'].'<br>';
}

?>
