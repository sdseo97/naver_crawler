-- Smart 부동산 크롤러 데이터베이스 설정
-- 데이터베이스: mcp4o

USE mcp4o;

-- 검색 히스토리 테이블
CREATE TABLE IF NOT EXISTS search_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location VARCHAR(255) NOT NULL COMMENT '검색 지역',
    result_data JSON NOT NULL COMMENT '크롤링 결과 데이터',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '검색 일시',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_location (location),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='부동산 검색 히스토리';

-- 사용자 즐겨찾기 테이블 (향후 확장용)
CREATE TABLE IF NOT EXISTS user_favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(100) NOT NULL COMMENT '사용자 ID',
    location VARCHAR(255) NOT NULL COMMENT '즐겨찾기 지역',
    alert_enabled BOOLEAN DEFAULT FALSE COMMENT '알림 활성화',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_location (location),
    UNIQUE KEY unique_user_location (user_id, location)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='사용자 즐겨찾기';

-- 지역 정보 테이블
CREATE TABLE IF NOT EXISTS locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT '지역명',
    latitude DECIMAL(10, 8) NOT NULL COMMENT '위도',
    longitude DECIMAL(11, 8) NOT NULL COMMENT '경도',
    region_code VARCHAR(20) COMMENT '행정구역 코드',
    parent_region VARCHAR(255) COMMENT '상위 지역',
    search_count INT DEFAULT 0 COMMENT '검색 횟수',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_coordinates (latitude, longitude),
    INDEX idx_search_count (search_count)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='지역 정보';

-- 크롤링 로그 테이블
CREATE TABLE IF NOT EXISTS crawl_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location VARCHAR(255) NOT NULL COMMENT '크롤링 지역',
    status ENUM('started', 'in_progress', 'completed', 'failed') NOT NULL COMMENT '크롤링 상태',
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '시작 시간',
    end_time TIMESTAMP NULL COMMENT '종료 시간',
    duration_seconds INT COMMENT '소요 시간(초)',
    complexes_found INT DEFAULT 0 COMMENT '발견된 단지 수',
    units_found INT DEFAULT 0 COMMENT '발견된 매물 수',
    error_message TEXT COMMENT '오류 메시지',
    user_agent TEXT COMMENT '사용자 에이전트',
    ip_address VARCHAR(45) COMMENT 'IP 주소',
    INDEX idx_location (location),
    INDEX idx_status (status),
    INDEX idx_start_time (start_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='크롤링 로그';

-- 부동산 단지 정보 테이블 (상세 정보 저장용)
CREATE TABLE IF NOT EXISTS real_estate_complexes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    search_history_id INT NOT NULL COMMENT '검색 히스토리 ID',
    complex_name VARCHAR(255) COMMENT '단지명',
    location VARCHAR(255) NOT NULL COMMENT '위치',
    price_per_pyeong DECIMAL(10, 2) COMMENT '평당가(만원)',
    average_price DECIMAL(10, 2) COMMENT '평균가격(억원)',
    min_price DECIMAL(10, 2) COMMENT '최소가격(억원)',
    max_price DECIMAL(10, 2) COMMENT '최대가격(억원)',
    area_sqm INT COMMENT '면적(㎡)',
    available_units INT COMMENT '매물 수',
    building_year YEAR COMMENT '건축년도',
    total_households INT COMMENT '총 세대수',
    parking_ratio DECIMAL(5, 2) COMMENT '주차비율',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (search_history_id) REFERENCES search_history(id) ON DELETE CASCADE,
    INDEX idx_location (location),
    INDEX idx_price_range (min_price, max_price),
    INDEX idx_area (area_sqm),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='부동산 단지 상세 정보';

-- 시스템 설정 테이블
CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE COMMENT '설정 키',
    setting_value TEXT COMMENT '설정 값',
    description TEXT COMMENT '설정 설명',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='시스템 설정';

-- 기본 지역 데이터 삽입
INSERT INTO locations (name, latitude, longitude, region_code, parent_region) VALUES
('서울특별시 강남구 역삼동', 37.5014, 127.0366, '1168000000', '서울특별시'),
('서울특별시 송파구 잠실동', 37.5133, 127.1028, '1171000000', '서울특별시'),
('경기도 하남시 창우동', 37.5388, 127.2229, '4113000000', '경기도'),
('경기도 성남시 분당구', 37.3595, 127.1052, '4113500000', '경기도'),
('서울특별시 서초구 서초동', 37.4836, 127.0327, '1165000000', '서울특별시'),
('서울특별시 마포구 상암동', 37.5789, 126.8895, '1144000000', '서울특별시'),
('인천광역시 연수구 송도동', 37.3894, 126.6381, '2817000000', '인천광역시'),
('경기도 용인시 수지구', 37.3267, 127.0947, '4146000000', '경기도'),
('경기도 고양시 일산동구', 37.6564, 126.7729, '4128100000', '경기도'),
('대전광역시 유성구 도룡동', 36.3629, 127.3461, '3017000000', '대전광역시');

-- 기본 시스템 설정 삽입
INSERT INTO system_settings (setting_key, setting_value, description) VALUES
('crawl_delay_seconds', '2', '크롤링 간 지연 시간(초)'),
('max_concurrent_crawls', '3', '동시 크롤링 최대 수'),
('data_retention_days', '90', '데이터 보관 기간(일)'),
('enable_auto_cleanup', 'true', '자동 정리 활성화'),
('api_rate_limit_per_hour', '100', '시간당 API 호출 제한');

-- 인덱스 및 성능 최적화
ANALYZE TABLE search_history;
ANALYZE TABLE locations;
ANALYZE TABLE crawl_logs;
ANALYZE TABLE real_estate_complexes;

-- 뷰 생성: 최근 검색 통계
CREATE OR REPLACE VIEW recent_search_stats AS
SELECT 
    location,
    COUNT(*) as search_count,
    MAX(created_at) as last_search,
    AVG(JSON_EXTRACT(result_data, '$.summary.totalComplexes')) as avg_complexes,
    AVG(JSON_EXTRACT(result_data, '$.summary.totalAvailableUnits')) as avg_units
FROM search_history 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY location
ORDER BY search_count DESC;

-- 뷰 생성: 인기 지역 순위
CREATE OR REPLACE VIEW popular_locations AS
SELECT 
    l.name,
    l.search_count,
    COUNT(sh.id) as recent_searches,
    l.latitude,
    l.longitude
FROM locations l
LEFT JOIN search_history sh ON l.name = sh.location
WHERE sh.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) OR sh.created_at IS NULL
GROUP BY l.id
ORDER BY recent_searches DESC, l.search_count DESC
LIMIT 10;

-- 트리거: 검색시 지역별 카운트 증가
DELIMITER //
CREATE TRIGGER update_location_search_count 
AFTER INSERT ON search_history
FOR EACH ROW
BEGIN
    UPDATE locations 
    SET search_count = search_count + 1 
    WHERE name = NEW.location;
    
    -- 새 지역인 경우 자동 추가
    INSERT IGNORE INTO locations (name, latitude, longitude, search_count) 
    VALUES (NEW.location, 37.5665, 126.9780, 1);
END//
DELIMITER ;

-- 정리 작업을 위한 이벤트 생성 (주기적 데이터 정리)
-- SET GLOBAL event_scheduler = ON;
-- 
-- CREATE EVENT IF NOT EXISTS cleanup_old_data
-- ON SCHEDULE EVERY 1 DAY
-- STARTS CURRENT_TIMESTAMP
-- DO
-- BEGIN
--     -- 90일 이전 데이터 삭제
--     DELETE FROM search_history WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
--     DELETE FROM crawl_logs WHERE start_time < DATE_SUB(NOW(), INTERVAL 90 DAY);
-- END;

-- 권한 설정 (필요시)
-- GRANT SELECT, INSERT, UPDATE, DELETE ON mcp4o.* TO 'web_user'@'localhost';
-- FLUSH PRIVILEGES;

-- 백업을 위한 프로시저 (선택사항)
DELIMITER //
CREATE PROCEDURE backup_search_data(IN backup_days INT)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE backup_table_name VARCHAR(100);
    
    SET backup_table_name = CONCAT('search_history_backup_', DATE_FORMAT(NOW(), '%Y%m%d'));
    
    SET @sql = CONCAT('CREATE TABLE IF NOT EXISTS ', backup_table_name, ' AS 
                      SELECT * FROM search_history 
                      WHERE created_at >= DATE_SUB(NOW(), INTERVAL ', backup_days, ' DAY)');
    
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    
    SELECT CONCAT('백업 완료: ', backup_table_name) as result;
END//
DELIMITER ;

-- 초기 설정 완료 로그
INSERT INTO crawl_logs (location, status, start_time, end_time, duration_seconds, error_message) 
VALUES ('SYSTEM_SETUP', 'completed', NOW(), NOW(), 0, 'Database setup completed successfully');

SELECT 'Smart 부동산 크롤러 데이터베이스 설정이 완료되었습니다.' as setup_status;