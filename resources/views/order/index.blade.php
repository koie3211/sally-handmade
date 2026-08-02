<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $group->shop_name ? $group->shop_name.'｜' : '' }}勤美揪手搖</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Noto+Serif+TC:wght@600;900&family=Noto+Sans+TC:wght@400;500;700&display=swap" rel="stylesheet">
<style>
  :root{
    --bg:#f8f5ee;
    --ink:#2b2318;
    --surface:#ffffff;
    --surface-2:#f1ebdc;
    --line: rgba(43,35,24,0.11);
    --cream:#f2e6ce;
    --cream-dim:#8c7f68;
    --amber:#c9863a;
    --amber-dim:#d9ac74;
    --jade:#4f7a5c;
    --clay:#b85c38;
    --grey:#9a8f7c;
    --shadow-sm: 0 1px 2px rgba(43,35,24,0.05), 0 1px 1px rgba(43,35,24,0.04);
    --shadow-md: 0 6px 20px rgba(43,35,24,0.08);
  }
  *{box-sizing:border-box;}
  html,body{margin:0;padding:0;}
  body{
    background:
      radial-gradient(1200px 600px at 15% -10%, rgba(201,134,58,0.06), transparent 60%),
      radial-gradient(900px 500px at 100% 0%, rgba(79,122,92,0.06), transparent 55%),
      var(--bg);
    color:var(--ink);
    font-family:'Noto Sans TC', sans-serif;
    -webkit-font-smoothing:antialiased;
    min-height:100vh;
    padding-bottom:60px;
  }
  ::selection{ background:var(--amber); color:#fff; }
  button{font-family:inherit;}
  input,textarea{ font-family:inherit; }
  :focus-visible{ outline:2px solid var(--amber); outline-offset:2px; }

  header.top{
    position:sticky; top:0; z-index:40;
    display:flex; align-items:center; justify-content:space-between;
    padding:14px 20px; gap:10px; flex-wrap:wrap;
    background:rgba(248,245,238,0.85);
    backdrop-filter: blur(10px);
    border-bottom:1px solid var(--line);
  }
  .brand{ display:flex; align-items:center; gap:10px; }
  .brand-mark{
    width:34px; height:34px; border-radius:50%; flex-shrink:0;
    background:#fff2e2; border:1px solid rgba(201,134,58,0.25);
    display:flex; align-items:center; justify-content:center;
  }
  .brand-name{ font-family:'Noto Serif TC',serif; font-weight:900; font-size:19px; letter-spacing:0.04em; }
  .brand-sub{ font-size:11px; color:var(--cream-dim); letter-spacing:0.08em; }

  .header-actions{ display:flex; gap:8px; }
  .cart-btn{
    display:flex; align-items:center; gap:8px;
    background:var(--surface-2); border:1px solid var(--line);
    color:var(--ink); padding:9px 14px; border-radius:999px;
    cursor:pointer; font-size:14px; font-weight:500;
    transition:border-color .2s, transform .15s;
  }
  .cart-btn:hover{ border-color:var(--amber); }
  .cart-btn:active{ transform:scale(0.97); }
  .cart-btn.done{ background:var(--ink); border-color:var(--ink); color:#fff; }
  .cart-btn.done:hover{ border-color:var(--jade); }
  .cart-count{
    background:var(--amber); color:var(--ink); font-weight:700; font-size:12px;
    min-width:19px; height:19px; border-radius:999px;
    display:flex; align-items:center; justify-content:center; padding:0 4px;
  }

  .hero{
    position:relative; overflow:hidden;
    padding:52px 20px 36px;
    border-bottom:1px solid var(--line);
    display:flex; align-items:flex-end; gap:28px;
  }
  .hero-sign{
    writing-mode: vertical-rl;
    font-family:'Noto Serif TC', serif; font-weight:600;
    font-size:13px; letter-spacing:0.5em;
    color:var(--cream-dim);
    border-right:1px solid var(--line);
    padding-right:16px; height:160px;
    display:flex; align-items:center;
    flex-shrink:0;
  }
  .hero-text h1{
    font-family:'Noto Serif TC',serif; font-weight:900;
    font-size:clamp(30px, 5.4vw, 52px);
    line-height:1.1; margin:0 0 12px;
    letter-spacing:0.02em;
  }
  .shop-name-line{
    display:inline-flex; align-items:center; gap:7px;
    font-size:13px; font-weight:700; color:var(--amber);
    border:1px solid var(--amber-dim); background:rgba(216,155,74,0.08);
    padding:5px 13px; border-radius:999px; margin-bottom:14px;
  }
  .hero-text h1 em{ font-style:normal; color:var(--amber); }
  .hero-text p{ margin:0; color:var(--cream-dim); font-size:14.5px; max-width:44ch; line-height:1.7; }
  .hero-stats{ margin-top:16px; display:flex; flex-wrap:wrap; gap:10px; }
  .hero-stat{
    font-size:12.5px; color:var(--cream-dim);
    border:1px dashed var(--line); padding:7px 12px; border-radius:999px;
  }
  .hero-stat b{ font-weight:700; }
  .hero-stat.ordered b{ color:var(--jade); }
  .hero-stat.pass b{ color:var(--grey); }
  .hero-stat.unset b{ color:var(--amber); }

  main{ max-width:1080px; margin:0 auto; padding:34px 20px 0; }
  .section-head{ display:flex; align-items:baseline; justify-content:space-between; margin-bottom:18px; flex-wrap:wrap; gap:8px;}
  .section-head h2{ font-family:'Noto Serif TC',serif; font-weight:900; font-size:22px; margin:0; }
  .section-head span{ font-size:12.5px; color:var(--cream-dim); }

  .roster-grid{
    display:grid; grid-template-columns:repeat(auto-fill, minmax(230px,1fr));
    gap:14px;
  }
  .person-card{
    background:var(--surface); border:1px solid var(--line); border-radius:18px;
    padding:14px; cursor:pointer; text-align:left;
    display:flex; align-items:center; gap:12px;
    box-shadow:var(--shadow-sm);
    transition:transform .16s ease, border-color .16s ease, background .16s ease, box-shadow .16s ease;
  }
  .person-card:hover{ transform:translateY(-2px); border-color:var(--amber-dim); box-shadow:var(--shadow-md); }
  .person-card:active{ transform:translateY(0) scale(0.99); }
  .avatar{
    width:46px; height:46px; border-radius:50%; flex-shrink:0;
    background:var(--surface-2);
    display:flex; align-items:center; justify-content:center;
    font-family:'Noto Serif TC',serif; font-weight:900; font-size:12.5px; color:var(--ink);
    letter-spacing:-0.5px; line-height:1;
  }
  .avatar.wide{
    width:auto; min-width:46px; height:32px; border-radius:16px; padding:0 12px;
    font-family:'Noto Sans TC', sans-serif; font-size:12px; font-weight:700;
    letter-spacing:0; white-space:nowrap;
  }
  .avatar.ordered{ background:var(--jade); color:#fff; }
  .avatar.unset{ background:transparent; border:1.5px dashed var(--amber); color:var(--amber); }
  .avatar.pass{ background:var(--surface-2); border:1px solid var(--line); color:var(--cream-dim); }
  .p-info{ min-width:0; flex:1; }
  .p-status{ font-size:12px; color:var(--cream-dim); line-height:1.5; display:flex; align-items:center; flex-wrap:wrap; gap:6px; }
  .p-status.ordered-text{ color:var(--cream-dim); }
  .p-status.pass-text{ color:var(--grey); }
  .p-status.unset-text{ color:var(--amber); font-weight:600; }
  .copy-btn{
    background:var(--surface-2); border:1px solid var(--line); color:var(--amber);
    font-size:10.5px; padding:2px 9px; border-radius:999px; cursor:pointer; font-weight:700;
    flex-shrink:0;
  }
  .copy-btn:hover{ border-color:var(--amber); background:var(--ink); }

  .overlay{
    position:fixed; inset:0; background:rgba(10,7,4,0.6);
    backdrop-filter: blur(2px);
    display:none; align-items:flex-end; justify-content:center;
    z-index:60;
  }
  .overlay.open{ display:flex; }
  @media (min-width:760px){ .overlay{ align-items:center; } }

  .panel{
    background:var(--surface); width:100%; max-width:640px;
    border-radius:24px 24px 0 0;
    max-height:92vh; overflow-y:auto;
    border:1px solid var(--line); border-bottom:none;
    box-shadow:var(--shadow-md);
    animation:slideUp .28s ease; position:relative;
  }
  @media (min-width:760px){ .panel{ border-radius:24px; border-bottom:1px solid var(--line); max-height:88vh; } }
  @keyframes slideUp{ from{ transform:translateY(24px); opacity:0; } to{ transform:translateY(0); opacity:1; } }

  .panel-head{ display:flex; gap:18px; padding:22px 22px 6px; }
  .cup-wrap{ flex-shrink:0; width:110px; }
  .panel-title h3{ font-family:'Noto Serif TC',serif; font-size:21px; margin:0 0 6px; font-weight:900; }
  .panel-title p{ margin:0; font-size:13px; color:var(--cream-dim); line-height:1.6; }
  .panel-close{
    position:absolute; top:18px; right:18px;
    width:32px; height:32px; border-radius:50%; border:1px solid var(--line);
    background:var(--surface-2); color:var(--ink); cursor:pointer; font-size:16px;
    display:flex; align-items:center; justify-content:center;
  }
  .panel-body{ padding:14px 22px 22px; }

  .pass-toggle{
    display:flex; align-items:center; justify-content:space-between;
    background:var(--surface-2); border:1px solid var(--line); border-radius:14px;
    padding:12px 16px; margin-bottom:18px;
  }
  .pass-toggle-label{ font-size:13.5px; }
  .pass-toggle-label span{ display:block; font-size:11.5px; color:var(--cream-dim); margin-top:2px; }
  .switch{
    width:44px; height:26px; border-radius:999px; background:var(--ink); border:1px solid var(--line);
    position:relative; cursor:pointer; flex-shrink:0;
  }
  .switch .knob{
    width:20px; height:20px; border-radius:50%; background:var(--surface);
    position:absolute; top:2px; left:2px; transition:transform .18s, background .18s;
  }
  .switch.on{ background:var(--clay); }
  .switch.on .knob{ transform:translateX(18px); background:var(--cream); }

  .opt-group{ margin-bottom:20px; }
  .opt-label{ font-size:12.5px; color:var(--cream-dim); margin-bottom:9px; letter-spacing:0.04em; }
  .opt-row{ display:flex; flex-wrap:wrap; gap:8px; }
  .opt-pill{
    border:1px solid var(--line); background:var(--surface-2); color:var(--ink);
    padding:8px 14px; border-radius:999px; font-size:13.5px; cursor:pointer;
    transition:all .15s;
  }
  .opt-pill:hover{ border-color:var(--amber-dim); }
  .opt-pill.active{ background:var(--amber); color:var(--ink); border-color:var(--amber); font-weight:700; }
  .opt-pill.small{ font-size:11px; padding:5px 10px; }
  .fade-section{ transition:opacity .18s; }
  .custom-input{ margin-top:8px; font-size:12.5px; padding:8px 12px; }
  .fade-section.dim{ opacity:0.35; pointer-events:none; }

  .panel-footer{ display:flex; align-items:center; justify-content:flex-end; gap:12px; margin-top:8px; flex-wrap:wrap; }
  .btn-ghost{
    background:none; border:1px solid var(--line); color:var(--cream-dim); font-weight:500;
    padding:12px 18px; border-radius:999px; cursor:pointer; font-size:13.5px;
  }
  .btn-ghost:hover{ border-color:var(--amber); color:var(--ink); }
  .btn-primary{
    background:var(--jade); color:#0f1a13; border:none; font-weight:700;
    padding:13px 26px; border-radius:999px; cursor:pointer; font-size:14.5px;
    transition:transform .15s, filter .15s;
  }
  .btn-primary:hover{ filter:brightness(1.08); }
  .btn-primary:active{ transform:scale(0.97); }

  textarea, .text-input{
    width:100%; background:var(--surface-2); border:1px solid var(--line); color:var(--ink);
    border-radius:12px; padding:10px 13px; font-size:13.5px; line-height:1.6; resize:vertical;
  }
  textarea::placeholder, .text-input::placeholder{ color:var(--cream-dim); opacity:0.7; }
  .inline-add{ display:flex; gap:8px; margin-top:8px; }
  .inline-add .text-input{ flex:1; }
  .paste-zone{
    border:1.5px dashed var(--line); border-radius:14px;
    padding:22px 14px; text-align:center; font-size:12.5px; color:var(--cream-dim);
    cursor:text; transition:border-color .15s, color .15s;
  }
  .paste-zone:hover{ border-color:var(--amber-dim); }
  .paste-zone:focus{ outline:none; border-color:var(--amber); color:var(--ink); }

  /* Summary / settings side panel */
  .cart-overlay{ position:fixed; inset:0; background:rgba(10,7,4,0.6); display:none; z-index:70; justify-content:flex-end; }
  .cart-overlay.open{ display:flex; }
  .cart-panel{ width:100%; max-width:440px; background:var(--surface); height:100%; border-left:1px solid var(--line); box-shadow:var(--shadow-md); display:flex; flex-direction:column; animation:slideLeft .25s ease; overflow-y:auto; }
  @keyframes slideLeft{ from{ transform:translateX(24px); opacity:0.5; } to{ transform:translateX(0); opacity:1; } }
  .cart-head{ padding:20px 20px 14px; border-bottom:1px solid var(--line); display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; background:var(--surface); z-index:2; }
  .cart-head h3{ font-family:'Noto Serif TC',serif; margin:0; font-size:19px; font-weight:900; }
  .cart-head p{ margin:4px 0 0; font-size:12.5px; color:var(--cream-dim); }
  .cart-items{ flex:1; padding:14px 20px; }
  .sum-block{ margin-bottom:22px; }
  .sum-title{ font-size:12px; color:var(--cream-dim); letter-spacing:0.05em; margin-bottom:10px; }
  .sum-row{ padding:10px 0; border-bottom:1px solid var(--line); font-size:13.5px; }
  .sum-row-top{ display:flex; align-items:center; justify-content:space-between; gap:8px; }
  .sum-row .qty{ background:var(--surface-2); color:var(--amber); font-weight:700; font-size:12px; padding:2px 9px; border-radius:999px; flex-shrink:0; }
  .sum-row-names{ font-size:12px; color:var(--cream-dim); margin-top:4px; line-height:1.5; }
  .history-card{ border:1px solid var(--line); border-radius:12px; margin-bottom:8px; overflow:hidden; }
  .history-head{
    width:100%; display:flex; justify-content:space-between; align-items:flex-start;
    padding:10px 12px; background:var(--surface-2); border:none; cursor:pointer;
    font-size:12.5px; color:var(--ink); text-align:left; font-family:inherit; gap:10px;
  }
  .history-head > span:first-child{ display:flex; flex-direction:column; align-items:flex-start; gap:2px; }
  .history-shop{ font-size:11px; color:var(--cream-dim); font-weight:500; }
  .history-head .qty{ background:none; color:var(--cream-dim); font-weight:500; font-size:11.5px; padding:0; flex-shrink:0; white-space:nowrap; }
  .history-head:hover{ background:var(--line); }
  .history-detail{ padding:2px 12px 12px; }
  .history-detail .sum-row{ padding:7px 0; font-size:12.5px; }
  .history-restore-btn{ width:100%; margin-top:8px; font-size:12.5px; padding:9px; }
  .chip{ display:inline-flex; align-items:center; gap:6px; background:var(--surface-2); border:1px solid var(--line); padding:6px 11px; border-radius:999px; font-size:12.5px; margin:4px 6px 0 0; }
  .chip.clickable{ cursor:pointer; }
  .chip.clickable:hover{ border-color:var(--amber); }
  .chip .x{ opacity:0.6; cursor:pointer; }
  .chip .x:hover{ opacity:1; color:var(--clay); }
  .cart-foot{ padding:16px 20px 22px; border-top:1px solid var(--line); }
  .btn-checkout{ width:100%; background:var(--amber); color:var(--ink); border:none; font-weight:700; padding:14px; border-radius:999px; cursor:pointer; font-size:14.5px; }

  .toast{
    position:fixed; bottom:24px; left:50%; transform:translate(-50%, 20px);
    background:var(--cream); color:var(--ink); padding:11px 20px; border-radius:999px;
    font-size:13.5px; font-weight:600; opacity:0; pointer-events:none; transition:all .25s;
    z-index:100; max-width:90vw; text-align:center;
  }
  .toast.show{ opacity:1; transform:translate(-50%, 0); }

  .menu-inline{
    position:sticky; top:-1px; z-index:5;
    background:var(--surface); border:1px solid var(--line); border-radius:14px;
    box-shadow:var(--shadow-sm);
    overflow:hidden; margin-bottom:18px;
  }
  .menu-inline-head{
    display:flex; align-items:center; justify-content:space-between; gap:8px;
    padding:7px 10px; font-size:11px; color:var(--cream-dim);
    background:var(--surface-2); border-bottom:1px solid var(--line);
  }
  .mini-zoom-btn{
    background:var(--surface); border:1px solid var(--line); color:var(--ink);
    border-radius:8px; padding:3px 9px; font-size:11px; cursor:pointer; font-weight:700;
  }
  .mini-zoom-btn:hover{ border-color:var(--amber); }
  .menu-inline-viewport{
    position:relative; width:100%; height:min(46vh, 400px);
    overflow:hidden; background:var(--ink); touch-action:none; cursor:grab;
  }
  .menu-inline-viewport.expanded{ height:min(72vh, 620px); }
  .menu-inline-viewport.dragging{ cursor:grabbing; }
  .menu-inline-content{ position:absolute; top:0; left:0; transform-origin:0 0; will-change:transform; }
  .menu-inline-content img{ display:block; max-width:none; user-select:none; -webkit-user-drag:none; }

  .zoom-btn{ padding:8px 13px; font-size:14px; font-weight:700; line-height:1; }
  .zoom-viewport{
    position:relative; width:100%; height:min(62vh, 520px);
    overflow:hidden; border-radius:14px; border:1px solid var(--line);
    background:var(--ink); touch-action:none; cursor:grab;
  }
  .zoom-viewport.dragging{ cursor:grabbing; }
  .zoom-content{ position:absolute; top:0; left:0; transform-origin:0 0; will-change:transform; }
  .zoom-content img{ display:block; max-width:none; user-select:none; -webkit-user-drag:none; }

  footer{ text-align:center; color:var(--cream-dim); font-size:12px; padding:36px 20px 10px; line-height:1.8; }
</style>
</head>
<body>

<header class="top">
  <div class="brand">
    <div class="brand-mark">
      <svg viewBox="0 0 40 40" width="24" height="24" xmlns="http://www.w3.org/2000/svg">
        <path d="M9 14 L14 4 L18 13 Z" fill="#e8934a"/>
        <path d="M31 14 L26 4 L22 13 Z" fill="#e8934a"/>
        <path d="M11.3 12.8 L14 7.5 L16.3 12.3 Z" fill="#ffd9b3"/>
        <path d="M28.7 12.8 L26 7.5 L23.7 12.3 Z" fill="#ffd9b3"/>
        <circle cx="20" cy="22" r="12.5" fill="#f2a860"/>
        <path d="M20 10.5 L20 14" stroke="#d9863c" stroke-width="1.6" stroke-linecap="round"/>
        <path d="M15 11.5 L16.5 14.5" stroke="#d9863c" stroke-width="1.6" stroke-linecap="round"/>
        <path d="M25 11.5 L23.5 14.5" stroke="#d9863c" stroke-width="1.6" stroke-linecap="round"/>
        <ellipse cx="15.5" cy="22" rx="1.7" ry="2.2" fill="#2b2318"/>
        <ellipse cx="24.5" cy="22" rx="1.7" ry="2.2" fill="#2b2318"/>
        <path d="M18.7 26 L21.3 26 L20 27.6 Z" fill="#c96b52"/>
        <path d="M20 27.6 Q20 29 18 29.3" stroke="#8a5a3c" stroke-width="1" fill="none" stroke-linecap="round"/>
        <path d="M20 27.6 Q20 29 22 29.3" stroke="#8a5a3c" stroke-width="1" fill="none" stroke-linecap="round"/>
        <path d="M6 21 L12.5 21.5" stroke="#c9863a" stroke-width="1" stroke-linecap="round"/>
        <path d="M6.5 25.5 L13 23.9" stroke="#c9863a" stroke-width="1" stroke-linecap="round"/>
        <path d="M34 21 L27.5 21.5" stroke="#c9863a" stroke-width="1" stroke-linecap="round"/>
        <path d="M33.5 25.5 L27 23.9" stroke="#c9863a" stroke-width="1" stroke-linecap="round"/>
      </svg>
    </div>
    <div>
      <div class="brand-name">勤美揪手搖</div>
      <div class="brand-sub">GROUP ORDER · 一起揪飲料</div>
    </div>
  </div>
  <div class="header-actions">
    <button class="cart-btn" id="openImageBtn">📋 菜單照片</button>
    <button class="cart-btn" id="openCartBtn">彙總 <span class="cart-count" id="cartCount">0</span></button>
  </div>
</header>

<section class="hero">
  <div class="hero-sign">今天喝什麼・報名接力</div>
  <div class="hero-text">
    <div id="shopNameLine" class="shop-name-line" style="display:none;"></div>
    <h1>這一團，你要<em>喝什麼</em>？</h1>
    <p>點自己的名字，選飲料、甜度、冰塊；不喝的人記得按 Pass。資料會即時存到伺服器，大家看到的是同一團。</p>
    <div class="hero-stats">
      <div class="hero-stat ordered">已選 <b id="statOrdered">0</b> 人</div>
      <div class="hero-stat pass">Pass <b id="statPass">0</b> 人</div>
      <div class="hero-stat unset">尚未選 <b id="statUnset">0</b> 人</div>
    </div>
  </div>
</section>

<main>
  <div class="section-head">
    <h2>團購名單</h2>
    <span id="rosterCountLabel">共 0 人・點名字開始選飲料</span>
  </div>
  <div class="roster-grid" id="rosterGrid"></div>
</main>

<footer>勤美揪手搖・選好記得按儲存；結單後明細會留在彙總的歷史記錄裡</footer>

<!-- Person customize overlay -->
<div class="overlay" id="overlay">
  <div class="panel" id="panel">
    <button class="panel-close" id="closePanel">✕</button>
    <div class="panel-head">
      <div class="cup-wrap">
        <svg viewBox="0 0 220 300" id="cupSvg" width="100%">
          <defs>
            <clipPath id="cupClip">
              <path d="M52,42 L168,42 L149,266 Q148,278 136,278 L84,278 Q72,278 71,266 Z"/>
            </clipPath>
            <linearGradient id="liquidGrad" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" id="liqStop1" stop-color="#8A5A34"/>
              <stop offset="100%" id="liqStop2" stop-color="#6b4326"/>
            </linearGradient>
          </defs>
          <rect x="46" y="28" width="128" height="16" rx="6" fill="#efe3c8" opacity="0.9"/>
          <rect x="100" y="6" width="20" height="26" rx="4" fill="#efe3c8" opacity="0.9"/>
          <g id="steamGroup" opacity="0">
            <path d="M92,2 C86,10 98,14 92,22" stroke="#f2e6ce" stroke-width="3" fill="none" stroke-linecap="round"/>
            <path d="M112,2 C106,10 118,14 112,22" stroke="#f2e6ce" stroke-width="3" fill="none" stroke-linecap="round"/>
            <path d="M132,2 C126,10 138,14 132,22" stroke="#f2e6ce" stroke-width="3" fill="none" stroke-linecap="round"/>
          </g>
          <g id="strawGroup">
            <rect x="122" y="0" width="10" height="90" rx="4" fill="#e8dcc0" transform="rotate(8 127 45)"/>
          </g>
          <path d="M52,42 L168,42 L149,266 Q148,278 136,278 L84,278 Q72,278 71,266 Z"
                fill="rgba(255,255,255,0.03)" stroke="#efe3c8" stroke-width="3"/>
          <g clip-path="url(#cupClip)">
            <rect id="liquidRect" x="40" y="95" width="140" height="190" fill="url(#liquidGrad)"/>
            <g id="iceGroup"></g>
          </g>
          <path d="M52,42 L168,42 L149,266 Q148,278 136,278 L84,278 Q72,278 71,266 Z"
                fill="none" stroke="#1c140d" stroke-width="1.5" opacity="0.35"/>
        </svg>
      </div>
      <div class="panel-title">
        <h3 id="panelName">姓名</h3>
        <p id="panelDesc">點選飲品、甜度與冰塊</p>
      </div>
    </div>

    <div class="panel-body">
      <div class="pass-toggle">
        <div class="pass-toggle-label">跳過這一團（Pass）<span>不喝的話按這裡，其他欄位會自動略過</span></div>
        <div class="switch" id="passSwitch"><div class="knob"></div></div>
      </div>

      <div id="panelMenuLink"></div>

      <div class="fade-section" id="fadeSection">
        <div class="opt-group">
          <div class="opt-label">飲品名稱（照著菜單手動輸入）</div>
          <input class="text-input" id="drinkInput" list="drinkSuggestions" placeholder="例如：紅茶">
          <datalist id="drinkSuggestions"></datalist>
        </div>
        <div class="opt-group" id="quickPickGroup">
          <div class="opt-label">快速選取（設定頁建立的常見品項，點一下直接帶入）</div>
          <div class="opt-row" id="drinkRow"></div>
        </div>
        <div class="opt-group">
          <div class="opt-label">選擇障礙來偷一下別人點的</div>
          <div class="opt-row" id="othersPickRow"></div>
        </div>
        <div class="opt-group">
          <div class="opt-label">甜度</div>
          <div class="opt-row" id="sugarRow"></div>
          <input class="text-input custom-input" id="sugarCustomInput" placeholder="或自行輸入，例如：三分糖">
        </div>
        <div class="opt-group">
          <div class="opt-label">冰塊</div>
          <div class="opt-row" id="iceRow"></div>
          <input class="text-input custom-input" id="iceCustomInput" placeholder="或自行輸入，例如：常溫">
        </div>
      </div>

      <div class="panel-footer">
        <button class="btn-ghost" id="clearBtn">清除選擇</button>
        <button class="btn-primary" id="saveBtn">儲存</button>
      </div>
    </div>
  </div>
</div>

<!-- Menu image viewer overlay (zoomable) -->
<div class="overlay" id="imageOverlay">
  <div class="panel" id="imagePanel" style="max-width:920px;">
    <button class="panel-close" id="closeImageOverlay">✕</button>
    <div style="padding:18px 18px 22px;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;flex-wrap:wrap;gap:8px;">
        <h3 style="font-family:'Noto Serif TC',serif;font-size:18px;margin:0;font-weight:900;">本次菜單</h3>
        <div style="display:flex;gap:6px;">
          <button class="btn-ghost zoom-btn" id="zoomOutBtn">－</button>
          <button class="btn-ghost zoom-btn" id="zoomResetBtn">重設</button>
          <button class="btn-ghost zoom-btn" id="zoomInBtn">＋</button>
        </div>
      </div>
      <div id="zoomViewport" class="zoom-viewport">
        <div id="imageViewerContent" class="zoom-content"></div>
      </div>
      <p style="margin:10px 0 0;font-size:11.5px;color:var(--cream-dim);text-align:center;">滑鼠滾輪或雙指縮放，拖曳可移動</p>
    </div>
  </div>
</div>

<!-- Summary overlay -->
<div class="cart-overlay" id="cartOverlay">
  <div class="cart-panel">
    <div class="cart-head">
      <h3>團購彙總</h3>
      <button class="panel-close" id="closeCart" style="position:static;">✕</button>
    </div>
    <div class="cart-items" id="cartItems"></div>
    <div class="cart-foot" style="display:flex; gap:10px; flex-wrap:wrap;">
      <button class="btn-ghost" id="finalizeBtn" style="flex:1;">✓ 結單</button>
      <button class="btn-checkout" id="copyBtn" style="flex:1;">複製團購文字</button>
    </div>
  </div>
</div>

<div class="toast" id="toast">已複製到剪貼簿</div>

@php
    $orderBoot = [
        'members' => $members,
        'histories' => $histories,
        'shopName' => $group->shop_name,
        'imageUrl' => $group->image_url,
        'round' => $group->round,
        'saveUrl' => $saveUrl,
        'finalizeUrl' => $finalizeUrl,
        'restoreUrlTemplate' => $restoreUrlTemplate,
        'stateUrl' => $stateUrl,
        'csrf' => csrf_token(),
    ];
@endphp
<script>
window.ORDER_BOOT = @json($orderBoot);
</script>
<script>
const BOOT = window.ORDER_BOOT || {};
const CSRF = BOOT.csrf || document.querySelector('meta[name="csrf-token"]')?.content || '';
const SAVE_URL = BOOT.saveUrl;
const FINALIZE_URL = BOOT.finalizeUrl;
const RESTORE_URL_TEMPLATE = BOOT.restoreUrlTemplate;
const STATE_URL = BOOT.stateUrl;

const PALETTE = [
  ['#8fae66','#5f7c3f'], ['#a97656','#7a4f36'], ['#e0916b','#b85c3f'], ['#a8b94a','#7c8f2f'],
  ['#d68fa0','#a75f74'], ['#e0a23c','#b8791f'], ['#c2ac3f','#93801f'], ['#b3c257','#8a9938'],
  ['#dfb93f','#b8901f'], ['#9a7fb0','#6f5188'], ['#6b98a8','#3f6e7e'], ['#c97a7a','#8e4a4a']
];

let DRINK_LIST = [];
function drinkByName(name){ return DRINK_LIST.find(d=>d.name===name) || null; }
function colorForDrink(name){
  if(!name) return null;
  const known = drinkByName(name);
  if(known) return [known.color, known.color2];
  let hash = 0;
  for(let i=0;i<name.length;i++){ hash = name.charCodeAt(i) + ((hash<<5)-hash); }
  return PALETTE[Math.abs(hash) % PALETTE.length];
}

async function copyText(text){
  try{
    if(navigator.clipboard && window.isSecureContext){
      await navigator.clipboard.writeText(text);
      return true;
    }
    throw new Error('insecure context');
  }catch(e){
    try{
      const ta = document.createElement('textarea');
      ta.value = text;
      ta.style.position = 'fixed';
      ta.style.left = '-9999px';
      ta.style.top = '0';
      document.body.appendChild(ta);
      ta.focus();
      ta.select();
      const ok = document.execCommand('copy');
      document.body.removeChild(ta);
      return ok;
    }catch(err){
      return false;
    }
  }
}

async function apiPost(url, body = {}){
  const res = await fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': CSRF,
      'X-Requested-With': 'XMLHttpRequest',
    },
    credentials: 'same-origin',
    body: JSON.stringify(body),
  });
  const data = await res.json().catch(()=>({}));
  if(!res.ok){
    let msg = data.message;
    if(!msg && data.errors){
      const first = Object.values(data.errors)[0];
      msg = Array.isArray(first) ? first[0] : String(first);
    }
    throw new Error(msg || '操作失敗');
  }
  return data;
}

async function apiGet(url){
  const res = await fetch(url, {
    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    credentials: 'same-origin',
  });
  const data = await res.json().catch(()=>({}));
  if(!res.ok) throw new Error(data.message || '載入失敗');
  return data;
}

const SUGARS = ['無糖','一分糖','微糖','少糖','半糖','全糖'];
const ICES = ['熱飲','去冰','微冰','少冰','正常冰'];
const ICE_CUBE_COUNT = { '熱飲':0, '去冰':0, '微冰':2, '少冰':4, '正常冰':6 };

let PEOPLE = (BOOT.members || []).map(m => ({...m}));
let ORDER_HISTORY = (BOOT.histories || []).map(h => ({...h}));
let expandedHistoryIds = new Set();
let shopName = BOOT.shopName || '';
let menuImageData = BOOT.imageUrl || null;

const rosterGrid = document.getElementById('rosterGrid');

function statusText(p){
  if(p.status==='pass') return { text:'Pass・這團不喝', cls:'pass-text', dot:'grey' };
  if(p.status==='unset') return { text:'尚未選擇，點我登記', cls:'unset-text', dot:'amber' };
  const missing = (!p.sugar || !p.ice);
  const parts = [p.drink];
  if(p.sugar) parts.push(p.sugar);
  if(p.ice) parts.push(p.ice);
  let text = parts.join('・');
  if(missing) text += '（甜度/冰塊未填）';
  return { text, cls:'ordered-text', dot:'jade' };
}

function isLatinName(name){ return /^[A-Za-z0-9 .'-]+$/.test(name.trim()); }

function applyMembers(list){
  PEOPLE = (list || []).map(m => ({...m}));
  renderRoster();
}

function applyHistories(list){
  ORDER_HISTORY = (list || []).map(h => ({...h}));
}

function renderRoster(){
  rosterGrid.innerHTML = '';
  PEOPLE.forEach((p, idx)=>{
    const st = statusText(p);
    const card = document.createElement('div');
    card.className = 'person-card';
    card.setAttribute('tabindex', '0');
    card.setAttribute('role', 'button');
    card.title = p.name;
    let avatarClass = isLatinName(p.name) ? 'wide' : '';
    if(p.status==='ordered'){
      avatarClass += ' ordered';
    } else if(p.status==='pass'){
      avatarClass += ' pass';
    } else {
      avatarClass += ' unset';
    }
    const avatarLabel = isLatinName(p.name) ? p.name : p.name.slice(0,2);
    const copyBtnHtml = (p.status==='ordered' && p.drink)
      ? `<button class="copy-btn" data-copy-idx="${idx}">偷一下</button>`
      : '';
    card.innerHTML = `
      <div class="avatar ${avatarClass}">${avatarLabel}</div>
      <div class="p-info">
        <div class="p-status ${st.cls}">${st.text}${copyBtnHtml}</div>
      </div>`;
    card.addEventListener('click', (e)=>{
      if(e.target.closest('.copy-btn')) return;
      openPersonPanel(idx);
    });
    card.addEventListener('keydown', (e)=>{
      if(e.key==='Enter' || e.key===' '){ e.preventDefault(); openPersonPanel(idx); }
    });
    rosterGrid.appendChild(card);
  });
  rosterGrid.querySelectorAll('.copy-btn').forEach(btn=>{
    btn.addEventListener('click', async (e)=>{
      e.stopPropagation();
      const idx = Number(btn.dataset.copyIdx);
      const name = PEOPLE[idx].drink;
      const ok = await copyText(name);
      showToast(ok ? `已複製「${name}」` : '複製失敗，請手動選取文字');
    });
  });
  document.getElementById('rosterCountLabel').textContent = `共 ${PEOPLE.length} 人・點名字開始選飲料`;
  updateHeroStats();
}

function updateHeroStats(){
  const ordered = PEOPLE.filter(p=>p.status==='ordered').length;
  const pass = PEOPLE.filter(p=>p.status==='pass').length;
  const unset = PEOPLE.filter(p=>p.status==='unset').length;
  document.getElementById('statOrdered').textContent = ordered;
  document.getElementById('statPass').textContent = pass;
  document.getElementById('statUnset').textContent = unset;
  document.getElementById('cartCount').textContent = ordered;
}

const overlay = document.getElementById('overlay');
const panelName = document.getElementById('panelName');
const panelDesc = document.getElementById('panelDesc');
const drinkRow = document.getElementById('drinkRow');
const sugarRow = document.getElementById('sugarRow');
const iceRow = document.getElementById('iceRow');
const fadeSection = document.getElementById('fadeSection');
const passSwitch = document.getElementById('passSwitch');

let editingIdx = null;
let draft = null;
let saving = false;

function openPersonPanel(idx){
  editingIdx = idx;
  const p = PEOPLE[idx];
  draft = { isPass: p.status==='pass', drink: p.drink || null, sugar: p.sugar || null, ice: p.ice || null };
  panelName.textContent = p.name;
  panelDesc.textContent = '點選飲品、甜度與冰塊，或直接跳過這一團';
  renderMenuPreviewOnce();
  buildPanel();
  overlay.classList.add('open');
}

function renderMenuPreviewOnce(){
  const wrap = document.getElementById('panelMenuLink');
  wrap.innerHTML = menuImageData
    ? `<div class="menu-inline">
         <div class="menu-inline-head">
           <span>本次菜單・拖曳移動、滾輪或雙指縮放</span>
           <div style="display:flex; gap:4px;">
             <button class="mini-zoom-btn" id="inlineZoomOutBtn">－</button>
             <button class="mini-zoom-btn" id="inlineZoomResetBtn">重設</button>
             <button class="mini-zoom-btn" id="inlineZoomInBtn">＋</button>
             <button class="mini-zoom-btn" id="inlineExpandBtn">⤢</button>
           </div>
         </div>
         <div class="menu-inline-viewport" id="menuInlineViewport">
           <div class="menu-inline-content" id="menuInlineContent">
             <img src="${menuImageData}" id="menuInlineImg" alt="本次菜單">
           </div>
         </div>
       </div>`
    : '';
  if(!menuImageData) return;

  const viewport = document.getElementById('menuInlineViewport');
  const content = document.getElementById('menuInlineContent');
  const img = document.getElementById('menuInlineImg');
  const ctrl = createZoomController(viewport, content);
  const doFit = ()=> ctrl.fitImage(img);
  fitWhenReady(img, doFit);

  document.getElementById('inlineZoomInBtn').addEventListener('click', ()=> ctrl.zoomBy(1.3));
  document.getElementById('inlineZoomOutBtn').addEventListener('click', ()=> ctrl.zoomBy(0.75));
  document.getElementById('inlineZoomResetBtn').addEventListener('click', doFit);
  document.getElementById('inlineExpandBtn').addEventListener('click', ()=>{
    viewport.classList.toggle('expanded');
    doFit();
  });
}

function getDistinctOrders(){
  const seen = new Map();
  PEOPLE.forEach(p=>{
    if(p.status==='ordered' && p.drink){
      const key = [p.drink, p.sugar||'', p.ice||''].join('|');
      if(seen.has(key)) seen.get(key).count++;
      else seen.set(key, { drink:p.drink, sugar:p.sugar, ice:p.ice, count:1 });
    }
  });
  return Array.from(seen.values()).sort((a,b)=> b.count-a.count);
}

function buildPanel(){
  passSwitch.classList.toggle('on', draft.isPass);
  fadeSection.classList.toggle('dim', draft.isPass);

  const drinkInput = document.getElementById('drinkInput');
  drinkInput.value = draft.drink || '';
  const dl = document.getElementById('drinkSuggestions');
  const suggestionNames = new Set(DRINK_LIST.map(d=>d.name));
  PEOPLE.forEach(p=>{ if(p.drink) suggestionNames.add(p.drink); });
  dl.innerHTML = Array.from(suggestionNames).map(n=>`<option value="${n}"></option>`).join('');

  const quickGroup = document.getElementById('quickPickGroup');
  drinkRow.innerHTML = '';
  if(DRINK_LIST.length===0){
    quickGroup.style.display = 'none';
  } else {
    quickGroup.style.display = '';
    DRINK_LIST.forEach(d=>{
      const active = draft.drink === d.name;
      const b = document.createElement('button');
      b.className = 'opt-pill' + (active?' active':'');
      b.textContent = d.name;
      b.addEventListener('click', ()=>{ draft.drink = d.name; buildPanel(); });
      drinkRow.appendChild(b);
    });
  }

  const othersRow = document.getElementById('othersPickRow');
  othersRow.innerHTML = '';
  const distinct = getDistinctOrders();
  if(distinct.length===0){
    othersRow.innerHTML = `<span style="font-size:12.5px;color:var(--cream-dim);">目前還沒有人選，你可以當第一個！</span>`;
  } else {
    distinct.forEach(o=>{
      const chip = document.createElement('button');
      chip.className = 'opt-pill small';
      chip.textContent = [o.drink, o.sugar, o.ice].filter(Boolean).join('・') + `（${o.count}人）`;
      chip.addEventListener('click', ()=>{ draft.drink=o.drink; draft.sugar=o.sugar; draft.ice=o.ice; buildPanel(); });
      othersRow.appendChild(chip);
    });
  }

  sugarRow.innerHTML = '';
  SUGARS.forEach(s=>{
    const active = draft.sugar === s;
    const b = document.createElement('button');
    b.className = 'opt-pill' + (active?' active':'');
    b.textContent = s;
    b.addEventListener('click', ()=>{ draft.sugar = s; buildPanel(); updateCup(); });
    sugarRow.appendChild(b);
  });

  iceRow.innerHTML = '';
  ICES.forEach(s=>{
    const active = draft.ice === s;
    const b = document.createElement('button');
    b.className = 'opt-pill' + (active?' active':'');
    b.textContent = s;
    b.addEventListener('click', ()=>{ draft.ice = s; buildPanel(); updateCup(); });
    iceRow.appendChild(b);
  });

  const sugarCustomInput = document.getElementById('sugarCustomInput');
  const iceCustomInput = document.getElementById('iceCustomInput');
  sugarCustomInput.value = (draft.sugar && !SUGARS.includes(draft.sugar)) ? draft.sugar : '';
  iceCustomInput.value = (draft.ice && !ICES.includes(draft.ice)) ? draft.ice : '';

  updateCup();
}

document.getElementById('drinkInput').addEventListener('input', (e)=>{
  draft.drink = e.target.value.trim() || null;
  updateCup();
});

function wireCustomInput(inputId, field, presets, pillRow, alsoAffectsCup){
  document.getElementById(inputId).addEventListener('input', (e)=>{
    draft[field] = e.target.value.trim() || null;
    pillRow.querySelectorAll('.opt-pill').forEach(b=>{
      b.classList.toggle('active', presets.includes(draft[field]) && b.textContent===draft[field]);
    });
    if(alsoAffectsCup) updateCup();
  });
}
wireCustomInput('sugarCustomInput', 'sugar', SUGARS, sugarRow, true);
wireCustomInput('iceCustomInput', 'ice', ICES, iceRow, true);

passSwitch.addEventListener('click', ()=>{
  draft.isPass = !draft.isPass;
  buildPanel();
});

document.getElementById('clearBtn').addEventListener('click', ()=>{
  draft = { isPass:false, drink:null, sugar:null, ice:null };
  buildPanel();
});

document.getElementById('saveBtn').addEventListener('click', async ()=>{
  if(saving || editingIdx===null) return;
  const p = PEOPLE[editingIdx];
  saving = true;
  const btn = document.getElementById('saveBtn');
  btn.disabled = true;
  try{
    const data = await apiPost(SAVE_URL, {
      member_id: p.id,
      is_pass: !!draft.isPass,
      drink: draft.isPass ? null : (draft.drink || null),
      sugar: draft.isPass ? null : (draft.sugar || null),
      ice: draft.isPass ? null : (draft.ice || null),
    });
    if(data.members) applyMembers(data.members);
    overlay.classList.remove('open');
    showToast(data.message || '已儲存');
  }catch(err){
    showToast(err.message || '儲存失敗');
  }finally{
    saving = false;
    btn.disabled = false;
  }
});

document.getElementById('closePanel').addEventListener('click', ()=> overlay.classList.remove('open'));
overlay.addEventListener('click', (e)=>{ if(e.target===overlay) overlay.classList.remove('open'); });

const liqStop1 = document.getElementById('liqStop1');
const liqStop2 = document.getElementById('liqStop2');
const liquidRect = document.getElementById('liquidRect');
const iceGroup = document.getElementById('iceGroup');
const steamGroup = document.getElementById('steamGroup');
const strawGroup = document.getElementById('strawGroup');

function updateCup(){
  const c = draft.drink ? colorForDrink(draft.drink) : null;
  const hasDrink = !!c && !draft.isPass;

  liqStop1.setAttribute('stop-color', hasDrink ? c[0] : '#544537');
  liqStop2.setAttribute('stop-color', hasDrink ? c[1] : '#382c22');
  liquidRect.setAttribute('opacity', hasDrink ? '1' : '0.3');

  const isHot = draft.ice === '熱飲';
  steamGroup.setAttribute('opacity', (isHot && hasDrink)? '0.8':'0');
  strawGroup.setAttribute('opacity', (isHot && hasDrink)? '0':'1');

  const sugarIdx = draft.sugar ? SUGARS.indexOf(draft.sugar) : 2;
  const fillY = 95 - Math.max(0,sugarIdx)*3;
  liquidRect.setAttribute('y', fillY);
  liquidRect.setAttribute('height', 285-fillY);

  iceGroup.innerHTML = '';
  if(hasDrink && !isHot && draft.ice){
    const count = ICE_CUBE_COUNT[draft.ice] || 0;
    for(let i=0;i<count;i++){
      const row = Math.floor(i/3), col = i%3;
      const x = 78 + col*30 + (row%2? 8:0);
      const y = fillY + 8 + row*24;
      const r = document.createElementNS('http://www.w3.org/2000/svg','rect');
      r.setAttribute('x', x); r.setAttribute('y', y);
      r.setAttribute('width', 20); r.setAttribute('height', 20); r.setAttribute('rx', 5);
      r.setAttribute('fill', 'rgba(255,255,255,0.55)');
      r.setAttribute('stroke', 'rgba(255,255,255,0.8)');
      iceGroup.appendChild(r);
    }
  }
}

const cartOverlay = document.getElementById('cartOverlay');
const cartItemsEl = document.getElementById('cartItems');

function buildGroups(orderedList){
  const groups = {};
  orderedList.forEach(p=>{
    const key = [p.drink, p.sugar||'（甜度未填）', p.ice||'（冰塊未填）'].join('・');
    if(!groups[key]) groups[key] = { count:0, names:[] };
    groups[key].count++;
    groups[key].names.push(p.name);
  });
  return Object.keys(groups)
    .map(k=>({ label:k, count:groups[k].count, names:groups[k].names }))
    .sort((a,b)=> b.count-a.count);
}

function renderSummary(){
  cartItemsEl.innerHTML = '';

  const ordered = PEOPLE.filter(p=>p.status==='ordered');
  const passList = PEOPLE.filter(p=>p.status==='pass');
  const unsetList = PEOPLE.filter(p=>p.status==='unset');
  const groupList = buildGroups(ordered);

  const block1 = document.createElement('div');
  block1.className = 'sum-block';
  block1.innerHTML = `<div class="sum-title">依飲品彙整（共 ${ordered.length} 杯）</div>`;
  if(groupList.length===0){
    block1.innerHTML += `<div class="sum-row" style="border:none;color:var(--cream-dim);">目前還沒有人選好飲料</div>`;
  } else {
    groupList.forEach(g=>{
      const row = document.createElement('div');
      row.className = 'sum-row';
      row.innerHTML = `
        <div class="sum-row-top"><span>${g.label}</span><span class="qty">×${g.count}</span></div>
        <div class="sum-row-names">${g.names.join('、')}</div>`;
      block1.appendChild(row);
    });
  }
  cartItemsEl.appendChild(block1);

  const block2 = document.createElement('div');
  block2.className = 'sum-block';
  block2.innerHTML = `<div class="sum-title">尚未選擇（${unsetList.length} 人）</div>`;
  if(unsetList.length===0){
    block2.innerHTML += `<div style="color:var(--cream-dim); font-size:13px;">大家都選好了 🎉</div>`;
  } else {
    const wrap = document.createElement('div');
    unsetList.forEach(p=>{
      const idx = PEOPLE.indexOf(p);
      const chip = document.createElement('button');
      chip.className = 'chip clickable';
      chip.style.border = '1px solid var(--line)';
      chip.textContent = p.name;
      chip.addEventListener('click', ()=>{ cartOverlay.classList.remove('open'); openPersonPanel(idx); });
      wrap.appendChild(chip);
    });
    block2.appendChild(wrap);
  }
  cartItemsEl.appendChild(block2);

  const block3 = document.createElement('div');
  block3.className = 'sum-block';
  block3.innerHTML = `<div class="sum-title">Pass（${passList.length} 人）</div>
    <div style="color:var(--cream-dim); font-size:13px; line-height:1.8;">${passList.map(p=>p.name).join('、') || '無'}</div>`;
  cartItemsEl.appendChild(block3);

  renderHistoryBlock();
}

function renderHistoryBlock(){
  const block = document.createElement('div');
  block.className = 'sum-block';
  block.innerHTML = `<div class="sum-title">歷史結單記錄（共 ${ORDER_HISTORY.length} 筆）</div>`;
  if(ORDER_HISTORY.length===0){
    block.innerHTML += `<div style="color:var(--cream-dim); font-size:13px;">結單後，明細會暫存在這裡</div>`;
  } else {
    ORDER_HISTORY.forEach(rec=>{
      const expanded = expandedHistoryIds.has(String(rec.id));
      const card = document.createElement('div');
      card.className = 'history-card';
      const detailHtml = (rec.groups || []).map(g=>`
        <div class="sum-row">
          <div class="sum-row-top"><span>${g.label}</span><span class="qty">×${g.count}</span></div>
          <div class="sum-row-names">${(g.names||[]).join('、')}</div>
        </div>`).join('');
      card.innerHTML = `
        <button class="history-head" data-hid="${rec.id}">
          <span>
            <b>${rec.dateStr}・${rec.timeStr}</b>
            <span class="history-shop">🏪 ${rec.shopName || '未填店名'}</span>
          </span>
          <span class="qty">共 ${rec.total} 杯　${expanded?'▲':'▼'}</span>
        </button>
        <div class="history-detail" style="display:${expanded?'block':'none'};">
          ${detailHtml}
          <button class="btn-ghost history-restore-btn" data-hid="${rec.id}">↺ 恢復這筆訂單</button>
        </div>`;
      block.appendChild(card);
    });
  }
  cartItemsEl.appendChild(block);
  block.querySelectorAll('.history-head').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const id = String(btn.dataset.hid);
      if(expandedHistoryIds.has(id)) expandedHistoryIds.delete(id); else expandedHistoryIds.add(id);
      renderSummary();
    });
  });
  block.querySelectorAll('.history-restore-btn').forEach(btn=>{
    btn.addEventListener('click', async (e)=>{
      e.stopPropagation();
      const id = btn.dataset.hid;
      const hasCurrentOrders = PEOPLE.some(p=>p.status==='ordered' || p.status==='pass');
      const msg = hasCurrentOrders
        ? '目前這一團已經有人選好了，恢復這筆訂單會直接覆蓋目前的選擇，確定要恢復嗎？'
        : '確定要恢復這筆訂單嗎？大家的選擇會變回結單當時的狀態。';
      if(!confirm(msg)) return;
      try{
        const url = RESTORE_URL_TEMPLATE.replace('__ID__', id);
        const data = await apiPost(url);
        if(data.members) applyMembers(data.members);
        if(data.histories) applyHistories(data.histories);
        renderSummary();
        showToast(data.message || '已恢復');
      }catch(err){
        showToast(err.message || '恢復失敗');
      }
    });
  });
}

document.getElementById('finalizeBtn').addEventListener('click', async ()=>{
  const ordered = PEOPLE.filter(p=>p.status==='ordered');
  if(ordered.length===0){ showToast('目前還沒有人選飲料，無法結單'); return; }
  if(!confirm(`確定要結單嗎？這 ${ordered.length} 杯的明細會存起來，大家的選擇會清空，開始下一輪。`)) return;
  try{
    const data = await apiPost(FINALIZE_URL);
    if(data.members) applyMembers(data.members);
    if(data.histories) applyHistories(data.histories);
    renderSummary();
    showToast(data.message || '已結單');
  }catch(err){
    showToast(err.message || '結單失敗');
  }
});

document.getElementById('openCartBtn').addEventListener('click', ()=>{ renderSummary(); cartOverlay.classList.add('open'); });
document.getElementById('closeCart').addEventListener('click', ()=> cartOverlay.classList.remove('open'));
cartOverlay.addEventListener('click', (e)=>{ if(e.target===cartOverlay) cartOverlay.classList.remove('open'); });

const toast = document.getElementById('toast');
function showToast(msg){
  toast.textContent = msg;
  toast.classList.add('show');
  clearTimeout(showToast._t);
  showToast._t = setTimeout(()=> toast.classList.remove('show'), 1900);
}

document.getElementById('copyBtn').addEventListener('click', async ()=>{
  const lines = PEOPLE.map(p=>{
    if(p.status==='pass') return `${p.name}：pass`;
    if(p.status==='ordered'){
      const parts = [p.drink, p.sugar, p.ice].filter(Boolean);
      return `${p.name}：${parts.join('、')}`;
    }
    return `${p.name}：`;
  });
  const text = lines.join('\n');
  const ok = await copyText(text);
  showToast(ok ? '已複製到剪貼簿' : '複製失敗，請手動選取文字');
});

function updateShopNameDisplay(){
  const el = document.getElementById('shopNameLine');
  if(shopName){
    el.textContent = `🏪 本次訂購：${shopName}`;
    el.style.display = 'inline-flex';
  } else {
    el.style.display = 'none';
  }
}

function touchDist(t){ const dx=t[0].clientX-t[1].clientX, dy=t[0].clientY-t[1].clientY; return Math.sqrt(dx*dx+dy*dy); }

function fitWhenReady(img, run){
  if(img.decode){
    img.decode().then(run).catch(run);
  } else if(img.complete && img.naturalWidth){
    run();
  } else {
    img.onload = run;
  }
}

function createZoomController(viewportEl, contentEl){
  const state = { scale:1, panX:0, panY:0 };

  function apply(){
    contentEl.style.transform = `translate(${state.panX}px, ${state.panY}px) scale(${state.scale})`;
  }
  function zoomBy(factor, cx, cy){
    if(cx===undefined){ cx = viewportEl.clientWidth/2; cy = viewportEl.clientHeight/2; }
    const newScale = Math.min(8, Math.max(0.3, state.scale*factor));
    state.panX = cx - (cx-state.panX)*(newScale/state.scale);
    state.panY = cy - (cy-state.panY)*(newScale/state.scale);
    state.scale = newScale;
    apply();
  }
  function fitImage(img){
    const vw = viewportEl.clientWidth, vh = viewportEl.clientHeight;
    const iw = img.naturalWidth || vw, ih = img.naturalHeight || vh;
    const fitScale = Math.min(vw/iw, vh/ih) || 1;
    state.scale = fitScale;
    state.panX = (vw - iw*fitScale)/2;
    state.panY = (vh - ih*fitScale)/2;
    apply();
  }

  viewportEl.addEventListener('wheel', (e)=>{
    e.preventDefault();
    const rect = viewportEl.getBoundingClientRect();
    zoomBy(e.deltaY < 0 ? 1.12 : 0.9, e.clientX-rect.left, e.clientY-rect.top);
  }, { passive:false });

  let dragState = null;
  viewportEl.addEventListener('mousedown', (e)=>{
    dragState = { x:e.clientX, y:e.clientY, panX:state.panX, panY:state.panY };
    viewportEl.classList.add('dragging');
  });
  window.addEventListener('mousemove', (e)=>{
    if(!dragState) return;
    state.panX = dragState.panX + (e.clientX-dragState.x);
    state.panY = dragState.panY + (e.clientY-dragState.y);
    apply();
  });
  window.addEventListener('mouseup', ()=>{ dragState=null; viewportEl.classList.remove('dragging'); });

  let touchState = null;
  viewportEl.addEventListener('touchstart', (e)=>{
    if(e.touches.length===1){
      touchState = { mode:'pan', x:e.touches[0].clientX, y:e.touches[0].clientY, panX:state.panX, panY:state.panY };
    } else if(e.touches.length===2){
      touchState = {
        mode:'pinch', dist:touchDist(e.touches), scale:state.scale,
        midX:(e.touches[0].clientX+e.touches[1].clientX)/2,
        midY:(e.touches[0].clientY+e.touches[1].clientY)/2,
        panX:state.panX, panY:state.panY
      };
    }
  }, { passive:false });
  viewportEl.addEventListener('touchmove', (e)=>{
    e.preventDefault();
    if(!touchState) return;
    if(touchState.mode==='pan' && e.touches.length===1){
      state.panX = touchState.panX + (e.touches[0].clientX - touchState.x);
      state.panY = touchState.panY + (e.touches[0].clientY - touchState.y);
      apply();
    } else if(touchState.mode==='pinch' && e.touches.length===2){
      const d = touchDist(e.touches);
      const newScale = Math.min(8, Math.max(0.3, touchState.scale * (d/touchState.dist)));
      const rect = viewportEl.getBoundingClientRect();
      const cx = touchState.midX-rect.left, cy = touchState.midY-rect.top;
      state.panX = cx - (cx-touchState.panX)*(newScale/touchState.scale);
      state.panY = cy - (cy-touchState.panY)*(newScale/touchState.scale);
      state.scale = newScale;
      apply();
    }
  }, { passive:false });
  viewportEl.addEventListener('touchend', ()=>{ touchState=null; });

  return { zoomBy, fitImage, apply, state };
}

const zoomViewport = document.getElementById('zoomViewport');
const zoomContentEl = document.getElementById('imageViewerContent');
const modalZoomCtrl = createZoomController(zoomViewport, zoomContentEl);

function openImageViewer(){
  if(menuImageData){
    zoomContentEl.innerHTML = `<img id="zoomImg" src="${menuImageData}" alt="本次菜單">`;
    const img = document.getElementById('zoomImg');
    fitWhenReady(img, ()=> modalZoomCtrl.fitImage(img));
  } else {
    zoomContentEl.innerHTML = `<p style="color:var(--cream);opacity:0.75;font-size:13.5px;line-height:1.8;padding:24px;max-width:320px;">這一團還沒有上傳菜單照片，請管理員到後台補上。</p>`;
  }
  document.getElementById('imageOverlay').classList.add('open');
}

document.getElementById('openImageBtn').addEventListener('click', openImageViewer);
document.getElementById('closeImageOverlay').addEventListener('click', ()=> document.getElementById('imageOverlay').classList.remove('open'));
document.getElementById('imageOverlay').addEventListener('click', (e)=>{ if(e.target.id==='imageOverlay') e.currentTarget.classList.remove('open'); });
document.getElementById('zoomInBtn').addEventListener('click', ()=> modalZoomCtrl.zoomBy(1.35));
document.getElementById('zoomOutBtn').addEventListener('click', ()=> modalZoomCtrl.zoomBy(0.7));
document.getElementById('zoomResetBtn').addEventListener('click', openImageViewer);

async function refreshState(){
  try{
    const data = await apiGet(STATE_URL);
    if(data.members) applyMembers(data.members);
    if(data.histories) applyHistories(data.histories);
    if(typeof data.shopName !== 'undefined'){ shopName = data.shopName || ''; updateShopNameDisplay(); }
    if(typeof data.imageUrl !== 'undefined'){ menuImageData = data.imageUrl || null; }
  }catch(e){ /* ignore polling errors */ }
}

updateShopNameDisplay();
renderRoster();
setInterval(refreshState, 20000);

</script>
</body>
</html>
