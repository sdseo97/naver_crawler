<?php
require_once 'config.php';

// GET 요청만 허용
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('GET 요청만 허용됩니다.', 405);
}

session_start();

// 진행률 가져오기
$progress = isset($_SESSION['crawl_progress']) ? $_SESSION['crawl_progress'] : 0;

// 진행률에 따른 메시지
$messages = [
    0 => '크롤링을 준비중입니다...',
    25 => '네이버 부동산 사이트에 접속중입니다...',
    50 => '부동산 데이터를 수집중입니다...',
    75 => '데이터를 분석하고 정리중입니다...',
    100 => '크롤링이 완료되었습니다!'
];

$message = $messages[0];
foreach ($messages as $threshold => $msg) {
    if ($progress >= $threshold) {
        $message = $msg;
    }
}

successResponse([
    'progress' => $progress,
    'message' => $message,
    'completed' => $progress >= 100
]);
?>