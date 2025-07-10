<?php
require_once 'config.php';

// GET 요청만 허용
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('GET 요청만 허용됩니다.', 405);
}

try {
    $pdo = getDBConnection();
    $formattedHistory = [];
    $total = 0;
    
    // 페이지네이션 파라미터
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? min(50, max(1, intval($_GET['limit']))) : 10;
    $offset = ($page - 1) * $limit;
    
    if ($pdo) {
        // 데이터베이스에서 조회
        try {
            // 전체 개수 조회
            $countStmt = $pdo->query("SELECT COUNT(*) as total FROM search_history");
            $total = $countStmt->fetch()['total'];
            
            // 검색 히스토리 조회
            $stmt = $pdo->prepare("
                SELECT 
                    id,
                    location,
                    JSON_EXTRACT(result_data, '$.summary.totalComplexes') as total_complexes,
                    JSON_EXTRACT(result_data, '$.summary.totalAvailableUnits') as total_units,
                    JSON_EXTRACT(result_data, '$.summary.averagePrice') as average_price,
                    created_at
                FROM search_history 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$limit, $offset]);
            $history = $stmt->fetchAll();
            
            // 결과 포맷팅
            $formattedHistory = array_map(function($item) {
                return [
                    'id' => $item['id'],
                    'location' => $item['location'],
                    'totalComplexes' => intval($item['total_complexes']),
                    'totalUnits' => intval($item['total_units']),
                    'averagePrice' => trim($item['average_price'], '"'),
                    'searchDate' => date('Y-m-d H:i', strtotime($item['created_at']))
                ];
            }, $history);
        } catch (PDOException $e) {
            logMessage("데이터베이스 히스토리 조회 실패, 파일에서 읽기 시도", 'WARNING');
            $pdo = null; // 파일 읽기로 fallback
        }
    }
    
    if (!$pdo) {
        // 파일에서 히스토리 읽기
        $filename = 'search_history_' . date('Y-m-d') . '.json';
        $historyData = loadFromFile($filename) ?: [];
        
        // 최근 며칠간의 파일도 확인
        for ($i = 1; $i <= 7; $i++) {
            $pastDate = date('Y-m-d', strtotime("-{$i} days"));
            $pastFilename = 'search_history_' . $pastDate . '.json';
            $pastData = loadFromFile($pastFilename) ?: [];
            $historyData = array_merge($historyData, $pastData);
        }
        
        // 날짜순 정렬
        usort($historyData, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        $total = count($historyData);
        $paginatedData = array_slice($historyData, $offset, $limit);
        
        // 결과 포맷팅
        $formattedHistory = array_map(function($item) {
            $summary = $item['result_data']['summary'] ?? [];
            return [
                'id' => $item['id'],
                'location' => $item['location'],
                'totalComplexes' => $summary['totalComplexes'] ?? 0,
                'totalUnits' => $summary['totalAvailableUnits'] ?? 0,
                'averagePrice' => $summary['averagePrice'] ?? '0억원',
                'searchDate' => date('Y-m-d H:i', strtotime($item['created_at']))
            ];
        }, $paginatedData);
    }
    
    successResponse([
        'history' => $formattedHistory,
        'pagination' => [
            'currentPage' => $page,
            'totalPages' => ceil($total / $limit),
            'totalItems' => $total,
            'itemsPerPage' => $limit
        ]
    ]);
    
} catch (Exception $e) {
    logMessage("히스토리 조회 오류: " . $e->getMessage(), 'ERROR');
    errorResponse('히스토리를 가져오는 중 오류가 발생했습니다.', 500, 'HISTORY_FETCH_ERROR');
}
?>