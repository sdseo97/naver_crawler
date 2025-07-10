# 네이버 부동산 페이지 분석 리포트

## 분석 개요
- **분석 대상**: https://new.land.naver.com/complexes?ms=37.5388065,127.2229095,17&a=APT&b=A1&e=RETAIL
- **분석 시간**: 2025-07-10T03:32:55.328Z
- **페이지 제목**: 네이버페이 부동산
- **사용 도구**: Playwright (Node.js)

## 핵심 API 엔드포인트 발견

### 1. 부동산 단지 정보 API
```
GET https://new.land.naver.com/api/complexes/single-markers/2.0
```

**주요 파라미터:**
- `cortarNo`: 법정동코드 (4145010300)
- `zoom`: 지도 줌 레벨 (17)
- `priceType`: 가격 타입 (RETAIL)
- `realEstateType`: 부동산 타입 (APT:PRE - 아파트:분양권)
- `tradeType`: 거래 타입 (A1 - 매매)
- `leftLon`, `rightLon`, `topLat`, `bottomLat`: 지도 영역 좌표
- `priceMin`, `priceMax`: 가격 범위 (0 ~ 900000000)
- `areaMin`, `areaMax`: 면적 범위
- `isPresale`: 분양권 포함 여부 (true)

### 2. 행정구역 정보 API
```
GET https://new.land.naver.com/api/cortars
```

**주요 파라미터:**
- `zoom`: 지도 줌 레벨
- `centerLat`, `centerLon`: 중심 좌표

### 3. 개발계획 정보 API
```
GET https://new.land.naver.com/api/developmentplan/road/list
GET https://new.land.naver.com/api/developmentplan/rail/list
GET https://new.land.naver.com/api/developmentplan/jigu/list
```

## 네트워크 요청 분석 결과

### 수집된 데이터
- **전체 네트워크 요청**: 109개
- **API 관련 요청**: 37개
- **콘솔 로그**: 2개 (주로 경고 메시지)

### 주요 외부 서비스
1. **네이버 지도 API**: `oapi.map.naver.com`
2. **정적 리소스**: `finance-fe-static.pstatic.net`
3. **에러 추적**: `sentry-fin.naver.com`
4. **이미지 CDN**: `landthumb-phinf.pstatic.net`

## 페이지 구조 분석

### 화면 구성
1. **지도 영역**: 네이버 지도 기반의 인터랙티브 맵
2. **단지 마커**: 부동산 단지 위치를 표시하는 보라색 마커
3. **가격 정보**: 각 마커에 매매가 정보 표시
4. **필터 옵션**: 상단의 검색 및 필터 기능

### 기술 스택 확인
- **프론트엔드**: React 기반 SPA
- **지도 엔진**: 네이버 지도 API v3
- **번들러**: Webpack (vendor.js 파일 확인)
- **에러 추적**: Sentry

## API 활용 방안

### 1. 부동산 데이터 수집
`/api/complexes/single-markers/2.0` 엔드포인트를 통해 다음 정보 수집 가능:
- 단지별 위치 정보
- 가격 범위
- 거래 유형별 필터링
- 지역별 검색

### 2. 지역 정보 수집
`/api/cortars` 엔드포인트를 통해:
- 행정구역 경계 정보
- 줌 레벨별 상세도 조절

### 3. 개발계획 정보
개발계획 API들을 통해:
- 도로 개발 계획
- 철도 개발 계획
- 지구 단위 계획

## 보안 및 제한사항

### 1. 요청 제한
- Referer 헤더 검증 가능성
- User-Agent 검증
- 과도한 요청 시 차단 가능성

### 2. 인증
- 일부 상세 정보는 로그인 필요할 수 있음
- 네이버 로그인 상태 확인 API 존재

## 권장사항

### 1. API 사용 시 주의사항
- 적절한 요청 간격 유지
- 실제 브라우저와 유사한 헤더 설정
- robots.txt 및 이용약관 준수

### 2. 데이터 활용
- 실시간 부동산 시세 모니터링
- 지역별 가격 동향 분석
- 개발계획과 연계한 투자 분석

## 파일 위치
- **상세 분석 데이터**: `/mnt/d/Cloud/SynologyDrive/Project/1.Projects/mcp/logs/naver_land_analysis.json`
- **페이지 스크린샷**: `/mnt/d/Cloud/SynologyDrive/Project/1.Projects/mcp/naver_land_screenshot.png`
- **분석 스크립트**: `/mnt/d/Cloud/SynologyDrive/Project/1.Projects/mcp/naver_land_analysis.js`