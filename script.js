// script.js
document.addEventListener('DOMContentLoaded', () => {
  // โหลดกระทู้เมื่อเข้าหน้า
  fetchThreads();

  // Login form
  const loginForm = document.getElementById('login-form');
  if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const username = document.getElementById('login-username').value;
      const password = document.getElementById('login-password').value;
      const res = await postJSON('login.php', { username, password });
      if (res.status === 'success') {
        window.location.href = res.redirect || 'index.php';
      } else {
        document.getElementById('login-error').textContent = res.message || 'Login failed';
      }
    });
  }

  // Register form
  const registerForm = document.getElementById('register-form');
  if (registerForm) {
    registerForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const username = document.getElementById('register-username').value;
      const email = document.getElementById('register-email').value;
      const password = document.getElementById('register-password').value;
      const confirm = document.getElementById('register-confirm-password').value;
      const res = await postJSON('register.php', { username, email, password, confirm });
      if (res.status === 'success') {
        window.location.href = res.redirect || 'index.php';
      } else {
        document.getElementById('register-error').textContent = res.message || 'Register failed';
      }
    });
  }

  // Create thread
  document.addEventListener('DOMContentLoaded', () => {

  const form = document.getElementById('create-thread-form');
  const alertBox = document.getElementById('thread-alert');
  const threadsContainer = document.getElementById('forum-threads');

  // ฟังก์ชันโหลดกระทู้
  function loadThreads() {
    fetch('fetch_threads.php')
      .then(res => res.json())
      .then(data => {
        threadsContainer.innerHTML = '';
        if (data.length === 0) {
          threadsContainer.innerHTML = '<p>ยังไม่มีกระทู้</p>';
          return;
        }
        data.forEach(thread => {
          const card = document.createElement('div');
          card.className = 'card mb-3';
          card.innerHTML = `
            <div class="card-body">
              <h5 class="card-title">${thread.title}</h5>
              <h6 class="card-subtitle mb-2 text-muted">
                ${thread.username} | ${thread.category} | ${thread.created_at}
              </h6>
              <p class="card-text">${thread.content.replace(/\n/g, '<br>')}</p>
            </div>
          `;
          threadsContainer.appendChild(card);
        });
      });
  }

  // โหลดกระทู้ตอนเริ่มหน้า
  loadThreads();

  // ฟังก์ชันสร้างกระทู้
  form.addEventListener('submit', e => {
    e.preventDefault();

    const formData = new FormData();
    formData.append('title', document.getElementById('threadTitle').value);
    formData.append('content', document.getElementById('threadContent').value);
    formData.append('category_id', document.getElementById('threadCategory').value);

    fetch('create_thread.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      alertBox.textContent = data.message;
      alertBox.className = data.status === 'success' ? 'text-success' : 'text-danger';
      if (data.status === 'success') {
        form.reset();
        loadThreads(); // โหลดกระทู้ใหม่ทันที
      }
    });
  });

});


// helper
async function postJSON(url, data) {
  try {
    const r = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    return await r.json();
  } catch (e) {
    return { status: 'error', message: 'Network error' };
  }
}

async function fetchThreads() {
  try {
    const r = await fetch('fetch_threads.php');
    const j = await r.json();
    if (j.status === 'success') {
      const container = document.getElementById('forum-threads');
      container.innerHTML = '';
      j.threads.forEach(t => {
        const card = document.createElement('div');
        card.className = 'card mb-2';
        card.innerHTML = `
          <div class="card-body">
            <h5 class="card-title">${escapeHtml(t.title)}</h5>
            <p class="card-text">${escapeHtml(t.content.slice(0,250))}${t.content.length>250?'...':''}</p>
            <p class="text-muted small">โดย ${escapeHtml(t.author_name)} | หมวด: ${escapeHtml(t.category_name)} | ความคิดเห็น ${t.comment_count} | ถูกใจ ${t.like_count}</p>
          </div>
        `;
        container.appendChild(card);
      });
    }
  } catch (e) { console.error(e); }
}

function escapeHtml(str) {
  if (!str) return '';
  return String(str).replace(/[&<>"'`=\/]/g, function(s) {
    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#x2F;','`':'&#x60;','=':'&#x3D;'}[s];
  });
}
});

