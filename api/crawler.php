<?php
require_once 'config.php';

// POST 요청만 허용
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('POST 요청만 허용됩니다.', 405);
}

// 입력 데이터 받기
$input = json_decode(file_get_contents('php://input'), true);
validateInput($input, ['location']);

$location = trim($input['location']);
logMessage("크롤링 요청: {$location}");

// 좌표 변환
$coords = getCoordinatesFromLocation($location);
$lat = $coords['lat'];
$lon = $coords['lon'];

// 네이버 부동산 크롤링 시뮬레이션 함수
function crawlNaverRealEstate($location, $lat, $lon) {
    logMessage("크롤링 시작: {$location} ({$lat}, {$lon})");
    
    // 실제 크롤링 대신 시뮬레이션 데이터 생성
    // 실제 환경에서는 Playwright나 cURL을 사용하여 크롤링
    $simulatedData = generateSimulatedData($location);
    
    // 데이터베이스에 저장
    saveToDatabase($location, $simulatedData);
    
    logMessage("크롤링 완료: {$location}");
    return $simulatedData;
}

// 시뮬레이션 데이터 생성 함수
function generateSimulatedData($location) {
    $basePrice = rand(30000, 80000); // 기본 가격 (만원)
    $complexCount = rand(5, 15);
    
    $complexes = [];
    for ($i = 1; $i <= $complexCount; $i++) {
        $area = rand(60, 150); // 면적 (㎡)
        $pricePerPyeong = rand(2000, 3500); // 평당가 (만원)
        $avgPrice = round(($area * $pricePerPyeong / 3.305) / 10000, 1); // 평균가격 (억원)
        $minPrice = round($avgPrice * 0.8, 1);
        $maxPrice = round($avgPrice * 1.3, 1);
        $units = rand(10, 150);
        
        $complexes[] = [
            'id' => $i,
            'pricePerPyeong' => number_format($pricePerPyeong) . '만원',
            'averagePrice' => $avgPrice . '억원',
            'priceRange' => $minPrice . '억원 ~ ' . $maxPrice . '억원',
            'area' => $area . '㎡',
            'availableUnits' => $units,
            'type' => '아파트'
        ];
    }
    
    // 통계 계산
    $totalUnits = array_sum(array_column($complexes, 'availableUnits'));
    $prices = array_map(function($c) { 
        return floatval(str_replace('억원', '', $c['averagePrice'])); 
    }, $complexes);
    $areas = array_map(function($c) { 
        return intval(str_replace('㎡', '', $c['area'])); 
    }, $complexes);
    
    $avgPrice = round(array_sum($prices) / count($prices), 1);
    $avgArea = round(array_sum($areas) / count($areas));
    $minPrice = min($prices);
    $maxPrice = max($prices);
    
    return [
        'timestamp' => date('c'),
        'location' => $location,
        'source' => '네이버 부동산 (시뮬레이션)',
        'complexes' => $complexes,
        'summary' => [
            'totalComplexes' => $complexCount,
            'totalAvailableUnits' => $totalUnits,
            'priceRange' => $minPrice . '억원 ~ ' . $maxPrice . '억원',
            'averagePrice' => $avgPrice . '억원',
            'averageArea' => $avgArea . '㎡',
            'mostExpensive' => $maxPrice . '억원',
            'cheapest' => $minPrice . '억원'
        ]
    ];
}

// 데이터베이스 저장 함수
function saveToDatabase($location, $data) {
    $pdo = getDBConnection();
    if ($pdo) {
        try {
            // 검색 히스토리 저장
            $stmt = $pdo->prepare("
                INSERT INTO search_history (location, result_data, created_at) 
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$location, json_encode($data, JSON_UNESCAPED_UNICODE)]);
            
            logMessage("데이터베이스 저장 완료: {$location}");
            return true;
        } catch (PDOException $e) {
            logMessage("데이터베이스 저장 실패: " . $e->getMessage(), 'ERROR');
        }
    }
    
    // 데이터베이스 연결 실패시 파일로 저장
    $filename = 'search_history_' . date('Y-m-d') . '.json';
    $historyData = loadFromFile($filename) ?: [];
    
    $historyData[] = [
        'id' => time() . rand(1000, 9999),
        'location' => $location,
        'result_data' => $data,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    if (saveToFile($filename, $historyData)) {
        logMessage("파일 저장 완료: {$location}");
        return true;
    } else {
        logMessage("파일 저장 실패: {$location}", 'ERROR');
        return false;
    }
}

try {
    // 크롤링 진행 상황 업데이트를 위한 세션 시작
    session_start();
    $_SESSION['crawl_progress'] = 0;
    
    // 진행률 업데이트
    $_SESSION['crawl_progress'] = 25;
    
    // 크롤링 실행
    $result = crawlNaverRealEstate($location, $lat, $lon);
    
    $_SESSION['crawl_progress'] = 75;
    
    // 결과 검증
    if (!$result || !isset($result['complexes'])) {
        errorResponse('크롤링 결과를 가져올 수 없습니다.', 500, 'CRAWL_FAILED');
    }
    
    $_SESSION['crawl_progress'] = 100;
    
    // 성공 응답
    successResponse($result, '크롤링이 성공적으로 완료되었습니다.');
    
} catch (Exception $e) {
    logMessage("크롤링 오류: " . $e->getMessage(), 'ERROR');
    errorResponse('크롤링 중 오류가 발생했습니다: ' . $e->getMessage(), 500, 'CRAWL_ERROR');
}
?>