// Smart 부동산 크롤러 - 메인 JavaScript
class RealEstateCrawler {
    constructor() {
        this.currentData = null;
        this.chart = null;
        this.searchTimeout = null;
        this.progressInterval = null;
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadHistory();
        this.setupNavigation();
    }

    bindEvents() {
        // 검색 폼 이벤트
        const searchForm = document.getElementById('searchForm');
        const locationInput = document.getElementById('locationInput');
        const searchBtn = document.getElementById('searchBtn');

        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.performSearch();
        });

        // 자동완성 이벤트
        locationInput.addEventListener('input', (e) => {
            this.handleAutocomplete(e.target.value);
        });

        locationInput.addEventListener('focus', () => {
            if (locationInput.value.length >= 2) {
                this.handleAutocomplete(locationInput.value);
            }
        });

        // 외부 클릭 시 자동완성 닫기
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-input-group')) {
                this.hideAutocomplete();
            }
        });

        // 빠른 검색 태그 이벤트
        document.querySelectorAll('.quick-tag').forEach(tag => {
            tag.addEventListener('click', () => {
                const location = tag.getAttribute('data-location');
                locationInput.value = location;
                this.performSearch();
            });
        });

        // 정렬 및 필터 이벤트
        const sortSelect = document.getElementById('sortSelect');
        const priceFilter = document.getElementById('priceFilter');

        if (sortSelect) {
            sortSelect.addEventListener('change', () => {
                this.sortData(sortSelect.value);
            });
        }

        if (priceFilter) {
            priceFilter.addEventListener('input', () => {
                this.filterData(parseFloat(priceFilter.value));
                document.getElementById('priceFilterValue').textContent = priceFilter.value;
            });
        }

        // 내보내기 버튼
        const exportBtn = document.getElementById('exportBtn');
        if (exportBtn) {
            exportBtn.addEventListener('click', () => {
                this.exportData();
            });
        }
    }

    setupNavigation() {
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const target = link.getAttribute('href').substring(1);
                this.showSection(target);
                
                // 활성 네비게이션 업데이트
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                link.classList.add('active');
            });
        });
    }

    showSection(sectionName) {
        // 모든 섹션 숨기기
        document.querySelectorAll('section').forEach(section => {
            section.style.display = 'none';
        });

        // 해당 섹션 표시
        let targetSection;
        switch(sectionName) {
            case 'home':
                targetSection = document.querySelector('.hero');
                break;
            case 'search':
                targetSection = document.querySelector('.hero');
                break;
            case 'history':
                targetSection = document.getElementById('historySection');
                this.loadHistory();
                break;
            default:
                targetSection = document.querySelector('.hero');
        }

        if (targetSection) {
            targetSection.style.display = 'block';
        }
    }

    async handleAutocomplete(query) {
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }

        if (query.length < 2) {
            this.hideAutocomplete();
            return;
        }

        this.searchTimeout = setTimeout(async () => {
            try {
                const response = await fetch(`api/autocomplete.php?q=${encodeURIComponent(query)}`);
                const result = await response.json();
                
                if (result.success && result.data.length > 0) {
                    this.showAutocomplete(result.data);
                } else {
                    this.hideAutocomplete();
                }
            } catch (error) {
                console.error('자동완성 오류:', error);
                this.hideAutocomplete();
            }
        }, 300);
    }

    showAutocomplete(suggestions) {
        const dropdown = document.getElementById('autocomplete');
        dropdown.innerHTML = '';
        
        suggestions.forEach(suggestion => {
            const item = document.createElement('div');
            item.className = 'autocomplete-item';
            item.textContent = suggestion;
            item.addEventListener('click', () => {
                document.getElementById('locationInput').value = suggestion;
                this.hideAutocomplete();
            });
            dropdown.appendChild(item);
        });
        
        dropdown.style.display = 'block';
    }

    hideAutocomplete() {
        const dropdown = document.getElementById('autocomplete');
        dropdown.style.display = 'none';
    }

    async performSearch() {
        const location = document.getElementById('locationInput').value.trim();
        if (!location) {
            this.showError('검색할 지역을 입력해주세요.');
            return;
        }

        // UI 상태 변경
        this.showLoadingSection();
        this.hideAutocomplete();

        try {
            // 크롤링 시작
            const response = await fetch('api/crawler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ location })
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const result = await response.json();
            
            if (result.success) {
                this.currentData = result.data;
                this.showResults();
                this.updateStatistics();
                this.renderTable();
                this.renderChart();
            } else {
                throw new Error(result.error?.message || '알 수 없는 오류가 발생했습니다.');
            }

        } catch (error) {
            console.error('검색 오류:', error);
            this.showError('검색 중 오류가 발생했습니다: ' + error.message);
            this.hideLoadingSection();
        }
    }

    showLoadingSection() {
        // 다른 섹션 숨기기
        document.getElementById('resultsSection').style.display = 'none';
        
        // 로딩 섹션 표시
        const loadingSection = document.getElementById('loadingSection');
        loadingSection.style.display = 'block';
        
        // 진행률 모니터링 시작
        this.startProgressMonitoring();
    }

    hideLoadingSection() {
        document.getElementById('loadingSection').style.display = 'none';
        if (this.progressInterval) {
            clearInterval(this.progressInterval);
        }
    }

    startProgressMonitoring() {
        let progress = 0;
        const progressFill = document.getElementById('progressFill');
        const progressText = document.getElementById('progressText');
        const loadingText = document.getElementById('loadingText');

        // 가짜 진행률 시뮬레이션
        this.progressInterval = setInterval(() => {
            progress += Math.random() * 10;
            if (progress > 95) progress = 95;

            progressFill.style.width = progress + '%';
            progressText.textContent = Math.round(progress) + '%';

            // 진행률에 따른 메시지 변경
            if (progress < 25) {
                loadingText.textContent = '네이버 부동산 사이트에 접속하여 데이터를 가져오고 있습니다.';
            } else if (progress < 50) {
                loadingText.textContent = '부동산 정보를 수집하고 있습니다.';
            } else if (progress < 75) {
                loadingText.textContent = '데이터를 분석하고 정리하고 있습니다.';
            } else {
                loadingText.textContent = '크롤링을 완료하고 있습니다.';
            }
        }, 500);
    }

    showResults() {
        this.hideLoadingSection();
        
        // 결과 섹션 표시
        const resultsSection = document.getElementById('resultsSection');
        resultsSection.style.display = 'block';
        resultsSection.classList.add('fade-in');
        
        // 결과 제목 업데이트
        document.getElementById('resultsTitle').textContent = `${this.currentData.location} 부동산 정보`;
        document.getElementById('resultsTime').textContent = new Date().toLocaleString('ko-KR');
    }

    updateStatistics() {
        if (!this.currentData || !this.currentData.summary) return;

        const summary = this.currentData.summary;
        
        document.getElementById('totalComplexes').textContent = summary.totalComplexes || 0;
        document.getElementById('totalUnits').textContent = summary.totalAvailableUnits || 0;
        document.getElementById('avgPrice').textContent = summary.averagePrice || '0억';
        document.getElementById('avgArea').textContent = summary.averageArea || '0㎡';
        document.getElementById('resultsCount').textContent = `${summary.totalComplexes || 0}개 단지`;
    }

    renderTable() {
        if (!this.currentData || !this.currentData.complexes) return;

        const tableBody = document.getElementById('tableBody');
        tableBody.innerHTML = '';

        this.currentData.complexes.forEach((complex, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${complex.pricePerPyeong}</td>
                <td>${complex.averagePrice}</td>
                <td>${complex.priceRange}</td>
                <td>${complex.area}</td>
                <td>${complex.availableUnits}개</td>
                <td><button class="detail-btn" onclick="app.showComplexDetail(${complex.id})">상세</button></td>
            `;
            tableBody.appendChild(row);
        });
    }

    sortData(sortBy) {
        if (!this.currentData || !this.currentData.complexes) return;

        const complexes = [...this.currentData.complexes];
        
        complexes.sort((a, b) => {
            switch(sortBy) {
                case 'price':
                    return this.extractNumber(a.averagePrice) - this.extractNumber(b.averagePrice);
                case 'area':
                    return this.extractNumber(a.area) - this.extractNumber(b.area);
                case 'units':
                    return b.availableUnits - a.availableUnits;
                case 'pricePerPyeong':
                    return this.extractNumber(a.pricePerPyeong) - this.extractNumber(b.pricePerPyeong);
                default:
                    return 0;
            }
        });

        this.currentData.complexes = complexes;
        this.renderTable();
    }

    filterData(maxPrice) {
        if (!this.currentData || !this.currentData.complexes) return;

        const tableBody = document.getElementById('tableBody');
        const rows = tableBody.querySelectorAll('tr');

        rows.forEach((row, index) => {
            const complex = this.currentData.complexes[index];
            const price = this.extractNumber(complex.averagePrice);
            
            if (price <= maxPrice) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    extractNumber(str) {
        return parseFloat(str.replace(/[^0-9.]/g, '')) || 0;
    }

    renderChart() {
        if (!this.currentData || !this.currentData.complexes) return;

        const ctx = document.getElementById('priceChart').getContext('2d');
        
        // 기존 차트 제거
        if (this.chart) {
            this.chart.destroy();
        }

        const prices = this.currentData.complexes.map(c => this.extractNumber(c.averagePrice));
        const labels = this.currentData.complexes.map((c, i) => `단지 ${i + 1}`);

        this.chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: '평균 가격 (억원)',
                    data: prices,
                    backgroundColor: 'rgba(37, 99, 235, 0.6)',
                    borderColor: 'rgba(37, 99, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: '가격 (억원)'
                        }
                    }
                }
            }
        });
    }

    async loadHistory() {
        try {
            const response = await fetch('api/history.php');
            const result = await response.json();
            
            if (result.success && result.data.history) {
                this.renderHistory(result.data.history);
            }
        } catch (error) {
            console.error('히스토리 로드 오류:', error);
        }
    }

    renderHistory(history) {
        const historyList = document.getElementById('historyList');
        historyList.innerHTML = '';

        if (history.length === 0) {
            historyList.innerHTML = '<p class="text-center">검색 히스토리가 없습니다.</p>';
            return;
        }

        history.forEach(item => {
            const historyItem = document.createElement('div');
            historyItem.className = 'history-item';
            historyItem.innerHTML = `
                <div>
                    <h4>${item.location}</h4>
                    <p>${item.totalComplexes}개 단지, ${item.totalUnits}개 매물 (평균: ${item.averagePrice})</p>
                    <small>${item.searchDate}</small>
                </div>
                <button class="search-btn" onclick="app.searchFromHistory('${item.location}')">
                    <i class="fas fa-redo"></i>
                    다시 검색
                </button>
            `;
            historyList.appendChild(historyItem);
        });
    }

    searchFromHistory(location) {
        document.getElementById('locationInput').value = location;
        this.showSection('home');
        this.performSearch();
    }

    showComplexDetail(complexId) {
        const complex = this.currentData.complexes.find(c => c.id === complexId);
        if (!complex) return;

        alert(`단지 상세 정보\n\n평당가: ${complex.pricePerPyeong}\n평균가격: ${complex.averagePrice}\n가격범위: ${complex.priceRange}\n면적: ${complex.area}\n매물수: ${complex.availableUnits}개`);
    }

    exportData() {
        if (!this.currentData) {
            this.showError('내보낼 데이터가 없습니다.');
            return;
        }

        const dataStr = JSON.stringify(this.currentData, null, 2);
        const dataBlob = new Blob([dataStr], {type: 'application/json'});
        const url = URL.createObjectURL(dataBlob);
        
        const link = document.createElement('a');
        link.href = url;
        link.download = `부동산_데이터_${this.currentData.location}_${new Date().toISOString().split('T')[0]}.json`;
        link.click();
        
        URL.revokeObjectURL(url);
    }

    showError(message) {
        // 간단한 알림 (실제로는 모달이나 토스트 메시지 사용 권장)
        alert('오류: ' + message);
    }

    showSuccess(message) {
        // 간단한 알림 (실제로는 모달이나 토스트 메시지 사용 권장)
        alert('성공: ' + message);
    }
}

// 애플리케이션 초기화
let app;
document.addEventListener('DOMContentLoaded', () => {
    app = new RealEstateCrawler();
});

// 전역 함수들 (HTML에서 직접 호출)
window.searchFromHistory = (location) => app.searchFromHistory(location);
window.showComplexDetail = (complexId) => app.showComplexDetail(complexId);