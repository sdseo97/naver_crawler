<?php
require_once 'config.php';

// GET 요청만 허용
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('GET 요청만 허용됩니다.', 405);
}

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($query) < 2) {
    successResponse([]);
}

// 지역 데이터 (실제로는 데이터베이스나 외부 API에서 가져옴)
$locations = [
    // 서울특별시
    '서울특별시 강남구 역삼동',
    '서울특별시 강남구 삼성동',
    '서울특별시 강남구 청담동',
    '서울특별시 서초구 서초동',
    '서울특별시 서초구 반포동',
    '서울특별시 송파구 잠실동',
    '서울특별시 송파구 문정동',
    '서울특별시 강서구 마곡동',
    '서울특별시 마포구 상암동',
    '서울특별시 용산구 한남동',
    '서울특별시 성동구 성수동',
    '서울특별시 광진구 건대입구',
    
    // 경기도
    '경기도 성남시 분당구 정자동',
    '경기도 성남시 분당구 서현동',
    '경기도 성남시 수정구 신흥동',
    '경기도 용인시 수지구 동천동',
    '경기도 용인시 기흥구 신갈동',
    '경기도 하남시 창우동',
    '경기도 하남시 신장동',
    '경기도 과천시 중앙동',
    '경기도 안양시 동안구 평촌동',
    '경기도 군포시 산본동',
    '경기도 부천시 중동',
    '경기도 김포시 장기동',
    '경기도 파주시 운정동',
    '경기도 고양시 일산동구 장항동',
    '경기도 고양시 덕양구 행신동',
    
    // 인천광역시
    '인천광역시 연수구 송도동',
    '인천광역시 남동구 구월동',
    '인천광역시 서구 청라동',
    
    // 대전광역시
    '대전광역시 유성구 도룡동',
    '대전광역시 서구 둔산동',
    
    // 대구광역시
    '대구광역시 수성구 범어동',
    '대구광역시 달서구 상인동',
    
    // 부산광역시
    '부산광역시 해운대구 해운대동',
    '부산광역시 수영구 광안동',
    '부산광역시 연제구 거제동'
];

// 검색어와 매칭되는 지역 찾기
$matches = array_filter($locations, function($location) use ($query) {
    return stripos($location, $query) !== false;
});

// 결과를 관련도 순으로 정렬
usort($matches, function($a, $b) use ($query) {
    $posA = stripos($a, $query);
    $posB = stripos($b, $query);
    
    if ($posA === $posB) {
        return strlen($a) - strlen($b); // 짧은 것 우선
    }
    
    return $posA - $posB; // 앞에 나오는 것 우선
});

// 최대 10개까지만 반환
$results = array_slice($matches, 0, 10);

// 검색 히스토리에서 관련 지역도 추가
try {
    $pdo = getDBConnection();
    if ($pdo) {
        $stmt = $pdo->prepare("
            SELECT DISTINCT location 
            FROM search_history 
            WHERE location LIKE ? 
            ORDER BY created_at DESC 
            LIMIT 5
        ");
        $stmt->execute(["%{$query}%"]);
        $historyResults = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // 중복 제거하고 합치기
        $results = array_unique(array_merge($results, $historyResults));
        $results = array_slice($results, 0, 10);
    }
} catch (PDOException $e) {
    logMessage("자동완성 히스토리 조회 오류: " . $e->getMessage(), 'ERROR');
}

successResponse($results);
?>