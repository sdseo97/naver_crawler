# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is an MCP (Model Context Protocol) development and testing environment. The project serves as a testing ground for various MCP servers including Playwright, Context7, Google Search, and other integrations.

## Architecture

- **Local Development Server**: Designed to run on http://localhost with PHP backend
- **Database**: MySQL database (mcp4o) for data persistence
- **Logging**: Error logs to be stored in `/logs/` directory
- **MCP Integration**: Multiple MCP servers for browser automation, search, and documentation lookup

## Development Environment

### Project Structure
```
/mnt/d/Cloud/SynologyDrive/Project/1.Projects/mcp/
├── .claude/
│   └── settings.local.json    # Claude Code MCP permissions
├── SHRIMP/
│   └── WebGUI.md             # Task Manager UI reference
├── CLAUDE.md                 # This guidance file
└── logs/                     # Error logs directory (create if needed)
```

### Local Server Setup

The project directory `/mnt/d/Cloud/SynologyDrive/Project/1.Projects/mcp` maps to `http://localhost`:

```bash
# Create basic project structure
mkdir -p logs

# Create entry point (if needed)
# Entry point: index.php
# Navigate to: http://localhost
```

### Database Configuration

MySQL database connection:
- Host: localhost
- Database: mcp4o
- Username: root
- Password: 1Q2w3e4r5t!

### Test Credentials

For testing login functionality:
- Email: sdseo97@gmail.com
- Password: 1q2w3e4r5t!

## MCP Server Usage

### Playwright Automation

Use Playwright MCP server for browser automation:
```javascript
// Navigate to page
{"tool": "playwright", "parameters": {"action": "goto", "url": "https://example.com"}}

// Click elements
{"tool": "playwright", "parameters": {"action": "click", "selector": "#login-button"}}

// Fill forms
{"tool": "playwright", "parameters": {"action": "fill", "selector": "input[name='q']", "text": "search term"}}

// Take screenshots
{"tool": "playwright", "parameters": {"action": "screenshot", "path": "screenshot.png"}}
```

### Context7 Documentation

Query library documentation:
```javascript
{"tool": "context7", "parameters": {"query": "axios latest version"}}
```

### Google Search Integration

Perform web searches before Playwright browsing:
```javascript
{"tool": "googleSearch", "parameters": {"query": "search terms"}}
```

## Development Guidelines

1. **DOM Analysis First**: When using Playwright, analyze DOM structure before interacting with elements
2. **Systematic Testing**: Check for text boxes, buttons, and links on each page
3. **Error Logging**: All execution errors should be logged to `/logs/` directory
4. **Database Access**: Connect directly to MySQL when data verification is needed
5. **User Consent**: Always request approval before proceeding with significant operations

## Common Development Tasks

### Initial Setup
```bash
# Create required directories
mkdir -p logs

# Check web server status
curl -I http://localhost

# Test database connection
mysql -u root -p1Q2w3e4r5t! -h localhost mcp4o
```

### MCP Server Testing
```bash
# Verify MCP permissions in .claude/settings.local.json
# Test Playwright navigation
# Test Google Search integration
# Test Context7 documentation queries
```



(너는 항상 한국어로 대답해줘.)


너는 MCP를 사용할 수 있어.
다음 예시들을 살펴보고 적절히 활용해줘.


Playwright MCP Server 사용 예시
페이지 열기
{ "tool":"playwright","parameters":{"action":"goto","url":"https://example.com"}} ,
로그인 버튼 클릭
{ "tool":"playwright","parameters":{"action":"click","selector":"#login-button"}} ,
검색어 입력 후 엔터
{ "tool":"playwright","parameters":{"action":"fill","selector":"input[name='q']","text":"MCP Server"}} ,
{ "tool":"playwright","parameters":{"action":"press","selector":"input[name='q']","key":"Enter"}} ,
페이지 스크린샷 저장
{ "tool":"playwright","parameters":{"action":"screenshot","path":"search-results.png"}} ,
콘솔 에러 로그 수집
{ "tool":"playwright","parameters":{"action":"getConsoleLogs"}} ,
네트워크 요청 내역 수집
{ "tool":"playwright","parameters":{"action":"getNetworkRequests"}} ,
JS 평가(페이지 타이틀 가져오기)
{ "tool":"playwright","parameters":{"action":"evaluate","expression":"document.title"}} ,
접근성 스냅샷(구조화된 DOM)
{ "tool":"playwright","parameters":{"action":"accessibilitySnapshot"}}
라이브러리 버전 조회
{ "tool": "context7", "parameters": {"query": "axios 최신 버전 알려줘"}}
패키지 목록 검색
{ "tool": "context7", "parameters": {"query": "lodash 사용법 예시"}}



다음 지침을 지켜줘.

1. /mnt/d/Cloud/SynologyDrive/Project/1.Projects/mcp 은 다음 웹사이트에 대한 루트 폴더야:  http://localhost  (개개인의 상황에 따라 URL 바꾸세요)
2. http://localhost에 들어가면 /mnt/d/Cloud/SynologyDrive/Project/1.Projects/mcp/index.php 페이지 내용이 뜨게 돼.
3. playwright로 접속해 사이트 조사할 때에는 DOM 구조를 먼저 확인한 후, 그에 맞게 클릭, 내용 보기를 진행해줘. 그리고 특정 웹페이지가 나오면 먼저 텍스트 박스와 버튼이나 링크가 있는지 살펴보고 필요하면 이것저것 눌러서 진행해봐.
4. 웹 자료 검색 시, google search를 한 후, 이에 기반해 playwright 브라우징을 해줘.
5. DB는 Mysql이야. 필요하면 직접 접속해서 확인해. (각자의 DB 환경에 따라 바꾸세요)
6. /mnt/d/Cloud/SynologyDrive/Project/1.Projects/mcp 폴더는 http://localhost를 가리켜. 따라서 http://localhost/site 말고 http://localhost로 접속해야 해.
7. 로그 정보는 /mnt/d/Cloud/SynologyDrive/Project/1.Projects/mcp/logs 이곳에 있어. 그래서 실행 오류는 이곳에 쌓이도록 코딩해야 해.
8. 테스트를 위해 다음 사용자 정보 사용해 로그인할 것 (개개인의 상황에 따라 바꾸세요)
아이디: sdseo97@gmail.com
비밀번호: 1q2w3e4r5t!
9. 작업을 임의로 진행하지 말고, 작업 전에 동의를 받아야 해.
10. Mysql 접속 계정은 다음과 같아. (각자의 DB 환경에 따라 바꾸세요. 저는 Mysql 쓰고 있어, 넣었습니다)
   HOST: localhost
   DB 명: mcp4o
   DB 아이디: root
   DB 패스워드: 1Q2w3e4r5t!
일을 할거야.
11.작업이 완료되면 READ_ME.md 파일을 작성해줘.
