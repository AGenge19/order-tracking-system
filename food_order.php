<?php
session_start();

if (!isset($_SESSION['orders'])) {
    $_SESSION['orders'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    if ($_POST['action'] === 'add') {
        $food = trim($_POST['food'] ?? '');
        $qty  = intval($_POST['qty'] ?? 0);
        if ($food && $qty > 0) {
            $found = false;
            foreach ($_SESSION['orders'] as &$row) {
                if (strtolower($row['food']) === strtolower($food)) {
                    $row['qty'] += $qty;
                    $found = true; break;
                }
            }
            if (!$found) {
                $_SESSION['orders'][] = ['id' => uniqid(), 'food' => $food, 'qty' => $qty];
            }
        }
        echo json_encode($_SESSION['orders']); exit;
    }

    if ($_POST['action'] === 'remove') {
        $id = $_POST['id'];
        $_SESSION['orders'] = array_values(array_filter($_SESSION['orders'], fn($r) => $r['id'] !== $id));
        echo json_encode($_SESSION['orders']); exit;
    }

    if ($_POST['action'] === 'clear') {
        $_SESSION['orders'] = [];
        echo json_encode([]); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Food Order Tracker</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Source+Sans+3:wght@300;400;600&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

body {
  min-height: 100vh;
  background: #1a1a2e;
  background-image: radial-gradient(ellipse at 20% 20%, #16213e 0%, #1a1a2e 60%);
  font-family: 'Source Sans 3', sans-serif;
  color: #f0ece4;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 2.5rem 1rem 4rem;
}

h1 {
  font-family: 'Playfair Display', serif;
  font-size: 2.8rem;
  font-weight: 900;
  letter-spacing: -0.02em;
  color: #f0ece4;
  margin-bottom: 0.3rem;
  text-align: center;
}

.subtitle {
  font-size: 0.9rem;
  color: #8a8fa8;
  margin-bottom: 2.5rem;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  text-align: center;
}

.card {
  background: #16213e;
  border: 1px solid #2a2f4a;
  border-radius: 12px;
  width: 100%;
  max-width: 620px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,0.4);
}

/* INPUT SECTION */
.input-section {
  padding: 1.8rem 2rem;
  border-bottom: 1px solid #2a2f4a;
  display: flex;
  gap: 0.8rem;
  align-items: flex-end;
}

.field { flex: 1; }
.field label {
  display: block;
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #8a8fa8;
  margin-bottom: 0.45rem;
}

.field input {
  width: 100%;
  background: #0f1427;
  border: 1.5px solid #2a2f4a;
  border-radius: 8px;
  padding: 0.75rem 1rem;
  font-family: 'Source Sans 3', sans-serif;
  font-size: 0.95rem;
  color: #f0ece4;
  outline: none;
  transition: border-color 0.2s;
}

.field input:focus { border-color: #e8a040; }
.field input::placeholder { color: #3d4265; }

.qty-field { width: 110px; flex: none; }

.btn-add {
  background: #e8a040;
  color: #1a1a2e;
  border: none;
  border-radius: 8px;
  padding: 0.75rem 1.4rem;
  font-family: 'Source Sans 3', sans-serif;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  white-space: nowrap;
  transition: background 0.2s, transform 0.1s;
  flex-shrink: 0;
}
.btn-add:hover { background: #d4902e; transform: translateY(-1px); }
.btn-add:active { transform: translateY(0); }

/* ORDER LIST */
.order-list { padding: 0; }

.list-header {
  display: grid;
  grid-template-columns: 1fr 100px 40px;
  gap: 1rem;
  padding: 0.8rem 2rem;
  background: #0f1427;
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: #8a8fa8;
}

.order-row {
  display: grid;
  grid-template-columns: 1fr 100px 40px;
  gap: 1rem;
  padding: 1rem 2rem;
  border-bottom: 1px solid #1e2340;
  align-items: center;
  animation: fadeIn 0.3s ease;
  transition: background 0.15s;
}
.order-row:hover { background: #1e2340; }
.order-row:last-child { border-bottom: none; }

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-6px); }
  to   { opacity: 1; transform: translateY(0); }
}

.food-name {
  font-family: 'Playfair Display', serif;
  font-size: 1.05rem;
  font-weight: 700;
  color: #f0ece4;
}

.food-qty {
  font-size: 1.1rem;
  font-weight: 600;
  color: #e8a040;
  text-align: center;
}

.btn-remove {
  background: none;
  border: none;
  color: #3d4265;
  cursor: pointer;
  font-size: 1.1rem;
  line-height: 1;
  transition: color 0.2s;
  text-align: center;
}
.btn-remove:hover { color: #d04040; }

/* EMPTY STATE */
.empty {
  text-align: center;
  padding: 3rem 2rem;
  color: #3d4265;
}
.empty-icon { font-size: 2.5rem; margin-bottom: 0.8rem; }
.empty p { font-size: 0.9rem; line-height: 1.8; }

/* TOTAL SECTION */
.total-section {
  background: #0f1427;
  border-top: 2px solid #e8a040;
  padding: 1.5rem 2rem;
  display: none;
}

.total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.4rem;
}

.total-label {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #8a8fa8;
}

.total-number {
  font-family: 'Playfair Display', serif;
  font-size: 2.2rem;
  font-weight: 900;
  color: #e8a040;
}

.total-sub {
  font-size: 0.8rem;
  color: #8a8fa8;
}

/* ACTIONS */
.actions {
  display: flex;
  gap: 0.8rem;
  padding: 1.2rem 2rem;
  border-top: 1px solid #2a2f4a;
  display: none;
}

.btn-pdf {
  flex: 1;
  background: #e8a040;
  color: #1a1a2e;
  border: none;
  border-radius: 8px;
  padding: 0.85rem;
  font-family: 'Source Sans 3', sans-serif;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  transition: background 0.2s, transform 0.1s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}
.btn-pdf:hover { background: #d4902e; transform: translateY(-1px); }

.btn-clear {
  background: none;
  border: 1.5px solid #2a2f4a;
  color: #8a8fa8;
  border-radius: 8px;
  padding: 0.85rem 1.2rem;
  font-family: 'Source Sans 3', sans-serif;
  font-size: 0.88rem;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-clear:hover { border-color: #d04040; color: #d04040; }

/* TOAST */
.toast {
  position: fixed;
  bottom: 1.5rem;
  left: 50%;
  transform: translateX(-50%) translateY(80px);
  background: #e8a040;
  color: #1a1a2e;
  padding: 0.7rem 1.5rem;
  border-radius: 50px;
  font-weight: 600;
  font-size: 0.88rem;
  opacity: 0;
  transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
  white-space: nowrap;
  z-index: 999;
}
.toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

@media (max-width: 500px) {
  h1 { font-size: 2rem; }
  .input-section { flex-direction: column; }
  .qty-field { width: 100%; }
  .btn-add { width: 100%; }
}
</style>
</head>
<body>

<h1>Food Order Tracker</h1>
<p class="subtitle">Add items · See totals · Download PDF</p>

<div class="card">

  <!-- INPUT -->
  <div class="input-section">
    <div class="field">
      <label>Food Item</label>
      <input type="text" id="foodInput" placeholder="e.g. Chicken, Rice, Bread…">
    </div>
    <div class="field qty-field">
      <label>Quantity</label>
      <input type="number" id="qtyInput" placeholder="0" min="1">
    </div>
    <button class="btn-add" onclick="addItem()">+ Add</button>
  </div>

  <!-- LIST HEADER -->
  <div class="list-header" id="listHeader" style="display:none">
    <span>Food Item</span>
    <span style="text-align:center">Quantity</span>
    <span></span>
  </div>

  <!-- ORDER ROWS -->
  <div class="order-list" id="orderList">
    <div class="empty">
      <div class="empty-icon"></div>
      <p>No food items added yet.<br>Type a food name and quantity above to start.</p>
    </div>
  </div>

  <!-- TOTAL -->
  <div class="total-section" id="totalSection">
    <div class="total-row">
      <div>
        <div class="total-label">Total Items (all combined)</div>
        <div class="total-sub" id="itemBreakdown"></div>
      </div>
      <div class="total-number" id="totalNum">0</div>
    </div>
  </div>

  <!-- ACTIONS -->
  <div class="actions" id="actionsBar">
    <button class="btn-pdf" onclick="downloadPDF()">⬇ Download as PDF</button>
    <button class="btn-clear" onclick="clearAll()">Clear All</button>
  </div>

</div>

<div class="toast" id="toast"></div>

<script>
let orders = <?php echo json_encode($_SESSION['orders']); ?>;

function post(data) {
  return fetch('', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams(data)
  }).then(r => r.json());
}

function toast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2500);
}

function render() {
  const list   = document.getElementById('orderList');
  const total  = document.getElementById('totalSection');
  const acts   = document.getElementById('actionsBar');
  const header = document.getElementById('listHeader');

  if (!orders.length) {
    list.innerHTML = `<div class="empty"><div class="empty-icon"></div><p>No food items added yet.<br>Type a food name and quantity above to start.</p></div>`;
    total.style.display = 'none';
    acts.style.display  = 'none';
    header.style.display = 'none';
    return;
  }

  header.style.display = 'grid';
  list.innerHTML = orders.map(o => `
    <div class="order-row">
      <div class="food-name">${esc(o.food)}</div>
      <div class="food-qty">${o.qty}</div>
      <button class="btn-remove" onclick="removeItem('${o.id}')" title="Remove">✕</button>
    </div>
  `).join('');

  const grand = orders.reduce((s, o) => s + parseInt(o.qty), 0);
  document.getElementById('totalNum').textContent = grand;
  document.getElementById('itemBreakdown').textContent =
    orders.map(o => `${o.food}: ${o.qty}`).join('  ·  ');

  total.style.display = 'block';
  acts.style.display  = 'flex';
}

function esc(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function addItem() {
  const food = document.getElementById('foodInput').value.trim();
  const qty  = parseInt(document.getElementById('qtyInput').value);
  if (!food) { toast('Please enter a food name'); return; }
  if (!qty || qty < 1) { toast('Please enter a quantity'); return; }

  post({ action: 'add', food, qty }).then(data => {
    orders = data;
    render();
    document.getElementById('foodInput').value = '';
    document.getElementById('qtyInput').value  = '';
    document.getElementById('foodInput').focus();
    toast('✓ ' + food + ' added');
  });
}

function removeItem(id) {
  post({ action: 'remove', id }).then(data => { orders = data; render(); toast('Item removed'); });
}

function clearAll() {
  if (!confirm('Clear all items?')) return;
  post({ action: 'clear' }).then(data => { orders = data; render(); toast('Cleared'); });
}

function downloadPDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({ unit: 'mm', format: 'a4' });

  // Background header strip
  doc.setFillColor(22, 33, 62);
  doc.rect(0, 0, 210, 40, 'F');
  doc.setFillColor(232, 160, 64);
  doc.rect(0, 38, 210, 2, 'F');

  // Title
  doc.setTextColor(240, 236, 228);
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(22);
  doc.text('FOOD ORDER SUMMARY', 14, 18);
  doc.setFont('helvetica', 'normal');
  doc.setFontSize(9);
  doc.setTextColor(138, 143, 168);
  doc.text('Generated: ' + new Date().toLocaleString(), 14, 28);

  // Table
  const rows = orders.map((o, i) => [
    String(i + 1),
    o.food,
    String(o.qty)
  ]);

  doc.autoTable({
    startY: 50,
    head: [['#', 'Food Item', 'Quantity']],
    body: rows,
    styles: {
      font: 'helvetica',
      fontSize: 11,
      cellPadding: 5,
    },
    headStyles: {
      fillColor: [22, 33, 62],
      textColor: [240, 236, 228],
      fontStyle: 'bold',
      fontSize: 9,
      halign: 'left'
    },
    columnStyles: {
      0: { cellWidth: 15, halign: 'center', textColor: [138,143,168] },
      2: { cellWidth: 35, halign: 'center', fontStyle: 'bold', textColor: [180, 100, 20] }
    },
    alternateRowStyles: { fillColor: [240, 236, 228] },
    margin: { left: 14, right: 14 }
  });

  const afterTable = doc.lastAutoTable.finalY + 10;
  const grand = orders.reduce((s, o) => s + parseInt(o.qty), 0);

  // Total box
  doc.setFillColor(22, 33, 62);
  doc.rect(14, afterTable, 182, 22, 'F');
  doc.setFillColor(232, 160, 64);
  doc.rect(14, afterTable, 4, 22, 'F');

  doc.setTextColor(138, 143, 168);
  doc.setFont('helvetica', 'normal');
  doc.setFontSize(8);
  doc.text('TOTAL QUANTITY OF ALL FOOD ITEMS', 24, afterTable + 8);

  doc.setTextColor(232, 160, 64);
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(16);
  doc.text(String(grand), 24, afterTable + 18);

  doc.setTextColor(240, 236, 228);
  doc.setFontSize(9);
  doc.text('items total across ' + orders.length + ' food type' + (orders.length !== 1 ? 's' : ''), 40, afterTable + 18);

  // Footer
  doc.setTextColor(138, 143, 168);
  doc.setFont('helvetica', 'normal');
  doc.setFontSize(7);
  doc.text('Food Order Tracker', 14, 287);
  doc.text(new Date().toLocaleDateString(), 196, 287, { align: 'right' });

  doc.save('food-order-' + Date.now() + '.pdf');
  toast('✓ PDF downloaded!');
}

// Press Enter to add
['foodInput','qtyInput'].forEach(id => {
  document.getElementById(id).addEventListener('keydown', e => {
    if (e.key === 'Enter') addItem();
  });
});

render();
</script>
</body>
</html>
