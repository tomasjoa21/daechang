// 설정 페이지 관련
// 네이티브 코어 업데이트 관련
// 정보를 저장하고 관리합니다.

# 네이티브 코어 버전 동기화 파일들입니다.
1. /config.php
    define('G5_DB_ENGINE', 'MyISAM');
    ...
    // define('G5_SMTP',      '127.0.0.1');
    define('G5_SMTP',      '116.120.58.58');
2. /plugin/jquery-ui/datepicker.php
    jquery-ui 충돌이 나요. 예전 꺼는 주석 처리하고 별도로 선언한 최근 jquery-ui를 사용하도록 합니다.
    

