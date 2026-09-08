function switchTab(tab) {
  const credentialsPanel = document.getElementById('credentialsPanel');
  const scanPanel = document.getElementById('scanPanel');

  if (!credentialsPanel || !scanPanel) return;

  const tabs = document.querySelectorAll('.tab');
  if (tabs.length >= 2) {
    tabs[0].classList.toggle('active', tab === 'credentials');
    tabs[1].classList.toggle('active', tab === 'scan');
  }

  credentialsPanel.style.display = tab === 'credentials' ? 'block' : 'none';
  scanPanel.classList.toggle('active', tab === 'scan');
}

  /* Updated redirect to use Laravel routes (no .html) */
  function redirect(role) {
    if (role === 'admin') window.location.href = '/admin-dashboard';
    else if (role === 'student') window.location.href = '/student-dashboard';
    else if (role === 'staff') window.location.href = '/staff-dashboard';
    else if (role === 'visitor') window.location.href = '/visitor-dashboard';
    else window.location.href = '/researcher-dashboard';
  }

  function updatePlaceholder() {
    const input = document.getElementById('userId');
    if (!input) return;
    input.placeholder = 'Enter your student ID or email';
  }

window.onload = updatePlaceholder;
