const apiBase = 'api.php';

async function fetchList() {
  const res = await fetch(`${apiBase}?action=list`);
  const data = await res.json();
  renderTable(data);
}

function renderTable(items){
  const tbody = document.querySelector('#contactsTable tbody');
  tbody.innerHTML = '';
  items.forEach(item => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${item.id}</td>
      <td>${escapeHtml(item.name)}</td>
      <td>${escapeHtml(item.email)}</td>
      <td>${escapeHtml(item.phone || '')}</td>
      <td>
        <button class="action-btn" data-id="${item.id}" data-action="edit">Edit</button>
        <button class="action-btn" data-id="${item.id}" data-action="delete">Delete</button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function escapeHtml(str){
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

async function submitForm(e){
  e.preventDefault();
  const id = document.getElementById('contactId').value;
  const name = document.getElementById('name').value.trim();
  const email = document.getElementById('email').value.trim();
  const phone = document.getElementById('phone').value.trim();
  const payload = { id, name, email, phone };
  const action = id ? 'update' : 'create';
  const method = id ? 'PUT' : 'POST';

  const res = await fetch(`${apiBase}?action=${action}`, {
    method,
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });
  const json = await res.json();
  if (json.error) showMessage(json.error, true); else {
    showMessage('Saved.');
    resetForm();
    fetchList();
  }
}

function showMessage(msg, isError=false){
  const el = document.getElementById('message');
  el.textContent = msg;
  el.style.color = isError ? 'crimson' : 'green';
  setTimeout(()=> el.textContent = '', 4000);
}

function resetForm(){
  document.getElementById('contactForm').reset();
  document.getElementById('contactId').value = '';
}

async function handleTableClick(e){
  const btn = e.target.closest('button[data-action]');
  if (!btn) return;
  const id = btn.dataset.id;
  const action = btn.dataset.action;
  if (action === 'delete'){
    if (!confirm('Delete this contact?')) return;
    const res = await fetch(`${apiBase}?action=delete&id=${id}`, { method: 'DELETE' });
    const json = await res.json();
    if (json.error) showMessage(json.error, true); else { showMessage('Deleted.'); fetchList(); }
    return;
  }
  if (action === 'edit'){
    // load item into form
    const rows = document.querySelectorAll('#contactsTable tbody tr');
    for (const r of rows){
      const tdId = r.children[0].textContent;
      if (String(tdId) === String(id)){
        document.getElementById('contactId').value = id;
        document.getElementById('name').value = r.children[1].textContent;
        document.getElementById('email').value = r.children[2].textContent;
        document.getElementById('phone').value = r.children[3].textContent;
        window.scrollTo({top:0,behavior:'smooth'});
        break;
      }
    }
  }
}

function init(){
  document.getElementById('contactForm').addEventListener('submit', submitForm);
  document.getElementById('resetBtn').addEventListener('click', resetForm);
  document.querySelector('#contactsTable').addEventListener('click', handleTableClick);
  fetchList();
}

window.addEventListener('DOMContentLoaded', init);
