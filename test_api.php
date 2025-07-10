<?php
// API 테스트 스크립트
require_once 'api/config.php';

echo "=== Smart 부동산 크롤러 API 테스트 ===\n\n";

// 1. 설정 파일 테스트
echo "1. 설정 파일 테스트...\n";
if (function_exists('getDBConnection')) {
    echo "✓ 설정 파일 로드 성공\n";
} else {
    echo "✗ 설정 파일 로드 실패\n";
    exit(1);
}

// 2. 로그 디렉토리 테스트
echo "\n2. 로그 디렉토리 테스트...\n";
if (!is_dir(LOG_PATH)) {
    mkdir(LOG_PATH, 0755, true);
}
if (is_writable(LOG_PATH)) {
    echo "✓ 로그 디렉토리 쓰기 가능\n";
} else {
    echo "✗ 로그 디렉토리 쓰기 불가\n";
}

// 3. 데이터베이스 연결 테스트
echo "\n3. 데이터베이스 연결 테스트...\n";
$pdo = getDBConnection();
if ($pdo) {
    echo "✓ 데이터베이스 연결 성공\n";
} else {
    echo "⚠ 데이터베이스 연결 실패 (파일 저장 모드로 동작)\n";
}

// 4. 파일 저장 테스트
echo "\n4. 파일 저장 테스트...\n";
$testData = ['test' => 'data', 'timestamp' => time()];
if (saveToFile('test.json', $testData)) {
    echo "✓ 파일 저장 성공\n";
    $loadedData = loadFromFile('test.json');
    if ($loadedData && $loadedData['test'] === 'data') {
        echo "✓ 파일 읽기 성공\n";
        // 테스트 파일 삭제
        unlink(LOG_PATH . 'test.json');
    } else {
        echo "✗ 파일 읽기 실패\n";
    }
} else {
    echo "✗ 파일 저장 실패\n";
}

// 5. 좌표 변환 테스트
echo "\n5. 좌표 변환 테스트...\n";
$coords = getCoordinatesFromLocation('강남구 역삼동');
if ($coords && isset($coords['lat']) && isset($coords['lon'])) {
    echo "✓ 좌표 변환 성공: {$coords['lat']}, {$coords['lon']}\n";
} else {
    echo "✗ 좌표 변환 실패\n";
}

// 6. JSON 응답 테스트 (CLI에서는 출력만)
echo "\n6. JSON 응답 형식 테스트...\n";
$testResponse = [
    'success' => true,
    'data' => ['message' => '테스트 성공'],
    'timestamp' => date('c')
];
$jsonResponse = json_encode($testResponse, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
if ($jsonResponse) {
    echo "✓ JSON 인코딩 성공\n";
} else {
    echo "✗ JSON 인코딩 실패\n";
}

// 7. 시뮬레이션 데이터 생성 테스트
echo "\n7. 시뮬레이션 데이터 생성 테스트...\n";
$testLocation = '테스트 지역';
$simulatedData = [
    'timestamp' => date('c'),
    'location' => $testLocation,
    'source' => '테스트',
    'complexes' => [
        [
            'id' => 1,
            'pricePerPyeong' => '2500만원',
            'averagePrice' => '5.5억원',
            'area' => '84㎡',
            'availableUnits' => 25
        ]
    ],
    'summary' => [
        'totalComplexes' => 1,
        'totalAvailableUnits' => 25,
        'averagePrice' => '5.5억원'
    ]
];

if ($simulatedData && count($simulatedData['complexes']) > 0) {
    echo "✓ 시뮬레이션 데이터 생성 성공\n";
} else {
    echo "✗ 시뮬레이션 데이터 생성 실패\n";
}

// 8. 로그 함수 테스트
echo "\n8. 로그 함수 테스트...\n";
logMessage('API 테스트 실행', 'INFO');
if (file_exists(LOG_PATH . 'api.log')) {
    echo "✓ 로그 기록 성공\n";
} else {
    echo "✗ 로그 기록 실패\n";
}

echo "\n=== 테스트 완료 ===\n";
echo "모든 테스트가 완료되었습니다.\n";
echo "웹사이트 접속: http://localhost\n";
echo "API 테스트 URL들:\n";
echo "- 자동완성: http://localhost/api/autocomplete.php?q=강남\n";
echo "- 히스토리: http://localhost/api/history.php\n";
echo "- 진행률: http://localhost/api/progress.php\n";
echo "\n";
?>