
// phpWineBrowserView.cpp: CphpWineBrowserView 클래스의 구현
//

#include "pch.h"
#include "framework.h"
// SHARED_HANDLERS는 미리 보기, 축소판 그림 및 검색 필터 처리기를 구현하는 ATL 프로젝트에서 정의할 수 있으며
// 해당 프로젝트와 문서 코드를 공유하도록 해 줍니다.
#ifndef SHARED_HANDLERS
#include "phpWineBrowser.h"
#endif

#include "phpWineBrowserDoc.h"
#include "phpWineBrowserView.h"

#ifdef _DEBUG
#define new DEBUG_NEW
#endif

#include "iphlpapi.h"
#pragma comment(lib,"iphlpapi.lib")


// CphpWineBrowserView

IMPLEMENT_DYNCREATE(CphpWineBrowserView, CHtmlView)

BEGIN_MESSAGE_MAP(CphpWineBrowserView, CHtmlView)
END_MESSAGE_MAP()

// CphpWineBrowserView 생성/소멸

CphpWineBrowserView::CphpWineBrowserView() noexcept
{
	// TODO: 여기에 생성 코드를 추가합니다.

}
 
// 소멸자 정의
CphpWineBrowserView::~CphpWineBrowserView()
{
    // php 데몬 종료
    CString strExecPHP;
    strExecPHP = ExecCmd("taskkill /im php.exe");
    //AfxMessageBox(strExecPHP);
}

BOOL CphpWineBrowserView::PreCreateWindow(CREATESTRUCT& cs)
{
	// TODO: CREATESTRUCT cs를 수정하여 여기에서
	//  Window 클래스 또는 스타일을 수정합니다.

	return CHtmlView::PreCreateWindow(cs);
}

void CphpWineBrowserView::OnInitialUpdate()
{
	CHtmlView::OnInitialUpdate();

	//Navigate2(_T("http://www.msdn.microsoft.com/korea/visualc/"),nullptr, nullptr);
	//Navigate2(("http://www.naver.com/"), NULL, NULL);
 
    //ini파일 읽기-단순파일읽기
    CStdioFile portFile;
    CFileException e;
    if (!portFile.Open((_T("server.ini")), CFile::modeRead, &e)) {  // Open 함수로 파일 가져오기
        e.ReportError();
    }
    CString setServer;
    portFile.ReadString(setServer);
    portFile.Close();
    //setServer.Replace(_T("[::port::]"), _T("12345"));
    //AfxMessageBox(setServer);

    // 커맨드 실행 출력
    //AfxMessageBox(_T("phpWine"));
    //ShellExecute(NULL, _T("open"), _T("echo"), _T("1234 > test.txt"), NULL, SW_SHOWNORMAL);
    //ShellExecute(NULL, _T("explore"), _T("C:\\"), NULL, NULL, SW_SHOWNORMAL);	// 탐색기 커맨드
    //ShellExecute(NULL, _T("start"), _T("/ max http://naver.com"), NULL, NULL, SW_SHOWNORMAL);	// 탐색기 커맨드
    //ShellExecute(NULL, NULL, _T("python --version > port.ini"), NULL, NULL, SW_HIDE);
    //WinExec("C:\\Program Files\\Internet Explorer\\iexplore.exe", SW_HIDE);
    //WinExec("C:\\Program Files\\Internet Explorer\\iexplore.exe", SW_HIDE);
    //WinExec("php_bin\\php.exe -i > ini.txt", SW_HIDE);
    CString strPort, strExecPHP, url, etc;
    strPort = ExecCmd("php_bin\\php.exe -n portscan.php");
    //AfxMessageBox(strPort);

    setServer.Replace("[::port::]", strPort);
    //AfxMessageBox(setServer);
           
   
    // 데몬실행
    ExecDaemon("php_bin\\php.exe", setServer);
    //ExecDaemon("php_bin\\php.exe", " -S localhost:21099 -t webDoc -c php.ini");

    url = "http://localhost:";
    url += strPort;
    //url += "21099";
    
    // 콘솔창 열어서 출력
    //#pragma comment(linker, "/entry:WinMainCRTStartup /subsystem:console");
    //printf("123123");

    Navigate2(url, NULL, NULL);
}


// CphpWineBrowserView 진단

#ifdef _DEBUG
void CphpWineBrowserView::AssertValid() const
{
	CHtmlView::AssertValid();
}

void CphpWineBrowserView::Dump(CDumpContext& dc) const
{
	CHtmlView::Dump(dc);
}

CphpWineBrowserDoc* CphpWineBrowserView::GetDocument() const // 디버그되지 않은 버전은 인라인으로 지정됩니다.
{
	ASSERT(m_pDocument->IsKindOf(RUNTIME_CLASS(CphpWineBrowserDoc)));
	return (CphpWineBrowserDoc*)m_pDocument;
}
#endif //_DEBUG


// CphpWineBrowserView 메시지 처리기

// 데몬 실행함수
BOOL CphpWineBrowserView::ExecDaemon(CString exeCmd, CString param)
{
    // 데몬실행
    SHELLEXECUTEINFO ShExecInfo = { 0 };
    ShExecInfo.cbSize = sizeof(SHELLEXECUTEINFO);
    //ShExecInfo.fMask = SEE_MASK_NOCLOSEPROCESS;   // 프로세스 중단까지 기다림
    ShExecInfo.hwnd = NULL;
    ShExecInfo.lpVerb = NULL;
    ShExecInfo.lpFile = exeCmd;         // "php_bin\\php.exe";
    ShExecInfo.lpParameters = param;    //setServer;
    ShExecInfo.lpDirectory = NULL;
    //ShExecInfo.nShow = SW_MINIMIZE; // 작은창처리
    ShExecInfo.nShow = SW_HIDE;  // 히든처리-닫기가 안됨
    ShExecInfo.hInstApp = NULL;
    ShellExecuteEx(&ShExecInfo);
    WaitForSingleObject(ShExecInfo.hProcess, INFINITE);
    CloseHandle(ShExecInfo.hProcess);

    return true;
}

// 커맨드 실행함수
CString CphpWineBrowserView::ExecCmd(CString pCmdArg)
{
    //   Handle Inheritance - to pipe child's stdout via pipes to parent, handles must be inherited.
    //   SECURITY_ATTRIBUTES.bInheritHandle must be TRUE
    //   CreateProcess parameter bInheritHandles must be TRUE;
    //   STARTUPINFO.dwFlags must have STARTF_USESTDHANDLES set.

    CString strResult; // Contains result of cmdArg.

    HANDLE hChildStdoutRd; // Read-side, used in calls to ReadFile() to get child's stdout output.
    HANDLE hChildStdoutWr; // Write-side, given to child process using si struct.

    BOOL fSuccess;

    // Create security attributes to create pipe.
    SECURITY_ATTRIBUTES saAttr = { sizeof(SECURITY_ATTRIBUTES) };
    saAttr.bInheritHandle = TRUE; // Set the bInheritHandle flag so pipe handles are inherited by child process. Required.
    saAttr.lpSecurityDescriptor = NULL;

    // Create a pipe to get results from child's stdout.
    // I'll create only 1 because I don't need to pipe to the child's stdin.
    if (!CreatePipe(&hChildStdoutRd, &hChildStdoutWr, &saAttr, 0))
    {
        return strResult;
    }

    STARTUPINFO si = { sizeof(STARTUPINFO) }; // specifies startup parameters for child process.

    si.dwFlags = STARTF_USESHOWWINDOW | STARTF_USESTDHANDLES; // STARTF_USESTDHANDLES is Required.
    si.hStdOutput = hChildStdoutWr; // Requires STARTF_USESTDHANDLES in dwFlags.
    si.hStdError = hChildStdoutWr; // Requires STARTF_USESTDHANDLES in dwFlags.
    // si.hStdInput remains null.
    si.wShowWindow = SW_HIDE; // Prevents cmd window from flashing. Requires STARTF_USESHOWWINDOW in dwFlags.

    PROCESS_INFORMATION pi = { 0 };

    // Create the child process.
    fSuccess = CreateProcess(
        NULL,
        (LPSTR)pCmdArg.GetBuffer(pCmdArg.GetLength()),     // command line
        NULL,               // process security attributes
        NULL,               // primary thread security attributes
        TRUE,               // TRUE=handles are inherited. Required.
        CREATE_NEW_CONSOLE, // creation flags
        NULL,               // use parent's environment
        NULL,               // use parent's current directory
        &si,                // __in, STARTUPINFO pointer
        &pi);               // __out, receives PROCESS_INFORMATION

    if (!fSuccess)
    {
        return _T("fail CreateProcess");
        return strResult;
    }

    // Wait until child processes exit. Don't wait forever.
    WaitForSingleObject(pi.hProcess, 2000);
    TerminateProcess(pi.hProcess, 0); // Kill process if it is still running. Tested using cmd "ping blah -n 99"

    // Close the write end of the pipe before reading from the read end of the pipe.
    if (!CloseHandle(hChildStdoutWr))
    {
        return strResult;
    }

    // Read output from the child process.
    for (;;)
    {
        DWORD dwRead;
        CHAR chBuf[4096];

        // Read from pipe that is the standard output for child process.
        bool done = !ReadFile(hChildStdoutRd, chBuf, 4096, &dwRead, NULL) || dwRead == 0;
        if (done)
        {
            break;
        }

        // Append result to string.
        strResult += CString(chBuf, dwRead);
    }

    // Close process and thread handles.
    CloseHandle(hChildStdoutRd);

    // CreateProcess docs specify that these must be closed. 
    CloseHandle(pi.hProcess);
    CloseHandle(pi.hThread);

    return strResult;
}

// 이하 빈포트 찾는 함수
/*BOOL _GetAvailableTCPPort(u_short portNum)
{
    PMIB_TCPTABLE pTable = NULL;
    DWORD         dwSize = 0;
    if (GetTcpTable(NULL, &dwSize, TRUE) == ERROR_INSUFFICIENT_BUFFER)
    {
        if (dwSize > 0) {
            pTable = new MIB_TCPTABLE[dwSize];
            if (GetTcpTable(pTable, &dwSize, TRUE) != NO_ERROR) return false;
        }
    }
    if (pTable == NULL) return false;
    else {
        for (DWORD nidx = 0;nidx < pTable->dwNumEntries;nidx++) {
            u_short localPort = ntohs((u_short)pTable->table[nidx].dwLocalPort);
            if (portNum == localPort) return false;
        }
    }
    delete[] pTable;
    return true;    // 사용 가능 포트!
}
/*
static PMIB_TCPTABLE _GetTCPTable() {
    PMIB_TCPTABLE pTable = NULL;
    DWORD         dwSize = 0;
    if (::GetTcpTable(NULL, &dwSize, TRUE) == ERROR_INSUFFICIENT_BUFFER) {
        if (dwSize > 0) {
            pTable = new MIB_TCPTABLE[dwSize];
            if (::GetTcpTable(pTable, &dwSize, TRUE) == NO_ERROR) {
                return pTable;
            }
        }
    }
    return NULL;
};

static int _GetAvailableTCPPort(const u_short nMin, const u_short nMax, const u_short nCnt = 1) {
    PMIB_TCPTABLE pTable = _GetTCPTable();
    if (pTable) {
        int nAvaPort = -1;
        for (u_short nport = nMin;nport <= nMax;nport++) {
            BOOL bfind = TRUE;
            for (DWORD nidx = 0;nidx < pTable->dwNumEntries;nidx++) {
                for (u_short noff = 0;noff < nCnt;noff++) {
                    if (::htons(nport + noff) == (u_short)pTable->table[nidx].dwLocalPort) {
                        nport += noff;
                        bfind = FALSE;
                        break;
                    }
                }
                if (bfind == FALSE) {
                    break;
                }
            }
            if (bfind == TRUE) {
                nAvaPort = (int)nport;
                break;
            }
        }
        delete[] pTable;
        return nAvaPort;
    }
    return -1;
};
*/