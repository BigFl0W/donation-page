<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>HopeConnect NGO — Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

:root{
  --brand:#0f766e;
  --brand-light:#14b8a6;
  --brand-dim:#ccfbf1;
  --brand-bg:#f0fdfa;
  --amber:#d97706;
  --amber-l:#fbbf24;
  --amber-bg:#fffbeb;
  --rose:#dc2626;
  --rose-bg:#fef2f2;
  --blue:#2563eb;
  --blue-bg:#eff6ff;
  --violet:#7c3aed;
  --violet-bg:#f5f3ff;
  --dark:#0c1220;
  --mid:#374151;
  --muted:#6b7280;
  --soft:#9ca3af;
  --border:#e5e7eb;
  --surface:#f9fafb;
  --white:#ffffff;
  --sidebar-w:265px;
  --header-h:66px;
  --radius:14px;
  --shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.06);
  --shadow-md:0 4px 20px rgba(0,0,0,.1);
}

body{
  font-family:'Plus Jakarta Sans',sans-serif;
  background:var(--surface);
  color:var(--dark);
  min-height:100vh;
  overflow-x:hidden;
  font-size:14px;
  line-height:1.5;
}

::-webkit-scrollbar{width:4px;height:4px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:#d1d5db;border-radius:99px}
::-webkit-scrollbar-thumb:hover{background:#9ca3af}

/* ═══════ LAYOUT ═══════ */
.app{display:flex;min-height:100vh}

/* ═══════ OVERLAY (mobile) ═══════ */
.sidebar-overlay{
  display:none;
  position:fixed;inset:0;
  background:rgba(0,0,0,.5);
  z-index:199;
  backdrop-filter:blur(2px);
}
.sidebar-overlay.show{display:block}

/* ═══════ SIDEBAR ═══════ */
.sidebar{
  width:var(--sidebar-w);
  background:var(--dark);
  display:flex;flex-direction:column;
  position:fixed;top:0;left:0;
  height:100vh;z-index:200;
  transition:transform .3s cubic-bezier(.4,0,.2,1),width .3s cubic-bezier(.4,0,.2,1);
  overflow:hidden;
}

/* Collapsed (desktop) */
.sidebar.collapsed{width:72px}
.sidebar.collapsed .nav-text,
.sidebar.collapsed .brand-text,
.sidebar.collapsed .nav-section,
.sidebar.collapsed .user-text,
.sidebar.collapsed .footer-links,
.sidebar.collapsed .nav-badge{display:none!important}
.sidebar.collapsed .brand{padding:18px;justify-content:center}
.sidebar.collapsed .nav-item{padding:12px;justify-content:center}
.sidebar.collapsed .nav-item i.nav-icon{margin:0}
.sidebar.collapsed .sidebar-footer{padding:14px;justify-content:center}
.sidebar.collapsed .user-ava{margin:0}

/* Mobile hidden */
@media(max-width:1023px){
  .sidebar{transform:translateX(-100%)}
  .sidebar.mobile-open{transform:translateX(0)}
  .sidebar.collapsed{width:var(--sidebar-w);transform:translateX(-100%)}
  .sidebar.collapsed.mobile-open{transform:translateX(0)}
}

/* Brand */
.brand{
  display:flex;align-items:center;gap:12px;
  padding:20px 18px 16px;
  border-bottom:1px solid rgba(255,255,255,.07);
  flex-shrink:0;
}
.brand-logo{
  width:38px;height:38px;border-radius:10px;
  background:linear-gradient(135deg,var(--brand-light),var(--brand));
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:.9rem;flex-shrink:0;
}
.brand-text{}
.brand-name{
  font-family:'Instrument Serif',serif;
  font-size:1.1rem;color:#fff;white-space:nowrap;line-height:1.2;
}
.brand-sub{font-size:.68rem;color:#6b7280;white-space:nowrap;margin-top:1px;letter-spacing:.3px}

/* Nav scroll */
.nav-scroll{flex:1;overflow-y:auto;overflow-x:hidden;padding:10px 0}
.nav-section{
  font-size:.65rem;font-weight:700;letter-spacing:1.6px;
  text-transform:uppercase;color:#4b5563;
  padding:14px 18px 5px;white-space:nowrap;
}
.nav-item{
  display:flex;align-items:center;gap:11px;
  padding:10px 18px;cursor:pointer;
  border-left:2px solid transparent;
  transition:all .18s ease;white-space:nowrap;
  position:relative;
}
.nav-item:hover{background:rgba(255,255,255,.05)}
.nav-item.active{
  background:rgba(20,184,166,.12);
  border-left-color:var(--brand-light);
}
.nav-item.active .nav-icon{color:var(--brand-light)}
.nav-item.active .nav-text{color:#f1f5f9;font-weight:600}
.nav-icon{
  font-size:.9rem;color:#6b7280;
  transition:color .18s;flex-shrink:0;
  width:18px;text-align:center;
}
.nav-text{font-size:.83rem;color:#9ca3af;transition:color .18s;flex:1}
.nav-badge{
  font-size:.62rem;font-weight:700;
  padding:2px 7px;border-radius:99px;
  background:var(--rose);color:#fff;
}
.nav-badge.green{background:#059669}
.nav-badge.amber{background:var(--amber)}

/* Sidebar footer */
.sidebar-footer{
  border-top:1px solid rgba(255,255,255,.07);
  padding:14px 18px;
  display:flex;align-items:center;gap:10px;
  flex-shrink:0;
}
.user-ava{
  width:34px;height:34px;border-radius:50%;flex-shrink:0;
  background:linear-gradient(135deg,var(--amber-l),var(--amber));
  display:flex;align-items:center;justify-content:center;
  font-size:.72rem;font-weight:700;color:#fff;
}
.user-text{}
.user-name{font-size:.8rem;font-weight:700;color:#f1f5f9}
.user-role{font-size:.68rem;color:#4b5563;margin-top:1px}
.footer-links{
  border-top:1px solid rgba(255,255,255,.07);
  padding:10px 18px;display:flex;gap:14px;flex-shrink:0;
}
.footer-links a{
  font-size:.7rem;color:#4b5563;text-decoration:none;transition:color .18s;
}
.footer-links a:hover{color:var(--brand-light)}
.footer-links a.danger:hover{color:#f87171}

/* ═══════ MAIN ═══════ */
.main{
  margin-left:var(--sidebar-w);flex:1;
  transition:margin-left .3s cubic-bezier(.4,0,.2,1);
  min-height:100vh;display:flex;flex-direction:column;
}
.main.collapsed{margin-left:72px}
@media(max-width:1023px){
  .main,.main.collapsed{margin-left:0}
}

/* ═══════ TOPBAR ═══════ */
.topbar{
  height:var(--header-h);background:var(--white);
  border-bottom:1px solid var(--border);
  display:flex;align-items:center;gap:14px;
  padding:0 24px;position:sticky;top:0;z-index:100;
}
.menu-btn{
  width:36px;height:36px;border-radius:9px;
  border:none;background:none;cursor:pointer;
  display:flex;flex-direction:column;align-items:center;
  justify-content:center;gap:4.5px;flex-shrink:0;
  transition:background .18s;
}
.menu-btn:hover{background:var(--surface)}
.menu-btn span{
  display:block;width:18px;height:1.8px;
  background:var(--mid);border-radius:2px;transition:all .28s;
}
.page-heading{}
.page-title{font-size:.95rem;font-weight:700;color:var(--dark);line-height:1}
.breadcrumb{
  font-size:.72rem;color:var(--muted);
  display:flex;align-items:center;gap:5px;margin-top:2px;
}
.breadcrumb i{font-size:.55rem}

.topbar-search{
  flex:1;max-width:340px;margin-left:auto;position:relative;
}
.topbar-search input{
  width:100%;padding:9px 14px 9px 36px;
  border:1px solid var(--border);border-radius:10px;
  font-family:'Plus Jakarta Sans',sans-serif;font-size:.82rem;
  background:var(--surface);color:var(--dark);outline:none;
  transition:border .18s,box-shadow .18s;
}
.topbar-search input:focus{border-color:var(--brand-light);box-shadow:0 0 0 3px rgba(20,184,166,.1)}
.topbar-search i{
  position:absolute;left:12px;top:50%;transform:translateY(-50%);
  color:var(--soft);font-size:.8rem;
}
.topbar-right{display:flex;align-items:center;gap:6px;margin-left:10px}
.tb-btn{
  width:36px;height:36px;border-radius:9px;
  border:1px solid var(--border);background:var(--white);cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  font-size:.88rem;color:var(--mid);position:relative;transition:all .18s;
}
.tb-btn:hover{background:var(--surface);border-color:var(--brand-light);color:var(--brand)}
.tb-dot{
  position:absolute;top:5px;right:5px;
  width:7px;height:7px;border-radius:50%;
  background:var(--rose);border:2px solid var(--white);
}
.tb-avatar{
  width:34px;height:34px;border-radius:50%;
  background:linear-gradient(135deg,var(--brand-light),var(--brand));
  display:flex;align-items:center;justify-content:center;
  font-size:.7rem;font-weight:700;color:#fff;cursor:pointer;
  border:2px solid var(--brand-dim);flex-shrink:0;
}

/* ═══════ CONTENT ═══════ */
.content{flex:1;padding:26px;display:none}
.content.active{display:block;animation:pageIn .28s ease}
@keyframes pageIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}

/* ═══════ STATS GRID ═══════ */
.stats-grid{
  display:grid;grid-template-columns:repeat(4,1fr);
  gap:16px;margin-bottom:22px;
}
@media(max-width:1279px){.stats-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:479px){.stats-grid{grid-template-columns:1fr}}

.stat-card{
  background:var(--white);border-radius:var(--radius);
  padding:20px;border:1px solid var(--border);
  box-shadow:var(--shadow);position:relative;overflow:hidden;
  transition:transform .2s,box-shadow .2s;
}
.stat-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-md)}
.stat-card::after{
  content:'';position:absolute;right:-16px;top:-16px;
  width:80px;height:80px;border-radius:50%;opacity:.07;
}
.stat-card.t1::after{background:var(--brand)}
.stat-card.t2::after{background:var(--amber)}
.stat-card.t3::after{background:var(--rose)}
.stat-card.t4::after{background:var(--blue)}

.stat-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px}
.stat-icon-wrap{
  width:42px;height:42px;border-radius:11px;
  display:flex;align-items:center;justify-content:center;
  font-size:.95rem;
}
.t1 .stat-icon-wrap{background:var(--brand-bg);color:var(--brand)}
.t2 .stat-icon-wrap{background:var(--amber-bg);color:var(--amber)}
.t3 .stat-icon-wrap{background:var(--rose-bg);color:var(--rose)}
.t4 .stat-icon-wrap{background:var(--blue-bg);color:var(--blue)}
.t5 .stat-icon-wrap{background:var(--violet-bg);color:var(--violet)}

.stat-trend{
  font-size:.7rem;font-weight:600;
  padding:3px 8px;border-radius:99px;
  display:flex;align-items:center;gap:4px;
}
.stat-trend.up{background:#dcfce7;color:#15803d}
.stat-trend.down{background:var(--rose-bg);color:var(--rose)}
.stat-trend.neutral{background:var(--surface);color:var(--muted)}

.stat-value{font-size:1.75rem;font-weight:800;color:var(--dark);line-height:1;letter-spacing:-1px}
.stat-label{font-size:.75rem;color:var(--muted);margin-top:4px;font-weight:500}
.stat-sub{font-size:.72rem;color:var(--soft);margin-top:10px}

/* ═══════ CARD ═══════ */
.card{
  background:var(--white);border-radius:var(--radius);
  border:1px solid var(--border);padding:20px 22px;
  box-shadow:var(--shadow);
}
.card-hd{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:18px;gap:10px;flex-wrap:wrap;
}
.card-hd-left{}
.card-title{font-size:.9rem;font-weight:700;color:var(--dark)}
.card-sub{font-size:.75rem;color:var(--muted);margin-top:2px}
.card-link{
  font-size:.76rem;font-weight:600;color:var(--brand);
  padding:6px 12px;border-radius:8px;border:1px solid var(--brand-dim);
  background:var(--brand-bg);cursor:pointer;transition:all .18s;
  text-decoration:none;white-space:nowrap;
}
.card-link:hover{background:var(--brand-dim)}

/* ═══════ CHARTS ROW ═══════ */
.charts-row{
  display:grid;grid-template-columns:1.65fr 1fr;gap:16px;margin-bottom:22px;
}
@media(max-width:1199px){.charts-row{grid-template-columns:1fr}}

/* Bar chart */
.bar-chart-wrap{display:flex;flex-direction:column;gap:8px}
.bar-chart{
  display:flex;align-items:flex-end;gap:8px;
  height:130px;padding-top:8px;
}
.bar-col{flex:1;display:flex;flex-direction:column;align-items:center;gap:5px}
.bar-stack{width:100%;display:flex;align-items:flex-end;gap:2px;height:105px}
.bar{
  flex:1;border-radius:4px 4px 0 0;min-width:6px;
  transition:opacity .18s;cursor:pointer;
}
.bar:hover{opacity:.75}
.bar.primary{background:var(--brand)}
.bar.secondary{background:var(--brand-dim)}
.bar-lbl{font-size:.62rem;color:var(--soft);font-weight:500}
.chart-legend{
  display:flex;gap:16px;margin-top:6px;
}
.legend-item{display:flex;align-items:center;gap:6px;font-size:.72rem;color:var(--muted)}
.legend-dot{width:8px;height:8px;border-radius:2px;flex-shrink:0}

/* Donut */
.donut-wrap{display:flex;align-items:center;gap:20px;flex-wrap:wrap}
.donut-legend{display:flex;flex-direction:column;gap:9px;min-width:120px}
.dl-item{display:flex;align-items:center;gap:8px;font-size:.78rem}
.dl-dot{width:9px;height:9px;border-radius:3px;flex-shrink:0}
.dl-lbl{color:var(--muted);flex:1}
.dl-val{font-weight:700;color:var(--dark)}

/* Mini stats below donut */
.mini-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:16px}
.mini-stat{
  background:var(--surface);border-radius:9px;
  padding:11px 13px;border:1px solid var(--border);text-align:center;
}
.mini-stat .v{font-size:1.1rem;font-weight:800;color:var(--dark)}
.mini-stat .l{font-size:.68rem;color:var(--muted);margin-top:2px}

/* ═══════ TWO COL ═══════ */
.two-col{display:grid;grid-template-columns:1.3fr 1fr;gap:16px}
@media(max-width:1199px){.two-col{grid-template-columns:1fr}}

/* ═══════ FEED / LIST ═══════ */
.feed-list{display:flex;flex-direction:column}
.feed-row{
  display:flex;align-items:center;gap:11px;
  padding:11px 0;border-bottom:1px solid var(--border);
}
.feed-row:last-child{border-bottom:none}
.feed-ava{
  width:34px;height:34px;border-radius:50%;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  font-size:.72rem;font-weight:700;color:#fff;
}
.feed-info{flex:1;min-width:0}
.feed-name{font-size:.82rem;font-weight:600;color:var(--dark);
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.feed-sub{font-size:.72rem;color:var(--muted)}
.feed-amt{font-size:.88rem;font-weight:700;color:var(--brand);white-space:nowrap}

/* Patient status */
.ps-list{display:flex;flex-direction:column}
.ps-row{
  display:flex;align-items:center;gap:10px;
  padding:10px 0;border-bottom:1px solid var(--border);
}
.ps-row:last-child{border-bottom:none}
.ps-bar{width:3px;height:36px;border-radius:2px;flex-shrink:0}
.ps-info{flex:1;min-width:0}
.ps-name{font-size:.82rem;font-weight:600;color:var(--dark)}
.ps-detail{font-size:.71rem;color:var(--muted);margin-top:1px}

/* ═══════ BADGES ═══════ */
.badge{
  display:inline-flex;align-items:center;gap:4px;
  padding:3px 9px;border-radius:99px;
  font-size:.7rem;font-weight:600;white-space:nowrap;
}
.badge i{font-size:.6rem}
.badge.success{background:#dcfce7;color:#15803d}
.badge.warning{background:#fef3c7;color:#92400e}
.badge.danger{background:#fee2e2;color:#991b1b}
.badge.info{background:#dbeafe;color:#1e40af}
.badge.neutral{background:var(--surface);color:var(--mid)}
.badge.violet{background:#ede9fe;color:#5b21b6}
.badge.teal{background:var(--brand-dim);color:var(--brand)}
.badge.dark{background:var(--dark);color:#fff}

/* ═══════ TABLE ═══════ */
.toolbar{
  display:flex;align-items:center;gap:8px;
  margin-bottom:16px;flex-wrap:wrap;
}
.search-box{
  position:relative;display:flex;
}
.search-box input{
  padding:8px 12px 8px 34px;
  border:1px solid var(--border);border-radius:9px;
  font-family:'Plus Jakarta Sans',sans-serif;font-size:.81rem;
  background:var(--surface);color:var(--dark);
  outline:none;transition:border .18s;width:200px;
}
.search-box input:focus{border-color:var(--brand-light)}
.search-box i{
  position:absolute;left:11px;top:50%;transform:translateY(-50%);
  color:var(--soft);font-size:.78rem;
}
.filter-btn{
  padding:8px 13px;border:1px solid var(--border);border-radius:9px;
  background:var(--white);font-family:'Plus Jakarta Sans',sans-serif;
  font-size:.8rem;color:var(--mid);cursor:pointer;
  display:flex;align-items:center;gap:6px;transition:all .18s;
}
.filter-btn i{font-size:.8rem;color:var(--soft)}
.filter-btn:hover{border-color:var(--brand-light);color:var(--brand)}
.btn-primary{
  padding:8px 16px;border:none;border-radius:9px;
  background:var(--brand);color:#fff;
  font-family:'Plus Jakarta Sans',sans-serif;font-size:.8rem;font-weight:600;
  cursor:pointer;display:flex;align-items:center;gap:7px;
  transition:background .18s;white-space:nowrap;
}
.btn-primary i{font-size:.8rem}
.btn-primary:hover{background:var(--brand-light)}
.btn-primary.ml{margin-left:auto}
.btn-secondary{
  padding:8px 14px;border:1px solid var(--border);border-radius:9px;
  background:var(--white);color:var(--mid);
  font-family:'Plus Jakarta Sans',sans-serif;font-size:.8rem;font-weight:500;
  cursor:pointer;display:flex;align-items:center;gap:7px;transition:all .18s;
}
.btn-secondary:hover{border-color:var(--brand-light);color:var(--brand)}

.data-table{width:100%;border-collapse:collapse}
.data-table th{
  text-align:left;font-size:.67rem;font-weight:700;
  letter-spacing:.7px;text-transform:uppercase;
  color:var(--muted);padding:9px 13px;
  border-bottom:1px solid var(--border);
  white-space:nowrap;background:var(--surface);
}
.data-table th:first-child{border-radius:8px 0 0 0}
.data-table th:last-child{border-radius:0 8px 0 0}
.data-table td{
  padding:12px 13px;font-size:.82rem;
  border-bottom:1px solid var(--border);
  vertical-align:middle;
}
.data-table tr:last-child td{border-bottom:none}
.data-table tbody tr{transition:background .15s}
.data-table tbody tr:hover td{background:var(--surface)}

.cell-user{display:flex;align-items:center;gap:9px}
.cell-ava{
  width:32px;height:32px;border-radius:50%;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  font-size:.7rem;font-weight:700;color:#fff;
}
.cell-name{font-size:.81rem;font-weight:600;color:var(--dark);display:block}
.cell-sub{font-size:.7rem;color:var(--muted)}

.action-btns{display:flex;gap:5px}
.action-btn{
  width:28px;height:28px;border-radius:7px;
  border:1px solid var(--border);background:var(--white);
  cursor:pointer;display:flex;align-items:center;
  justify-content:center;font-size:.75rem;color:var(--muted);
  transition:all .18s;
}
.action-btn:hover.view{border-color:var(--blue);color:var(--blue);background:var(--blue-bg)}
.action-btn:hover.edit{border-color:var(--brand);color:var(--brand);background:var(--brand-bg)}
.action-btn:hover.del{border-color:var(--rose);color:var(--rose);background:var(--rose-bg)}

/* Pagination */
.pagination{
  display:flex;align-items:center;gap:5px;
  margin-top:16px;justify-content:flex-end;flex-wrap:wrap;
}
.page-info{font-size:.74rem;color:var(--muted);margin-right:auto}
.page-btn{
  width:30px;height:30px;border-radius:7px;
  border:1px solid var(--border);background:var(--white);
  cursor:pointer;font-size:.78rem;font-weight:500;
  display:flex;align-items:center;justify-content:center;
  transition:all .18s;color:var(--mid);
}
.page-btn:hover,.page-btn.on{background:var(--brand);color:#fff;border-color:var(--brand)}

/* Tabs */
.tabs{display:flex;gap:4px;margin-bottom:18px;flex-wrap:wrap}
.tab-btn{
  padding:7px 14px;border-radius:8px;
  border:1px solid var(--border);background:var(--white);
  font-family:'Plus Jakarta Sans',sans-serif;font-size:.78rem;font-weight:500;
  color:var(--muted);cursor:pointer;transition:all .18s;
}
.tab-btn:hover{border-color:var(--brand-light);color:var(--brand)}
.tab-btn.on{background:var(--brand);color:#fff;border-color:var(--brand)}

/* Progress bar */
.prog-wrap{width:100%;background:var(--border);border-radius:99px;height:5px;margin:6px 0}
.prog-bar{height:100%;border-radius:99px;background:linear-gradient(90deg,var(--brand),var(--brand-light))}
.prog-bar.urgent{background:linear-gradient(90deg,#f87171,var(--rose))}

/* Section header */
.section-hd{
  display:flex;align-items:flex-start;justify-content:space-between;
  margin-bottom:18px;flex-wrap:wrap;gap:10px;
}
.section-hd h2{font-size:.95rem;font-weight:700;color:var(--dark)}
.section-hd p{font-size:.76rem;color:var(--muted);margin-top:2px}

/* ═══════ CAMPAIGNS ═══════ */
.campaign-grid{
  display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:18px;
}
@media(max-width:899px){.campaign-grid{grid-template-columns:1fr 1fr}}
@media(max-width:599px){.campaign-grid{grid-template-columns:1fr}}

.campaign-card{
  border:1px solid var(--border);border-radius:11px;padding:16px;
  transition:box-shadow .2s;
}
.campaign-card:hover{box-shadow:var(--shadow-md)}
.cmp-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px}
.cmp-name{font-size:.84rem;font-weight:700;color:var(--dark)}
.cmp-date{font-size:.7rem;color:var(--muted);margin-top:2px}
.cmp-nums{display:flex;justify-content:space-between;font-size:.74rem;color:var(--muted);margin-bottom:5px}
.cmp-pct{font-size:.71rem;font-weight:700;margin-top:4px}

/* ═══════ PARTNERS ═══════ */
.partners-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
@media(max-width:899px){.partners-grid{grid-template-columns:1fr 1fr}}
@media(max-width:599px){.partners-grid{grid-template-columns:1fr}}

.partner-card{
  background:var(--white);border:1px solid var(--border);
  border-radius:11px;padding:17px;
  display:flex;align-items:center;gap:13px;
  transition:all .2s;cursor:pointer;
}
.partner-card:hover{border-color:var(--brand-light);box-shadow:0 4px 14px rgba(15,118,110,.1)}
.partner-logo{
  width:46px;height:46px;border-radius:10px;
  background:var(--surface);border:1px solid var(--border);
  display:flex;align-items:center;justify-content:center;
  font-size:.9rem;color:var(--mid);flex-shrink:0;
}
.partner-info{flex:1;min-width:0}
.partner-name{font-size:.83rem;font-weight:700;color:var(--dark)}
.partner-type{font-size:.71rem;color:var(--muted);margin-top:1px}
.partner-since{font-size:.68rem;color:var(--brand);font-weight:600;margin-top:5px}

/* ═══════ BLOG ═══════ */
.blog-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
@media(max-width:1099px){.blog-grid{grid-template-columns:1fr 1fr}}
@media(max-width:599px){.blog-grid{grid-template-columns:1fr}}

.blog-card{
  background:var(--white);border:1px solid var(--border);
  border-radius:12px;overflow:hidden;
  transition:all .22s;cursor:pointer;
}
.blog-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-md)}
.blog-thumb{
  height:130px;display:flex;align-items:center;justify-content:center;font-size:2.4rem;
}
.blog-body{padding:14px}
.blog-tag{
  font-size:.65rem;font-weight:700;letter-spacing:.8px;
  text-transform:uppercase;color:var(--brand);margin-bottom:6px;
}
.blog-title{font-size:.85rem;font-weight:700;color:var(--dark);line-height:1.4;margin-bottom:5px}
.blog-excerpt{font-size:.75rem;color:var(--muted);line-height:1.5}
.blog-meta{
  display:flex;align-items:center;gap:8px;
  font-size:.7rem;color:var(--muted);margin-top:11px;flex-wrap:wrap;
}
.blog-meta span{display:flex;align-items:center;gap:4px}

/* ═══════ GALLERY ═══════ */
.gallery-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
@media(max-width:1099px){.gallery-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:699px){.gallery-grid{grid-template-columns:repeat(2,1fr)}}

.gallery-item{
  border-radius:10px;overflow:hidden;aspect-ratio:1;
  position:relative;cursor:pointer;
}
.g-thumb{
  width:100%;height:100%;display:flex;align-items:center;
  justify-content:center;font-size:2.2rem;transition:transform .3s;
}
.gallery-item:hover .g-thumb{transform:scale(1.06)}
.g-overlay{
  position:absolute;inset:0;
  background:linear-gradient(to top,rgba(12,18,32,.75),transparent);
  opacity:0;transition:opacity .28s;
  display:flex;flex-direction:column;justify-content:space-between;padding:10px;
}
.gallery-item:hover .g-overlay{opacity:1}
.g-actions{display:flex;gap:5px;justify-content:flex-end}
.g-btn{
  width:26px;height:26px;border-radius:6px;
  background:rgba(255,255,255,.92);border:none;cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  font-size:.72rem;color:var(--mid);transition:all .18s;
}
.g-btn:hover{background:#fff}
.g-caption{font-size:.72rem;color:#fff;font-weight:500}

/* ═══════ SECURITY ═══════ */
.security-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
@media(max-width:899px){.security-grid{grid-template-columns:1fr}}

.activity-list{display:flex;flex-direction:column}
.act-row{
  display:flex;align-items:flex-start;gap:11px;
  padding:11px 0;border-bottom:1px solid var(--border);
}
.act-row:last-child{border-bottom:none}
.act-icon{
  width:30px;height:30px;border-radius:8px;
  display:flex;align-items:center;justify-content:center;
  font-size:.78rem;flex-shrink:0;margin-top:1px;
}
.act-icon.login{background:var(--brand-bg);color:var(--brand)}
.act-icon.warn{background:var(--amber-bg);color:var(--amber)}
.act-icon.danger{background:var(--rose-bg);color:var(--rose)}
.act-icon.info{background:var(--blue-bg);color:var(--blue)}
.act-body{flex:1;min-width:0}
.act-title{font-size:.8rem;font-weight:600;color:var(--dark);display:block}
.act-desc{font-size:.71rem;color:var(--muted);margin-top:1px}
.act-time{font-size:.68rem;color:var(--soft);white-space:nowrap;padding-top:2px}

.sys-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.sys-stat{
  background:var(--surface);border-radius:9px;padding:12px 14px;border:1px solid var(--border);
}
.sys-val{font-size:1.2rem;font-weight:800;color:var(--dark)}
.sys-lbl{font-size:.7rem;color:var(--muted);margin-top:2px}

/* ═══════ SETTINGS FORM ═══════ */
.form-field{margin-bottom:14px}
.form-label{font-size:.76rem;font-weight:600;color:var(--mid);margin-bottom:5px;display:block}
.form-input{
  width:100%;padding:9px 13px;
  border:1px solid var(--border);border-radius:9px;
  font-family:'Plus Jakarta Sans',sans-serif;font-size:.83rem;
  color:var(--dark);outline:none;transition:border .18s,box-shadow .18s;
}
.form-input:focus{border-color:var(--brand-light);box-shadow:0 0 0 3px rgba(20,184,166,.1)}

.gateway-row{
  display:flex;align-items:center;justify-content:space-between;
  padding:12px 14px;border:1px solid var(--border);border-radius:10px;
  margin-bottom:8px;transition:border .18s;
}
.gateway-row:hover{border-color:var(--brand-light)}
.gw-left{display:flex;align-items:center;gap:10px}
.gw-icon{width:34px;height:34px;border-radius:8px;background:var(--surface);
  border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:.9rem}
.gw-name{font-size:.82rem;font-weight:600;color:var(--dark)}
.gw-desc{font-size:.7rem;color:var(--muted)}

.toggle-switch{
  width:38px;height:20px;background:var(--brand);border-radius:99px;
  position:relative;cursor:pointer;flex-shrink:0;
}
.toggle-switch::after{
  content:'';position:absolute;right:3px;top:3px;
  width:14px;height:14px;border-radius:50%;background:#fff;
  transition:all .2s;
}
.toggle-switch.off{background:var(--border)}
.toggle-switch.off::after{right:auto;left:3px}

.notif-row{
  display:flex;justify-content:space-between;align-items:center;
  padding:12px 0;border-bottom:1px solid var(--border);
}
.notif-row:last-child{border-bottom:none}
.notif-label{font-size:.82rem;font-weight:600;color:var(--dark)}
.notif-desc{font-size:.71rem;color:var(--muted);margin-top:2px}

/* ═══════ RESPONSIVE HELPERS ═══════ */
.hide-sm{display:block}
@media(max-width:767px){
  .hide-sm{display:none}
  .content{padding:16px}
  .topbar{padding:0 16px;gap:10px}
  .topbar-search{max-width:none;flex:1}
  .stat-value{font-size:1.45rem}
}
@media(max-width:479px){
  .topbar-search{display:none}
  .card{padding:16px}
  .data-table th:nth-child(n+4),.data-table td:nth-child(n+4){display:none}
}

/* ═══════ EMPTY / MONO ═══════ */
.mono{font-family:'Courier New',monospace;font-size:.78rem;color:var(--muted)}

/* ═══════ ALERT BANNER ═══════ */
.alert-banner{
  display:flex;align-items:center;gap:10px;
  padding:11px 16px;border-radius:10px;
  margin-bottom:16px;font-size:.8rem;
}
.alert-banner.warn{background:#fffbeb;border:1px solid #fde68a;color:#92400e}
.alert-banner.danger{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}
.alert-banner i{flex-shrink:0}

</style>
</head>
<body>
<div class="app">

<!-- OVERLAY -->
<div class="sidebar-overlay" id="overlay" onclick="closeMobile()"></div>

<!-- ══════════ SIDEBAR ══════════ -->
<aside class="sidebar" id="sidebar">

  <div class="brand">
    <div class="brand-logo"><i class="fas fa-hands-holding-heart"></i></div>
    <div class="brand-text">
      <div class="brand-name">HopeConnect</div>
      <div class="brand-sub">NGO Admin Portal</div>
    </div>
  </div>

  <nav class="nav-scroll">
    <div class="nav-section">Overview</div>
    <div class="nav-item active" onclick="showPage('dashboard',this)">
      <i class="fas fa-chart-pie nav-icon"></i>
      <span class="nav-text">Dashboard</span>
    </div>

    <div class="nav-section">Management</div>
    <div class="nav-item" onclick="showPage('donations',this)">
      <i class="fas fa-hand-holding-dollar nav-icon"></i>
      <span class="nav-text">Donations</span>
      <span class="nav-badge">12</span>
    </div>
    <div class="nav-item" onclick="showPage('users',this)">
      <i class="fas fa-users nav-icon"></i>
      <span class="nav-text">Users</span>
      <span class="nav-badge green">248</span>
    </div>
    <div class="nav-item" onclick="showPage('patients',this)">
      <i class="fas fa-hospital-user nav-icon"></i>
      <span class="nav-text">Patients</span>
      <span class="nav-badge amber">5</span>
    </div>
    <div class="nav-item" onclick="showPage('partners',this)">
      <i class="fas fa-handshake nav-icon"></i>
      <span class="nav-text">Partners</span>
    </div>

    <div class="nav-section">Content</div>
    <div class="nav-item" onclick="showPage('blog',this)">
      <i class="fas fa-newspaper nav-icon"></i>
      <span class="nav-text">Blog &amp; News</span>
    </div>
    <div class="nav-item" onclick="showPage('gallery',this)">
      <i class="fas fa-images nav-icon"></i>
      <span class="nav-text">Gallery</span>
    </div>

    <div class="nav-section">System</div>
    <div class="nav-item" onclick="showPage('security',this)">
      <i class="fas fa-shield-halved nav-icon"></i>
      <span class="nav-text">Security</span>
      <span class="nav-badge">3</span>
    </div>
    <div class="nav-item" onclick="showPage('settings',this)">
      <i class="fas fa-gear nav-icon"></i>
      <span class="nav-text">Settings</span>
    </div>
  </nav>

  <div class="sidebar-footer">
    <div class="user-ava">SA</div>
    <div class="user-text">
      <div class="user-name">Super Admin</div>
      <div class="user-role">Administrator</div>
    </div>
  </div>
  <div class="footer-links">
    <a href="#">Help</a>
    <a href="#">Privacy</a>
    <a href="#" class="danger" style="color:#4b5563">Logout</a>
  </div>
</aside>

<!-- ══════════ MAIN ══════════ -->
<div class="main" id="main">

  <!-- TOPBAR -->
  <header class="topbar">
    <button class="menu-btn" id="menuBtn" onclick="toggleSidebar()" aria-label="Toggle menu">
      <span></span><span></span><span></span>
    </button>
    <div class="page-heading">
      <div class="page-title" id="pageTitle">Dashboard</div>
      <div class="breadcrumb">
        <span>HopeConnect</span>
        <i class="fas fa-chevron-right"></i>
        <span id="breadSub">Overview</span>
      </div>
    </div>
    <div class="topbar-search">
      <i class="fas fa-search"></i>
      <input type="text" placeholder="Search anything…" aria-label="Search"/>
    </div>
    <div class="topbar-right">
      <button class="tb-btn" title="Notifications" aria-label="Notifications">
        <i class="fas fa-bell"></i>
        <span class="tb-dot"></span>
      </button>
      <button class="tb-btn hide-sm" title="Messages" aria-label="Messages">
        <i class="fas fa-envelope"></i>
      </button>
      <div class="tb-avatar" title="Profile">SA</div>
    </div>
  </header>

  <!-- ══════════════════════════════
       DASHBOARD
  ══════════════════════════════ -->
  <div class="content active" id="page-dashboard">

    <div class="alert-banner warn">
      <i class="fas fa-triangle-exclamation"></i>
      <span><strong>3 security alerts</strong> require your attention — <a href="#" onclick="showPage('security',document.querySelector('[onclick*=security]'))" style="color:inherit;font-weight:700;text-decoration:underline">Review now</a></span>
    </div>

    <div class="stats-grid">
      <div class="stat-card t1">
        <div class="stat-top">
          <div class="stat-icon-wrap"><i class="fas fa-dollar-sign"></i></div>
          <span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i>18.4%</span>
        </div>
        <div class="stat-value">$248,500</div>
        <div class="stat-label">Total Donations This Year</div>
        <div class="stat-sub"><i class="far fa-clock" style="margin-right:4px"></i>Updated just now</div>
      </div>
      <div class="stat-card t2">
        <div class="stat-top">
          <div class="stat-icon-wrap"><i class="fas fa-users"></i></div>
          <span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i>6.2%</span>
        </div>
        <div class="stat-value">2,481</div>
        <div class="stat-label">Registered Users</div>
        <div class="stat-sub"><i class="far fa-clock" style="margin-right:4px"></i>+154 this week</div>
      </div>
      <div class="stat-card t3">
        <div class="stat-top">
          <div class="stat-icon-wrap"><i class="fas fa-hospital-user"></i></div>
          <span class="stat-trend down"><i class="fas fa-arrow-trend-down"></i>2 today</span>
        </div>
        <div class="stat-value">312</div>
        <div class="stat-label">Active Patients</div>
        <div class="stat-sub"><i class="far fa-clock" style="margin-right:4px"></i>2 discharged today</div>
      </div>
      <div class="stat-card t4">
        <div class="stat-top">
          <div class="stat-icon-wrap"><i class="fas fa-handshake"></i></div>
          <span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i>3 new</span>
        </div>
        <div class="stat-value">47</div>
        <div class="stat-label">Active Partners</div>
        <div class="stat-sub"><i class="far fa-clock" style="margin-right:4px"></i>+3 this month</div>
      </div>
    </div>

    <div class="charts-row">
      <div class="card">
        <div class="card-hd">
          <div class="card-hd-left">
            <div class="card-title">Donation Overview</div>
            <div class="card-sub">Monthly donations — 2025</div>
          </div>
          <a class="card-link" onclick="showPage('donations',null)"><i class="fas fa-arrow-right"></i> View All</a>
        </div>
        <div class="bar-chart-wrap">
          <div class="bar-chart">
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:55%"></div><div class="bar secondary" style="height:38%"></div></div><div class="bar-lbl">Jan</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:70%"></div><div class="bar secondary" style="height:52%"></div></div><div class="bar-lbl">Feb</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:45%"></div><div class="bar secondary" style="height:30%"></div></div><div class="bar-lbl">Mar</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:80%"></div><div class="bar secondary" style="height:65%"></div></div><div class="bar-lbl">Apr</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:90%"></div><div class="bar secondary" style="height:72%"></div></div><div class="bar-lbl">May</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:60%"></div><div class="bar secondary" style="height:44%"></div></div><div class="bar-lbl">Jun</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:75%"></div><div class="bar secondary" style="height:58%"></div></div><div class="bar-lbl">Jul</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:100%"></div><div class="bar secondary" style="height:80%"></div></div><div class="bar-lbl">Aug</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:82%"></div><div class="bar secondary" style="height:66%"></div></div><div class="bar-lbl">Sep</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:68%"></div><div class="bar secondary" style="height:50%"></div></div><div class="bar-lbl">Oct</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:55%"></div><div class="bar secondary" style="height:40%"></div></div><div class="bar-lbl">Nov</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:40%"></div><div class="bar secondary" style="height:28%"></div></div><div class="bar-lbl">Dec</div></div>
          </div>
          <div class="chart-legend">
            <div class="legend-item"><div class="legend-dot" style="background:var(--brand)"></div>Received</div>
            <div class="legend-item"><div class="legend-dot" style="background:var(--brand-dim)"></div>Disbursed</div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-hd">
          <div class="card-hd-left">
            <div class="card-title">Patients by Category</div>
            <div class="card-sub">Current distribution</div>
          </div>
        </div>
        <div class="donut-wrap">
          <svg width="110" height="110" viewBox="0 0 110 110" style="flex-shrink:0">
            <circle cx="55" cy="55" r="40" fill="none" stroke="#e5e7eb" stroke-width="16"/>
            <circle cx="55" cy="55" r="40" fill="none" stroke="#0f766e" stroke-width="16" stroke-dasharray="100 151" stroke-dashoffset="0" transform="rotate(-90 55 55)"/>
            <circle cx="55" cy="55" r="40" fill="none" stroke="#fbbf24" stroke-width="16" stroke-dasharray="60 191" stroke-dashoffset="-100" transform="rotate(-90 55 55)"/>
            <circle cx="55" cy="55" r="40" fill="none" stroke="#dc2626" stroke-width="16" stroke-dasharray="40 211" stroke-dashoffset="-160" transform="rotate(-90 55 55)"/>
            <circle cx="55" cy="55" r="40" fill="none" stroke="#2563eb" stroke-width="16" stroke-dasharray="51 200" stroke-dashoffset="-200" transform="rotate(-90 55 55)"/>
            <text x="55" y="50" text-anchor="middle" font-size="13" font-weight="800" fill="#0c1220" font-family="Plus Jakarta Sans,sans-serif">312</text>
            <text x="55" y="63" text-anchor="middle" font-size="8" fill="#6b7280" font-family="Plus Jakarta Sans,sans-serif">patients</text>
          </svg>
          <div class="donut-legend">
            <div class="dl-item"><div class="dl-dot" style="background:#0f766e"></div><span class="dl-lbl">Medical Aid</span><span class="dl-val">40%</span></div>
            <div class="dl-item"><div class="dl-dot" style="background:#fbbf24"></div><span class="dl-lbl">Nutrition</span><span class="dl-val">24%</span></div>
            <div class="dl-item"><div class="dl-dot" style="background:#dc2626"></div><span class="dl-lbl">Mental Health</span><span class="dl-val">16%</span></div>
            <div class="dl-item"><div class="dl-dot" style="background:#2563eb"></div><span class="dl-lbl">Emergency</span><span class="dl-val">20%</span></div>
          </div>
        </div>
        <div class="mini-grid">
          <div class="mini-stat"><div class="v">89%</div><div class="l">Recovery Rate</div></div>
          <div class="mini-stat"><div class="v">14d</div><div class="l">Avg. Stay</div></div>
          <div class="mini-stat"><div class="v">97</div><div class="l">Discharged</div></div>
          <div class="mini-stat"><div class="v">6</div><div class="l">Critical</div></div>
        </div>
      </div>
    </div>

    <div class="two-col">
      <div class="card">
        <div class="card-hd">
          <div class="card-hd-left">
            <div class="card-title">Recent Donations</div>
            <div class="card-sub">Last 24 hours</div>
          </div>
          <a class="card-link" onclick="showPage('donations',null)"><i class="fas fa-arrow-right"></i> View All</a>
        </div>
        <div class="feed-list">
          <div class="feed-row">
            <div class="feed-ava" style="background:#0f766e">AM</div>
            <div class="feed-info"><div class="feed-name">Amara Osei</div><div class="feed-sub"><i class="fas fa-credit-card" style="margin-right:3px"></i>One-time · Credit Card</div></div>
            <span class="badge success"><i class="fas fa-check"></i>Completed</span>
            <div class="feed-amt">$500</div>
          </div>
          <div class="feed-row">
            <div class="feed-ava" style="background:#2563eb">KC</div>
            <div class="feed-info"><div class="feed-name">Kwame Asante</div><div class="feed-sub"><i class="fas fa-building-columns" style="margin-right:3px"></i>Monthly · Bank Transfer</div></div>
            <span class="badge success"><i class="fas fa-check"></i>Completed</span>
            <div class="feed-amt">$1,200</div>
          </div>
          <div class="feed-row">
            <div class="feed-ava" style="background:#d97706">FN</div>
            <div class="feed-info"><div class="feed-name">Fatima Njoku</div><div class="feed-sub"><i class="fas fa-mobile-screen" style="margin-right:3px"></i>One-time · Mobile Money</div></div>
            <span class="badge warning"><i class="fas fa-clock"></i>Pending</span>
            <div class="feed-amt">$250</div>
          </div>
          <div class="feed-row">
            <div class="feed-ava" style="background:#7c3aed">EM</div>
            <div class="feed-info"><div class="feed-name">Emmanuel Mensah</div><div class="feed-sub"><i class="fas fa-building-columns" style="margin-right:3px"></i>Annual · Wire Transfer</div></div>
            <span class="badge success"><i class="fas fa-check"></i>Completed</span>
            <div class="feed-amt">$5,000</div>
          </div>
          <div class="feed-row">
            <div class="feed-ava" style="background:#dc2626">CI</div>
            <div class="feed-info"><div class="feed-name">Corporate: Intex Ltd</div><div class="feed-sub"><i class="fas fa-building-columns" style="margin-right:3px"></i>One-time · Bank Transfer</div></div>
            <span class="badge danger"><i class="fas fa-xmark"></i>Failed</span>
            <div class="feed-amt">$10,000</div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-hd">
          <div class="card-hd-left">
            <div class="card-title">Patient Admissions</div>
            <div class="card-sub">Today's status</div>
          </div>
        </div>
        <div class="ps-list">
          <div class="ps-row">
            <div class="ps-bar" style="background:#0f766e"></div>
            <div class="ps-info"><div class="ps-name">Chidinma A. — F/28</div><div class="ps-detail"><i class="fas fa-bed" style="margin-right:3px"></i>Medical Aid · Ward B</div></div>
            <span class="badge teal">Stable</span>
          </div>
          <div class="ps-row">
            <div class="ps-bar" style="background:#dc2626"></div>
            <div class="ps-info"><div class="ps-name">Musa Ibrahim — M/45</div><div class="ps-detail"><i class="fas fa-heart-pulse" style="margin-right:3px"></i>Emergency · ICU</div></div>
            <span class="badge danger">Critical</span>
          </div>
          <div class="ps-row">
            <div class="ps-bar" style="background:#fbbf24"></div>
            <div class="ps-info"><div class="ps-name">Grace Eze — F/12</div><div class="ps-detail"><i class="fas fa-apple-alt" style="margin-right:3px"></i>Nutrition · Ward A</div></div>
            <span class="badge warning">Monitoring</span>
          </div>
          <div class="ps-row">
            <div class="ps-bar" style="background:#2563eb"></div>
            <div class="ps-info"><div class="ps-name">Tunde Bakare — M/33</div><div class="ps-detail"><i class="fas fa-brain" style="margin-right:3px"></i>Mental Health · Clinic C</div></div>
            <span class="badge info">In Session</span>
          </div>
          <div class="ps-row">
            <div class="ps-bar" style="background:#059669"></div>
            <div class="ps-info"><div class="ps-name">Adaeze Nwosu — F/19</div><div class="ps-detail"><i class="fas fa-bed" style="margin-right:3px"></i>Medical Aid · Ward B</div></div>
            <span class="badge success">Discharged</span>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- ══════════════════════════════
       DONATIONS
  ══════════════════════════════ -->
  <div class="content" id="page-donations">
    <div class="stats-grid">
      <div class="stat-card t1">
        <div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-dollar-sign"></i></div><span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i>18.4%</span></div>
        <div class="stat-value">$248.5K</div><div class="stat-label">Total Raised</div>
      </div>
      <div class="stat-card t4">
        <div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-receipt"></i></div><span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i>54 this week</span></div>
        <div class="stat-value">1,842</div><div class="stat-label">Total Transactions</div>
      </div>
      <div class="stat-card t2">
        <div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-hourglass-half"></i></div><span class="stat-trend neutral">Needs action</span></div>
        <div class="stat-value">12</div><div class="stat-label">Pending Review</div>
      </div>
      <div class="stat-card t3">
        <div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-circle-xmark"></i></div><span class="stat-trend down"><i class="fas fa-arrow-down"></i>3 txns</span></div>
        <div class="stat-value">$3,200</div><div class="stat-label">Failed / Reversed</div>
      </div>
    </div>

    <div class="card" style="margin-bottom:18px">
      <div class="card-hd">
        <div class="card-hd-left"><div class="card-title">Active Campaigns</div></div>
        <button class="btn-primary"><i class="fas fa-plus"></i> New Campaign</button>
      </div>
      <div class="campaign-grid">
        <div class="campaign-card">
          <div class="cmp-top"><div><div class="cmp-name">Medical Equipment Fund</div><div class="cmp-date"><i class="far fa-calendar" style="margin-right:3px"></i>Ends Dec 31, 2025</div></div><span class="badge success">Active</span></div>
          <div class="cmp-nums"><span>$42,000 raised</span><span>$60,000 goal</span></div>
          <div class="prog-wrap"><div class="prog-bar" style="width:70%"></div></div>
          <div class="cmp-pct" style="color:var(--brand)">70% funded</div>
        </div>
        <div class="campaign-card">
          <div class="cmp-top"><div><div class="cmp-name">Child Nutrition Program</div><div class="cmp-date"><i class="far fa-calendar" style="margin-right:3px"></i>Ends Mar 15, 2026</div></div><span class="badge success">Active</span></div>
          <div class="cmp-nums"><span>$18,500 raised</span><span>$25,000 goal</span></div>
          <div class="prog-wrap"><div class="prog-bar" style="width:74%"></div></div>
          <div class="cmp-pct" style="color:var(--brand)">74% funded</div>
        </div>
        <div class="campaign-card">
          <div class="cmp-top"><div><div class="cmp-name">Emergency Relief 2025</div><div class="cmp-date"><i class="far fa-clock" style="margin-right:3px"></i>Ongoing</div></div><span class="badge warning">Urgent</span></div>
          <div class="cmp-nums"><span>$8,200 raised</span><span>$50,000 goal</span></div>
          <div class="prog-wrap"><div class="prog-bar urgent" style="width:16%"></div></div>
          <div class="cmp-pct" style="color:var(--rose)">16% funded</div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="toolbar">
        <div class="search-box"><i class="fas fa-search"></i><input placeholder="Search donations…"/></div>
        <button class="filter-btn"><i class="far fa-calendar"></i> Date Range</button>
        <button class="filter-btn"><i class="fas fa-credit-card"></i> Method</button>
        <button class="filter-btn"><i class="fas fa-circle-half-stroke"></i> Status</button>
        <button class="btn-primary ml"><i class="fas fa-download"></i> Export CSV</button>
      </div>
      <div style="overflow-x:auto">
        <table class="data-table">
          <thead>
            <tr><th>Donor</th><th>Amount</th><th>Campaign</th><th>Method</th><th>Date</th><th>Status</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <tr>
              <td><div class="cell-user"><div class="cell-ava" style="background:#0f766e">AM</div><div><span class="cell-name">Amara Osei</span><span class="cell-sub">amara@email.com</span></div></div></td>
              <td><strong>$500.00</strong></td><td>Medical Equipment</td>
              <td><span class="badge info"><i class="fas fa-credit-card"></i>Card</span></td>
              <td class="mono">May 7, 2025</td>
              <td><span class="badge success"><i class="fas fa-check"></i>Completed</span></td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button></div></td>
            </tr>
            <tr>
              <td><div class="cell-user"><div class="cell-ava" style="background:#2563eb">KC</div><div><span class="cell-name">Kwame Asante</span><span class="cell-sub">kwame@corp.com</span></div></div></td>
              <td><strong>$1,200.00</strong></td><td>Child Nutrition</td>
              <td><span class="badge neutral"><i class="fas fa-building-columns"></i>Bank</span></td>
              <td class="mono">May 7, 2025</td>
              <td><span class="badge success"><i class="fas fa-check"></i>Completed</span></td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button></div></td>
            </tr>
            <tr>
              <td><div class="cell-user"><div class="cell-ava" style="background:#d97706">FN</div><div><span class="cell-name">Fatima Njoku</span><span class="cell-sub">f.njoku@mail.ng</span></div></div></td>
              <td><strong>$250.00</strong></td><td>Emergency Relief</td>
              <td><span class="badge violet"><i class="fas fa-mobile-screen"></i>Mobile</span></td>
              <td class="mono">May 6, 2025</td>
              <td><span class="badge warning"><i class="fas fa-clock"></i>Pending</span></td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button><button class="action-btn del"><i class="fas fa-trash"></i></button></div></td>
            </tr>
            <tr>
              <td><div class="cell-user"><div class="cell-ava" style="background:#7c3aed">EM</div><div><span class="cell-name">Emmanuel Mensah</span><span class="cell-sub">e.mensah@org.gh</span></div></div></td>
              <td><strong>$5,000.00</strong></td><td>Medical Equipment</td>
              <td><span class="badge neutral"><i class="fas fa-building-columns"></i>Wire</span></td>
              <td class="mono">May 5, 2025</td>
              <td><span class="badge success"><i class="fas fa-check"></i>Completed</span></td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button></div></td>
            </tr>
            <tr>
              <td><div class="cell-user"><div class="cell-ava" style="background:#dc2626">CI</div><div><span class="cell-name">Intex Ltd</span><span class="cell-sub">giving@intex.com</span></div></div></td>
              <td><strong>$10,000.00</strong></td><td>General Fund</td>
              <td><span class="badge neutral"><i class="fas fa-building-columns"></i>Bank</span></td>
              <td class="mono">May 4, 2025</td>
              <td><span class="badge danger"><i class="fas fa-xmark"></i>Failed</span></td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button><button class="action-btn del"><i class="fas fa-trash"></i></button></div></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="pagination">
        <span class="page-info">Showing 1–5 of 1,842 entries</span>
        <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
        <button class="page-btn on">1</button><button class="page-btn">2</button><button class="page-btn">3</button>
        <button class="page-btn">…</button><button class="page-btn">368</button>
        <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════
       USERS
  ══════════════════════════════ -->
  <div class="content" id="page-users">
    <div class="stats-grid">
      <div class="stat-card t1"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-users"></i></div><span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i>6.2%</span></div><div class="stat-value">2,481</div><div class="stat-label">Total Users</div></div>
      <div class="stat-card t4"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-user-check"></i></div><span class="stat-trend up">84.8%</span></div><div class="stat-value">2,104</div><div class="stat-label">Verified</div></div>
      <div class="stat-card t2"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-user-clock"></i></div><span class="stat-trend neutral">10% of total</span></div><div class="stat-value">248</div><div class="stat-label">Pending Verification</div></div>
      <div class="stat-card t3"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-user-slash"></i></div><span class="stat-trend down"><i class="fas fa-arrow-down"></i>2 this week</span></div><div class="stat-value">129</div><div class="stat-label">Suspended</div></div>
    </div>
    <div class="card">
      <div class="toolbar">
        <div class="search-box"><i class="fas fa-search"></i><input placeholder="Search users…"/></div>
        <button class="filter-btn"><i class="fas fa-tag"></i> Role</button>
        <button class="filter-btn"><i class="fas fa-circle-half-stroke"></i> Status</button>
        <button class="filter-btn"><i class="far fa-calendar"></i> Joined</button>
        <button class="btn-primary ml"><i class="fas fa-user-plus"></i> Add User</button>
      </div>
      <div class="tabs">
        <button class="tab-btn on">All Users</button>
        <button class="tab-btn">Admins</button>
        <button class="tab-btn">Volunteers</button>
        <button class="tab-btn">Donors</button>
        <button class="tab-btn">Staff</button>
      </div>
      <div style="overflow-x:auto">
        <table class="data-table">
          <thead><tr><th>User</th><th>Role</th><th>Location</th><th>Joined</th><th>Last Active</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
            <tr>
              <td><div class="cell-user"><div class="cell-ava" style="background:#0f766e">SA</div><div><span class="cell-name">Super Admin</span><span class="cell-sub">admin@hopeconnect.org</span></div></div></td>
              <td><span class="badge danger"><i class="fas fa-crown"></i>Super Admin</span></td>
              <td><i class="fas fa-location-dot" style="color:var(--soft);margin-right:4px"></i>Lagos, NG</td>
              <td class="mono">Jan 1, 2023</td><td class="mono">Just now</td>
              <td><span class="badge success"><i class="fas fa-circle"></i>Online</span></td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button></div></td>
            </tr>
            <tr>
              <td><div class="cell-user"><div class="cell-ava" style="background:#7c3aed">AK</div><div><span class="cell-name">Aisha Kamara</span><span class="cell-sub">a.kamara@hopeconnect.org</span></div></div></td>
              <td><span class="badge violet"><i class="fas fa-shield"></i>Admin</span></td>
              <td><i class="fas fa-location-dot" style="color:var(--soft);margin-right:4px"></i>Accra, GH</td>
              <td class="mono">Mar 12, 2023</td><td class="mono">2h ago</td>
              <td><span class="badge success"><i class="fas fa-circle"></i>Online</span></td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button><button class="action-btn del"><i class="fas fa-trash"></i></button></div></td>
            </tr>
            <tr>
              <td><div class="cell-user"><div class="cell-ava" style="background:#2563eb">OT</div><div><span class="cell-name">Oluwaseun Taiwo</span><span class="cell-sub">o.taiwo@volunteer.org</span></div></div></td>
              <td><span class="badge info"><i class="fas fa-person-digging"></i>Volunteer</span></td>
              <td><i class="fas fa-location-dot" style="color:var(--soft);margin-right:4px"></i>Abuja, NG</td>
              <td class="mono">Jun 5, 2024</td><td class="mono">1d ago</td>
              <td><span class="badge teal"><i class="fas fa-circle"></i>Active</span></td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button><button class="action-btn del"><i class="fas fa-trash"></i></button></div></td>
            </tr>
            <tr>
              <td><div class="cell-user"><div class="cell-ava" style="background:#d97706">MB</div><div><span class="cell-name">Miriam Boateng</span><span class="cell-sub">m.boateng@email.com</span></div></div></td>
              <td><span class="badge neutral"><i class="fas fa-heart"></i>Donor</span></td>
              <td><i class="fas fa-location-dot" style="color:var(--soft);margin-right:4px"></i>Kumasi, GH</td>
              <td class="mono">Sep 18, 2024</td><td class="mono">5d ago</td>
              <td><span class="badge warning"><i class="fas fa-circle"></i>Away</span></td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button><button class="action-btn del"><i class="fas fa-trash"></i></button></div></td>
            </tr>
            <tr>
              <td><div class="cell-user"><div class="cell-ava" style="background:#dc2626">XY</div><div><span class="cell-name">Xavier Yeboah</span><span class="cell-sub">x.yeboah@staff.org</span></div></div></td>
              <td><span class="badge teal"><i class="fas fa-id-badge"></i>Staff</span></td>
              <td><i class="fas fa-location-dot" style="color:var(--soft);margin-right:4px"></i>Nairobi, KE</td>
              <td class="mono">Feb 2, 2025</td><td class="mono">3w ago</td>
              <td><span class="badge danger"><i class="fas fa-ban"></i>Suspended</span></td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button><button class="action-btn del"><i class="fas fa-trash"></i></button></div></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="pagination">
        <span class="page-info">Showing 1–5 of 2,481 users</span>
        <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
        <button class="page-btn on">1</button><button class="page-btn">2</button><button class="page-btn">3</button>
        <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════
       PATIENTS
  ══════════════════════════════ -->
  <div class="content" id="page-patients">
    <div class="stats-grid">
      <div class="stat-card t1"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-hospital-user"></i></div><span class="stat-trend neutral">Active</span></div><div class="stat-value">312</div><div class="stat-label">Total Active Patients</div></div>
      <div class="stat-card t3"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-triangle-exclamation"></i></div><span class="stat-trend down">Urgent</span></div><div class="stat-value">6</div><div class="stat-label">Critical Cases</div></div>
      <div class="stat-card t4"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-circle-check"></i></div><span class="stat-trend up">This month</span></div><div class="stat-value">97</div><div class="stat-label">Discharged (Month)</div></div>
      <div class="stat-card t2"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-clipboard-list"></i></div><span class="stat-trend neutral">Queued</span></div><div class="stat-value">42</div><div class="stat-label">Awaiting Admission</div></div>
    </div>
    <div class="card">
      <div class="toolbar">
        <div class="search-box"><i class="fas fa-search"></i><input placeholder="Search patients…"/></div>
        <button class="filter-btn"><i class="fas fa-bed"></i> Ward</button>
        <button class="filter-btn"><i class="fas fa-stethoscope"></i> Category</button>
        <button class="filter-btn"><i class="fas fa-circle-half-stroke"></i> Status</button>
        <button class="btn-primary ml"><i class="fas fa-user-plus"></i> Admit Patient</button>
      </div>
      <div style="overflow-x:auto">
        <table class="data-table">
          <thead><tr><th>Patient ID</th><th>Name</th><th>Age/Sex</th><th>Category</th><th>Ward</th><th>Admitted</th><th>Status</th><th>Assigned To</th><th>Actions</th></tr></thead>
          <tbody>
            <tr>
              <td><span class="mono">#PT-0042</span></td>
              <td><div class="cell-user"><div class="cell-ava" style="background:#0f766e">CA</div><div><span class="cell-name">Chidinma Agu</span></div></div></td>
              <td>28 / F</td><td><span class="badge teal"><i class="fas fa-kit-medical"></i>Medical Aid</span></td>
              <td>Ward B</td><td class="mono">May 1, 2025</td>
              <td><span class="badge success">Stable</span></td>
              <td style="color:var(--muted)">Dr. Obi</td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button></div></td>
            </tr>
            <tr>
              <td><span class="mono">#PT-0067</span></td>
              <td><div class="cell-user"><div class="cell-ava" style="background:#dc2626">MI</div><div><span class="cell-name">Musa Ibrahim</span></div></div></td>
              <td>45 / M</td><td><span class="badge danger"><i class="fas fa-siren"></i>Emergency</span></td>
              <td>ICU</td><td class="mono">May 7, 2025</td>
              <td><span class="badge danger">Critical</span></td>
              <td style="color:var(--muted)">Dr. Afolabi</td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button></div></td>
            </tr>
            <tr>
              <td><span class="mono">#PT-0091</span></td>
              <td><div class="cell-user"><div class="cell-ava" style="background:#fbbf24">GE</div><div><span class="cell-name">Grace Eze</span></div></div></td>
              <td>12 / F</td><td><span class="badge warning"><i class="fas fa-apple-whole"></i>Nutrition</span></td>
              <td>Ward A</td><td class="mono">Apr 22, 2025</td>
              <td><span class="badge warning">Monitoring</span></td>
              <td style="color:var(--muted)">Nurse Bello</td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button></div></td>
            </tr>
            <tr>
              <td><span class="mono">#PT-0112</span></td>
              <td><div class="cell-user"><div class="cell-ava" style="background:#2563eb">TB</div><div><span class="cell-name">Tunde Bakare</span></div></div></td>
              <td>33 / M</td><td><span class="badge info"><i class="fas fa-brain"></i>Mental Health</span></td>
              <td>Clinic C</td><td class="mono">Mar 15, 2025</td>
              <td><span class="badge info">In Session</span></td>
              <td style="color:var(--muted)">Dr. Uche</td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button></div></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="pagination">
        <span class="page-info">Showing 1–4 of 312 patients</span>
        <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
        <button class="page-btn on">1</button><button class="page-btn">2</button>
        <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════
       PARTNERS
  ══════════════════════════════ -->
  <div class="content" id="page-partners">
    <div class="stats-grid" style="grid-template-columns:repeat(3,1fr)">
      <div class="stat-card t1"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-handshake"></i></div><span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i>+3</span></div><div class="stat-value">47</div><div class="stat-label">Active Partners</div></div>
      <div class="stat-card t4"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-earth-africa"></i></div></div><div class="stat-value">18</div><div class="stat-label">Countries Covered</div></div>
      <div class="stat-card t2"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-sack-dollar"></i></div></div><div class="stat-value">$1.2M</div><div class="stat-label">Partner Contributions</div></div>
    </div>
    <div class="card">
      <div class="section-hd">
        <div><h2>All Partners</h2><p>Organizations supporting HopeConnect</p></div>
        <button class="btn-primary"><i class="fas fa-plus"></i> Add Partner</button>
      </div>
      <div class="partners-grid">
        <div class="partner-card">
          <div class="partner-logo"><i class="fas fa-building-columns"></i></div>
          <div class="partner-info"><div class="partner-name">UNICEF West Africa</div><div class="partner-type">International Organization</div><div class="partner-since"><i class="far fa-calendar" style="margin-right:3px"></i>Since 2021 · $480,000 contributed</div></div>
          <span class="badge success">Active</span>
        </div>
        <div class="partner-card">
          <div class="partner-logo"><i class="fas fa-hospital"></i></div>
          <div class="partner-info"><div class="partner-name">Lagos State Hospital</div><div class="partner-type">Healthcare Facility</div><div class="partner-since"><i class="far fa-calendar" style="margin-right:3px"></i>Since 2022 · Medical resources</div></div>
          <span class="badge success">Active</span>
        </div>
        <div class="partner-card">
          <div class="partner-logo"><i class="fas fa-landmark"></i></div>
          <div class="partner-info"><div class="partner-name">First Bank Foundation</div><div class="partner-type">Financial Institution</div><div class="partner-since"><i class="far fa-calendar" style="margin-right:3px"></i>Since 2023 · $250,000/yr</div></div>
          <span class="badge success">Active</span>
        </div>
        <div class="partner-card">
          <div class="partner-logo"><i class="fas fa-leaf"></i></div>
          <div class="partner-info"><div class="partner-name">Green Earth NGO</div><div class="partner-type">Environmental Organization</div><div class="partner-since"><i class="far fa-calendar" style="margin-right:3px"></i>Since 2024 · Joint programs</div></div>
          <span class="badge teal">Active</span>
        </div>
        <div class="partner-card">
          <div class="partner-logo"><i class="fas fa-graduation-cap"></i></div>
          <div class="partner-info"><div class="partner-name">EduAfrica Trust</div><div class="partner-type">Education Nonprofit</div><div class="partner-since"><i class="far fa-calendar" style="margin-right:3px"></i>Since 2023 · Scholarship fund</div></div>
          <span class="badge warning">Renewal</span>
        </div>
        <div class="partner-card">
          <div class="partner-logo"><i class="fas fa-plane"></i></div>
          <div class="partner-info"><div class="partner-name">Air Peace Foundation</div><div class="partner-type">Corporate CSR</div><div class="partner-since"><i class="far fa-calendar" style="margin-right:3px"></i>Since 2024 · Logistics support</div></div>
          <span class="badge success">Active</span>
        </div>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════
       BLOG
  ══════════════════════════════ -->
  <div class="content" id="page-blog">
    <div class="section-hd">
      <div><h2>Blog &amp; News Management</h2><p>Create, manage and publish posts</p></div>
      <button class="btn-primary"><i class="fas fa-pen-to-square"></i> New Post</button>
    </div>
    <div class="tabs">
      <button class="tab-btn on">All Posts</button>
      <button class="tab-btn">Published</button>
      <button class="tab-btn">Drafts</button>
      <button class="tab-btn">Scheduled</button>
      <button class="tab-btn">Archived</button>
    </div>
    <div class="blog-grid">
      <div class="blog-card">
        <div class="blog-thumb" style="background:linear-gradient(135deg,var(--brand-bg),var(--brand-dim))"><i class="fas fa-earth-africa" style="color:var(--brand);font-size:2.5rem"></i></div>
        <div class="blog-body">
          <div class="blog-tag"><i class="fas fa-kit-medical" style="margin-right:3px"></i>Healthcare</div>
          <div class="blog-title">How We Treated 200 Children in Remote Communities This Quarter</div>
          <div class="blog-excerpt">Our medical teams reached the most isolated villages, providing essential care to over 200 children...</div>
          <div class="blog-meta">
            <span><i class="fas fa-user"></i>Admin</span>
            <span><i class="far fa-calendar"></i>May 6, 2025</span>
            <span><i class="fas fa-eye"></i>1.2k</span>
            <span class="badge success" style="margin-left:auto">Published</span>
          </div>
        </div>
      </div>
      <div class="blog-card">
        <div class="blog-thumb" style="background:linear-gradient(135deg,var(--amber-bg),#fde68a)"><i class="fas fa-apple-whole" style="color:var(--amber);font-size:2.5rem"></i></div>
        <div class="blog-body">
          <div class="blog-tag"><i class="fas fa-apple-whole" style="margin-right:3px"></i>Nutrition</div>
          <div class="blog-title">Launching Our New Child Nutrition Program in 5 States</div>
          <div class="blog-excerpt">Malnutrition affects millions of children across West Africa. Here's how our new program addresses root causes...</div>
          <div class="blog-meta">
            <span><i class="fas fa-user"></i>Aisha K.</span>
            <span><i class="far fa-calendar"></i>May 3, 2025</span>
            <span><i class="fas fa-eye"></i>845</span>
            <span class="badge success" style="margin-left:auto">Published</span>
          </div>
        </div>
      </div>
      <div class="blog-card">
        <div class="blog-thumb" style="background:linear-gradient(135deg,#ede9fe,#ddd6fe)"><i class="fas fa-brain" style="color:#7c3aed;font-size:2.5rem"></i></div>
        <div class="blog-body">
          <div class="blog-tag"><i class="fas fa-brain" style="margin-right:3px"></i>Mental Health</div>
          <div class="blog-title">Breaking the Stigma: Mental Health Outreach in Urban Areas</div>
          <div class="blog-excerpt">Our counselors are working to bring mental health support to communities where it was once taboo...</div>
          <div class="blog-meta">
            <span><i class="fas fa-user"></i>Dr. Uche</span>
            <span><i class="far fa-calendar"></i>Apr 28, 2025</span>
            <span><i class="fas fa-eye"></i>622</span>
            <span class="badge warning" style="margin-left:auto">Draft</span>
          </div>
        </div>
      </div>
      <div class="blog-card">
        <div class="blog-thumb" style="background:linear-gradient(135deg,#fee2e2,#fecaca)"><i class="fas fa-house-flood-water" style="color:#dc2626;font-size:2.5rem"></i></div>
        <div class="blog-body">
          <div class="blog-tag"><i class="fas fa-siren" style="margin-right:3px"></i>Emergency</div>
          <div class="blog-title">Flood Response 2025: How Your Donations Made a Difference</div>
          <div class="blog-excerpt">When floods devastated three communities in April, your generosity enabled us to respond within hours...</div>
          <div class="blog-meta">
            <span><i class="fas fa-user"></i>Admin</span>
            <span><i class="far fa-calendar"></i>Apr 20, 2025</span>
            <span><i class="fas fa-eye"></i>3.4k</span>
            <span class="badge success" style="margin-left:auto">Published</span>
          </div>
        </div>
      </div>
      <div class="blog-card">
        <div class="blog-thumb" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe)"><i class="fas fa-graduation-cap" style="color:#2563eb;font-size:2.5rem"></i></div>
        <div class="blog-body">
          <div class="blog-tag"><i class="fas fa-graduation-cap" style="margin-right:3px"></i>Education</div>
          <div class="blog-title">50 Scholarships Awarded — Meet This Year's Recipients</div>
          <div class="blog-excerpt">This year we surpassed our scholarship target, awarding 50 full bursaries to exceptional students...</div>
          <div class="blog-meta">
            <span><i class="fas fa-user"></i>Olumide S.</span>
            <span><i class="far fa-calendar"></i>May 10 (Sched.)</span>
            <span class="badge info" style="margin-left:auto">Scheduled</span>
          </div>
        </div>
      </div>
      <div class="blog-card">
        <div class="blog-thumb" style="background:linear-gradient(135deg,#dcfce7,#bbf7d0)"><i class="fas fa-handshake" style="color:#059669;font-size:2.5rem"></i></div>
        <div class="blog-body">
          <div class="blog-tag"><i class="fas fa-handshake" style="margin-right:3px"></i>Partnerships</div>
          <div class="blog-title">New Partnership with UNICEF West Africa Announced</div>
          <div class="blog-excerpt">We are proud to announce a landmark partnership that will expand our reach to 3 new countries...</div>
          <div class="blog-meta">
            <span><i class="fas fa-user"></i>Admin</span>
            <span><i class="far fa-calendar"></i>Apr 14, 2025</span>
            <span><i class="fas fa-eye"></i>2.1k</span>
            <span class="badge success" style="margin-left:auto">Published</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════
       GALLERY
  ══════════════════════════════ -->
  <div class="content" id="page-gallery">
    <div class="section-hd">
      <div><h2>Media Gallery</h2><p>Photos and videos from our programs</p></div>
      <div style="display:flex;gap:8px">
        <button class="btn-secondary"><i class="fas fa-folder-plus"></i> New Album</button>
        <button class="btn-primary"><i class="fas fa-upload"></i> Upload Media</button>
      </div>
    </div>
    <div class="tabs">
      <button class="tab-btn on">All Media</button>
      <button class="tab-btn">Healthcare</button>
      <button class="tab-btn">Nutrition</button>
      <button class="tab-btn">Events</button>
      <button class="tab-btn">Videos</button>
    </div>
    <div class="gallery-grid">
      <div class="gallery-item">
        <div class="g-thumb" style="background:linear-gradient(135deg,#0f766e,#14b8a6)"><i class="fas fa-hospital" style="color:rgba(255,255,255,.8)"></i></div>
        <div class="g-overlay">
          <div class="g-actions"><button class="g-btn"><i class="fas fa-pen"></i></button><button class="g-btn"><i class="fas fa-trash"></i></button></div>
          <div class="g-caption">Medical Camp — March 2025</div>
        </div>
      </div>
      <div class="gallery-item">
        <div class="g-thumb" style="background:linear-gradient(135deg,#d97706,#fbbf24)"><i class="fas fa-apple-whole" style="color:rgba(255,255,255,.8)"></i></div>
        <div class="g-overlay">
          <div class="g-actions"><button class="g-btn"><i class="fas fa-pen"></i></button><button class="g-btn"><i class="fas fa-trash"></i></button></div>
          <div class="g-caption">Nutrition Drive — Kano</div>
        </div>
      </div>
      <div class="gallery-item">
        <div class="g-thumb" style="background:linear-gradient(135deg,#1d4ed8,#3b82f6)"><i class="fas fa-graduation-cap" style="color:rgba(255,255,255,.8)"></i></div>
        <div class="g-overlay">
          <div class="g-actions"><button class="g-btn"><i class="fas fa-pen"></i></button><button class="g-btn"><i class="fas fa-trash"></i></button></div>
          <div class="g-caption">Scholarship Ceremony 2025</div>
        </div>
      </div>
      <div class="gallery-item">
        <div class="g-thumb" style="background:linear-gradient(135deg,#7c3aed,#a78bfa)"><i class="fas fa-handshake" style="color:rgba(255,255,255,.8)"></i></div>
        <div class="g-overlay">
          <div class="g-actions"><button class="g-btn"><i class="fas fa-pen"></i></button><button class="g-btn"><i class="fas fa-trash"></i></button></div>
          <div class="g-caption">UNICEF Partnership Signing</div>
        </div>
      </div>
      <div class="gallery-item">
        <div class="g-thumb" style="background:linear-gradient(135deg,#dc2626,#f87171)"><i class="fas fa-truck-medical" style="color:rgba(255,255,255,.8)"></i></div>
        <div class="g-overlay">
          <div class="g-actions"><button class="g-btn"><i class="fas fa-pen"></i></button><button class="g-btn"><i class="fas fa-trash"></i></button></div>
          <div class="g-caption">Flood Response — April 2025</div>
        </div>
      </div>
      <div class="gallery-item">
        <div class="g-thumb" style="background:linear-gradient(135deg,#059669,#34d399)"><i class="fas fa-seedling" style="color:rgba(255,255,255,.8)"></i></div>
        <div class="g-overlay">
          <div class="g-actions"><button class="g-btn"><i class="fas fa-pen"></i></button><button class="g-btn"><i class="fas fa-trash"></i></button></div>
          <div class="g-caption">Community Garden Project</div>
        </div>
      </div>
      <div class="gallery-item">
        <div class="g-thumb" style="background:linear-gradient(135deg,#0e7490,#22d3ee)"><i class="fas fa-droplet" style="color:rgba(255,255,255,.8)"></i></div>
        <div class="g-overlay">
          <div class="g-actions"><button class="g-btn"><i class="fas fa-pen"></i></button><button class="g-btn"><i class="fas fa-trash"></i></button></div>
          <div class="g-caption">Clean Water Initiative</div>
        </div>
      </div>
      <div class="gallery-item">
        <div class="g-thumb" style="background:linear-gradient(135deg,#92400e,#fbbf24)"><i class="fas fa-star" style="color:rgba(255,255,255,.8)"></i></div>
        <div class="g-overlay">
          <div class="g-actions"><button class="g-btn"><i class="fas fa-pen"></i></button><button class="g-btn"><i class="fas fa-trash"></i></button></div>
          <div class="g-caption">Annual Gala 2024</div>
        </div>
      </div>
    </div>
    <div class="pagination" style="margin-top:18px">
      <span class="page-info">Showing 1–8 of 246 media files</span>
      <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
      <button class="page-btn on">1</button><button class="page-btn">2</button><button class="page-btn">3</button>
      <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
    </div>
  </div>

  <!-- ══════════════════════════════
       SECURITY
  ══════════════════════════════ -->
  <div class="content" id="page-security">
    <div class="stats-grid">
      <div class="stat-card t1"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-server"></i></div><span class="stat-trend up">Healthy</span></div><div class="stat-value">99.8%</div><div class="stat-label">System Uptime</div></div>
      <div class="stat-card t3"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-bell"></i></div><span class="stat-trend down">Review needed</span></div><div class="stat-value">3</div><div class="stat-label">Active Alerts</div></div>
      <div class="stat-card t4"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-shield-halved"></i></div><span class="stat-trend up">This month</span></div><div class="stat-value">1,248</div><div class="stat-label">Blocked Attempts</div></div>
      <div class="stat-card t2"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-key"></i></div></div><div class="stat-value">18</div><div class="stat-label">Active Sessions</div></div>
    </div>
    <div class="security-grid">
      <div class="card">
        <div class="card-hd">
          <div class="card-hd-left"><div class="card-title"><i class="fas fa-bell" style="color:var(--rose);margin-right:6px"></i>Security Alerts</div></div>
          <span class="badge danger">3 Active</span>
        </div>
        <div class="activity-list">
          <div class="act-row">
            <div class="act-icon danger"><i class="fas fa-triangle-exclamation"></i></div>
            <div class="act-body"><span class="act-title">Multiple Failed Login Attempts</span><span class="act-desc">IP: 197.210.84.12 — 14 attempts in 5 minutes</span></div>
            <div class="act-time">2m ago</div>
          </div>
          <div class="act-row">
            <div class="act-icon warn"><i class="fas fa-eye"></i></div>
            <div class="act-body"><span class="act-title">Unusual Admin Access</span><span class="act-desc">Admin accessed from new device in Kenya</span></div>
            <div class="act-time">1h ago</div>
          </div>
          <div class="act-row">
            <div class="act-icon warn"><i class="fas fa-gauge-high"></i></div>
            <div class="act-body"><span class="act-title">API Rate Limit Exceeded</span><span class="act-desc">Donations API — 500 req/min exceeded</span></div>
            <div class="act-time">3h ago</div>
          </div>
          <div class="act-row">
            <div class="act-icon info"><i class="fas fa-certificate"></i></div>
            <div class="act-body"><span class="act-title">SSL Certificate Renewal</span><span class="act-desc">Certificate expires in 14 days — auto-renew on</span></div>
            <div class="act-time">1d ago</div>
          </div>
          <div class="act-row">
            <div class="act-icon login"><i class="fas fa-fire-flame-simple"></i></div>
            <div class="act-body"><span class="act-title">Firewall Rules Updated</span><span class="act-desc">IP blocklist refreshed — 48 IPs added</span></div>
            <div class="act-time">2d ago</div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fas fa-key" style="color:var(--amber);margin-right:6px"></i>Recent Login Activity</div>
        </div>
        <div class="activity-list">
          <div class="act-row">
            <div class="act-icon login"><i class="fas fa-check"></i></div>
            <div class="act-body"><span class="act-title">Super Admin — Lagos, NG</span><span class="act-desc"><i class="fab fa-chrome" style="margin-right:3px"></i>Chrome · Windows · 197.210.10.4</span></div>
            <div class="act-time">Just now</div>
          </div>
          <div class="act-row">
            <div class="act-icon login"><i class="fas fa-check"></i></div>
            <div class="act-body"><span class="act-title">Aisha Kamara — Accra, GH</span><span class="act-desc"><i class="fab fa-safari" style="margin-right:3px"></i>Safari · macOS · 154.68.22.1</span></div>
            <div class="act-time">2h ago</div>
          </div>
          <div class="act-row">
            <div class="act-icon danger"><i class="fas fa-xmark"></i></div>
            <div class="act-body"><span class="act-title">Unknown User — Failed Login</span><span class="act-desc"><i class="fab fa-firefox" style="margin-right:3px"></i>Firefox · Linux · 185.220.101.4</span></div>
            <div class="act-time">4h ago</div>
          </div>
          <div class="act-row">
            <div class="act-icon login"><i class="fas fa-check"></i></div>
            <div class="act-body"><span class="act-title">Olumide Taiwo — Abuja, NG</span><span class="act-desc"><i class="fab fa-android" style="margin-right:3px"></i>Chrome · Android · 105.112.4.21</span></div>
            <div class="act-time">1d ago</div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fas fa-display" style="color:var(--brand);margin-right:6px"></i>System Health</div>
          <span class="badge success"><i class="fas fa-circle-check"></i>All Systems Go</span>
        </div>
        <div class="sys-grid">
          <div class="sys-stat"><div class="sys-val">62%</div><div class="sys-lbl">CPU Usage</div></div>
          <div class="sys-stat"><div class="sys-val">4.2 GB</div><div class="sys-lbl">RAM (of 8 GB)</div></div>
          <div class="sys-stat"><div class="sys-val">248 GB</div><div class="sys-lbl">Storage Used</div></div>
          <div class="sys-stat"><div class="sys-val">24ms</div><div class="sys-lbl">Avg Response</div></div>
          <div class="sys-stat"><div class="sys-val">TLS 1.3</div><div class="sys-lbl">Encryption</div></div>
          <div class="sys-stat"><div class="sys-val">v3.4.1</div><div class="sys-lbl">App Version</div></div>
        </div>
        <div style="margin-top:16px;padding:13px;background:var(--brand-bg);border-radius:9px;border:1px solid var(--brand-dim)">
          <div style="font-size:.8rem;font-weight:700;color:var(--brand);margin-bottom:3px"><i class="fas fa-lock" style="margin-right:5px"></i>2-Factor Authentication</div>
          <div style="font-size:.75rem;color:var(--mid)">Enabled for all admin accounts. Last audit: Apr 30, 2025.</div>
        </div>
      </div>

      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fas fa-shield-halved" style="color:var(--violet);margin-right:6px"></i>Roles &amp; Permissions</div>
          <button class="btn-secondary"><i class="fas fa-pen"></i> Edit Roles</button>
        </div>
        <div style="overflow-x:auto">
          <table class="data-table" style="font-size:.76rem">
            <thead><tr><th>Role</th><th>Users</th><th>Donations</th><th>Patients</th><th>Security</th></tr></thead>
            <tbody>
              <tr><td><span class="badge danger">Super Admin</span></td><td><i class="fas fa-check" style="color:var(--brand)"></i> Full</td><td><i class="fas fa-check" style="color:var(--brand)"></i> Full</td><td><i class="fas fa-check" style="color:var(--brand)"></i> Full</td><td><i class="fas fa-check" style="color:var(--brand)"></i> Full</td></tr>
              <tr><td><span class="badge violet">Admin</span></td><td><i class="fas fa-check" style="color:var(--brand)"></i> Full</td><td><i class="fas fa-check" style="color:var(--brand)"></i> Full</td><td><i class="fas fa-check" style="color:var(--brand)"></i> Full</td><td><i class="fas fa-eye" style="color:var(--blue)"></i> View</td></tr>
              <tr><td><span class="badge info">Staff</span></td><td><i class="fas fa-eye" style="color:var(--blue)"></i> View</td><td><i class="fas fa-eye" style="color:var(--blue)"></i> View</td><td><i class="fas fa-check" style="color:var(--brand)"></i> Full</td><td><i class="fas fa-xmark" style="color:var(--soft)"></i> None</td></tr>
              <tr><td><span class="badge neutral">Volunteer</span></td><td><i class="fas fa-xmark" style="color:var(--soft)"></i> None</td><td><i class="fas fa-xmark" style="color:var(--soft)"></i> None</td><td><i class="fas fa-eye" style="color:var(--blue)"></i> View</td><td><i class="fas fa-xmark" style="color:var(--soft)"></i> None</td></tr>
              <tr><td><span class="badge teal">Donor</span></td><td><i class="fas fa-xmark" style="color:var(--soft)"></i> None</td><td><i class="fas fa-rotate" style="color:var(--amber)"></i> Own</td><td><i class="fas fa-xmark" style="color:var(--soft)"></i> None</td><td><i class="fas fa-xmark" style="color:var(--soft)"></i> None</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════
       SETTINGS
  ══════════════════════════════ -->
  <div class="content" id="page-settings">
    <div class="two-col">
      <div style="display:flex;flex-direction:column;gap:18px">
        <div class="card">
          <div class="card-hd">
            <div class="card-hd-left"><div class="card-title"><i class="fas fa-building" style="margin-right:6px;color:var(--muted)"></i>Organization Profile</div></div>
            <button class="btn-primary"><i class="fas fa-floppy-disk"></i> Save</button>
          </div>
          <div class="form-field"><label class="form-label">Organization Name</label><input class="form-input" value="HopeConnect NGO"/></div>
          <div class="form-field"><label class="form-label">Contact Email</label><input class="form-input" value="info@hopeconnect.org"/></div>
          <div class="form-field"><label class="form-label">Phone</label><input class="form-input" value="+234 800 000 0000"/></div>
          <div class="form-field"><label class="form-label">Headquarters</label><input class="form-input" value="14 Victoria Island, Lagos, Nigeria"/></div>
        </div>
        <div class="card">
          <div class="card-hd"><div class="card-title"><i class="fas fa-credit-card" style="margin-right:6px;color:var(--muted)"></i>Payment Gateways</div></div>
          <div class="gateway-row">
            <div class="gw-left"><div class="gw-icon"><i class="fas fa-bolt" style="color:var(--brand)"></i></div><div><div class="gw-name">Paystack</div><div class="gw-desc">West Africa payments</div></div></div>
            <span class="badge success"><i class="fas fa-plug"></i>Connected</span>
          </div>
          <div class="gateway-row">
            <div class="gw-left"><div class="gw-icon"><i class="fab fa-stripe-s" style="color:#6772e5"></i></div><div><div class="gw-name">Stripe</div><div class="gw-desc">International cards</div></div></div>
            <span class="badge success"><i class="fas fa-plug"></i>Connected</span>
          </div>
          <div class="gateway-row">
            <div class="gw-left"><div class="gw-icon"><i class="fas fa-wave-square" style="color:var(--amber)"></i></div><div><div class="gw-name">Flutterwave</div><div class="gw-desc">Mobile money</div></div></div>
            <span class="badge warning"><i class="fas fa-clock"></i>Pending</span>
          </div>
        </div>
      </div>

      <div style="display:flex;flex-direction:column;gap:18px">
        <div class="card">
          <div class="card-hd"><div class="card-title"><i class="fas fa-bell" style="margin-right:6px;color:var(--muted)"></i>Notification Settings</div></div>
          <div class="notif-row">
            <div><div class="notif-label">New Donation Alerts</div><div class="notif-desc">Get notified for every donation</div></div>
            <div class="toggle-switch"></div>
          </div>
          <div class="notif-row">
            <div><div class="notif-label">Patient Admissions</div><div class="notif-desc">Critical case alerts</div></div>
            <div class="toggle-switch"></div>
          </div>
          <div class="notif-row">
            <div><div class="notif-label">Security Alerts</div><div class="notif-desc">Suspicious activity warnings</div></div>
            <div class="toggle-switch"></div>
          </div>
          <div class="notif-row">
            <div><div class="notif-label">Weekly Reports</div><div class="notif-desc">Email digest every Monday</div></div>
            <div class="toggle-switch off"></div>
          </div>
        </div>
        <div class="card">
          <div class="card-hd"><div class="card-title"><i class="fas fa-globe" style="margin-right:6px;color:var(--muted)"></i>Website Integration</div></div>
          <div class="form-field">
            <label class="form-label">Public API Key</label>
            <div style="display:flex;gap:8px">
              <input class="form-input" value="pk_live_••••••••••••••••••••" style="font-family:monospace;flex:1"/>
              <button class="btn-secondary"><i class="fas fa-copy"></i></button>
            </div>
          </div>
          <div class="form-field">
            <label class="form-label">Webhook URL</label>
            <input class="form-input" value="https://hopeconnect.org/api/webhook"/>
          </div>
          <button class="btn-primary" style="margin-top:4px"><i class="fas fa-rotate"></i> Regenerate Keys</button>
        </div>
      </div>
    </div>
  </div>

</div><!-- /main -->
</div><!-- /app -->

<script>
const PAGES = {
  dashboard:'Dashboard',donations:'Donations',users:'Users',
  patients:'Patients',partners:'Partners',blog:'Blog & News',
  gallery:'Gallery',security:'Security',settings:'Settings'
};

function showPage(id, el) {
  document.querySelectorAll('.content').forEach(c => c.classList.remove('active'));
  const pg = document.getElementById('page-' + id);
  if (pg) pg.classList.add('active');
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  if (el) el.classList.add('active');
  else {
    document.querySelectorAll('.nav-item').forEach(n => {
      if (n.getAttribute('onclick') && n.getAttribute('onclick').includes("'"+id+"'")) n.classList.add('active');
    });
  }
  document.getElementById('pageTitle').textContent = PAGES[id] || id;
  document.getElementById('breadSub').textContent = PAGES[id] || id;
  // Close mobile sidebar
  if (window.innerWidth < 1024) closeMobile();
}

let isCollapsed = false;
let mobileOpen = false;

function toggleSidebar() {
  const sb = document.getElementById('sidebar');
  const mn = document.getElementById('main');
  const ov = document.getElementById('overlay');
  if (window.innerWidth < 1024) {
    mobileOpen = !mobileOpen;
    sb.classList.toggle('mobile-open', mobileOpen);
    ov.classList.toggle('show', mobileOpen);
  } else {
    isCollapsed = !isCollapsed;
    sb.classList.toggle('collapsed', isCollapsed);
    mn.classList.toggle('collapsed', isCollapsed);
  }
}

function closeMobile() {
  mobileOpen = false;
  document.getElementById('sidebar').classList.remove('mobile-open');
  document.getElementById('overlay').classList.remove('show');
}

// Tabs
document.querySelectorAll('.tabs').forEach(group => {
  group.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      group.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('on'));
      btn.classList.add('on');
    });
  });
});

// Toggle switches (settings)
document.querySelectorAll('.toggle-switch').forEach(sw => {
  sw.addEventListener('click', () => sw.classList.toggle('off'));
});

// Responsive: reset sidebar on resize
window.addEventListener('resize', () => {
  if (window.innerWidth >= 1024) {
    document.getElementById('sidebar').classList.remove('mobile-open');
    document.getElementById('overlay').classList.remove('show');
    mobileOpen = false;
  }
});
</script>
</body>
</html>
