/*====================================================
 ASTRA AI
 Part 5 - Core JavaScript
=====================================================*/

const clock = document.getElementById("clock");
const dateBox = document.getElementById("date");

const chatBox = document.getElementById("chatBox");
const input = document.getElementById("userInput");

const sendBtn = document.getElementById("sendBtn");
const micBtn = document.getElementById("micBtn");

const suggestionsBox = document.getElementById("suggestionsBox");
const suggestionsList = document.getElementById("suggestionsList");

const loader = document.getElementById("loader");

/*======================================
 LOADER
======================================*/

window.addEventListener("load", () => {
    setTimeout(() => {
        loader.classList.add("hide");
    }, 1800);
});

/*======================================
 CLOCK
======================================*/

function updateClock() {
    const now = new Date();

    clock.innerHTML = now.toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit"
    });

    dateBox.innerHTML = now.toLocaleDateString([], {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric"
    });
}

updateClock();
setInterval(updateClock, 1000);

/*======================================
 CHAT LOCK (ONE QUESTION AT A TIME)

 isAwaitingReply is the single source of
 truth for "is ASTRA currently answering
 something". sendMessage() checks it
 before starting a new request, and the
 input/buttons are disabled while it's
 true, so a new question can only be sent
 once the previous one has been fully
 answered - no overlapping requests, and
 no old reply arriving after a newer one.
======================================*/

const TYPING_CHAR_DELAY_MS = 18;   // speed of the letter-by-letter effect
const REQUEST_TIMEOUT_MS = 30000;  // give up waiting for a reply after 30s

let isAwaitingReply = false;

function setChatEnabled(enabled) {
    input.disabled = !enabled;
    sendBtn.disabled = !enabled;
    micBtn.disabled = !enabled;

    if (enabled) {
        input.focus();
    }
}

/*======================================
 ENTER KEY
======================================*/

input.addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
        sendMessage();
    }
});

/*======================================
 SEND BUTTON
======================================*/

sendBtn.addEventListener("click", sendMessage);

/*======================================
 CREATE MESSAGE

 Renders a chat bubble and reveals its
 text one character at a time. Pass
 onComplete to run something once the
 animation finishes. User bubbles get an
 edit control appended once typing ends,
 so the person can revise and resend
 their own question (see EDIT MESSAGE
 below).
======================================*/

function addMessage(text, type, onComplete, personnel) {
    const div = document.createElement("div");
    div.className = type === "user" ? "user-message" : "ai-message";
    chatBox.appendChild(div);

    const textSpan = document.createElement("span");
    textSpan.className = "message-text";
    div.appendChild(textSpan);

    let i = 0;

    function revealNextCharacter() {
        if (i < text.length) {
            textSpan.innerHTML += text.charAt(i);
            i++;
            chatBox.scrollTop = chatBox.scrollHeight;
            setTimeout(revealNextCharacter, TYPING_CHAR_DELAY_MS);
            return;
        }

        if (type === "user") {
            addEditControl(div, textSpan);
        }

        if (Array.isArray(personnel) && personnel.length > 0) {
            renderPersonnelCards(div, personnel);
        }

        if (onComplete) {
            onComplete();
        }
    }

    revealNextCharacter();

    return div;
}

/*======================================
 SMART QUESTION SUGGESTIONS

 ASTRA asks the server for questions related
 to the student's current topic. The questions
 come from the knowledge database, so the
 suggestions automatically grow when new
 knowledge is added by the administrator.
======================================*/

function clearSuggestions() {
    if (!suggestionsList) return;

    suggestionsList.innerHTML = "";

    if (suggestionsBox) {
        suggestionsBox.classList.remove("show");
    }
}

function renderSuggestions(suggestions) {
    if (!suggestionsList || !suggestionsBox) return;

    suggestionsList.innerHTML = "";

    if (!Array.isArray(suggestions) || suggestions.length === 0) {
        suggestionsBox.classList.remove("show");
        return;
    }

    suggestions.forEach(question => {
        const button = document.createElement("button");

        button.type = "button";
        button.className = "suggestion-chip";
        button.textContent = question;

        button.addEventListener("click", () => {
            if (isAwaitingReply) return;

            input.value = question;
            sendMessage();
        });

        suggestionsList.appendChild(button);
    });

    suggestionsBox.classList.add("show");
    chatBox.scrollTop = chatBox.scrollHeight;
}

function loadSuggestions(topic) {
    if (!suggestionsList || !suggestionsBox) return;

    fetch("database/suggestions.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "message=" + encodeURIComponent(topic)
    })
    .then(res => res.json())
    .then(data => {
        renderSuggestions(data.suggestions || []);
    })
    .catch(() => {
        clearSuggestions();
    });
}

// Show useful starter questions only on the main chat screen.
window.addEventListener("DOMContentLoaded", () => {
    const infoPage = document.getElementById("informationPage");
    const mapPage = document.getElementById("coreMapPage");

    const infoHidden = !infoPage || getComputedStyle(infoPage).display === "none";
    const mapHidden = !mapPage || getComputedStyle(mapPage).display === "none";

    if (infoHidden && mapHidden) {
        loadSuggestions("Core Gateway College student services tuition admission courses");
    } else {
        clearSuggestions();
    }
});


/*======================================
 SEND MESSAGE

 The single entry point for asking ASTRA
 something - the Enter key, the Send
 button, and voice input all call this.
 If a reply is already in progress, the
 attempt is blocked so only ever one
 question is being answered at a time.
======================================*/

function sendMessage() {
    if (isAwaitingReply) {
        addSystemMessage("Please wait for ASTRA to finish answering.");
        return;
    }

    const text = input.value.trim();

    if (text.length < 2) {
        addSystemMessage("Please enter a complete question.");
        return;
    }

    addMessage(text, "user");
    input.value = "";
    clearSuggestions();
    askAstra(text);
}

/*======================================
 ASK ASTRA

 Sends a question to ask.php and renders
 the reply. Shared by sendMessage() (new
 questions typed in the input bar) and
 resendEditedMessage() (an existing user
 bubble that was edited in place).
======================================*/

function askAstra(questionText) {
    isAwaitingReply = true;
    setChatEnabled(false);

    typingAnimation();

    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), REQUEST_TIMEOUT_MS);

    fetch("database/ask.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "message=" + encodeURIComponent(questionText),
        signal: controller.signal
    })
    .then(res => res.json())
    .then(data => {
        clearTimeout(timeoutId);
        removeTyping();
        addMessage(data.reply, "ai", onReplyFinished, data.personnel || []);
        speak(data.reply);

        // Show 3 related questions underneath the conversation.
        loadSuggestions(questionText);
    })
    .catch(() => {
        clearTimeout(timeoutId);
        removeTyping();
        addMessage(
            "Sorry, I'm having trouble answering right now. Please try again.",
            "ai",
            onReplyFinished
        );

        loadSuggestions(questionText);
    });
}

function onReplyFinished() {
    isAwaitingReply = false;
    setChatEnabled(true);
}

/*======================================
 CGCI PERSONNEL RESULT CARDS
======================================*/

function renderPersonnelCards(messageDiv, personnel) {
    const wrap = document.createElement("div");
    wrap.className = "ai-personnel-results";

    personnel.forEach(person => {
        const card = document.createElement("div");
        card.className = "ai-personnel-card";

        const img = document.createElement("img");
        img.className = "ai-personnel-photo";
        img.src = person.image || "";
        img.alt = person.name || "CGCI Personnel";
        img.loading = "lazy";
        img.onerror = function () {
            this.style.display = "none";
        };

        const info = document.createElement("div");
        info.className = "ai-personnel-info";

        const name = document.createElement("div");
        name.className = "ai-personnel-name";
        name.textContent = person.name || "";

        const position = document.createElement("div");
        position.className = "ai-personnel-position";
        position.textContent = person.position || "";

        const department = document.createElement("div");
        department.className = "ai-personnel-department";
        department.textContent = person.department || "";

        info.appendChild(name);
        info.appendChild(position);
        if (person.department) info.appendChild(department);

        card.appendChild(img);
        card.appendChild(info);
        wrap.appendChild(card);
    });

    messageDiv.appendChild(wrap);
    chatBox.scrollTop = chatBox.scrollHeight;
}

/*======================================
 EDIT MESSAGE

 Lets the user click the pencil icon on
 one of their own chat bubbles, revise
 the text inline, and send it again.
 Sending the edit removes everything that
 came after that bubble (the old reply
 included) and asks ASTRA fresh, the same
 way ChatGPT-style "edit & regenerate"
 works.
======================================*/

function addEditControl(messageDiv, textSpan) {
    const editBtn = document.createElement("button");
    editBtn.type = "button";
    editBtn.className = "edit-msg-btn";
    editBtn.title = "Edit & resend";
    editBtn.innerHTML = '<i class="fa-solid fa-pen"></i>';

    editBtn.addEventListener("click", () => startEditingMessage(messageDiv, textSpan));

    messageDiv.appendChild(editBtn);
}

function startEditingMessage(messageDiv, textSpan) {
    if (isAwaitingReply) {
        addSystemMessage("Please wait for ASTRA to finish answering.");
        return;
    }

    // already editing this bubble - ignore repeat clicks
    if (messageDiv.classList.contains("editing")) {
        return;
    }

    const editBtn = messageDiv.querySelector(".edit-msg-btn");
    const currentText = textSpan.textContent;

    messageDiv.classList.add("editing");
    textSpan.style.display = "none";
    if (editBtn) {
        editBtn.style.display = "none";
    }

    const textarea = document.createElement("textarea");
    textarea.className = "edit-msg-textarea";
    textarea.value = currentText;

    const actions = document.createElement("div");
    actions.className = "edit-msg-actions";

    const saveBtn = document.createElement("button");
    saveBtn.type = "button";
    saveBtn.className = "edit-msg-save";
    saveBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Send';

    const cancelBtn = document.createElement("button");
    cancelBtn.type = "button";
    cancelBtn.className = "edit-msg-cancel";
    cancelBtn.innerHTML = '<i class="fa-solid fa-xmark"></i> Cancel';

    actions.appendChild(saveBtn);
    actions.appendChild(cancelBtn);

    messageDiv.appendChild(textarea);
    messageDiv.appendChild(actions);

    textarea.focus();
    textarea.setSelectionRange(textarea.value.length, textarea.value.length);

    function stopEditing() {
        textarea.remove();
        actions.remove();
        textSpan.style.display = "";
        if (editBtn) {
            editBtn.style.display = "";
        }
        messageDiv.classList.remove("editing");
    }

    function submitEdit() {
        const newText = textarea.value.trim();

        if (newText.length < 2) {
            addSystemMessage("Please enter a complete question.");
            return;
        }

        stopEditing();
        resendEditedMessage(messageDiv, textSpan, newText);
    }

    saveBtn.addEventListener("click", submitEdit);
    cancelBtn.addEventListener("click", stopEditing);

    textarea.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            submitEdit();
        } else if (e.key === "Escape") {
            stopEditing();
        }
    });
}

function resendEditedMessage(messageDiv, textSpan, newText) {
    if (isAwaitingReply) {
        addSystemMessage("Please wait for ASTRA to finish answering.");
        return;
    }

    textSpan.textContent = newText;

    // drop every bubble that followed the edited question
    // (its old reply, typing indicator, etc.) - the
    // conversation continues from here with a fresh answer
    while (messageDiv.nextSibling) {
        messageDiv.nextSibling.remove();
    }

    askAstra(newText);
}

/*======================================
 TYPING INDICATOR
======================================*/

function typingAnimation() {
    const typing = document.createElement("div");

    typing.className = "ai-message";
    typing.id = "typing";
    typing.innerHTML = "ASTRA is typing...";

    chatBox.appendChild(typing);
    chatBox.scrollTop = chatBox.scrollHeight;
}

function removeTyping() {
    const t = document.getElementById("typing");

    if (t) {
        t.remove();
    }
}

/*======================================
 TEXT TO SPEECH (Female Voice)
======================================*/

let femaleVoice = null;

// Load available voices
function loadVoices() {
    const voices = speechSynthesis.getVoices();

    // Try to find a female voice
    femaleVoice = voices.find(v =>
        v.name.includes("Zira") ||
        v.name.includes("Aria") ||
        v.name.includes("Jenny") ||
        v.name.includes("Samantha") ||
        v.name.includes("Female") ||
        v.name.includes("Google US English")
    );

    // If none found, use the first English voice
    if (!femaleVoice) {
        femaleVoice = voices.find(v => v.lang.startsWith("en"));
    }
}

// Some browsers load voices asynchronously
speechSynthesis.onvoiceschanged = loadVoices;
loadVoices();

function speak(text) {
    if (!("speechSynthesis" in window)) return;

    speechSynthesis.cancel();

    const speech = new SpeechSynthesisUtterance(text);

    speech.voice = femaleVoice;
    speech.lang = "en-US";

    // Female sounding settings
    speech.rate = 0.9;
    speech.pitch = 1.2;
    speech.volume = 100;

    speechSynthesis.speak(speech);
}

/*======================================
 PARTICLES
======================================*/

const particleLayer = document.getElementById("particles");

for (let i = 0; i < 60; i++) {
    const p = document.createElement("span");

    p.style.position = "absolute";
    p.style.width = "2px";
    p.style.height = "2px";
    p.style.borderRadius = "50%";
    p.style.background = "#00ffff";
    p.style.left = Math.random() * 100 + "%";
    p.style.top = Math.random() * 100 + "%";
    p.style.opacity = Math.random();
    p.style.animation = "floatParticle " + (8 + Math.random() * 10) + "s linear infinite";

    particleLayer.appendChild(p);
}

/*======================================
 DYNAMICALLY INJECTED STYLES
 (particle animation, chat bubbles, and
 a dimmed look for the input area while
 ASTRA is answering)
======================================*/

const style = document.createElement("style");

style.innerHTML = `

@keyframes floatParticle {
    0% {
        transform: translateY(0px);
        opacity: .2;
    }
    50% {
        opacity: 1;
    }
    100% {
        transform: translateY(-120vh);
        opacity: 0;
    }
}

.user-message {
    margin-left: auto;
    margin-bottom: 15px;
    padding: 15px;
    max-width: 70%;
    background: #00b8ff33;
    border-right: 4px solid cyan;
    color: white;
    border-radius: 8px;
}

#userInput:disabled,
#sendBtn:disabled,
#micBtn:disabled {
    opacity: .5;
    cursor: not-allowed;
}

`;

document.head.appendChild(style);

/*====================================================
 ASTRA AI
 PART 6 - VOICE RECOGNITION
====================================================*/

const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

if (SpeechRecognition) {

    const recognition = new SpeechRecognition();

    recognition.lang = "en-US";
    recognition.continuous = false;
    recognition.interimResults = false;

    let listening = false;

    micBtn.addEventListener("click", () => {
        if (!listening) {
            recognition.start();
        } else {
            recognition.stop();
        }
    });

    recognition.onstart = () => {
        listening = true;
        micBtn.classList.add("listening");
        micBtn.innerHTML = '<i class="fa-solid fa-microphone-lines"></i>';
        addSystemMessage("🎤 Listening...");
    };

    recognition.onend = () => {
        listening = false;
        micBtn.classList.remove("listening");
        micBtn.innerHTML = '<i class="fa-solid fa-microphone"></i>';
    };

    recognition.onerror = function (e) {
        addSystemMessage("Voice Error : " + e.error);
    };

    recognition.onresult = function (event) {
        const speech = event.results[0][0].transcript;
        input.value = speech;
        sendMessage();
    };

} else {
    addSystemMessage("Voice Recognition is not supported by this browser.");
}

/*=========================================
 SYSTEM MESSAGE
=========================================*/

function addSystemMessage(text) {
    const div = document.createElement("div");

    div.className = "system-message";
    div.innerHTML = text;

    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
}

/*==================================
 Mouse Glow
==================================*/

const glow = document.querySelector(".mouse-glow");

document.addEventListener("mousemove", function (e) {
    glow.style.left = e.clientX + "px";
    glow.style.top = e.clientY + "px";
});

/*==================================
 CGCI PERSONNEL
==================================*/

function openPersonnel() {
    document.querySelector(".chat-window").style.display = "none";
    document.querySelector(".input-area").style.display = "none";
    document.querySelector(".personnel-page").style.display = "block";
}

function closePersonnel() {
    document.querySelector(".personnel-page").style.display = "none";
    document.querySelector(".chat-window").style.display = "block";
    document.querySelector(".input-area").style.display = "flex";
}

function openDepartment(id) {
    window.location.href = "database/personnel.php?department=" + id;
}