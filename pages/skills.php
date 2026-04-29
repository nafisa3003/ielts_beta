<?php
require_once '../includes/config.php';
$user = auth_required();
$active_page='skills'; $sidebar_user=$user; $page_title='Skill Modules';
$extra_css=['dashboard.css']; $topbar_links='dashboard';
require_once '../includes/header.php'; require_once '../includes/sidebar.php';
$skills = query('SELECT * FROM skill_progress WHERE user_id=?',[$user['id']]);
$sm=[];foreach($skills as $s)$sm[$s['skill']]=$s;
?>
<div class="app-layout">
<main class="app-main">
  <div class="pg-header"><div><div class="pg-title">📚 Skill Modules</div><div class="pg-sub">Practice each IELTS skill individually with targeted exercises</div></div></div>
  <div class="grid2" style="gap:18px;">
    <?php
    $skills_info=[
      'listening'=>[
        'icon'=>'🎧','bg'=>'#eef2ff','color'=>'var(--teal)',
        'desc'=>'Focus on synonyms. The recording often uses different words than the question paper.',
        'tips'=>['4 Sections','40 Questions','30 Minutes'],
        'sessions'=>24
      ],
      'reading'  =>[
        'icon'=>'📖','bg'=>'#ecfeff','color'=>'#06b6d4',
        'desc'=>'Master "Skimming" and "Scanning". Don\'t read every word; look for keywords first.',
        'tips'=>['3 Passages','60 Minutes','2700 Words'],
        'sessions'=>18
      ],
      'writing'  =>[
        'icon'=>'✍️','bg'=>'#f0fdf4','color'=>'var(--green)',
        'desc'=>'Structure is key. Use a clear introduction, 2 body paragraphs, and a firm conclusion.',
        'tips'=>['Task 1 & 2','60 Minutes','Formal Tone'],
        'sessions'=>11
      ],
      'speaking' =>[
        'icon'=>'🎤','bg'=>'#fffbeb','color'=>'var(--amber)',
        'desc'=>'Fluency over perfection. It is better to speak smoothly with small errors than to pause constantly.',
        'tips'=>['3 Parts','11-14 Mins','Face-to-Face'],
        'sessions'=>9
      ],
    ];
    foreach($skills_info as $skill=>$info):
      $score=floatval($sm[$skill]['band_score']??0);$pct=($score/9)*100;
    ?>
    <div class="skill-card">
      <div class="sk-icon" style="background:<?=$info['bg']?>"><?=$info['icon']?></div>
      <div style="font-family:var(--font);font-weight:700;font-size:18px;margin-bottom:6px;"><?=ucfirst($skill)?></div>
      <div style="font-size:13px;color:var(--text2);margin-bottom:12px;line-height:1.5;"><?=$info['desc']?></div>
      
      <div style="display:flex; gap:8px; margin-bottom:16px;">
        <?php foreach($info['tips'] as $tip): ?>
          <span style="font-size:10px; font-weight:700; background:rgba(0,0,0,0.05); padding:4px 8px; border-radius:6px; color:var(--text3);"><?= $tip ?></span>
        <?php endforeach; ?>
      </div>

      <div class="prog-bar"><div class="prog-fill" style="width:<?=$pct?>%;background:<?=$info['color']?>;"></div></div>
      <div style="font-size:12px;color:var(--text3);margin-bottom:14px;">Current Band: <?=number_format($score,1)?></div>
      <a href="#" onclick="Toast.show('Full practice engine coming in v1.1! Check AI Tutor for interactive prep.'); return false;" class="btn btn-primary btn-sm" style="width:100%; text-align:center;">Start Practice →</a>
    </div>
    <?php endforeach; ?>
  </div>
</main>
</div>
<?php require_once '../includes/footer.php'; ?>