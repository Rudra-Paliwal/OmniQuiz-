// ===== FILE: main.js =====
let currentSessionId = null;
let currentQuestion = null;
let timer = null;
let answeredCount = 0;        // 🧠 Tracks how many questions user answered
const MAX_QUESTIONS = 0;      // ✅ Change this number if you want more/less
let usedHints = {}; // Tracks per question
let askedQuestions = []; // 🔁 store question IDs already used


function showTab(tabId) {
  document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
  document.getElementById(tabId).style.display = 'block';
}

function updateConfidence(val) {
  document.getElementById('confVal').innerText = val;
}

async function login() {
  const email = document.getElementById('login_email').value;
  const password = document.getElementById('login_password').value;

  const res = await fetch('api/login.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ email, password })
  });

  const data = await res.json();
  console.log(data); // Debug if needed

  if (data.success) {
  currentSessionId = data.session_id;

  // ✅ Save login session
  localStorage.setItem("quiz_user", JSON.stringify(data));

  alert(`Welcome, ${data.name}!`);
  document.querySelector('.auth').style.display = 'none';

  if (data.role === 'admin') {
    document.getElementById('adminPanel').style.display = 'block';
  } else {
    startQuiz();
  }
}
else {
    alert(data.message);
  }
}


async function register() {
  const name = document.getElementById('reg_name').value;
  const email = document.getElementById('reg_email').value;
  const password = document.getElementById('reg_password').value;
  const res = await fetch('api/register.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ name, email, password })
  });
  const data = await res.json();
  alert(data.message);
}

async function startQuiz() {
  answeredCount = 0;
  document.getElementById('quizContainer').style.display = 'block';
  document.getElementById('quizPanel').style.display = 'block';
  document.getElementById('resultsPanel').style.display = 'none';

  // ✅ Get total question count from backend
  const res = await fetch("api/question_count.php");
  const data = await res.json();
  totalQuestions = data.total || 0;
 console.log("Total questions:", totalQuestions);
  loadQuestion();
}



async function loadQuestion() {
  const res = await fetch("api/get_question.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ exclude: askedQuestions }) // 🟡 send used IDs
  });

  const q = await res.json();

  if (q.end) {
    showResults();
    return;
  }

  currentQuestion = q;
  askedQuestions.push(q.id); // 🟢 remember this one

  document.getElementById("questionBox").innerHTML =
    `<p>${q.text}</p>` + Object.entries(q.options).map(([k, v]) =>
      `<label><input type="radio" name="option" value="${k}"> ${k}: ${v}</label><br>`
    ).join('');

  startTimer(30);
}


function startTimer(sec) {
  let t = sec;
  document.getElementById('timeLeft').innerText = t;
  timer = setInterval(() => {
    t--;
    document.getElementById('timeLeft').innerText = t;
    if (t <= 0) {
      clearInterval(timer);
      submitAnswer();
    }
  }, 1000);
}

function useHint() {
  const qid = currentQuestion.id;

  if (usedHints[qid]) {
    alert("Hint already used for this question:\n\n" + currentQuestion.hint);
    return;
  }

  alert("Hint: " + currentQuestion.hint + " (-10 pts)");
  usedHints[qid] = true;
}



async function submitAnswer() {
  clearInterval(timer);

  const opt = document.querySelector('input[name="option"]:checked');
  if (!opt) return alert("Please select an option.");

  const confidence = document.getElementById('confidence').value;

  const res = await fetch('api/submit_answer.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      question_id: currentQuestion.id,
      chosen_option: opt.value,
      confidence: confidence,
      hint_used: usedHints[currentQuestion.id] || false
    })
  });

  const data = await res.json();
  console.log("Submit Answer Response:", data); // 👈 Add this debug

  document.getElementById('feedback').innerText = data.message;

  if (data.next) {
    loadQuestion();
  } else {
    showResults();
  }
}



async function showResults() {
  document.getElementById("quizPanel").style.display = "none";
  document.getElementById("resultsPanel").style.display = "block";

  const res = await fetch("api/results.php");
  const data = await res.json();

  // ✅ Show total score
  document.getElementById("totalScoreText").innerText = `Total Score: ${data.total}`;

  // ✅ Render bar chart
  new Chart(document.getElementById("performanceChart"), {
    type: 'bar',
    data: {
      labels: data.labels,
      datasets: [{
        label: 'Score by Question',
        data: data.scores,
        backgroundColor: '#64b5f6'
      }]
    }
  });
}


async function tryAgain() {
  askedQuestions = [];
  answeredCount = 0;

  document.getElementById("resultsPanel").style.display = "none";
  document.getElementById("quizPanel").style.display = "block";
  document.getElementById("quizContainer").style.display = "block";
  document.getElementById("feedback").innerText = "";
  document.getElementById("confidence").value = 50;
  document.getElementById("confVal").innerText = "50";

  loadQuestion(); // 🎯 starts fresh
}



async function addQuestion() {
  console.log("📌 Add Question clicked");

  const payload = {
    text: document.getElementById('question_text').value,
    options_json: document.getElementById('options_json').value,
    correct_option: document.getElementById('correct_option').value,
    topic: document.getElementById('topic').value,
    difficulty: document.getElementById('difficulty').value,
    hint: document.getElementById('hint').value
  };

  console.log("Payload:", payload);

  const res = await fetch('api/add_question.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(payload)
  });

  const text = await res.text();
  console.log("Server Response:", text);

  try {
    const data = JSON.parse(text);
    document.getElementById("adminMsg").innerText = data.message;
  } catch (e) {
    document.getElementById("adminMsg").innerText = "❌ Failed to parse response";
  }
}



function showSection(section) {
  console.log("Switched to:", section);

  // All section IDs
  const sections = [
    "addQuestionSection",
    "resultsSection",
    "usersSection",
  ];

  // Hide all
  sections.forEach(id => {
    const el = document.getElementById(id);
    if (el) el.style.display = "none";
  });

  // Show the selected one
  const target = document.getElementById(section + "Section");
  if (target) target.style.display = "block";

  // Load content if needed
    if (section === 'add') addQuestion?.();
  if (section === 'users') fetchUsers?.();
  if (section === 'results') fetchResults?.();
}


async function fetchResults() {
  // Load Chart
  const chartRes = await fetch("api/results_chart.php");
  const chartData = await chartRes.json();

  const ctx = document.getElementById("resultsChart").getContext("2d");
  new Chart(ctx, {
    type: "bar",
    data: {
      labels: chartData.labels,
      datasets: [{
        label: "User Score",
        data: chartData.scores,
        backgroundColor: "#64b5f6"
      }]
    }
  });

  // Load Table
  const tableRes = await fetch("api/results_table.php");
  const responses = await tableRes.json();

  const tbody = document.querySelector("#resultsTable tbody");
  tbody.innerHTML = "";

responses.forEach(row => {
  const tr = document.createElement("tr");
  tr.innerHTML = `
    <td>${row.user}</td>
    <td>${row.question}</td>
    <td>${row.opt}</td>
    <td>${row.correct}</td>
    <td>${row.conf}</td>
    <td>${row.hint}</td>
    <td>${row.time}</td>
    <td>${row.score}</td>
  `;
  tbody.appendChild(tr);
});

}

function downloadMyResults() {
  fetch("api/download_my_results.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ session_id: currentSessionId })
  })
  .then(res => res.blob())
  .then(blob => {
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "my_results.csv";
    link.click();
  });
}

window.onload = function () {
  // ✅ Always reset view to welcome screen on refresh
  document.querySelector('.auth').style.display = 'block';
  document.getElementById('login').style.display = 'block';
  document.getElementById('register').style.display = 'none';

  // Optional: Clear any stored session data
  localStorage.removeItem("quiz_user");
};


function logout() {
  localStorage.removeItem("quiz_user");
  location.reload();
}

async function fetchUsers() {
  const res = await fetch("api/get_users.php");
  const users = await res.json();

  const tbody = document.querySelector("#usersTable tbody");
  tbody.innerHTML = "";

  users.forEach(user => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${user.id}</td>
      <td>${user.name}</td>
      <td>${user.email}</td>
      <td>${user.role}</td>
      <td><button onclick="deleteUser(${user.id})">❌ Delete</button></td>
    `;
    tbody.appendChild(tr);
  });
}

function deleteUser(id) {
  if (!confirm("Are you sure you want to delete this user?")) return;

  fetch("api/delete_user.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      alert("User deleted.");
      fetchUsers(); // refresh user table
    } else {
      alert("Error: " + data.error);
    }
  });
}

function showToast(message) {
  let toast = document.createElement("div");
  toast.className = "toast";
  toast.innerText = message;
  document.body.appendChild(toast);

  // Animate in
  setTimeout(() => toast.classList.add("show"), 100);

  // Auto remove
  setTimeout(() => {
    toast.classList.remove("show");
    setTimeout(() => toast.remove(), 500);
  }, 2500);
}


