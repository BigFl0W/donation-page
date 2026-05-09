<?php
require_once __DIR__ . "/config/autoload.php";
use App\Database;
use App\Helpers;

// Fetch settings
$settings = [];
$rawSettings = Database::fetchAll("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'about_%'");
foreach ($rawSettings as $s) {
    $settings[$s['setting_key']] = $s['setting_value'];
}

// Meta for Header
$page_title = "About Us | " . ($settings['about_hero_label'] ?? 'Our Journey');
$hero_title = ""; 
$section_title = "About Us";
$heroTitleHtml = nl2br(strip_tags((string)($settings['about_hero_title'] ?? 'Building Hope, Restoring Dignity.'), '<br><i><em><strong><span>'));

include "includes/header.php"; 
?>
<!-- Premium About Us CSS -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Instrument+Serif:ital@0;1&family=Cormorant+Garamond:wght@500;600;700&family=Alegreya+Sans+SC:wght@500;700&display=swap" rel="stylesheet">
<style>
    :root {
        --about-gold: var(--primary-color);
        --about-gold-light: #f3c96a;
        --about-dark: var(--bistre-color);
        --about-glass: rgba(255, 255, 255, 0.03);
    }
    body { font-family: 'Plus Jakarta Sans', sans-serif !important; background: #fff; color: #1e293b; overflow-x: hidden; }
    .breadcrumbs-page-wrap { display: none !important; }

    /* ══════ HERO SECTION ══════ */
    .hero-v3 {
        position: relative; padding: 120px 0 100px;
        background:
            radial-gradient(circle at top right, rgba(255,255,255,0.08) 0%, transparent 24%),
            linear-gradient(135deg, #8c6515 0%, #a9781f 36%, #bf8a28 100%);
        color: #fff; overflow: hidden;
    }
    .hero-v3::before {
        content: ''; position: absolute; top: -10%; right: -5%; width: 40%; height: 60%;
        background: radial-gradient(circle, rgba(49,35,30,0.24) 0%, transparent 70%);
        filter: blur(80px); pointer-events: none;
    }
    .hero-v3 .label {
        display: inline-block; padding: 8px 16px; background: rgba(49,35,30,0.18);
        border: 1px solid rgba(255,255,255,0.28); color: #fff7e3;
        border-radius: 99px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase;
        letter-spacing: 2px; margin-bottom: 24px;
    }
    .hero-v3 h1 {
        font-family: 'Instrument Serif', serif; font-size: clamp(3rem, 8vw, 5rem);
        line-height: 1; margin-bottom: 30px; font-style: italic; font-weight: 400;
    }
    .hero-v3 p {
        font-size: 1.2rem; color: rgba(255,247,227,0.9); max-width: 600px; line-height: 1.6; margin-bottom: 40px;
    }
    
    /* ══════ IMAGE COLLAGE ══════ */
    .collage-wrap { position: relative; height: 500px; }
    .c-img {
        position: absolute; border-radius: 24px; overflow: hidden;
        box-shadow: 0 30px 60px rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1);
        transition: transform 0.5s cubic-bezier(0.17, 0.67, 0.83, 0.67);
    }
    .c-img img { width: 100%; height: 100%; object-fit: cover; }
    .c-img.main { width: 350px; height: 450px; left: 10%; top: 0; z-index: 2; transform: rotate(-3deg); }
    .c-img.sec { width: 280px; height: 350px; right: 5%; top: 50px; z-index: 1; transform: rotate(5deg); }
    .c-img.float { width: 200px; height: 200px; left: 40%; bottom: 20px; z-index: 3; transform: rotate(10deg); }
    .c-img:hover { transform: scale(1.05) rotate(0deg); z-index: 10; }

    /* ══════ STATS BAR ══════ */
    .stats-v3 {
        margin-top: -42px; position: relative; z-index: 20;
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 22px;
    }
    .stat-box {
        text-align: center;
        padding: 30px 22px 26px;
        border-radius: 28px;
        background: rgba(255, 248, 235, 0.18);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        box-shadow: 0 18px 40px rgba(49,35,30,0.12);
    }
    .stat-box h2 {
        font-family: 'Cormorant Garamond', serif;
        font-size: clamp(2.6rem, 4vw, 3.4rem);
        font-weight: 600; color: #fff; margin-bottom: 10px; line-height: 1;
        letter-spacing: 0.5px;
    }
    .stat-box p {
        font-family: 'Alegreya Sans SC', sans-serif;
        font-size: 1rem; color: rgba(255,247,227,0.96); font-weight: 700;
        text-transform: none; letter-spacing: 1.6px; margin: 0;
    }

    /* ══════ STORY SECTION ══════ */
    .story-section { padding: 120px 0; background: #fff; }
    .story-grid { display: grid; grid-template-columns: 1fr 1.2fr; gap: 80px; align-items: center; }
    .story-content h2 { font-family: 'Instrument Serif', serif; font-size: 3.5rem; line-height: 1.1; margin-bottom: 30px; font-style: italic; }
    .story-content .lead { font-size: 1.25rem; font-weight: 600; color: var(--about-dark); margin-bottom: 25px; line-height: 1.5; }
    .story-content .text { font-size: 1.05rem; color: #475569; line-height: 1.8; margin-bottom: 40px; }

    /* ══════ QUOTE SECTION ══════ */
    .quote-v3 { padding: 100px 0; background: #f8fafc; }
    .quote-card {
        background: #fff; border-radius: 40px; padding: 60px; display: grid; grid-template-columns: 0.8fr 1.2fr; gap: 60px;
        box-shadow: 0 40px 100px rgba(0,0,0,0.08); border: 1px solid #fff;
    }
    .q-img { border-radius: 30px; overflow: hidden; height: 400px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
    .q-img img { width: 100%; height: 100%; object-fit: cover; }
    .q-content { display: flex; flex-direction: column; justify-content: center; }
    .q-icon { font-size: 4rem; color: var(--about-gold); opacity: 0.2; margin-bottom: 20px; }
    .q-text { font-family: 'Instrument Serif', serif; font-size: 2.2rem; line-height: 1.3; font-style: italic; margin-bottom: 30px; }
    .q-author h4 { font-weight: 800; font-size: 1.2rem; margin-bottom: 5px; }
    .q-author p { color: var(--about-gold); font-weight: 700; font-size: 0.9rem; text-transform: uppercase; }

    /* ══════ TIMELINE ══════ */
    .timeline-v3 { padding: 100px 0; }
    .t-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; position: relative; }
    .t-grid::before { content: ''; position: absolute; top: 15px; left: 0; width: 100%; height: 2px; background: #f1f5f9; z-index: 1; }
    .t-node { position: relative; z-index: 2; padding-top: 50px; }
    .t-dot { 
        position: absolute; top: 8px; left: 0; width: 16px; height: 16px; background: #fff; 
        border: 4px solid var(--about-gold); border-radius: 50%; box-shadow: 0 0 0 5px #fff;
    }
    .t-year { display: block; font-weight: 800; font-size: 1.5rem; color: var(--about-dark); margin-bottom: 15px; }
    .t-node h4 { font-weight: 700; font-size: 1.1rem; margin-bottom: 10px; color: var(--about-dark); }
    .t-node p { font-size: 0.95rem; color: #64748b; line-height: 1.6; }

    @media (max-width: 991px) {
        .hero-v3 { text-align: center; }
        .hero-v3 p { margin: 0 auto 40px; }
        .collage-wrap { display: none; }
        .stats-v3 { grid-template-columns: repeat(2, 1fr); gap: 16px; margin-top: 40px; }
        .story-grid, .quote-card { grid-template-columns: 1fr; }
        .t-grid { grid-template-columns: 1fr; }
        .t-grid::before { display: none; }
    }
</style>

<section class="hero-v3">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <span class="label"><?php echo Helpers::e($settings['about_hero_label'] ?? 'Our Journey'); ?></span>
                <h1><?php echo $heroTitleHtml; ?></h1>
                <p><?php echo Helpers::e($settings['about_hero_desc'] ?? 'We are dedicated to creating a world where everyone has the opportunity to thrive, regardless of their background or circumstances.'); ?></p>
            </div>
            <div class="col-lg-6">
                <div class="collage-wrap">
                    <div class="c-img main"><img src="<?php echo Helpers::e($settings['about_img_1'] ?? 'assets/images/about_img.png'); ?>"></div>
                    <div class="c-img sec"><img src="<?php echo Helpers::e($settings['about_img_2'] ?? 'assets/images/about_img_2.jpg'); ?>"></div>
                    <div class="c-img float"><img src="<?php echo Helpers::e($settings['about_img_3'] ?? 'assets/images/about_img_2.jpg'); ?>"></div>
                </div>
            </div>
        </div>

        <div class="stats-v3">
            <div class="stat-box">
                <h2 class="stat-num"><?php echo Helpers::e($settings['about_stat_1_val'] ?? '14+'); ?></h2>
                <p><?php echo Helpers::e($settings['about_stat_1_label'] ?? 'Years of Service'); ?></p>
            </div>
            <div class="stat-box">
                <h2 class="stat-num"><?php echo Helpers::e($settings['about_stat_2_val'] ?? '50K+'); ?></h2>
                <p><?php echo Helpers::e($settings['about_stat_2_label'] ?? 'Lives Impacted'); ?></p>
            </div>
            <div class="stat-box">
                <h2 class="stat-num"><?php echo Helpers::e($settings['about_stat_3_val'] ?? '12+'); ?></h2>
                <p><?php echo Helpers::e($settings['about_stat_3_label'] ?? 'Active Programs'); ?></p>
            </div>
            <div class="stat-box">
                <h2 class="stat-num"><?php echo Helpers::e($settings['about_stat_4_val'] ?? '100%'); ?></h2>
                <p><?php echo Helpers::e($settings['about_stat_4_label'] ?? 'Direct Giving'); ?></p>
            </div>
        </div>
    </div>
</section>

<section class="story-section">
    <div class="container">
        <div class="story-grid">
            <div class="story-img" style="border-radius:40px; overflow:hidden; box-shadow:0 30px 80px rgba(0,0,0,0.1)">
                <img src="<?php echo Helpers::e($settings['about_story_img'] ?? 'assets/images/about_img.png'); ?>" style="width:100%; height:600px; object-fit:cover">
            </div>
            <div class="story-content">
                <h2><?php echo Helpers::e($settings['about_story_title'] ?? 'The Story Behind Our Mission'); ?></h2>
                <p class="lead"><?php echo Helpers::e($settings['about_story_lead'] ?? 'Founded with a single vision to help those in need, we have grown into a community of thousands.'); ?></p>
                <p class="text"><?php echo nl2br(Helpers::e($settings['about_story_text'] ?? "Our journey began over a decade ago when a small group of volunteers decided to take action against the growing inequality in their local community.\n\nToday, we operate in multiple regions, providing healthcare, education, and economic empowerment to those who need it most. We believe in transparency, local leadership, and sustainable impact.")); ?></p>
            </div>
        </div>
    </div>
</section>

<section class="quote-v3">
    <div class="container">
        <div class="quote-card">
            <div class="q-img"><img src="<?php echo Helpers::e($settings['about_founder_img'] ?? 'assets/images/user-1.jpg'); ?>"></div>
            <div class="q-content">
                <i class="fas fa-quote-left q-icon"></i>
                <div class="q-text"><?php echo Helpers::e($settings['about_quote_text'] ?? 'The best way to find yourself is to lose yourself in the service of others. We started with a simple vision, and today we are a global family.'); ?></div>
                <div class="q-author">
                    <h4><?php echo Helpers::e($settings['about_quote_author'] ?? 'Dr. Sarah Johnson'); ?></h4>
                    <p><?php echo Helpers::e($settings['about_quote_role'] ?? 'Founder & CEO'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="timeline-v3">
    <div class="container">
        <div style="text-align:center; margin-bottom:80px;">
            <h2 style="font-family:'Instrument Serif', serif; font-size:3.5rem; font-style:italic;">Our Milestones</h2>
            <p style="color:#64748b; font-weight:500;">A journey through time and impact.</p>
        </div>
        <div class="t-grid">
            <?php for($i=1; $i<=4; $i++): ?>
            <div class="t-node">
                <div class="t-dot"></div>
                <span class="t-year"><?php echo Helpers::e($settings["about_time_{$i}_year"] ?? (2010 + ($i-1)*4)); ?></span>
                <h4><?php echo Helpers::e($settings["about_time_{$i}_title"] ?? "Milestone $i"); ?></h4>
                <p><?php echo Helpers::e($settings["about_time_{$i}_desc"] ?? "Describing the major achievement or milestone reached during this period."); ?></p>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>
