<?php
// local에서 Timescale 에서 정보 추출
error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING );
@ini_set("display_errors", 1);

$dsn = "pgsql:dbname=hanjoo_www;port=5432 host=localhost";
try {
    $db = new PDO($dsn, "postgres", "hanjoo@ingglobal");
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
    echo $e->getMessage();
}

$sql = "SELECT dta_type, dta_no FROM g5_1_data_measure_61
        GROUP BY dta_type, dta_no
        ORDER BY dta_type, dta_no
";
$stmt = $db->query($sql);
for ($i=0; $row=sql_fetch_array_pg($result); $i++) {
    var_dump($row);
    echo '<br>';
    echo $row['dta_type'].'<br>';
    echo $row['dta_no'].'<br>';
}

?>
