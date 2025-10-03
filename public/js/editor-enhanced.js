document.addEventListener("DOMContentLoaded", function() {
    const firebaseConfig = {
        apiKey: "AIzaSyBaYLuVXEmk7US9O9SFN4lyUV_N7ewaoGE",
        authDomain: "notepad-7e2a5.firebaseapp.com",
        databaseURL: "https://notepad-7e2a5-default-rtdb.firebaseio.com",
        projectId: "notepad-7e2a5",
        storageBucket: "notepad-7e2a5.appspot.com",
        messagingSenderId: "690331289459",
        appId: "1:690331289459:web:f5c6cef8318c142982a706"
    };

    firebase.initializeApp(firebaseConfig);
    const db = firebase.database();
    const noteId = "{{ $notes->id }}";
    const contentRef = db.ref(`notes/${noteId}/content`);
    const usersRef = db.ref(`notes/${noteId}/users`);
    const typingRef = db.ref(`notes/${noteId}/typing`);

    const currentUser = {
        id: "{{ auth()->user()->id ?? 'guest_' + Date.now() }}",
        name: "{{ auth()->user()->username ?? 'Guest' }}",
        color: getRandomColor(),
        initial: "{{ auth()->user()->username ?? 'G' }}".charAt(0).toUpperCase()
    };

    const quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: '#toolbar-container',
            history: { delay: 1000, maxStack: 100, userOnly: true }
        },
        placeholder: 'Start writing... or type "/" for commands'
    });

    try {
        const savedContent = @json($notes->content ?? '{}');
        if (savedContent && savedContent !== '{}') quill.setContents(JSON.parse(savedContent));
    } catch(e) { console.log('Starting with empty document'); }

    function updateStats() {
        const text = quill.getText().trim();
        const words = text ? text.split(/\s+/).length : 0;
        const characters = text.length;
        const readTime = Math.ceil(words / 200);
        document.getElementById('wordCount').textContent = words;
        document.getElementById('charCount').textContent = characters;
        document.getElementById('readTime').textContent = readTime;
        document.getElementById('shareStats').textContent = 
            `${words} words • ${characters} characters • Last edited just now`;
    }

    let isLocalChange = false;
    let saveTimeout, typingTimeout;

    quill.on('text-change', function(delta, oldDelta, source) {
        updateStats();
        if (source === 'user') {
            isLocalChange = true;
            contentRef.set({ content: quill.getContents(), lastModified: Date.now(), user: currentUser.name });
            usersRef.child(currentUser.id).set({
                name: currentUser.name, color: currentUser.color, initial: currentUser.initial,
                lastActive: Date.now(), typing: true
            });
            typingRef.child(currentUser.id).set(true);
            clearTimeout(typingTimeout);
            typingTimeout = setTimeout(() => { typingRef.child(currentUser.id).remove(); }, 1000);
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(saveToServer, 1500);
        }
    });

    contentRef.on('value', snapshot => {
        const data = snapshot.val();
        if (data && !isLocalChange) {
            quill.setContents(data.content);
            updateStats();
            showEditingStatus(data.user);
        }
        isLocalChange = false;
    });

    usersRef.on('value', snapshot => {
        const activeUsers = [];
        const avatarGroup = document.getElementById('avatarGroup');
        const collaboratorsList = document.getElementById('collaboratorsList');
        avatarGroup.innerHTML = '';
        collaboratorsList.innerHTML = '';
        snapshot.forEach(childSnapshot => {
            const user = childSnapshot.val();
            if (Date.now() - user.lastActive < 10000) {
                activeUsers.push(user);
                const avatar = document.createElement('div');
                avatar.className = 'avatar-circle';
                avatar.style.backgroundColor = user.color;
                avatar.title = user.name;
                avatar.innerHTML = `<span class="avatar-initial">${user.initial}</span>${user.typing ? '<div class="typing-dot"></div>' : ''}`;
                avatarGroup.appendChild(avatar);
                const collaborator = document.createElement('div');
                collaborator.className = 'collaborator-badge';
                collaborator.innerHTML = `
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-2" style="background-color: ${user.color}">
                            <span class="avatar-initial">${user.initial}</span>
                        </div>
                        <span>${user.name}</span>
                        ${user.typing ? '<small class="text-muted ms-2">typing...</small>' : ''}
                    </div>`;
                collaboratorsList.appendChild(collaborator);
            }
        });
        document.getElementById('activeUsersCount').textContent = 
            `${activeUsers.length} ${activeUsers.length===1?'person':'people'} editing`;
    });

    typingRef.on('value', snapshot => {
        const typingUsers = [];
        snapshot.forEach(childSnapshot => {
            if (childSnapshot.key !== currentUser.id) {
                typingUsers.push(childSnapshot.key);
            }
        });
        const indicator = document.getElementById('typingIndicator');
        indicator.textContent = typingUsers.length > 0 ? `${typingUsers.join(', ')} typing...` : '';
    });

    function toggleFocusMode() { document.body.classList.toggle('focus-mode'); showToast(document.body.classList.contains('focus-mode')?'Focus mode enabled':'Focus mode disabled'); }
    function toggleFullscreen() { !document.fullscreenElement ? document.documentElement.requestFullscreen() : document.exitFullscreen(); }
    function showTemplates() { new bootstrap.Modal(document.getElementById('templatesModal')).show(); }
    function applyTemplate(type) {
        const templates = {
            'meeting': `Meeting Notes\n\nDate: ${new Date().toLocaleDateString()}\nTime: ${new Date().toLocaleTimeString()}\n\nAttendees:\n• \n• \n• \n\nAgenda:\n1. \n2. \n3. \n\nDiscussion:\n• \n• \n\nAction Items:\n- [ ] \n- [ ] \n\nNext Meeting: `,
            'blog': `# Blog Post Title\n\n## Introduction\nStart with an engaging hook...\n\n## Main Content\nDevelop your ideas...\n\n### Key Points\n- First important point\n- Second important point\n- Third important point\n\n## Conclusion\nSummarize your arguments...`,
            'todo': `# To-Do List\n\n## 🎯 Today\n- [ ] \n- [ ] \n- [ ] \n\n## 📅 This Week\n- [ ] \n- [ ] \n\n## 🗓️ This Month\n- [ ] \n\n## ✅ Completed\n- [x] \n- [x] `
        };
        quill.setText(templates[type]||'');
        bootstrap.Modal.getInstance(document.getElementById('templatesModal')).hide();
        showToast(`${type.charAt(0).toUpperCase()+type.slice(1)} template applied!`);
    }

    function exportDocument(format) {
        const content = quill.getText();
        const filename = `document-${Date.now()}.${format}`;
        if (format==='pdf'){ showToast('PDF export coming soon!'); }
        else { const blob = new Blob([content],{type: format==='doc'?'application/msword':'text/plain'}); downloadBlob(blob, filename); }
    }

    function downloadBlob(blob, filename) { const url = URL.createObjectURL(blob); const a=document.createElement('a'); a.href=url; a.download=filename; a.click(); URL.revokeObjectURL(url); }
    function showEditingStatus(userName){ const statusElement=document.getElementById('currentUser'); const statusGlow=document.getElementById('statusGlow'); statusElement.textContent=`${userName} is editing...`; statusGlow.classList.add('glowing'); setTimeout(()=>{statusElement.textContent='Ready to collaborate'; statusGlow.classList.remove('glowing');},3000);}
    function showSaveIndicator(){ const indicator=document.getElementById('saveStatus'); indicator.innerHTML='<i class="fa-solid fa-cloud-check text-success me-1"></i><span class="text-success">Just now</span>'; setTimeout(()=>{indicator.innerHTML='<i class="fa-solid fa-circle-check text-success me-1"></i><span class="text-muted">All changes saved</span>';},3000);}
    function saveToServer(){ const content=JSON.stringify(quill.getContents()); fetch("{{ route('notes.update', $notes->url) }}",{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":"{{ csrf_token() }}"},body:JSON.stringify({content:content})}).then(()=>{showSaveIndicator(); updateLastSaved();});}
    function updateLastSaved(){ document.getElementById('lastSaved').textContent=new Date().toLocaleTimeString(); }

    function showToast(msg){ const toastEl = document.getElementById('editorToast'); if(toastEl){ toastEl.querySelector('.toast-body').textContent = msg; const toast = new bootstrap.Toast(toastEl); toast.show(); } }

    function getRandomColor(){ const letters='0123456789ABCDEF'; let color='#'; for(let i=0;i<6;i++){ color+=letters[Math.floor(Math.random()*16)]; } return color; }

    updateStats();
    usersRef.child(currentUser.id).set({ name:currentUser.name, color:currentUser.color, initial:currentUser.initial, lastActive:Date.now() });
    usersRef.child(currentUser.id).onDisconnect().remove();
    typingRef.child(currentUser.id).onDisconnect().remove();

    document.addEventListener('keydown', e => {
        if(e.ctrlKey||e.metaKey){
            switch(e.key){
                case 's': e.preventDefault(); saveToServer(); break;
            }
        }
    });

    window.toggleFocusMode = toggleFocusMode;
    window.toggleFullscreen = toggleFullscreen;
    window.showTemplates = showTemplates;
    window.applyTemplate = applyTemplate;
    window.exportDocument = exportDocument;
    window.shareDocument = function(){ new bootstrap.Modal(document.getElementById('shareModal')).show(); };
    window.copyShareUrl = function(){ const input=document.getElementById('shareUrlInput'); input.select(); document.execCommand('copy'); showToast('Link copied to clipboard!'); };
});
