var quill = new Quill('#editor', {
      theme: 'snow',
      modules: {
          toolbar: '#toolbar-container' 
      }
  });

    
  var delta;

try {
    delta = JSON.parse(@json($notes->content ?? "{}"));
    quill.setContents(delta);
} catch (e) {
    quill.setText(@json($notes->content ?? ""));
}

  let saveTimer;

  function updateStats() {
      let text = quill.getText().trim();
      let words = text.length > 0 ? text.split(/\s+/).length : 0;
      let characters = text.length;
      let sentences = text.length > 0 ? (text.match(/[.!?]+/g) || []).length : 0;

      document.getElementById("wordCount").innerText =
          "Words: " + words + ", Characters: " + characters + ", Sentences: " + sentences;
  }

  quill.on('text-change', function() {
      updateStats();

      clearTimeout(saveTimer);
      saveTimer = setTimeout(() => {
          let content = JSON.stringify(quill.getContents());

          fetch("{{ route('notes.update', $notes->url) }}", {
              method: "POST",
              headers: {
                  "Content-Type": "application/json",
                  "X-CSRF-TOKEN": "{{ csrf_token() }}"
              },
              body: JSON.stringify({ content: content })
          })
          .then(res => console.log(res))
          .then(data => console.log("Autosaved", data))
          .catch(err => console.error("Error saving:", err));
      }, 5000); // 5 sekundi
  });

  //speach
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

    if (SpeechRecognition) {
        const recognition = new SpeechRecognition();
        recognition.continuous = true; 
        recognition.interimResults = true; 
        recognition.lang = 'en-US'; 

        let recognizing = false;

        document.getElementById('speechBtn').addEventListener('click', () => {
            if (!recognizing) {
                recognition.start();
            } else {
                recognition.stop();
            }
        });

        recognition.onstart = () => {
            recognizing = true;
            document.getElementById('speechBtn').classList.add('btn-success');
        };

        recognition.onend = () => {
            recognizing = false;
            document.getElementById('speechBtn').classList.remove('btn-success');
        };

        recognition.onresult = (event) => {
            let transcript = '';
            for (let i = event.resultIndex; i < event.results.length; ++i) {
                transcript += event.results[i][0].transcript;
            }

            const range = quill.getSelection();
            const position = range ? range.index : quill.getLength();
            quill.insertText(position, transcript);
            quill.setSelection(position + transcript.length);
            updateStats();
        };
    } else {
        alert('Your browser does not support Speech Recognition.');
    }
