<?php
// local에서 Timescale 에서 정보 추출
error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING );
@ini_set("display_errors", 1);

/*************************************************************************
PgSQL 관련 함수 모음
*************************************************************************/

// DB 연결
if(!function_exists('sql_connect_pg')){
function sql_connect_pg($host, $user, $pass, $db=G5_PGSQL_DB)
{
    $pg_link = @pg_connect(" host = $host dbname = $db user = $user password = $pass ") or die('PgSQL Host, User, Password, DB 정보에 오류가 있습니다.');
    $stat = pg_connection_status($pg_link);
    if ($stat) {
        die('Connect Error: '.$pg_link);
    } 
    return $pg_link;
}
}

if(!function_exists('sql_query_pg')){
function sql_query_pg($sql, $error=G5_DISPLAY_SQL_ERROR, $link=null)
{
    global $g5;

    if(!$link)
        $link = $g5['connect_pg'];

    // Blind SQL Injection 취약점 해결
    $sql = trim($sql);

    if ($error) {
        $result = pg_query($link, $sql) or die("<p>$sql</p> <p>error file : {$_SERVER['SCRIPT_NAME']}</p>");
    } else {
        try {
            $result = @pg_query($link, $sql);
        } catch (Exception $e) {
            $result = null;
        }
    }

    return $result;
}
}

if(!function_exists('sql_num_rows_pg')){
function sql_num_rows_pg($result)
{
    return pg_num_rows($result);
    // return pg_num_rows($result);
}
}

// 쿼리를 실행한 후 결과값에서 한행을 얻는다.
if(!function_exists('sql_fetch_pg')){
function sql_fetch_pg($sql, $error=G5_DISPLAY_SQL_ERROR, $link=null)
{
    global $g5;

    if(!$link)
        $link = $g5['connect_pg'];

    $result = sql_query_pg($sql, $error, $link);
    $row = sql_fetch_array_pg($result);
    return $row;
}
}

// 결과값에서 한행 연관배열(이름으로)로 얻는다.
if(!function_exists('sql_fetch_array_pg')){
function sql_fetch_array_pg($result)
{
    if( ! $result) return array();

    try {
        $row = @pg_fetch_assoc($result);
    } catch (Exception $e) {
        $row = null;
    }

    return $row;
}
}

// TimescaleDB 
// get_table_pg('g5_shop_item','it_id',215021535,'it_name')	// 4번째 매개변수는 테이블명과 같으면 생략할 수 있다.
if(!function_exists('get_table_pg')){
function get_table_pg($db_table,$db_field,$db_id,$db_fields='*')
{
    global $db;

	if(!$db_table||!$db_field||!$db_id)
		return false;
    
    $table_name = 'g5_1_'.$db_table;
    $sql = " SELECT ".$db_fields." FROM ".$table_name." WHERE ".$db_field." = '".$db_id."' LIMIT 1 ";
    $row = sql_fetch_pg($sql);
    return $row;
}
}
/*************************************************************************/

// timescale DB connect
// define('G5_PGSQL_HOST', '61.83.89.15');
define('G5_PGSQL_HOST', 'localhost');
define('G5_PGSQL_USER', 'postgres');
define('G5_PGSQL_PASSWORD', 'hanjoo@ingglobal');
define('G5_PGSQL_DB', 'hanjoo_www');

$connect_pg = sql_connect_pg(G5_PGSQL_HOST, G5_PGSQL_USER, G5_PGSQL_PASSWORD) or die('PgSQL Connect Error!!!');
$g5['connect_pg'] = $connect_pg;


$sql = "SELECT dta_type, dta_no FROM g5_1_data_measure_61
        GROUP BY dta_type, dta_no
        ORDER BY dta_type, dta_no
";
$result = sql_query_pg($sql,1);
$rows = sql_num_rows_pg($result);
echo $rows . " row(s) returned.\n";
for ($i=0;$row=sql_fetch_array_pg($result);$i++) {
    // var_dump($row);
    // echo '<br>';
    echo $row['dta_type'].'<br>';
    echo $row['dta_no'].'<br>';
}



