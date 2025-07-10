# 🚀 GitHub 업로드 가이드

## 수동 GitHub 저장소 생성 및 업로드

### 1. GitHub에서 저장소 생성
1. [GitHub](https://github.com)에 로그인
2. 우측 상단 "+" 버튼 클릭 → "New repository" 선택
3. 저장소 설정:
   - **Repository name**: `naver_crawler`
   - **Description**: `🏠 네이버 부동산 크롤러 - 실시간 부동산 정보를 크롤링하여 모던한 웹 인터페이스로 제공하는 웹 애플리케이션`
   - **Public** 선택
   - **Add a README file**: 체크 해제 (이미 있음)
   - **Add .gitignore**: 체크 해제 (이미 있음)
   - **Choose a license**: 체크 해제 (이미 있음)
4. "Create repository" 클릭

### 2. 로컬 저장소와 GitHub 연결
터미널에서 다음 명령어 실행:

```bash
# GitHub 저장소와 연결 (USERNAME을 실제 GitHub 사용자명으로 변경)
git remote add origin https://github.com/USERNAME/naver_crawler.git

# 브랜치명을 main으로 변경 (선택사항)
git branch -M main

# GitHub에 업로드
git push -u origin main
```

### 3. GitHub 사용자명 확인
GitHub 사용자명을 모르는 경우:
1. GitHub 프로필 페이지로 이동
2. URL에서 `github.com/USERNAME` 형태로 확인

### 4. 업로드 예시
예를 들어 GitHub 사용자명이 `sdseo97`인 경우:

```bash
git remote add origin https://github.com/sdseo97/naver_crawler.git
git branch -M main
git push -u origin main
```

### 5. 인증 방법
GitHub 업로드 시 인증이 필요한 경우:

#### 방법 1: Personal Access Token 사용
1. GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)
2. "Generate new token" 클릭
3. 권한 선택: `repo` 체크
4. 토큰 생성 후 복사
5. 업로드 시 비밀번호 대신 토큰 입력

#### 방법 2: SSH 키 사용
```bash
# SSH 키 생성
ssh-keygen -t ed25519 -C "your_email@example.com"

# 공개키 복사
cat ~/.ssh/id_ed25519.pub

# GitHub → Settings → SSH and GPG keys → New SSH key에 추가

# SSH URL로 원격 저장소 설정
git remote set-url origin git@github.com:USERNAME/naver_crawler.git
```

## 🎯 업로드 후 확인사항

### README.md 업데이트
저장소 생성 후 README.md의 다음 부분을 실제 사용자명으로 변경:

```markdown
# 변경 전
git clone https://github.com/your-username/naver_crawler.git

# 변경 후 (예시)
git clone https://github.com/sdseo97/naver_crawler.git
```

### 저장소 설정
1. **About 섹션**: 저장소 설명, 웹사이트 URL, 토픽 추가
2. **Topics**: `php`, `javascript`, `web-crawler`, `real-estate`, `naver`, `responsive-design` 등
3. **GitHub Pages**: 필요시 정적 사이트 호스팅 설정

### 브랜치 보호 규칙 (선택사항)
1. Settings → Branches
2. "Add rule" 클릭
3. `main` 브랜치에 대한 보호 규칙 설정

## 📝 커밋 메시지 컨벤션

향후 커밋 시 다음 형식 권장:

```
✨ feat: 새로운 기능 추가
🐛 fix: 버그 수정
📚 docs: 문서 업데이트
🎨 style: 코드 스타일 변경
♻️ refactor: 코드 리팩토링
⚡ perf: 성능 개선
✅ test: 테스트 추가/수정
🔧 chore: 빌드/설정 변경
```

## 🔗 유용한 링크

- [Git 기본 명령어](https://git-scm.com/docs)
- [GitHub 도움말](https://docs.github.com)
- [Markdown 문법](https://guides.github.com/features/mastering-markdown/)
- [GitHub Pages](https://pages.github.com/)

---

**모든 준비가 완료되었습니다! 🎉**