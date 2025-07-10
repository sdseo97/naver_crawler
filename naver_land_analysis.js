const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

async function analyzeNaverLand() {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    // 로그 수집을 위한 배열들
    const consoleLogs = [];
    const networkRequests = [];

    // 콘솔 로그 수집
    page.on('console', msg => {
        consoleLogs.push({
            type: msg.type(),
            text: msg.text(),
            location: msg.location()
        });
    });

    // 네트워크 요청 수집
    page.on('request', request => {
        networkRequests.push({
            url: request.url(),
            method: request.method(),
            headers: request.headers(),
            postData: request.postData()
        });
    });

    try {
        console.log('1. 페이지 접속 중...');
        await page.goto('https://new.land.naver.com/complexes?ms=37.5388065,127.2229095,17&a=APT&b=A1&e=RETAIL', {
            waitUntil: 'networkidle',
            timeout: 30000
        });

        console.log('2. 페이지 로드 완료 대기 중...');
        await page.waitForTimeout(5000);

        console.log('3. 접근성 스냅샷 수집 중...');
        const accessibilitySnapshot = await page.accessibility.snapshot();

        console.log('4. 스크린샷 촬영 중...');
        await page.screenshot({ 
            path: 'naver_land_screenshot.png',
            fullPage: true 
        });

        console.log('5. DOM 구조 분석 중...');
        const domStructure = await page.evaluate(() => {
            function analyzeDom(element, depth = 0) {
                if (depth > 3) return null;
                
                const result = {
                    tagName: element.tagName,
                    className: element.className,
                    id: element.id,
                    textContent: element.textContent ? element.textContent.substring(0, 100) : '',
                    children: []
                };

                for (let child of element.children) {
                    const childAnalysis = analyzeDom(child, depth + 1);
                    if (childAnalysis) {
                        result.children.push(childAnalysis);
                    }
                }

                return result;
            }

            return analyzeDom(document.body);
        });

        // 결과를 파일로 저장
        const results = {
            timestamp: new Date().toISOString(),
            url: page.url(),
            title: await page.title(),
            consoleLogs: consoleLogs,
            networkRequests: networkRequests.slice(0, 50), // 처음 50개만 저장
            accessibilitySnapshot: accessibilitySnapshot,
            domStructure: domStructure
        };

        // logs 디렉토리 생성
        const logsDir = path.join(__dirname, 'logs');
        if (!fs.existsSync(logsDir)) {
            fs.mkdirSync(logsDir, { recursive: true });
        }

        // 결과를 JSON 파일로 저장
        fs.writeFileSync(
            path.join(logsDir, 'naver_land_analysis.json'),
            JSON.stringify(results, null, 2),
            'utf8'
        );

        // API 엔드포인트 찾기
        const apiRequests = networkRequests.filter(req => 
            req.url.includes('api') || 
            req.url.includes('ajax') || 
            req.url.includes('json') ||
            req.url.includes('land') ||
            req.url.includes('complex')
        );

        console.log('\n=== 분석 결과 ===');
        console.log(`페이지 제목: ${await page.title()}`);
        console.log(`현재 URL: ${page.url()}`);
        console.log(`수집된 콘솔 로그: ${consoleLogs.length}개`);
        console.log(`수집된 네트워크 요청: ${networkRequests.length}개`);
        console.log(`API 관련 요청: ${apiRequests.length}개`);

        if (apiRequests.length > 0) {
            console.log('\n=== API 엔드포인트 ===');
            apiRequests.forEach((req, index) => {
                console.log(`${index + 1}. [${req.method}] ${req.url}`);
            });
        }

        console.log('\n결과가 logs/naver_land_analysis.json 파일에 저장되었습니다.');
        console.log('스크린샷이 naver_land_screenshot.png 파일에 저장되었습니다.');

    } catch (error) {
        console.error('오류 발생:', error);
        
        // 에러 로그 저장
        const logsDir = path.join(__dirname, 'logs');
        if (!fs.existsSync(logsDir)) {
            fs.mkdirSync(logsDir, { recursive: true });
        }
        
        fs.writeFileSync(
            path.join(logsDir, 'error.log'),
            `${new Date().toISOString()}: ${error.message}\n${error.stack}\n`,
            { flag: 'a' }
        );
    } finally {
        await browser.close();
    }
}

analyzeNaverLand().catch(console.error);