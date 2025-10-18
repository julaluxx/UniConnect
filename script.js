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
  const createThreadForm = document.getElementById('create-thread-form');
  if (createThreadForm) {
    createThreadForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const title = document.getElementById('threadTitle').value;
      const content = document.getElementById('threadContent').value;
      const category_id = document.getElementById('threadCategory').value;
      const btn = document.getElementById('create-thread-btn');
      btn.disabled = true;
      const res = await postJSON('create_thread.php', { title, content, category_id });
      btn.disabled = false;
      const alertBox = document.getElementById('thread-alert');
      if (res.status === 'success') {
        alertBox.innerHTML = '<div class="alert alert-success">สร้างกระทู้เรียบร้อย</div>';
        // clear form
        document.getElementById('threadTitle').value = '';
        document.getElementById('threadContent').value = '';
        document.getElementById('threadCategory').value = '';
        fetchThreads();
      } else {
        alertBox.innerHTML = `<div class="alert alert-danger">${res.message || 'Error'}</div>`;
      }
    });
  }
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
