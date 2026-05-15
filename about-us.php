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

$defaultCoreValues = implode("\n", [
    "Compassion|We show love, empathy and care to individuals and communities in need.",
    "Integrity|We uphold honesty, accountability and strong moral principles in all we do.",
    "Transparency|We remain open, trustworthy and responsible in our operations and use of resources.",
    "Equality|We believe every individual deserves fairness, dignity and equal opportunity regardless of background or status.",
    "Volunteerism|We encourage selfless service, teamwork and community participation to create lasting impact.",
    "Empowerment|We believe in equipping people with opportunities, support and resources for a better future.",
    "Excellence|We strive for professionalism, quality and impactful service delivery in every outreach and project.",
    "Inclusion|We promote unity, acceptance and equal participation for all members of society.",
    "Teamwork|We believe collaboration, unity and partnership strengthen our impact and mission.",
    "Service To Humanity|We are dedicated to improving lives, restoring hope and supporting the vulnerable through humanitarian service."
]);

$coreValuesRaw = (string)($settings['about_values_list'] ?? $defaultCoreValues);
$coreValues = [];
foreach (preg_split('/\r\n|\r|\n/', $coreValuesRaw) as $line) {
    $line = trim($line);
    if ($line === '') {
        continue;
    }
    $parts = array_map('trim', explode('|', $line, 2));
    $coreValues[] = [
        'title' => $parts[0] ?? '',
        'text' => $parts[1] ?? '',
    ];
}

$defaultStoryLead = "Friends at Heart Welfare Initiative was born from moments that broke our hearts.";
$defaultStoryText = "From the tears of a child sent home from school because there was no one to pay the school fees.\n\nFrom the silent cry of a patient lying in a hospital bed, afraid that he or she could not go home because the medical bills remained unpaid.\n\nFrom the quiet strength of underserved men and women trying to feed their children while hiding their own pain.\n\nWe saw the suffering.\nWe felt it.\nAnd we chose not to look away.\n\nWe are ordinary people with extraordinary compassion, people who believe that no human being should be defined by poverty or abandoned in their moment of greatest need.\n\nWe are the hands that hold when strength is failing.\n\nWe are the voice that speaks when hope feels lost.\n\nWe are the bridge between despair and a second chance.\n\nAs a registered organisation with the Corporate Affairs Commission and the Nigeria Network of NGOs, we stand not only with compassion but also with responsibility, ensuring that every act of kindness is transparent, accountable and truly life-changing.\n\nWe do not just pay school fees; we restore dreams.\n\nWe do not just settle hospital bills; we rescue dignity.\n\nWe do not just empower youths, men, women and underserved communities; we rebuild the future.\n\nAt Friends at Heart Welfare Initiative, love is not something we simply feel, it is something we do.\n\nAnd until no child is sent home from school because of unpaid fees, no patient is detained in a hospital because of unpaid medical bills and no youth, man, woman or underserved community feels forgotten, our hearts will continue to answer the call.";
$defaultMilestones = [
    [
        'year' => '2024',
        'title' => 'Foundation and early community service',
        'desc' => "Official creation of Friends At Heart Welfare Initiative\nDevelopment of the NGO's vision, mission and core values\nRegistration with the Corporate Affairs Commission and other regulatory agencies\nFormation of the leadership and volunteer team\nCharity visit to Ngwa Road Motherless Babies Home, Aba, Abia State\nWelfare visit to Father Basil Motherless Babies Home, Aba, Abia State\nSupport for vulnerable children and widows\nFood and clothing distribution programmes",
    ],
    [
        'year' => '2025',
        'title' => 'Education-focused outreach',
        'desc' => "Love in Action visit to Joy Rita International Foundation, Aba, Abia State\nPayment of school fees for less privileged students\nDistribution of educational materials to less privileged students",
    ],
    [
        'year' => '2026',
        'title' => 'Medical relief, visibility and partnerships',
        'desc' => "Settlement of hospital bills at Abia State Teaching Hospital, Aba, Abia State\nCare and support outreach to Victims of Need Social Home, Aba, Abia State\nSupport for patients with emergency medical needs\nEmotional and welfare support for vulnerable individuals\nHumanitarian outreach visit to Joy Rita Motherless International Foundation, Aba, Abia State\nExpansion of volunteer membership\nBuilding partnerships with individuals, organisations and sponsors\nStrengthening community engagement and participation\nLaunch of social media platforms\nPublication of press releases and humanitarian activities\nCreation of official website and online presence\nEstablishment of professional communication channels\nRegistration with the Nigeria Network of NGOs",
    ],
    [
        'year' => '',
        'title' => '',
        'desc' => '',
    ],
];

$timelineItems = [];
for ($i = 1; $i <= 4; $i++) {
    $defaultMilestone = $defaultMilestones[$i - 1] ?? ['year' => '', 'title' => '', 'desc' => ''];
    $year = trim((string)($settings["about_time_{$i}_year"] ?? $defaultMilestone['year']));
    $title = trim((string)($settings["about_time_{$i}_title"] ?? $defaultMilestone['title']));
    $desc = trim((string)($settings["about_time_{$i}_desc"] ?? $defaultMilestone['desc']));
    if ($year === '' && $title === '' && $desc === '') {
        continue;
    }
    $timelineItems[] = [
        'year' => $year,
        'title' => $title,
        'desc' => $desc,
        'points' => array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $desc)), static fn($line) => $line !== '')),
    ];
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

    /* CORE VALUES */
    .values-v3 { padding: 110px 0; background: linear-gradient(180deg, #fffaf0 0%, #ffffff 100%); }
    .values-header { text-align: center; max-width: 760px; margin: 0 auto 60px; }
    .values-header h2 { font-family: 'Instrument Serif', serif; font-size: 3.3rem; line-height: 1.05; font-style: italic; margin-bottom: 18px; }
    .values-header p { color: #64748b; font-size: 1.05rem; line-height: 1.8; margin: 0; }
    .values-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 22px; }
    .value-card {
        background: #fff;
        border: 1px solid rgba(191, 138, 40, 0.16);
        border-radius: 28px;
        padding: 28px 26px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.06);
        min-height: 100%;
    }
    .value-card h4 { margin-bottom: 10px; font-size: 1.15rem; color: var(--about-dark); font-weight: 800; }
    .value-card p { margin: 0; color: #64748b; line-height: 1.75; }

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

    /* ══════ MILESTONES ══════ */
    .timeline-v3 {
        padding: 120px 0;
        background:
            radial-gradient(circle at top left, rgba(243, 201, 106, 0.10), transparent 22%),
            linear-gradient(180deg, #fffdf8 0%, #ffffff 100%);
    }
    .timeline-shell {
        max-width: 1180px;
        margin: 0 auto;
    }
    .timeline-head {
        display: grid;
        grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
        gap: 36px;
        align-items: end;
        margin-bottom: 52px;
    }
    .timeline-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        color: #996812;
        font-size: 0.84rem;
        font-weight: 800;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        margin-bottom: 18px;
    }
    .timeline-eyebrow::before {
        content: '';
        width: 54px;
        height: 2px;
        border-radius: 999px;
        background: linear-gradient(90deg, #c88e24 0%, rgba(200, 142, 36, 0.15) 100%);
    }
    .timeline-head h2 {
        font-family: 'Instrument Serif', serif;
        font-size: clamp(3rem, 7vw, 4.5rem);
        line-height: 0.95;
        font-style: italic;
        margin: 0;
        color: var(--about-dark);
    }
    .timeline-head p {
        margin: 0;
        color: #64748b;
        font-size: 1.04rem;
        line-height: 1.85;
        max-width: 620px;
    }
    .milestone-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 28px;
    }
    .milestone-card {
        position: relative;
        background: #fff;
        border: 1px solid rgba(191, 138, 40, 0.14);
        border-radius: 34px;
        padding: 34px 30px 30px;
        box-shadow: 0 24px 55px rgba(49,35,30,0.08);
        overflow: hidden;
    }
    .milestone-card::before {
        content: '';
        position: absolute;
        inset: 0 0 auto 0;
        height: 6px;
        background: linear-gradient(90deg, #b8801d 0%, #d9a63e 48%, #f3d28b 100%);
    }
    .milestone-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }
    .milestone-year {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 88px;
        height: 44px;
        padding: 0 18px;
        border-radius: 999px;
        background: rgba(191, 138, 40, 0.10);
        color: #996812;
        font-weight: 800;
        font-size: 1rem;
        letter-spacing: 0.08em;
    }
    .milestone-index {
        color: rgba(49,35,30,0.18);
        font-size: 2.2rem;
        font-weight: 800;
        line-height: 1;
    }
    .milestone-card h3 {
        margin: 0 0 16px;
        font-size: 1.35rem;
        line-height: 1.35;
        color: var(--about-dark);
        font-weight: 800;
    }
    .milestone-points {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 12px;
    }
    .milestone-points li {
        position: relative;
        padding-left: 20px;
        color: #5c6b82;
        line-height: 1.7;
    }
    .milestone-points li::before {
        content: '';
        position: absolute;
        top: 11px;
        left: 0;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #c88e24;
        box-shadow: 0 0 0 4px rgba(200, 142, 36, 0.12);
    }

    @media (max-width: 991px) {
        .hero-v3 { text-align: center; }
        .hero-v3 p { margin: 0 auto 40px; }
        .collage-wrap { display: none; }
        .stats-v3 { grid-template-columns: repeat(2, 1fr); gap: 16px; margin-top: 40px; }
        .story-grid, .quote-card { grid-template-columns: 1fr; }
        .values-grid { grid-template-columns: 1fr; }
        .timeline-head { grid-template-columns: 1fr; gap: 20px; margin-bottom: 38px; }
        .milestone-grid { grid-template-columns: 1fr; }
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
                <h2><?php echo Helpers::e($settings['about_story_title'] ?? 'Who We Are'); ?></h2>
                <p class="lead"><?php echo Helpers::e($settings['about_story_lead'] ?? $defaultStoryLead); ?></p>
                <p class="text"><?php echo nl2br(Helpers::e($settings['about_story_text'] ?? $defaultStoryText)); ?></p>
            </div>
        </div>
    </div>
</section>

<section class="values-v3">
    <div class="container">
        <div class="values-header">
            <h2><?php echo Helpers::e($settings['about_values_title'] ?? 'Our Core Values'); ?></h2>
            <p><?php echo Helpers::e($settings['about_values_intro'] ?? 'The principles that guide how we serve, how we lead and how we remain accountable to the people and communities we support.'); ?></p>
        </div>
        <div class="values-grid">
            <?php foreach ($coreValues as $value): ?>
                <div class="value-card">
                    <h4><?php echo Helpers::e($value['title']); ?></h4>
                    <p><?php echo Helpers::e($value['text']); ?></p>
                </div>
            <?php endforeach; ?>
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
    <div class="container timeline-shell">
        <div class="timeline-head">
            <div>
                <span class="timeline-eyebrow"><?php echo Helpers::e($settings['about_timeline_label'] ?? 'Milestones of Impact'); ?></span>
                <h2><?php echo Helpers::e($settings['about_timeline_title'] ?? 'Our milestones in service, care and community action.'); ?></h2>
            </div>
            <p><?php echo Helpers::e($settings['about_timeline_intro'] ?? 'A growing record of compassionate action, institutional development and community outreach that continues to shape the mission of Friends At Heart Welfare Initiative.'); ?></p>
        </div>
        <div class="milestone-grid">
            <?php foreach ($timelineItems as $index => $item): ?>
            <article class="milestone-card">
                <div class="milestone-meta">
                    <span class="milestone-year"><?php echo Helpers::e($item['year']); ?></span>
                    <span class="milestone-index"><?php echo str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT); ?></span>
                </div>
                <h3><?php echo Helpers::e($item['title']); ?></h3>
                <?php if (!empty($item['points'])): ?>
                <ul class="milestone-points">
                    <?php foreach ($item['points'] as $point): ?>
                    <li><?php echo Helpers::e($point); ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>
