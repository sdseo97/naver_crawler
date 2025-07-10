# 🏠 Naver Real Estate Crawler

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://php.net)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6%2B-yellow.svg)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)

네이버 부동산에서 실시간으로 부동산 정보를 크롤링하여 사용자에게 깔끔하고 모던한 인터페이스로 제공하는 웹 애플리케이션입니다.

## ✨ 주요 기능

- 🔍 **실시간 부동산 검색**: 지역명 입력만으로 해당 지역의 부동산 정보 수집
- 🎯 **스마트 자동완성**: 편리한 지역 검색 및 인기 지역 빠른 태그
- 📊 **데이터 시각화**: 부동산 가격 분포 차트 및 통계 대시보드
- 📈 **검색 히스토리**: 과거 검색 기록 저장 및 재검색 기능
- 💾 **데이터 내보내기**: JSON 형태로 데이터 다운로드
- 📱 **반응형 디자인**: 모든 디바이스에서 완벽한 사용성

## 🛠 기술 스택

### Frontend
- **HTML5/CSS3**: 시맨틱 마크업 및 모던 CSS
- **Vanilla JavaScript**: 경량화된 프론트엔드 로직
- **Chart.js**: 데이터 시각화
- **Font Awesome**: 아이콘 라이브러리
- **Google Fonts**: 웹폰트 (Noto Sans KR)

### Backend
- **PHP 7.4+**: RESTful API 개발
- **MySQL 8.0**: 데이터베이스 (선택적)
- **JSON File Storage**: MySQL 대체 옵션

### Design
- **모던 UI/UX**: 그라디언트, 글래스모피즘 효과
- **애니메이션**: 부드러운 마이크로인터랙션
- **접근성**: ARIA 라벨 및 키보드 네비게이션 지원

## 🚀 빠른 시작

### 1. 저장소 클론
```bash
git clone https://github.com/your-username/naver_crawler.git
cd naver_crawler
```

### 2. 웹서버 설정
프로젝트를 웹서버 루트 디렉토리에 배치:
```bash
# Apache/Nginx 웹서버 루트에 복사
cp -r . /var/www/html/naver_crawler/

# 권한 설정
chmod 755 logs/
```

### 3. 데이터베이스 설정 (선택적)
```sql
-- MySQL 데이터베이스 생성
CREATE DATABASE mcp4o CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 테이블 생성
SOURCE database/setup.sql;
```

### 4. 설정 파일 수정
`api/config.php`에서 데이터베이스 설정 변경:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'mcp4o');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 5. 웹사이트 접속
브라우저에서 `http://localhost/naver_crawler/` 접속

## 📁 프로젝트 구조

```
naver_crawler/
├── 🌐 index.php                 # 메인 웹페이지
├── 📁 api/                      # PHP 백엔드 API
│   ├── config.php              # 설정 파일
│   ├── crawler.php             # 크롤링 API
│   ├── history.php             # 히스토리 API
│   ├── autocomplete.php        # 자동완성 API
│   └── progress.php            # 진행률 API
├── 📁 assets/                   # 정적 자원
│   ├── css/
│   │   └── style.css           # 메인 스타일시트
│   └── js/
│       └── app.js              # 프론트엔드 로직
├── 📁 database/                 # 데이터베이스
│   └── setup.sql               # MySQL 테이블 생성 스크립트
├── 📁 logs/                     # 로그 파일
├── 📄 README.md                # 프로젝트 문서
├── 📄 WEB_README.md            # 상세 웹 문서
└── 📄 .gitignore               # Git 무시 파일
```

## 📋 사용법

### 1. 지역 검색
1. 검색창에 지역명 입력 (예: "강남구 역삼동")
2. 자동완성에서 원하는 지역 선택
3. "검색하기" 버튼 클릭 또는 엔터키 입력

### 2. 결과 분석
- **통계 카드**: 주요 지표 한눈에 확인
- **데이터 테이블**: 상세 정보 조회 및 정렬
- **가격 차트**: 시각적 가격 분포 분석

### 3. 데이터 활용
- **필터링**: 가격대별 매물 필터
- **정렬**: 가격, 면적, 매물수별 정렬
- **내보내기**: JSON 형태로 데이터 다운로드
- **히스토리**: 과거 검색 기록 재활용

## 🎨 스크린샷

### 메인 화면
![메인 화면](docs/screenshots/main-page.png)

### 검색 결과
![검색 결과](docs/screenshots/search-results.png)

### 모바일 화면
![모바일 화면](docs/screenshots/mobile-view.png)

## 📊 API 엔드포인트

### POST `/api/crawler.php`
부동산 정보 크롤링 실행
```json
{
  "location": "강남구 역삼동"
}
```

### GET `/api/history.php`
검색 히스토리 조회
- Parameters: `page`, `limit`

### GET `/api/autocomplete.php`
지역명 자동완성
- Parameters: `q` (검색어)

### GET `/api/progress.php`
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
`api/config.php`의 좌표 데이터 추가:
```php
$coordinates = [
    '새로운 지역' => ['lat' => 37.5665, 'lon' => 126.9780],
    // ... 기존 지역들
];
```

## ⚠️ 주의사항

- **크롤링 정책**: 네이버 부동산 이용약관 준수 필요
- **요청 제한**: 과도한 요청으로 인한 IP 차단 주의
- **상업적 이용**: 별도 협의 필요
- **보안**: API 키 및 데이터베이스 정보 보호

## 🤝 기여하기

1. 이 저장소를 포크합니다
2. 새 기능 브랜치를 생성합니다 (`git checkout -b feature/amazing-feature`)
3. 변경사항을 커밋합니다 (`git commit -m 'Add amazing feature'`)
4. 브랜치에 푸시합니다 (`git push origin feature/amazing-feature`)
5. Pull Request를 생성합니다

## 📝 라이선스

이 프로젝트는 MIT 라이선스 하에 배포됩니다. 자세한 내용은 [LICENSE](LICENSE) 파일을 참조하세요.

## 🐛 이슈 리포트

버그나 기능 요청이 있으시면 [Issues](https://github.com/your-username/naver_crawler/issues) 페이지에서 등록해 주세요.

## 📞 지원

- 📧 이메일: support@example.com
- 📖 위키: [프로젝트 위키](https://github.com/your-username/naver_crawler/wiki)
- 💬 토론: [Discussions](https://github.com/your-username/naver_crawler/discussions)

## 🌟 스타 히스토리

[![Star History Chart](https://api.star-history.com/svg?repos=your-username/naver_crawler&type=Date)](https://star-history.com/#your-username/naver_crawler&Date)

---

**Made with ❤️ for Smart Real Estate Analysis**

### 🎯 향후 계획

- [ ] 실제 Playwright 크롤링 구현
- [ ] 사용자 인증 시스템
- [ ] 알림 기능 (가격 변동 알림)
- [ ] 지도 시각화
- [ ] 모바일 앱 개발
- [ ] 다국어 지원

### 🏆 기여자

<a href="https://github.com/your-username/naver_crawler/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=your-username/naver_crawler" />
</a>