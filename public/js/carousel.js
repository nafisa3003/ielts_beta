/* public/js/carousel.js — 3D card carousel for landing page */

const CARDS = [
  { icon:'🎧', title:'Listening Module',    desc:'Adaptive exercises with real accent varieties and section tracking.',   badge:'free', bl:'Free' },
  { icon:'📖', title:'Reading Module',      desc:'Cambridge-style passages: T/F/NG, matching, gap-fill.',               badge:'free', bl:'Free' },
  { icon:'✍️', title:'AI Essay Grading',    desc:'Instant band score feedback on Task 1 & 2 using IELTS rubrics.',      badge:'pro',  bl:'Engage+' },
  { icon:'🎤', title:'Speaking Coach',      desc:'AI pronunciation scoring across all 3 speaking parts.',                badge:'pro',  bl:'Engage+' },
  { icon:'🃏', title:'Vocabulary Deck',     desc:'3,000+ IELTS words with spaced repetition for deep recall.',          badge:'pro',  bl:'Engage+' },
  { icon:'🤖', title:'LangGraph AI Tutor',  desc:'24/7 contextual assistant powered by Groq + Tavily live search.',     badge:'ai',   bl:'Advanced' },
  { icon:'📚', title:'Cambridge Library',   desc:'Official Cambridge 15–18 books, audio files and study guides.',       badge:'ai',   bl:'Advanced' },
  { icon:'📝', title:'Full Mock Tests',     desc:'Timed 170-min tests with automatic band score calculation.',          badge:'pro',  bl:'Engage+' },
];

let currentIndex = 0;
let autoTimer = null;

function buildCarousel() {
  const track   = document.getElementById('carousel-track');
  const dotsEl  = document.getElementById('carousel-dots');
  if (!track || !dotsEl) return;

  const N     = CARDS.length;
  const step  = 360 / N;
  const radius = Math.round(N * 54);

  track.innerHTML = '';
  dotsEl.innerHTML = '';

  CARDS.forEach((c, i) => {
    const angle = step * i;
    const card  = document.createElement('div');
    card.className = 'c-card' + (i === 0 ? ' center-card' : '');
    card.style.transform = `rotateY(${angle}deg) translateZ(${radius}px)`;
    card.innerHTML = `
      <div class="c-icon">${c.icon}</div>
      <div class="c-title">${c.title}</div>
      <div class="c-desc">${c.desc}</div>
      <span class="c-badge ${c.badge}">${c.bl}</span>
    `;
    card.addEventListener('click', () => spinTo(i));
    track.appendChild(card);

    const dot = document.createElement('div');
    dot.className = 'dot' + (i === 0 ? ' active' : '');
    dot.addEventListener('click', () => spinTo(i));
    dotsEl.appendChild(dot);
  });

  startAuto();
}

function updateCarousel() {
  const N    = CARDS.length;
  const step = 360 / N;
  const angle = -currentIndex * step;
  const track = document.getElementById('carousel-track');
  if (track) track.style.transform = `rotateY(${angle}deg)`;
  document.querySelectorAll('.c-card').forEach((c, i) => c.classList.toggle('center-card', i === currentIndex));
  document.querySelectorAll('.dot').forEach((d, i) => d.classList.toggle('active', i === currentIndex));
}

function spinCarousel(dir) {
  currentIndex = (currentIndex + dir + CARDS.length) % CARDS.length;
  updateCarousel();
  resetAuto();
}

function spinTo(i) {
  currentIndex = i;
  updateCarousel();
  resetAuto();
}

function startAuto() {
  autoTimer = setInterval(() => spinCarousel(1), 3500);
}
function resetAuto() {
  clearInterval(autoTimer);
  startAuto();
}

// Keyboard navigation
document.addEventListener('keydown', e => {
  if (e.key === 'ArrowLeft')  spinCarousel(-1);
  if (e.key === 'ArrowRight') spinCarousel(1);
});

document.addEventListener('DOMContentLoaded', buildCarousel);
