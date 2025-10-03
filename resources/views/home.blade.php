@extends('default')

@section('content')
<div class="container-fluid p-0" style="height: 100vh;">
    <div class="notepad-container d-flex flex-column" style="height: 100%;">

        <!-- Header Section -->
        <div class="notepad-head p-3 bg-white border-bottom d-flex align-items-center justify-content-between shadow-sm">
            <div class="d-flex align-items-center">
                <div class="logo-container me-3">
                    <div class="logo-circle bg-primary text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px;">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                </div>
                <div>
                    <h5 class="notepad-title m-0 text-dark fw-semibold gradient-text">{{ $notes->title ?? 'Untitled Document' }}</h5>
                    <div class="d-flex align-items-center mt-1">
                        <span class="save-indicator" id="saveStatus">
                            <i class="fa-solid fa-circle-check text-success me-1"></i>
                            <span class="text-muted fs-7">All changes saved</span>
                        </span>
                        <div class="ms-3 document-info">
                            <small class="text-muted fs-7">
                                <i class="fa-solid fa-clock me-1"></i>
                                <span id="lastSaved">Just now</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex align-items-center">
                <!-- Collaboration Widget -->
                <div class="collaboration-widget me-4">
                    <div class="d-flex align-items-center">
                        <div class="live-pulse me-2"></div>
                        <div class="avatar-stack" id="avatarGroup"></div>
                        <small class="text-muted ms-2 fs-7" id="activeUsersCount">0 editing</small>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="btn-toolbar d-flex align-items-center">
                    <!-- Voice Typing -->
                    <div class="voice-btn-container me-2">
                        <button id="speechBtn" class="btn btn-outline-primary btn-sm rounded-pill position-relative" title="Voice typing">
                            <i class="fa-solid fa-microphone"></i>
                            <span class="voice-wave"></span>
                        </button>
                    </div>
                    <!-- Main Actions -->
                    <livewire:favorites :note-id="$notes->id" />
                    <livewire:note-password :note="$notes" />
                    <livewire:edit-url :note="$notes" />
                    
                    <div class="btn-group me-2" role="group">
                        <a href="{{ route('home') }}" class="btn btn-light btn-sm border" title="New document">
                            <i class="fa-solid fa-plus"></i>
                        </a>
                        <button class="btn btn-light btn-sm border" onclick="showTemplates()" title="Templates">
                            <i class="fa-solid fa-layer-group"></i>
                        </button>
                    </div>

                    <!-- Export Dropdown -->
                    <div class="dropdown me-2">
                        <button class="btn btn-light btn-sm border dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-download"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="exportDocument('pdf')"><i class="fa-solid fa-file-pdf me-2"></i>Export as PDF</a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportDocument('doc')"><i class="fa-solid fa-file-word me-2"></i>Export as Word</a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportDocument('txt')"><i class="fa-solid fa-file-lines me-2"></i>Export as Text</a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportDocument('html')"><i class="fa-solid fa-code me-2"></i>Export as HTML</a></li>
                        </ul>
                    </div>

                    <!-- Theme Toggle -->
                    <button id="darkModeToggle" class="btn btn-light btn-sm border me-2" title="Toggle theme">
                        <i class="fa-solid fa-moon"></i>
                    </button>

                    <!-- Share Button -->
                    <button class="btn btn-primary btn-sm rounded-pill px-3" onclick="shareDocument()" title="Share">
                        <i class="fa-solid fa-share-nodes me-1"></i>Share
                    </button>
                </div>
            </div>
        </div>

        <!-- Status Bar -->
        <div class="editing-status-bar px-3 py-2 bg-gradient-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="status-indicator d-flex align-items-center">
                    <div class="status-glow me-2" id="statusGlow"></div>
                    <div class="status-content">
                        <span class="current-editor fw-medium" id="currentUser">Ready to collaborate</span>
                        <small class="status-subtitle opacity-75 ms-2" id="typingIndicator"></small>
                    </div>
                </div>
                
                <!-- Document Stats -->
                <div class="document-stats d-flex align-items-center">
                    <div class="stat-item me-3">
                        <small><i class="fa-solid fa-font me-1"></i><span id="wordCount">0</span> words</small>
                    </div>
                    <div class="stat-item me-3">
                        <small><i class="fa-solid fa-keyboard me-1"></i><span id="charCount">0</span> chars</small>
                    </div>
                    <div class="stat-item">
                        <small><i class="fa-solid fa-clock me-1"></i><span id="readTime">0 min</span> read</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Toolbar -->
        <div id="toolbar-container" class="bg-light border-bottom py-2 px-3 glass-effect">
            <div class="d-flex flex-wrap align-items-center">
                <!-- Text Formatting -->
                <span class="ql-formats me-3">
                    <select class="ql-font">
                        <option value="" selected>Inter</option>
                        <option value="serif">Georgia</option>
                        <option value="monospace">JetBrains Mono</option>
                    </select>
                    <select class="ql-size">
                        <option value="small">Small</option>
                        <option value="normal" selected>Normal</option>
                        <option value="large">Large</option>
                        <option value="huge">Huge</option>
                    </select>
                </span>
                
                <span class="ql-formats me-3 border-end pe-3">
                    <button class="ql-bold" title="Bold (Ctrl+B)"></button>
                    <button class="ql-italic" title="Italic (Ctrl+I)"></button>
                    <button class="ql-underline" title="Underline (Ctrl+U)"></button>
                    <button class="ql-strike" title="Strikethrough"></button>
                </span>

                <!-- Colors -->
                <span class="ql-formats me-3 border-end pe-3">
                    <select class="ql-color" title="Text Color"></select>
                    <select class="ql-background" title="Background Color"></select>
                </span>

                <!-- Lists & Indent -->
                <span class="ql-formats me-3">
                    <button class="ql-list" value="ordered" title="Numbered List"></button>
                    <button class="ql-list" value="bullet" title="Bullet List"></button>
                    <button class="ql-indent" value="-1" title="Decrease Indent"></button>
                    <button class="ql-indent" value="+1" title="Increase Indent"></button>
                </span>

                <!-- Alignment -->
                <span class="ql-formats me-3">
                    <button class="ql-align" value="" title="Align Left"></button>
                    <button class="ql-align" value="center" title="Align Center"></button>
                    <button class="ql-align" value="right" title="Align Right"></button>
                    <button class="ql-align" value="justify" title="Justify"></button>
                </span>

                <!-- Media -->
                <span class="ql-formats me-3">
                    <button class="ql-link" title="Insert Link"></button>
                    <button class="ql-image" title="Insert Image"></button>
                    <button class="ql-video" title="Insert Video"></button>
                </span>

                <!-- Quick Formatting -->
                <span class="ql-formats me-3">
                    <button class="ql-clean" title="Clear Formatting"></button>
                    <button class="ql-code-block" title="Code Block"></button>
                    <button class="ql-blockquote" title="Blockquote"></button>
                </span>
            </div>
        </div>

        <!-- Editor Area -->
        <div class="editor-main-container flex-grow-1 d-flex">
            <div class="editor-container flex-grow-1 d-flex justify-content-center bg-white">
                <div class="editor-wrapper w-100" style="max-width: 800px;">
                    <div id="editor" class="py-5 px-4"></div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="notepad-footer border-top bg-light py-2 px-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="footer-left d-flex align-items-center">
                    <div class="connection-status me-3">
                        <div class="d-flex align-items-center">
                            <div class="connection-dot connected"></div>
                            <small class="text-muted ms-1">Connected</small>
                        </div>
                    </div>
                    <small class="text-muted" id="documentId">Document ID: {{ $notes->id }}</small>
                </div>
                <div class="footer-right d-flex align-items-center">
                    <button class="btn btn-outline-secondary btn-sm me-2" onclick="copyToClipboard()">
                        <i class="fa-solid fa-link me-1"></i>Copy Link
                    </button>
                    <button class="btn btn-outline-secondary btn-sm me-2" onclick="printDocument()">
                        <i class="fa-solid fa-print me-1"></i>Print
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Share Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="share-preview mb-4 p-3 bg-light rounded">
                    <h6>{{ $notes->title ?? 'Untitled Document' }}</h6>
                    <small class="text-muted" id="shareStats">0 words • 0 characters • Last edited just now</small>
                </div>
                
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="shareUrlInput" value="{{ url('/') }}/{{ $notes->url }}" readonly>
                    <button class="btn btn-primary" onclick="copyShareUrl()">
                        <i class="fa-solid fa-copy me-1"></i>Copy
                    </button>
                </div>

                <div class="collaborators-list mt-4">
                    <h6>Active Collaborators</h6>
                    <div id="collaboratorsList" class="d-flex flex-wrap gap-2 mt-2"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Templates Modal -->
<div class="modal fade" id="templatesModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Choose a Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="template-card card h-100" onclick="applyTemplate('meeting')">
                            <div class="card-body text-center">
                                <i class="fa-solid fa-users fa-2x text-primary mb-3"></i>
                                <h6>Meeting Notes</h6>
                                <small class="text-muted">Structured meeting template</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="template-card card h-100" onclick="applyTemplate('blog')">
                            <div class="card-body text-center">
                                <i class="fa-solid fa-blog fa-2x text-success mb-3"></i>
                                <h6>Blog Post</h6>
                                <small class="text-muted">SEO-friendly blog structure</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="template-card card h-100" onclick="applyTemplate('todo')">
                            <div class="card-body text-center">
                                <i class="fa-solid fa-list-check fa-2x text-warning mb-3"></i>
                                <h6>To-Do List</h6>
                                <small class="text-muted">Task management template</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="template-card card h-100" onclick="applyTemplate('code')">
                            <div class="card-body text-center">
                                <i class="fa-solid fa-code fa-2x text-info mb-3"></i>
                                <h6>Code Snippet</h6>
                                <small class="text-muted">Programming code template</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="template-card card h-100" onclick="applyTemplate('letter')">
                            <div class="card-body text-center">
                                <i class="fa-solid fa-envelope fa-2x text-danger mb-3"></i>
                                <h6>Business Letter</h6>
                                <small class="text-muted">Professional letter format</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="template-card card h-100" onclick="applyTemplate('report')">
                            <div class="card-body text-center">
                                <i class="fa-solid fa-chart-bar fa-2x text-purple mb-3"></i>
                                <h6>Project Report</h6>
                                <small class="text-muted">Comprehensive report template</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Word Count Modal -->
<div class="modal fade" id="wordCountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Document Statistics</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="stat-box p-3">
                            <h3 class="text-primary" id="modalWordCount">0</h3>
                            <small class="text-muted">Words</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stat-box p-3">
                            <h3 class="text-success" id="modalCharCount">0</h3>
                            <small class="text-muted">Characters</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stat-box p-3">
                            <h3 class="text-info" id="modalParagraphCount">0</h3>
                            <small class="text-muted">Paragraphs</small>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <small class="text-muted">Reading time: <span id="modalReadTime">0</span> minutes</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SOCKET + Quill -->
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const noteId = "{{ $notes->id }}";

    const currentUser = {
        id: "{{ auth()->user()->id ?? '' }}" || "guest_" + Date.now(),
        name: "{{ auth()->user()->username ?? 'Guest' }}",
        color: getRandomColor(),
        initial: "{{ auth()->user()->username ?? 'G' }}".charAt(0).toUpperCase()
    };

    const socket = io("http://localhost:3000", { transports: ["websocket"] });

    const quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: '#toolbar-container',
            history: {
                delay: 1000,
                maxStack: 100,
                userOnly: true
            }
        },
        placeholder: 'Start writing...'
    });

    // Load saved content iz Laravel baze
    try {
        const savedContent = @json($notes->content ?? '{}');
        if (savedContent && savedContent !== '{}') {
            quill.setContents(JSON.parse(savedContent));
        }
    } catch (e) {
        console.log('Empty document start');
    }

    // Stats
    function updateStats() {
        const text = quill.getText().trim();
        const words = text ? text.split(/\s+/).length : 0;
        const characters = text.length;
        const paragraphs = text.split('\n').filter(p => p.trim().length > 0).length;
        const readTime = Math.ceil(words / 200);

        document.getElementById('wordCount').textContent = words;
        document.getElementById('charCount').textContent = characters;
        document.getElementById('readTime').textContent = readTime;

        document.getElementById('shareStats').textContent =
            `${words} words • ${characters} characters • Last edited just now`;
    }

    // Text change -> šaljemo promjene
    quill.on('text-change', function(delta, oldDelta, source) {
        updateStats();
        if (source === 'user') {
            socket.emit("document:update", {
                noteId: noteId,
                content: quill.getContents(),
                user: currentUser
            });
        }
    });

    // Primamo remote promjene
    socket.on("document:update", (data) => {
        if (data.noteId === noteId && data.user.id !== currentUser.id) {
            quill.setContents(data.content);
            updateStats();
            showEditingStatus(data.user.name);
        }
    });

    // User join
    socket.emit("user:join", { noteId, user: currentUser });

    socket.on("users:update", (users) => {
    // Filtriraj duplikate po user.id
    const uniqueUsers = [...new Map(users.map(u => [u.id, u])).values()];

    const avatarGroup = document.getElementById('avatarGroup');
    const collaboratorsList = document.getElementById('collaboratorsList');
    
    avatarGroup.innerHTML = '';
    collaboratorsList.innerHTML = '';

    uniqueUsers.forEach(user => {
        const avatar = document.createElement('div');
        avatar.className = 'avatar-circle';
        avatar.style.backgroundColor = user.color;
        avatar.title = user.name;
        avatar.innerHTML = `<span class="avatar-initial">${user.initial}</span>`;
        avatarGroup.appendChild(avatar);

        const collaborator = document.createElement('div');
        collaborator.className = 'collaborator-badge';
        collaborator.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="avatar-circle me-2" style="background-color: ${user.color}">
                    <span class="avatar-initial">${user.initial}</span>
                </div>
                <span>${user.name}</span>
            </div>
        `;
        collaboratorsList.appendChild(collaborator);
    });

    document.getElementById('activeUsersCount').textContent =
            `${uniqueUsers.length} ${uniqueUsers.length === 1 ? 'person' : 'people'} editing`;
    });

    // Typing indicator
    quill.on('selection-change', function(range, oldRange, source) {
        if (source === 'user') {
            socket.emit("user:typing", { noteId, user: currentUser });
        }
    });

    socket.on("user:typing", (data) => {
        if (data.noteId === noteId && data.user.id !== currentUser.id) {
            document.getElementById('typingIndicator').textContent = `${data.user.name} is typing...`;
            setTimeout(() => {
                document.getElementById('typingIndicator').textContent = '';
            }, 1000);
        }
    });

    function getRandomColor() {
        const colors = ["#6c5ce7", "#00b894", "#d63031", "#fdcb6e", "#0984e3"];
        return colors[Math.floor(Math.random() * colors.length)];
    }
});
</script>

<style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --glass-bg: rgba(255, 255, 255, 0.25);
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

.logo-circle {
    background: var(--primary-gradient) !important;
}

.gradient-text {
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.editing-status-bar {
    background: var(--primary-gradient);
}

.status-glow {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #fff;
    transition: all 0.3s ease;
}

.status-glow.glowing {
    animation: pulse-glow 1.5s infinite;
    box-shadow: 0 0 10px #fff;
}

@keyframes pulse-glow {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.2); opacity: 0.7; }
    100% { transform: scale(1); opacity: 1; }
}

.avatar-stack {
    display: flex;
}

.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 12px;
    border: 2px solid white;
    margin-left: -8px;
    position: relative;
    transition: transform 0.2s ease;
}

.avatar-circle:hover {
    transform: scale(1.1);
    z-index: 10;
}

.avatar-circle:first-child {
    margin-left: 0;
}

.typing-dot {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    border: 2px solid white;
}

.live-pulse {
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    animation: live-pulse 2s infinite;
}

@keyframes live-pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.5); opacity: 0.7; }
    100% { transform: scale(1); opacity: 1; }
}

.voice-wave {
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    border: 2px solid #007bff;
    border-radius: 50%;
    animation: voice-pulse 1.5s infinite;
    opacity: 0;
}

@keyframes voice-pulse {
    0% { transform: scale(1); opacity: 1; }
    100% { transform: scale(1.3); opacity: 0; }
}

.voice-btn-container.recording .voice-wave {
    animation: voice-pulse 0.5s infinite;
}

.glass-effect {
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.focus-mode .notepad-head,
.focus-mode .editing-status-bar,
.focus-mode .notepad-footer {
    opacity: 0.1;
    transition: opacity 0.3s ease;
}

.focus-mode:hover .notepad-head,
.focus-mode:hover .editing-status-bar,
.focus-mode:hover .notepad-footer {
    opacity: 1;
}

.template-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.template-card:hover {
    transform: translateY(-5px);
    border-color: #007bff;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.connection-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.connection-dot.connected {
    background: #10b981;
    animation: connection-pulse 2s infinite;
}

@keyframes connection-pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.floating-actions .btn {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.floating-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.stat-box {
    border-radius: 10px;
    background: #f8f9fa;
    transition: transform 0.2s ease;
}

.stat-box:hover {
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .notepad-head {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .collaboration-widget {
        margin: 10px 0;
    }
    
    .floating-actions {
        bottom: 10px;
        right: 10px;
    }
    
    .floating-actions .btn {
        width: 45px;
        height: 45px;
    }
}

.dark-mode {
    background: #1a1a1a;
    color: #ffffff;
}

.dark-mode .notepad-head {
    background: #2d2d2d !important;
}

.dark-mode .editor-container {
    background: #1a1a1a !important;
}

.dark-mode .ql-editor {
    color: #ffffff;
}

.dark-mode .glass-effect {
    background: rgba(45, 45, 45, 0.8);
}

.fs-7 {
    font-size: 0.8rem !important;
}
</style>
@endsection