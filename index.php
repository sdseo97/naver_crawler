<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart 부동산 크롤러 - 실시간 부동산 정보</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <i class="fas fa-home"></i>
                <h1>Smart 부동산</h1>
            </div>
            <nav class="nav">
                <a href="#home" class="nav-link active">홈</a>
                <a href="#search" class="nav-link">검색</a>
                <a href="#history" class="nav-link">히스토리</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <div class="hero-content">
                <h2 class="hero-title">실시간 부동산 정보를 <span class="highlight">스마트하게</span></h2>
                <p class="hero-subtitle">네이버 부동산에서 실시간으로 데이터를 수집하여 최신 부동산 정보를 제공합니다</p>
                
                <!-- Search Form -->
                <div class="search-container">
                    <form id="searchForm" class="search-form">
                        <div class="search-input-group">
                            <i class="fas fa-map-marker-alt search-icon"></i>
                            <input type="text" id="locationInput" placeholder="검색할 지역을 입력하세요 (예: 하남시 창우동)" class="search-input" autocomplete="off">
                            <div id="autocomplete" class="autocomplete-dropdown"></div>
                        </div>
                        <button type="submit" class="search-btn" id="searchBtn">
                            <i class="fas fa-search"></i>
                            <span>검색하기</span>
                        </button>
                    </form>
                </div>

                <!-- Quick Search Tags -->
                <div class="quick-tags">
                    <span class="tag-label">인기 지역:</span>
                    <button class="quick-tag" data-location="강남구 역삼동">강남구 역삼동</button>
                    <button class="quick-tag" data-location="송파구 잠실동">송파구 잠실동</button>
                    <button class="quick-tag" data-location="하남시 창우동">하남시 창우동</button>
                    <button class="quick-tag" data-location="성남시 분당구">성남시 분당구</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Loading Section -->
    <section class="loading-section" id="loadingSection" style="display: none;">
        <div class="container">
            <div class="loading-content">
                <div class="loading-spinner">
                    <div class="spinner"></div>
                </div>
                <h3>부동산 정보를 수집중입니다...</h3>
                <p id="loadingText">네이버 부동산 사이트에 접속하여 데이터를 가져오고 있습니다.</p>
                <div class="loading-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                    <span class="progress-text" id="progressText">0%</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Results Section -->
    <section class="results-section" id="resultsSection" style="display: none;">
        <div class="container">
            <div class="results-header">
                <h2 id="resultsTitle">검색 결과</h2>
                <div class="results-meta">
                    <span id="resultsCount">0개 단지</span>
                    <span id="resultsTime"></span>
                    <button class="export-btn" id="exportBtn">
                        <i class="fas fa-download"></i>
                        내보내기
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="totalComplexes">0</h3>
                        <p>총 단지 수</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="totalUnits">0</h3>
                        <p>총 매물 수</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-won-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="avgPrice">0억</h3>
                        <p>평균 가격</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-area"></i>
                    </div>
                    <div class="stat-content">
                        <h3 id="avgArea">0㎡</h3>
                        <p>평균 면적</p>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="data-table-container">
                <div class="table-controls">
                    <div class="sort-controls">
                        <label>정렬:</label>
                        <select id="sortSelect">
                            <option value="price">가격순</option>
                            <option value="area">면적순</option>
                            <option value="units">매물수순</option>
                            <option value="pricePerPyeong">평당가순</option>
                        </select>
                    </div>
                    <div class="filter-controls">
                        <input type="range" id="priceFilter" min="0" max="20" step="0.5" class="price-slider">
                        <span class="filter-label">최대 <span id="priceFilterValue">20</span>억원</span>
                    </div>
                </div>
                
                <div class="table-wrapper">
                    <table class="data-table" id="dataTable">
                        <thead>
                            <tr>
                                <th>순번</th>
                                <th>평당가</th>
                                <th>평균가격</th>
                                <th>가격범위</th>
                                <th>면적</th>
                                <th>매물수</th>
                                <th>상세</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Chart Container -->
            <div class="chart-container">
                <h3>가격 분포 차트</h3>
                <canvas id="priceChart" width="400" height="200"></canvas>
            </div>
        </div>
    </section>

    <!-- History Section -->
    <section class="history-section" id="historySection" style="display: none;">
        <div class="container">
            <h2>검색 히스토리</h2>
            <div class="history-list" id="historyList">
                <!-- 검색 히스토리가 여기에 표시됩니다 -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Smart 부동산 크롤러. 교육 및 연구 목적으로 제작되었습니다.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>