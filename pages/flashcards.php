<?php
require_once '../includes/db_connection.php';
$user = auth_required();
$content = new IELTSContent();

$global_cards = $content->getGlobalFlashcards('all'); // Fetch all 217+ words
$user_cards   = $content->getUserFlashcards($user['id']);

$page_title = 'Vocabulary Word Bank';
$active_page = 'flashcards';
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="app-layout">
<main class="app-main">
  <div class="pg-header">
    <div>
      <div class="pg-title">🃏 IELTS Word Bank</div>
      <div class="pg-sub">Study <?= count($global_cards) ?> official academic words and manage your own list</div>
    </div>
    <div class="tabs" id="fc-tabs" style="margin:0;">
      <div class="tab active" onclick="switchMainMode('study', this)">Study & Word Bank</div>
      <div class="tab" onclick="switchMainMode('manage', this)">My Custom Cards</div>
    </div>
  </div>

  <style>
    /* 3D CARD STYLES */
    .fc-container { max-width: 600px; margin: 30px auto; perspective: 1000px; }
    .fc-card { width: 100%; aspect-ratio: 16 / 9; cursor: pointer; position: relative; transform-style: preserve-3d; transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
    .fc-card.flipped { transform: rotateY(180deg); }
    .fc-side { position: absolute; inset: 0; backface-visibility: hidden; border-radius: 20px; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 30px; text-align: center; border: 1px solid var(--border); box-shadow: var(--shadow-lg); }
    .fc-front { background: var(--white); }
    .fc-back { background: var(--teal); color: #fff; transform: rotateY(180deg); }
    .fc-word { font-size: 42px; font-weight: 800; color: var(--text); }
    .fc-def  { font-size: 20px; font-weight: 700; margin-bottom: 15px; line-height: 1.3; }
    .fc-ex   { font-size: 14px; opacity: 0.9; font-style: italic; }
    
    /* WORD BANK STYLES */
    .bank-header { display: flex; justify-content: space-between; align-items: center; margin-top: 40px; padding-bottom: 12px; border-bottom: 2px solid var(--border); }
    .difficulty-tabs { display: flex; gap: 8px; }
    .diff-tab { padding: 6px 14px; border-radius: 99px; font-size: 12px; font-weight: 700; cursor: pointer; background: var(--gray1); border: 1px solid var(--border); color: var(--text3); text-transform: uppercase; transition: all 0.2s; }
    .diff-tab.active { background: var(--teal); color: #fff; border-color: var(--teal); }
    
    .word-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 12px; margin-top: 20px; }
    .word-item { background: var(--white); border: 1px solid var(--border); padding: 12px 16px; border-radius: 12px; font-weight: 600; font-size: 14px; color: var(--text); cursor: pointer; transition: all 0.2s; display: flex; justify-content: space-between; align-items: center; }
    .word-item:hover { border-color: var(--teal); color: var(--teal); transform: translateY(-2px); }
    .word-item .diff-dot { width: 8px; height: 8px; border-radius: 50%; }
    .dot-easy   { background: #10b981; }
    .dot-medium { background: #f59e0b; }
    .dot-hard   { background: #ef4444; }

    /* CRUD STYLES */
    .crud-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 24px; }
    .crud-card { background: var(--white); border: 1px solid var(--border); border-radius: 16px; padding: 20px; }
    .add-form { background: var(--gray1); border: 1px solid var(--border); border-radius: 16px; padding: 24px; margin-bottom: 32px; max-width: 600px; }
    .form-input { width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; background: var(--white); color: var(--text); margin-top: 4px; }
  </style>

  <!-- MODE: STUDY & WORD BANK -->
  <div id="mode-study">
    <!-- 3D STUDY CARD -->
    <div class="fc-container">
      <div class="fc-card" id="fc-card" onclick="flipCard()">
        <div class="fc-side fc-front">
          <span id="card-diff-label" style="font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:10px;">---</span>
          <div class="fc-word" id="fc-word">---</div>
          <div style="margin-top:20px; font-size:11px; color:var(--text3);">Click to see definition</div>
        </div>
        <div class="fc-side fc-back">
          <div class="fc-def" id="fc-def">---</div>
          <div style="width:30px; height:2px; background:rgba(255,255,255,0.3); margin-bottom:15px;"></div>
          <div class="fc-ex" id="fc-ex">---</div>
        </div>
      </div>
      <div style="display:flex; justify-content:center; gap:16px; margin-top:24px;">
        <button class="btn btn-ghost" onclick="prevCard()">←</button>
        <button class="btn btn-primary" onclick="flipCard()">Flip</button>
        <button class="btn btn-primary" onclick="nextCard()">→</button>
      </div>
    </div>

    <!-- WORD BANK -->
    <div class="bank-header">
      <h3 style="font-weight:800;">📚 Global Word Bank</h3>
      <div style="display:flex; gap:12px; align-items:center;">
        <input type="text" id="fc-search" class="fi" style="width:200px; padding:6px 12px; height:34px;" placeholder="Search words..." oninput="handleSearch()">
        <div class="difficulty-tabs">
          <div class="diff-tab active" onclick="filterByDiff('all', this)">All</div>
          <div class="diff-tab" onclick="filterByDiff('easy', this)">Easy</div>
          <div class="diff-tab" onclick="filterByDiff('medium', this)">Medium</div>
          <div class="diff-tab" onclick="filterByDiff('hard', this)">Hard</div>
        </div>
      </div>
    </div>

    <div class="word-grid" id="global-word-grid">
      <!-- Dynamically rendered -->
    </div>

    <div id="pagination-controls" style="margin-top:24px; display:flex; justify-content:center; gap:8px;"></div>
  </div>

  <!-- MODE: MANAGE (CRUD) -->
  <div id="mode-manage" style="display:none;">
    <div class="add-form">
      <h3 style="font-weight:800; margin-bottom:16px;">Create Custom Card</h3>
      <div style="margin-bottom:12px;">
        <label style="font-size:11px; font-weight:700; color:var(--text3); text-transform:uppercase;">Word</label>
        <input type="text" id="new-word" class="form-input" placeholder="e.g. Ubiquitous">
      </div>
      <div style="margin-bottom:12px;">
        <label style="font-size:11px; font-weight:700; color:var(--text3); text-transform:uppercase;">Definition</label>
        <textarea id="new-def" class="form-input" style="height:60px;" placeholder="Definition..."></textarea>
      </div>
      <button class="btn btn-primary" onclick="saveCard()">Save Flashcard</button>
    </div>

    <h3 style="font-weight:800;">My Personal Words</h3>
    <div class="crud-grid">
      <?php foreach($user_cards as $c): ?>
      <div class="crud-card" id="card-<?= $c['id'] ?>">
        <div style="font-weight:800; font-size:18px; color:var(--text);"><?= htmlspecialchars($c['word']) ?></div>
        <div style="font-size:14px; color:var(--text2); margin-top:8px;"><?= htmlspecialchars($c['definition']) ?></div>
        <div style="margin-top:16px; display:flex; gap:8px;">
          <button class="btn btn-ghost btn-sm" onclick="deleteCard(<?= $c['id'] ?>)" style="color:#ef4444;">Delete</button>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if(empty($user_cards)): ?>
        <p style="color:var(--text3); font-size:14px;">No personal cards yet. Add your first one above!</p>
      <?php endif; ?>
    </div>
  </div>

</main>
</div>

<script>
const allCards = <?= json_encode($global_cards) ?>;
let filteredCards = [...allCards];
let gridCards = [...allCards];
let currentIdx = 0;
let isFlipped = false;

// Pagination state
let currentPage = 1;
const itemsPerPage = 48;
let currentDiff = 'all';
let searchQuery = '';

function switchMainMode(mode, el) {
  document.querySelectorAll('#fc-tabs .tab').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('mode-study').style.display = mode === 'study' ? 'block' : 'none';
  document.getElementById('mode-manage').style.display = mode === 'manage' ? 'block' : 'none';
}

function updateCardUI() {
  const c = filteredCards[currentIdx];
  if(!c) return;
  
  isFlipped = false;
  document.getElementById('fc-card').classList.remove('flipped');
  
  setTimeout(() => {
    document.getElementById('fc-word').textContent = c.word;
    document.getElementById('fc-def').textContent = c.definition;
    document.getElementById('fc-ex').textContent = c.example || 'No example provided.';
    const label = document.getElementById('card-diff-label');
    label.textContent = c.difficulty;
    label.style.color = c.difficulty === 'easy' ? '#10b981' : (c.difficulty === 'medium' ? '#f59e0b' : '#ef4444');
  }, 150);
}

function flipCard() { isFlipped = !isFlipped; document.getElementById('fc-card').classList.toggle('flipped', isFlipped); }
function nextCard() { if(currentIdx < filteredCards.length - 1) { currentIdx++; updateCardUI(); } }
function prevCard() { if(currentIdx > 0) { currentIdx--; updateCardUI(); } }
function jumpTo(idx) { 
  const word = allCards[idx].word;
  const findInFiltered = filteredCards.findIndex(c => c.word === word);
  if(findInFiltered !== -1) {
    currentIdx = findInFiltered;
    updateCardUI();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
}

function handleSearch() {
  searchQuery = document.getElementById('fc-search').value.toLowerCase();
  currentPage = 1;
  applyFilters();
}

function filterByDiff(diff, el) {
  if (el) {
    document.querySelectorAll('.diff-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
  }
  currentDiff = diff;
  currentPage = 1;
  applyFilters();
}

function applyFilters() {
  // 1. Filter for the 3D Study Card (only difficulty)
  filteredCards = currentDiff === 'all' ? [...allCards] : allCards.filter(c => c.difficulty === currentDiff);
  currentIdx = 0;
  updateCardUI();

  // 2. Filter for the Grid (difficulty + search)
  gridCards = allCards.filter(c => {
    const matchesDiff = currentDiff === 'all' || c.difficulty === currentDiff;
    const matchesSearch = c.word.toLowerCase().includes(searchQuery);
    return matchesDiff && matchesSearch;
  });

  renderWordGrid();
}

function renderWordGrid() {
  const grid = document.getElementById('global-word-grid');
  const pagin = document.getElementById('pagination-controls');
  if (!grid || !pagin) return;

  grid.innerHTML = '';
  pagin.innerHTML = '';

  const totalPages = Math.ceil(gridCards.length / itemsPerPage);
  const start = (currentPage - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  const pageItems = gridCards.slice(start, end);

  if (pageItems.length === 0) {
    grid.innerHTML = '<div style="grid-column: 1/-1; padding: 40px; text-align: center; color: var(--text3);">No words found matching your search.</div>';
    return;
  }

  pageItems.forEach(c => {
    const originalIdx = allCards.findIndex(orig => orig.word === c.word);
    const div = document.createElement('div');
    div.className = 'word-item';
    div.dataset.diff = c.difficulty;
    div.onclick = () => jumpTo(originalIdx);
    div.innerHTML = `
      ${c.word}
      <span class="diff-dot dot-${c.difficulty}"></span>
    `;
    grid.appendChild(div);
  });

  if (totalPages > 1) {
    for (let i = 1; i <= totalPages; i++) {
      const btn = document.createElement('button');
      btn.className = `btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-secondary'}`;
      btn.style.padding = '6px 12px';
      btn.textContent = i;
      btn.onclick = () => { 
        currentPage = i; 
        renderWordGrid(); 
        const header = document.querySelector('.bank-header');
        if(header) window.scrollTo({ top: header.offsetTop - 100, behavior: 'smooth' });
      };
      pagin.appendChild(btn);
    }
  }
}

// CRUD
async function saveCard() {
  const word = document.getElementById('new-word').value;
  const def = document.getElementById('new-def').value;
  if(!word || !def) return Toast.show('Word and Definition required', 'error');
  const res = await fetch('../api/flashcards.php?action=create', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ word, definition: def })
  });
  const data = await res.json();
  if(data.success) {
    Toast.show('Card created!');
    location.reload();
  }
}

async function deleteCard(id) {
  if(!confirm('Delete this card?')) return;
  const res = await fetch('../api/flashcards.php?action=delete', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ card_id: id })
  });
  const data = await res.json();
  if(data.success) { document.getElementById('card-'+id).remove(); Toast.show('Card deleted'); }
}

// Init
applyFilters();
</script>

<?php require_once '../includes/footer.php'; ?>
