
// phpWineBrowserView.h: CphpWineBrowserView 클래스의 인터페이스
//

#pragma once


class CphpWineBrowserView : public CHtmlView
{
protected: // serialization에서만 만들어집니다.
	CphpWineBrowserView() noexcept;
	DECLARE_DYNCREATE(CphpWineBrowserView);

// 특성입니다.
public:
	CphpWineBrowserDoc* GetDocument() const;

// 작업입니다.
public:
	CString ExecCmd(CString);
	BOOL ExecDaemon(CString, CString);
	//static PMIB_TCPTABLE _GetTCPTable();
	//static int _GetAvailableTCPPort(const u_short , const u_short , const u_short );
	//BOOL _GetAvailableTCPPort(u_short);

// 재정의입니다.
public:
	virtual BOOL PreCreateWindow(CREATESTRUCT& cs);
protected:
	virtual void OnInitialUpdate(); // 생성 후 처음 호출되었습니다.

// 구현입니다.
public:
	virtual ~CphpWineBrowserView();
#ifdef _DEBUG
	virtual void AssertValid() const;
	virtual void Dump(CDumpContext& dc) const;
#endif

protected:

// 생성된 메시지 맵 함수
protected:
	DECLARE_MESSAGE_MAP()
};

#ifndef _DEBUG  // phpWineBrowserView.cpp의 디버그 버전
inline CphpWineBrowserDoc* CphpWineBrowserView::GetDocument() const
   { return reinterpret_cast<CphpWineBrowserDoc*>(m_pDocument); }
#endif

