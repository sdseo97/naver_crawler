<?php
// 데이터베이스 설정
define('DB_HOST', 'localhost');
define('DB_NAME', 'mcp4o');
define('DB_USER', 'root');
define('DB_PASS', '1Q2w3e4r5t!');

// API 설정
define('API_VERSION', '1.0.0');
define('LOG_PATH', __DIR__ . '/../logs/');

// CORS 헤더 설정
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// OPTIONS 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 데이터베이스 연결 함수
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        logMessage("Database connection failed, using file storage instead", 'WARNING');
        return null;
    }
}

// 파일 기반 데이터 저장 함수 (데이터베이스 없을 때)
function saveToFile($filename, $data) {
    $filepath = LOG_PATH . $filename;
    if (!is_dir(LOG_PATH)) {
        mkdir(LOG_PATH, 0755, true);
    }
    return file_put_contents($filepath, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

// 파일에서 데이터 읽기 함수
function loadFromFile($filename) {
    $filepath = LOG_PATH . $filename;
    if (file_exists($filepath)) {
        $content = file_get_contents($filepath);
        return json_decode($content, true);
    }
    return null;
}

// 로그 함수
function logMessage($message, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
    
    if (!is_dir(LOG_PATH)) {
        mkdir(LOG_PATH, 0755, true);
    }
    
    file_put_contents(LOG_PATH . 'api.log', $logEntry, FILE_APPEND | LOCK_EX);
}

// JSON 응답 함수
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

// 에러 응답 함수
function errorResponse($message, $statusCode = 400, $errorCode = null) {
    logMessage("Error: {$message}", 'ERROR');
    jsonResponse([
        'success' => false,
        'error' => [
            'message' => $message,
            'code' => $errorCode
        ],
        'timestamp' => date('c')
    ], $statusCode);
}

// 성공 응답 함수
function successResponse($data, $message = null) {
    jsonResponse([
        'success' => true,
        'data' => $data,
        'message' => $message,
        'timestamp' => date('c')
    ]);
}

// 지역 좌표 변환 함수
function getCoordinatesFromLocation($location) {
    // 간단한 지역명-좌표 매핑 (실제로는 지오코딩 API 사용 권장)
    $coordinates = [
        '하남시 창우동' => ['lat' => 37.5388065, 'lon' => 127.2229095],
        '강남구 역삼동' => ['lat' => 37.5014, 'lon' => 127.0366],
        '송파구 잠실동' => ['lat' => 37.5133, 'lon' => 127.1028],
        '성남시 분당구' => ['lat' => 37.3595, 'lon' => 127.1052],
        '서초구 서초동' => ['lat' => 37.4836, 'lon' => 127.0327],
        '마포구 상암동' => ['lat' => 37.5789, 'lon' => 126.8895]
    ];
    
    foreach ($coordinates as $area => $coords) {
        if (strpos($location, $area) !== false || strpos($area, $location) !== false) {
            return $coords;
        }
    }
    
    // 기본 좌표 (서울시청)
    return ['lat' => 37.5665, 'lon' => 126.9780];
}

// 입력 검증 함수
function validateInput($data, $required = []) {
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            errorResponse("필수 필드가 누락되었습니다: {$field}", 400, 'MISSING_FIELD');
        }
    }
    return true;
}
?>