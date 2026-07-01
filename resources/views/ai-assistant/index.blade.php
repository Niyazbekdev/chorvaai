<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Yordamchi — ChorvaAI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #0f0f10; color: #ececec; height: 100dvh; display: flex; flex-direction: column; overflow: hidden; }

        /* ── Layout ── */
        .ai-layout { display: flex; height: 100dvh; overflow: hidden; }

        /* ── Sidebar ── */
        .ai-sidebar {
            width: 260px; min-width: 260px; background: #171717;
            border-right: 1px solid #2a2a2a; display: flex; flex-direction: column;
            transition: transform .3s ease;
        }
        .sidebar-head {
            padding: 16px 14px 12px; border-bottom: 1px solid #2a2a2a;
            display: flex; align-items: center; justify-content: space-between;
        }
        .sidebar-logo { display: flex; align-items: center; gap: 8px; text-decoration: none; }
        .sidebar-logo-icon {
            width: 30px; height: 30px; background: #10b981; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }
        .sidebar-logo-text { font-size: .9rem; font-weight: 700; color: #ececec; }
        .sidebar-logo-text span { color: #10b981; }
        .sidebar-close { display: none; background: none; border: none; color: #6b7280; cursor: pointer; padding: 4px; }

        .new-chat-btn {
            margin: 12px 14px; padding: 10px 14px; background: #10b981;
            border: none; border-radius: 10px; color: white; font-weight: 600;
            font-size: .88rem; cursor: pointer; display: flex; align-items: center;
            gap: 8px; transition: background .2s; width: calc(100% - 28px);
        }
        .new-chat-btn:hover { background: #059669; }

        .sidebar-section-title {
            padding: 8px 14px 4px; font-size: .72rem; color: #6b7280;
            text-transform: uppercase; letter-spacing: .06em; font-weight: 600;
        }
        .chat-history { flex: 1; overflow-y: auto; padding: 0 8px 8px; }
        .chat-history::-webkit-scrollbar { width: 4px; }
        .chat-history::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
        .history-item {
            padding: 9px 10px; border-radius: 8px; cursor: pointer;
            font-size: .84rem; color: #c0c0c0; transition: background .15s;
            display: flex; align-items: center; gap: 8px; white-space: nowrap;
            overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px;
        }
        .history-item:hover { background: #222; }
        .history-item.active { background: #1e3a2f; color: #6ee7b7; }
        .history-item svg { flex-shrink: 0; opacity: .6; }

        .sidebar-bottom {
            padding: 12px 14px; border-top: 1px solid #2a2a2a;
            display: flex; flex-direction: column; gap: 6px;
        }
        .sidebar-bottom a {
            display: flex; align-items: center; gap: 8px; padding: 8px 10px;
            border-radius: 8px; color: #9ca3af; text-decoration: none;
            font-size: .84rem; transition: background .15s;
        }
        .sidebar-bottom a:hover { background: #222; color: #ececec; }

        /* ── Main ── */
        .ai-main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

        /* topbar */
        .ai-topbar {
            padding: 14px 20px; border-bottom: 1px solid #2a2a2a;
            display: flex; align-items: center; gap: 12px; background: #111112;
        }
        .sidebar-toggle {
            display: none; background: none; border: none; color: #9ca3af;
            cursor: pointer; padding: 4px;
        }
        .topbar-model {
            display: flex; align-items: center; gap: 6px; background: #1e1e1f;
            border: 1px solid #333; border-radius: 8px; padding: 6px 12px;
            font-size: .84rem; color: #d1d5db; cursor: default;
        }
        .topbar-model-dot { width: 8px; height: 8px; background: #10b981; border-radius: 50%; animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
        .topbar-spacer { flex: 1; }

        /* messages */
        .ai-messages {
            flex: 1; overflow-y: auto; padding: 24px 0;
            scroll-behavior: smooth;
        }
        .ai-messages::-webkit-scrollbar { width: 6px; }
        .ai-messages::-webkit-scrollbar-thumb { background: #2a2a2a; border-radius: 4px; }
        .messages-inner { max-width: 720px; margin: 0 auto; padding: 0 20px; display: flex; flex-direction: column; gap: 20px; }

        /* welcome */
        .welcome-screen {
            max-width: 720px; margin: 0 auto; padding: 60px 20px 20px;
            display: flex; flex-direction: column; align-items: center; gap: 20px;
            text-align: center;
        }
        .welcome-icon {
            width: 64px; height: 64px; background: linear-gradient(135deg,#10b981,#059669);
            border-radius: 18px; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 40px rgba(16,185,129,.3);
        }
        .welcome-title { font-size: 1.8rem; font-weight: 700; color: #f3f4f6; }
        .welcome-subtitle { font-size: 1rem; color: #9ca3af; max-width: 400px; line-height: 1.6; }
        .suggestion-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; width: 100%; max-width: 560px; margin-top: 8px; }
        .suggestion-card {
            background: #1a1a1b; border: 1px solid #2a2a2a; border-radius: 12px;
            padding: 14px 16px; cursor: pointer; transition: border-color .2s, background .2s;
            text-align: left;
        }
        .suggestion-card:hover { border-color: #10b981; background: #1e2e27; }
        .suggestion-card-title { font-size: .88rem; font-weight: 600; color: #e5e7eb; margin-bottom: 4px; }
        .suggestion-card-desc { font-size: .78rem; color: #6b7280; line-height: 1.4; }

        /* message bubbles */
        .msg-row { display: flex; gap: 12px; }
        .msg-row.user { justify-content: flex-end; }
        .msg-avatar {
            width: 32px; height: 32px; border-radius: 10px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            margin-top: 2px;
        }
        .msg-avatar.ai-av { background: linear-gradient(135deg,#10b981,#059669); }
        .msg-avatar.user-av { background: #3b82f6; }
        .msg-bubble {
            max-width: 80%; border-radius: 16px; padding: 12px 16px;
            font-size: .92rem; line-height: 1.65;
        }
        .msg-bubble.ai { background: #1a1a1b; border: 1px solid #2a2a2a; color: #e5e7eb; border-radius: 4px 16px 16px 16px; }
        .msg-bubble.user { background: #2563eb; color: white; border-radius: 16px 4px 16px 16px; }
        .msg-bubble.user .file-preview-chip {
            background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.2);
            color: white;
        }

        /* file preview chip inside bubble */
        .file-preview-chip {
            display: inline-flex; align-items: center; gap: 6px;
            background: #222; border: 1px solid #333; border-radius: 8px;
            padding: 6px 10px; font-size: .78rem; color: #9ca3af;
            margin-bottom: 8px;
        }

        /* image in message */
        .msg-image { max-width: 300px; border-radius: 10px; margin-bottom: 8px; display: block; }

        /* typing dots */
        .typing-dots { display: flex; gap: 4px; align-items: center; padding: 4px 0; }
        .typing-dots span {
            width: 8px; height: 8px; background: #6b7280; border-radius: 50%;
            animation: dot-bounce .9s infinite;
        }
        .typing-dots span:nth-child(2) { animation-delay: .15s; }
        .typing-dots span:nth-child(3) { animation-delay: .3s; }
        @keyframes dot-bounce { 0%,80%,100%{transform:translateY(0)} 40%{transform:translateY(-6px)} }

        /* ── Input area ── */
        .ai-input-area { padding: 16px 20px 20px; background: #111112; border-top: 1px solid #1e1e1f; }
        .ai-input-wrap { max-width: 720px; margin: 0 auto; }
        .file-preview-bar {
            display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
            margin-bottom: 8px; padding: 8px 12px; background: #1a1a1b;
            border: 1px solid #2a2a2a; border-radius: 12px 12px 0 0; border-bottom: none;
        }
        .file-chip {
            display: flex; align-items: center; gap: 6px; background: #222;
            border: 1px solid #333; border-radius: 8px; padding: 5px 10px;
            font-size: .8rem; color: #d1d5db;
        }
        .file-chip-remove {
            background: none; border: none; color: #6b7280; cursor: pointer;
            font-size: 1rem; line-height: 1; padding: 0 2px;
        }
        .file-chip-remove:hover { color: #ef4444; }
        .file-preview-img { width: 40px; height: 40px; object-fit: cover; border-radius: 6px; }

        .input-box {
            display: flex; align-items: flex-end; gap: 10px;
            background: #1a1a1b; border: 1px solid #2e2e2f; border-radius: 14px;
            padding: 10px 12px; transition: border-color .2s;
        }
        .input-box:focus-within { border-color: #10b981; }
        .input-box.has-file { border-radius: 0 0 14px 14px; }
        .input-textarea {
            flex: 1; background: none; border: none; outline: none; color: #f3f4f6;
            font-size: .93rem; font-family: 'Inter', sans-serif; resize: none;
            max-height: 160px; line-height: 1.5; min-height: 24px;
        }
        .input-textarea::placeholder { color: #4b5563; }
        .input-actions { display: flex; align-items: center; gap: 6px; }
        .attach-btn {
            background: none; border: none; color: #6b7280; cursor: pointer;
            padding: 6px; border-radius: 8px; transition: color .15s, background .15s;
            display: flex; align-items: center;
        }
        .attach-btn:hover { color: #10b981; background: rgba(16,185,129,.1); }
        .send-btn {
            background: #10b981; border: none; border-radius: 10px;
            width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: background .2s; flex-shrink: 0;
        }
        .send-btn:hover { background: #059669; }
        .send-btn:disabled { background: #1f2937; cursor: not-allowed; }
        .send-btn svg { color: white; }

        .input-hint { font-size: .74rem; color: #374151; text-align: center; margin-top: 8px; }

        /* Mobile */
        @media (max-width: 768px) {
            .ai-sidebar {
                position: fixed; left: 0; top: 0; bottom: 0; z-index: 200;
                transform: translateX(-100%);
            }
            .ai-sidebar.open { transform: translateX(0); }
            .sidebar-toggle { display: flex; }
            .sidebar-close { display: flex; }
            .sidebar-overlay {
                display: none; position: fixed; inset: 0; background: rgba(0,0,0,.6);
                z-index: 199;
            }
            .sidebar-overlay.open { display: block; }
            .suggestion-grid { grid-template-columns: 1fr; }
            .ai-topbar { padding: 12px 14px; }
            .ai-input-area { padding: 12px 14px 16px; }
        }
    </style>
</head>
<body>
<div class="ai-layout">

    {{-- Sidebar overlay (mobile) --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    {{-- Sidebar --}}
    <aside class="ai-sidebar" id="aiSidebar">
        <div class="sidebar-head">
            <a href="{{ url('/') }}" class="sidebar-logo">
                <div class="sidebar-logo-icon">
                    <svg width="18" height="18" fill="none" stroke="white" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/>
                    </svg>
                </div>
                <span class="sidebar-logo-text">Chorva<span>AI</span></span>
            </a>
            <button class="sidebar-close" onclick="closeSidebar()">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <button class="new-chat-btn" onclick="newChat()">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Yangi suhbat
        </button>

        <div class="sidebar-section-title">Oxirgi suhbatlar</div>
        <div class="chat-history" id="chatHistory">
            {{-- History items injected by JS --}}
        </div>

        <div class="sidebar-bottom">
            <a href="{{ url('/marketplace') }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Bozor
            </a>
            <a href="{{ url('/') }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Bosh sahifa
            </a>
        </div>
    </aside>

    {{-- Main --}}
    <div class="ai-main">
        {{-- Topbar --}}
        <div class="ai-topbar">
            <button class="sidebar-toggle" onclick="openSidebar()">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="topbar-model">
                <div class="topbar-model-dot"></div>
                Gemini 2.5 Flash
            </div>
            <div class="topbar-spacer"></div>
            @auth
                <a href="{{ route('profile.edit') }}" style="display:flex;align-items:center;gap:6px;color:#9ca3af;text-decoration:none;font-size:.84rem;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ Auth::user()->first_name }}
                </a>
            @else
                <a href="{{ route('login') }}" style="background:#10b981;color:white;padding:7px 16px;border-radius:8px;text-decoration:none;font-size:.84rem;font-weight:600;">Kirish</a>
            @endauth
        </div>

        {{-- Messages container --}}
        <div class="ai-messages" id="aiMessages">
            {{-- Welcome screen --}}
            <div class="welcome-screen" id="welcomeScreen">
                <div class="welcome-icon">
                    <svg width="32" height="32" fill="none" stroke="white" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/>
                    </svg>
                </div>
                <div>
                    <div class="welcome-title">ChorvaAI Yordamchi</div>
                    <div class="welcome-subtitle">Chorva mollar, narxlar, parvarish va ko'proq haqida savol bering</div>
                </div>
                <div class="suggestion-grid">
                    <div class="suggestion-card" onclick="sendSuggestion('Sigir narxlari hozir qanday?')">
                        <div class="suggestion-card-title">Narxlar</div>
                        <div class="suggestion-card-desc">Sigir narxlari hozir qanday?</div>
                    </div>
                    <div class="suggestion-card" onclick="sendSuggestion('Chorva mollarni parvarishlash bo\'yicha umumiy maslahat bering')">
                        <div class="suggestion-card-title">Parvarish</div>
                        <div class="suggestion-card-desc">Chorva mollarni parvarishlash bo'yicha umumiy maslahat bering</div>
                    </div>
                    <div class="suggestion-card" onclick="sendSuggestion('Platformada qanday kategoriyalar bor?')">
                        <div class="suggestion-card-title">Kategoriyalar</div>
                        <div class="suggestion-card-desc">Platformada qanday kategoriyalar bor?</div>
                    </div>
                    <div class="suggestion-card" onclick="sendSuggestion('Chorva mollarni qayerdan sotib olish mumkin?')">
                        <div class="suggestion-card-title">Xarid</div>
                        <div class="suggestion-card-desc">Chorva mollarni qayerdan sotib olish mumkin?</div>
                    </div>
                </div>
            </div>
            {{-- Messages injected here --}}
            <div class="messages-inner" id="messagesInner"></div>
        </div>

        {{-- Input area --}}
        <div class="ai-input-area">
            <div class="ai-input-wrap">
                {{-- File preview bar --}}
                <div class="file-preview-bar" id="filePreviewBar" style="display:none">
                    <div id="fileChips"></div>
                </div>

                <div class="input-box" id="inputBox">
                    <textarea
                        id="msgInput"
                        class="input-textarea"
                        placeholder="ChorvaAI ga savol bering..."
                        rows="1"
                        maxlength="2000"
                        onkeydown="handleKey(event)"
                        oninput="autoResize(this)"
                    ></textarea>
                    <div class="input-actions">
                        <button class="attach-btn" onclick="document.getElementById('fileInput').click()" title="Fayl biriktirish">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                        </button>
                        <button class="send-btn" id="sendBtn" onclick="sendMessage()" title="Yuborish">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <input type="file" id="fileInput" accept="image/*,.pdf" style="display:none" onchange="handleFileSelect(this)">
                <div class="input-hint">ChorvaAI xato qilishi mumkin. Muhim ma'lumotlarni tekshiring.</div>
            </div>
        </div>
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let messages = [];
let selectedFile = null;
let isLoading = false;
let historyItems = [];

// ── Init ──
(async () => {
    await loadHistory();
    renderHistoryList();
    if (messages.length > 0) {
        document.getElementById('welcomeScreen').style.display = 'none';
        messages.forEach(m => appendMessage(m.role, m.content));
    }
    scrollBottom();
})();

async function loadHistory() {
    try {
        const res = await fetch('{{ route("ai-chat.history") }}', {
            headers: { 'X-CSRF-TOKEN': CSRF }
        });
        const data = await res.json();
        messages = data.messages || [];

        // Build history list from messages
        if (messages.length > 0) {
            const firstUser = messages.find(m => m.role === 'user');
            if (firstUser) {
                historyItems = [{ id: 1, title: firstUser.content.substring(0, 48) }];
            }
        }
    } catch(e) {}
}

function renderHistoryList() {
    const el = document.getElementById('chatHistory');
    if (historyItems.length === 0) {
        el.innerHTML = `<div style="padding:10px 10px;font-size:.8rem;color:#4b5563;text-align:center">Hali suhbat yo'q</div>`;
        return;
    }
    el.innerHTML = historyItems.map((h,i) => `
        <div class="history-item ${i===0?'active':''}">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <span style="overflow:hidden;text-overflow:ellipsis">${escHtml(h.title)}</span>
        </div>
    `).join('');
}

// ── Messages ──
function appendMessage(role, content, fileInfo) {
    const inner = document.getElementById('messagesInner');
    const div = document.createElement('div');
    div.className = `msg-row ${role === 'user' ? 'user' : ''}`;

    let avatarHtml = '';
    if (role !== 'user') {
        avatarHtml = `<div class="msg-avatar ai-av">
            <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
            </svg>
        </div>`;
    }

    let fileHtml = '';
    if (fileInfo) {
        if (fileInfo.dataUrl && fileInfo.type.startsWith('image/')) {
            fileHtml = `<img src="${fileInfo.dataUrl}" class="msg-image" alt="Fayl">`;
        } else if (fileInfo.name) {
            fileHtml = `<div class="file-preview-chip">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                ${escHtml(fileInfo.name)}
            </div>`;
        }
    }

    const bubbleContent = role === 'model' ? formatAiText(content) : escHtml(content);

    div.innerHTML = `
        ${avatarHtml}
        <div class="msg-bubble ${role === 'user' ? 'user' : 'ai'}">
            ${fileHtml}
            <div>${bubbleContent}</div>
        </div>
    `;
    inner.appendChild(div);
    return div;
}

function appendTyping() {
    const inner = document.getElementById('messagesInner');
    const div = document.createElement('div');
    div.className = 'msg-row';
    div.id = 'typingIndicator';
    div.innerHTML = `
        <div class="msg-avatar ai-av">
            <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
            </svg>
        </div>
        <div class="msg-bubble ai">
            <div class="typing-dots">
                <span></span><span></span><span></span>
            </div>
        </div>
    `;
    inner.appendChild(div);
    scrollBottom();
}

function removeTyping() {
    document.getElementById('typingIndicator')?.remove();
}

// ── Send ──
async function sendMessage() {
    const input = document.getElementById('msgInput');
    const text = input.value.trim();
    if ((!text && !selectedFile) || isLoading) return;

    hideWelcome();
    isLoading = true;
    setSendState(false);

    const fileInfo = selectedFile ? {
        name: selectedFile.name,
        type: selectedFile.type,
        dataUrl: selectedFile._dataUrl,
    } : null;

    appendMessage('user', text || (fileInfo?.name ?? ''), fileInfo);
    input.value = '';
    input.style.height = 'auto';
    clearFilePreview();

    appendTyping();
    scrollBottom();

    try {
        let reply;
        if (selectedFile && fileInfo) {
            selectedFile = null;
            reply = await sendWithFile(text, fileInfo._file);
        } else {
            reply = await sendText(text);
        }
        removeTyping();
        appendMessage('model', reply);

        // Update sidebar history
        if (historyItems.length === 0) {
            historyItems = [{ id: 1, title: (text || fileInfo?.name || 'Suhbat').substring(0, 48) }];
            renderHistoryList();
        }
    } catch(e) {
        removeTyping();
        appendMessage('model', 'Xatolik yuz berdi. Iltimos, qaytadan urinib ko\'ring.');
    } finally {
        isLoading = false;
        setSendState(true);
        scrollBottom();
    }
}

async function sendText(text) {
    const res = await fetch('{{ route("ai-chat.send") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ message: text }),
    });
    const data = await res.json();
    return data.reply || 'Kechirasiz, javob ololmadim.';
}

async function sendWithFile(text, file) {
    const form = new FormData();
    form.append('file', file);
    if (text) form.append('message', text);
    const res = await fetch('{{ route("ai-chat.send-file") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: form,
    });
    const data = await res.json();
    return data.reply || 'Kechirasiz, javob ololmadim.';
}

function sendSuggestion(text) {
    document.getElementById('msgInput').value = text;
    sendMessage();
}

// ── File handling ──
function handleFileSelect(input) {
    const file = input.files[0];
    if (!file) return;

    if (file.size > 10 * 1024 * 1024) {
        alert('Fayl hajmi 10MB dan oshmasin');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
        file._dataUrl = e.target.result;
        file._file = file;
        selectedFile = file;
        showFilePreview(file);
    };
    reader.readAsDataURL(file);
    input.value = '';
}

function showFilePreview(file) {
    const bar = document.getElementById('filePreviewBar');
    const chips = document.getElementById('fileChips');
    const inputBox = document.getElementById('inputBox');

    let content = '';
    if (file.type.startsWith('image/') && file._dataUrl) {
        content = `<img src="${file._dataUrl}" class="file-preview-img" alt=""> `;
    }
    content += `<div class="file-chip">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
        ${escHtml(file.name)}
        <button class="file-chip-remove" onclick="clearFilePreview()">×</button>
    </div>`;

    chips.innerHTML = content;
    bar.style.display = 'flex';
    inputBox.classList.add('has-file');
}

function clearFilePreview() {
    selectedFile = null;
    document.getElementById('filePreviewBar').style.display = 'none';
    document.getElementById('fileChips').innerHTML = '';
    document.getElementById('inputBox').classList.remove('has-file');
}

// ── Sidebar ──
function openSidebar() {
    document.getElementById('aiSidebar').classList.add('open');
    document.getElementById('sidebarOverlay').classList.add('open');
}
function closeSidebar() {
    document.getElementById('aiSidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('open');
}

function newChat() {
    fetch('{{ route("ai-chat.new") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF }
    }).finally(() => window.location.reload());
}

// ── Helpers ──
function hideWelcome() {
    const w = document.getElementById('welcomeScreen');
    if (w) w.style.display = 'none';
}

function scrollBottom() {
    const el = document.getElementById('aiMessages');
    el.scrollTop = el.scrollHeight;
}

function setSendState(enabled) {
    document.getElementById('sendBtn').disabled = !enabled;
}

function handleKey(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
}

function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 160) + 'px';
}

function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

function formatAiText(text) {
    // Replace newlines with <br> first, then format markdown
    return escHtml(text)
        // Bold: **text** — must do before italic
        .replace(/\*\*([\s\S]+?)\*\*/g, '<strong>$1</strong>')
        // List bullets: lines starting with * or - or numbers
        .replace(/(^|\n)(\* |• |- )/g, '$1• ')
        .replace(/(^|\n)(\d+\.\s)/g, '$1<br><strong>$2</strong>')
        // Inline code
        .replace(/`([^`]+)`/g, '<code style="background:#2a2a2a;padding:1px 5px;border-radius:4px;font-family:monospace;font-size:.85em">$1</code>')
        // Newlines to <br>
        .replace(/\n/g, '<br>');
}
</script>
</body>
</html>
