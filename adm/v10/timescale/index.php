<?php
// Timescale에서 제공하는 Cloud 서비스에 연결해서 정보 추출
error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING );
@ini_set("display_errors", 1);

// define('G5_MYSQL_HOST', 'vbjysvzz2g.hp6tz73i1r.tsdb.cloud.timescale.com');
// define('G5_MYSQL_USER', 'tsdbadmin');
// define('G5_MYSQL_PASSWORD', '^^tiAnne@@740620');
// define('G5_MYSQL_DB', 'tsdb');
// define('G5_MYSQL_SET_MODE', true);
//
// $link = @mysqli_connect(G5_MYSQL_HOST, G5_MYSQL_USER, G5_MYSQL_PASSWORD) or die('MySQL Host, User, Password, DB 정보에 오류가 있습니다.');
//
// $select_db  = sql_select_db(G5_MYSQL_DB, $connect_db) or die('MySQL DB Error!!!');
//
// echo 5;


// $db = new PDO('pgsql:dbname=tsdb;port=32530 host=vbjysvzz2g.hp6tz73i1r.tsdb.cloud.timescale.com', 'tsdbadmin', '^^tiAnne@@740620');
// $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// $db->beginTransaction();
// $oid = $db->pgsqlLOBCreate();
//
// $stmt = $db->prepare("  SELECT ROUND(AVG(fill_level), 2) AS avg_fill_level
//                             , time_bucket('10m',time) AS bucket
//                             , sensors.country_code, sensors.machine_id
//                         FROM fill_measurements, sensors
//                         WHERE sensor_id = sensors.id
//                             AND time >= '2021-04-05 02:00' AND time >= '2021-04-05 3:00'
//                         GROUP BY bucket, sensors.country_code, sensors.machine_id
//                         HAVING ROUND(AVG(fill_level),2) >= 251;
// ");
// $stmt->execute();
// // $stmt->execute(array($some_id));
// // $stmt->bindColumn('oid', $lob, PDO::PARAM_LOB);
// // $stmt->bindColumn('blob_type', $blob_type, PDO::PARAM_STR);
// // $stmt->bindColumn('filesize', $filesize, PDO::PARAM_STR);
// $stmt->fetch(PDO::FETCH_BOUND);
// $stream = $pdo->pgsqlLOBOpen($lob, 'r');
// $data = fread($stream, $filesize);
// header("Content-type: $blob_type");
// echo $data;

$dsn = "pgsql:dbname=tsdb;port=32530 host=vbjysvzz2g.hp6tz73i1r.tsdb.cloud.timescale.com";
// $dsn = "mysql:host=localhost;port=3306;dbname=testdb;charset=utf8";
try {
    $db = new PDO($dsn, "tsdbadmin", "^^tiAnne@@740620");
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // $keyword = "%테스트%";
    // $no = 1;
    // $query = "SELECT num, name FROM tb_test WHERE name LIKE ? AND num > ?";
    // $stmt = $db->prepare($query);
    // $stmt->execute(array($keyword, $no));

    $query = "  SELECT ROUND(AVG(fill_level), 2) AS avg_fill_level
                     , time_bucket('10m',time) AS bucket
                     , sensors.country_code, sensors.machine_id
                 FROM fill_measurements, sensors
                 WHERE sensor_id = sensors.id
                     AND time >= '2021-04-05 02:00' AND time >= '2021-04-05 3:00'
                 GROUP BY bucket, sensors.country_code, sensors.machine_id
                 HAVING ROUND(AVG(fill_level),2) >= 251;
    ";
    // echo $query.'<br>';
    // $stmt = $db->prepare($query);
    // $stmt->execute();
    // $result = $stmt->fetchAll(PDO::FETCH_NUM);
    // // var_dump(json_encode($result));
    // // echo count($result);
    // // echo '<br>';
    // for ($i=0; $row=sql_fetch_array_pg($result); $i++) {
    // // for($i = 0; $i < count($result); $i++) {
    //     var_dump($row);
    //     printf ("%s : %s <br />", $result[$i][0], $result[$i][1]);
    // }
    $stmt = $db->query($query);
    // var_dump(json_encode($result));
    // echo count($result);
    // echo '<br>';
    for ($i=0; $row=sql_fetch_array_pg($result); $i++) {
        var_dump($row);
        echo '<br>';
        echo $row['avg_fill_level'].'<br>';
        echo $row['bucket'].'<br>';
        echo $row['country_code'].'<br>';
        echo $row['machine_id'].'<br>';

    }
}
catch(PDOException $e) {
    echo $e->getMessage();
}

?>
