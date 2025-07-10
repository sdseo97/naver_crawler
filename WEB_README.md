# Smart 부동산 크롤러 웹 애플리케이션

## 🏠 프로젝트 개요
네이버 부동산에서 실시간으로 부동산 정보를 크롤링하여 사용자에게 깔끔하고 모던한 인터페이스로 제공하는 웹 애플리케이션입니다.

## ✨ 주요 기능

### 🔍 실시간 부동산 검색
- 지역명 입력만으로 해당 지역의 부동산 정보 수집
- 자동완성 기능으로 편리한 지역 검색
- 인기 지역 빠른 검색 태그 제공

### 📊 데이터 시각화
- 부동산 가격 분포 차트
- 통계 카드로 한눈에 보는 시장 현황
- 정렬 및 필터링 기능

### 📈 검색 히스토리
- 과거 검색 기록 저장 및 조회
- 재검색 기능으로 빠른 업데이트
- 페이지네이션 지원

### 💾 데이터 내보내기
- JSON 형태로 데이터 다운로드
- Excel 등 다른 프로그램에서 활용 가능

## 🛠 기술 스택

### Frontend
- **HTML5/CSS3**: 시맨틱 마크업 및 모던 CSS
- **JavaScript (Vanilla)**: 프론트엔드 로직 구현
- **Chart.js**: 데이터 시각화
- **Font Awesome**: 아이콘
- **Google Fonts**: 웹폰트 (Noto Sans KR)

### Backend
- **PHP 7.4+**: 백엔드 API 개발
- **MySQL 8.0**: 데이터베이스 (선택적)
- **JSON File Storage**: MySQL 대체 옵션

### 디자인
- **반응형 웹 디자인**: 모든 디바이스 지원
- **모던 UI/UX**: 그라디언트, 블러 효과, 애니메이션
- **접근성**: ARIA 라벨 및 키보드 네비게이션 지원

## 📁 프로젝트 구조

```
/mcp/
├── index.php                 # 메인 웹페이지
├── api/                      # PHP 백엔드 API
│   ├── config.php           # 설정 파일
│   ├── crawler.php          # 크롤링 API
│   ├── history.php          # 히스토리 API
│   ├── autocomplete.php     # 자동완성 API
│   └── progress.php         # 진행률 API
├── assets/                  # 정적 자원
│   ├── css/
│   │   └── style.css        # 메인 스타일시트
│   └── js/
│       └── app.js           # 프론트엔드 로직
├── database/                # 데이터베이스
│   └── setup.sql           # MySQL 테이블 생성 스크립트
├── logs/                    # 로그 파일
└── README.md               # 프로젝트 문서
```

## 🚀 설치 및 실행

### 1. 서버 요구사항
- PHP 7.4 이상
- MySQL 8.0 이상 (선택적)
- Apache/Nginx 웹서버

### 2. 프로젝트 설정
```bash
# 프로젝트 디렉토리를 웹서버 루트에 배치
# 예: /var/www/html/smart-real-estate/

# 로그 디렉토리 권한 설정
chmod 755 logs/

# PHP 설정 확인
php -v
```

### 3. 데이터베이스 설정 (선택적)
```sql
-- MySQL 데이터베이스 생성
CREATE DATABASE mcp4o CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 사용자 생성 및 권한 부여
CREATE USER 'web_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON mcp4o.* TO 'web_user'@'localhost';
FLUSH PRIVILEGES;

-- 테이블 생성
SOURCE database/setup.sql;
```

### 4. 설정 파일 수정
```php
// api/config.php에서 데이터베이스 설정 변경
define('DB_HOST', 'localhost');
define('DB_NAME', 'mcp4o');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

## 📋 사용법

### 1. 웹사이트 접속
- 브라우저에서 `http://localhost/smart-real-estate/` 접속

### 2. 지역 검색
1. 검색창에 지역명 입력 (예: "강남구 역삼동")
2. 자동완성에서 원하는 지역 선택
3. "검색하기" 버튼 클릭 또는 엔터키 입력

### 3. 결과 확인
- 통계 카드에서 주요 지표 확인
- 테이블에서 상세 정보 조회
- 차트에서 가격 분포 시각화

### 4. 데이터 활용
- 정렬/필터 기능으로 원하는 조건 설정
- "내보내기" 버튼으로 JSON 다운로드
- 히스토리에서 과거 검색 결과 조회

## 🎨 UI/UX 특징

### 모던 디자인
- **그라디언트 배경**: 시각적 임팩트
- **글래스모피즘**: 블러 효과와 반투명성
- **마이크로인터랙션**: 호버, 클릭 효과
- **스무드 애니메이션**: 부드러운 전환 효과

### 반응형 디자인
- **모바일 최적화**: 스마트폰에서도 완벽한 사용성
- **태블릿 지원**: 중간 크기 화면 대응
- **데스크톱 확장**: 대화면에서의 레이아웃 최적화

### 접근성
- **키보드 네비게이션**: 마우스 없이도 사용 가능
- **스크린 리더 지원**: 시각 장애인 접근성
- **고대비 모드**: 색상 대비 최적화

## 📊 API 엔드포인트

### POST /api/crawler.php
부동산 정보 크롤링 실행
```json
{
  "location": "강남구 역삼동"
}
```

### GET /api/history.php
검색 히스토리 조회
- 쿼리 파라미터: `page`, `limit`

### GET /api/autocomplete.php
지역명 자동완성
- 쿼리 파라미터: `q` (검색어)

### GET /api/progress.php
크롤링 진행률 조회

## 🔧 커스터마이징

### 색상 테마 변경
`assets/css/style.css`의 CSS 변수 수정:
```css
:root {
    --primary-color: #2563eb;
    --accent-color: #06b6d4;
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

### 지역 데이터 추가
`api/config.php`의 `getCoordinatesFromLocation()` 함수에 지역 추가:
```php
$coordinates = [
    '새로운 지역' => ['lat' => 37.5665, 'lon' => 126.9780],
    // ... 기존 지역들
];
```

## 🚨 주의사항

### 크롤링 관련
- 과도한 요청으로 인한 IP 차단 주의
- 네이버 부동산 이용약관 준수
- 상업적 이용시 별도 협의 필요

### 보안
- API 키 및 데이터베이스 비밀번호 보호
- SQL 인젝션 방지 (PDO 사용)
- XSS 공격 방지 (입력값 검증)

### 성능
- 로그 파일 정기적 정리
- 데이터베이스 인덱스 최적화
- 캐싱 전략 고려

## 🤝 기여하기

1. 이 저장소를 포크합니다
2. 새 기능 브랜치를 생성합니다 (`git checkout -b feature/amazing-feature`)
3. 변경사항을 커밋합니다 (`git commit -m 'Add amazing feature'`)
4. 브랜치에 푸시합니다 (`git push origin feature/amazing-feature`)
5. Pull Request를 생성합니다

## 📝 라이선스
이 프로젝트는 교육 및 연구 목적으로 개발되었습니다.

## 🐛 버그 리포트
문제를 발견하신 경우 이슈를 생성해 주세요.

## 📞 지원
- 이메일: support@example.com
- 문서: [Wiki 페이지]
- FAQ: [자주 묻는 질문]

---

**Made with ❤️ for Smart Real Estate Analysis**