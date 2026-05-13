<?php
declare(strict_types=1);

require_once __DIR__ . '/config/autoload.php';

use App\Database;
use App\Helpers;

$settings = [];
if (Database::available()) {
    $rawSettings = Database::fetchAll("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'volunteer_%'") ?: [];
    foreach ($rawSettings as $row) {
        $settings[(string)$row['setting_key']] = (string)($row['setting_value'] ?? '');
    }
}

$settingValue = static function (array $settings, string $key, string $default): string {
    $value = trim((string)($settings[$key] ?? ''));
    return $value !== '' ? $value : $default;
};

$pageTitle = $settingValue($settings, 'volunteer_page_title', 'Volunteer With Friends at Heart Welfare Initiative');
$pageDescription = $settingValue($settings, 'volunteer_page_description', 'Support outreach, community care, and practical compassion by serving as a volunteer with Friends at Heart Welfare Initiative.');
$heroTitle = $settingValue($settings, 'volunteer_hero_title', 'Volunteer with Friends at Heart Welfare Initiative');
$heroLabel = $settingValue($settings, 'volunteer_hero_label', 'Serve With Us');
$heroDescription = $settingValue($settings, 'volunteer_hero_description', 'Join a compassionate network of volunteers helping children, patients and underserved families through practical community support.');
$heroImage = $settingValue($settings, 'volunteer_hero_image', 'assets/images/about_img.png');
$primaryCtaLabel = $settingValue($settings, 'volunteer_primary_cta_label', 'Apply to Volunteer');
$primaryCtaUrl = $settingValue($settings, 'volunteer_primary_cta_url', 'contact-us');
$secondaryCtaLabel = $settingValue($settings, 'volunteer_secondary_cta_label', 'Speak With Our Team');
$secondaryCtaUrl = $settingValue($settings, 'volunteer_secondary_cta_url', 'contact-us');
$introTitle = $settingValue($settings, 'volunteer_intro_title', 'Where your time can make a real difference');
$introDescription = $settingValue($settings, 'volunteer_intro_description', 'Our volunteers support outreach logistics, beneficiary care, event coordination, fundraising campaigns and administrative follow-through. We welcome people who are dependable, compassionate and ready to serve with dignity.');
$opportunitiesTitle = $settingValue($settings, 'volunteer_opportunities_title', 'Volunteer opportunities');
$opportunitiesIntro = $settingValue($settings, 'volunteer_opportunities_intro', 'Choose the kind of contribution that best matches your strength, schedule and passion.');
$impactTitle = $settingValue($settings, 'volunteer_impact_title', 'Why people volunteer with us');
$impactLinesRaw = $settingValue($settings, 'volunteer_impact_lines', "Serve people directly with empathy and purpose.\nGain meaningful field and community experience.\nJoin a mission-driven team that values accountability and compassion.");
$processTitle = $settingValue($settings, 'volunteer_process_title', 'How joining works');
$processIntro = $settingValue($settings, 'volunteer_process_intro', 'We keep the process simple so committed volunteers can get started clearly and confidently.');
$finalCtaTitle = $settingValue($settings, 'volunteer_final_cta_title', 'Ready to serve with us?');
$finalCtaDescription = $settingValue($settings, 'volunteer_final_cta_description', 'Take the next step and let us know how you would like to contribute your time, energy and skills.');

$opportunities = [];
for ($i = 1; $i <= 3; $i++) {
    $opportunities[] = [
        'title' => $settingValue($settings, "volunteer_opportunity_{$i}_title", [
            1 => 'Community Outreach',
            2 => 'Programme Support',
            3 => 'Events and Campaigns',
        ][$i]),
        'description' => $settingValue($settings, "volunteer_opportunity_{$i}_description", [
            1 => 'Help with field visits, distributions, beneficiary engagement and on-site coordination during community interventions.',
            2 => 'Support school-fee drives, hospital-bill advocacy, case follow-up and everyday programme administration.',
            3 => 'Assist with planning, registrations, storytelling, guest coordination and fundraising events that move the mission forward.',
        ][$i]),
    ];
}

$processSteps = [];
for ($i = 1; $i <= 3; $i++) {
    $processSteps[] = [
        'title' => $settingValue($settings, "volunteer_step_{$i}_title", [
            1 => 'Submit your interest',
            2 => 'Have a short conversation',
            3 => 'Get matched and start serving',
        ][$i]),
        'description' => $settingValue($settings, "volunteer_step_{$i}_description", [
            1 => 'Reach out through the volunteer application link and tell us how you would like to help.',
            2 => 'Our team reviews your interest and discusses your availability, experience and preferred area of service.',
            3 => 'We place you where your contribution fits best and guide you into the next available opportunity.',
        ][$i]),
    ];
}

$impactLines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $impactLinesRaw))));

$page_title = $pageTitle;
$page_description = $pageDescription;
$canonical_url = Helpers::siteUrl('volunteer');
$breadcrumb_title = 'Volunteer';
$hero_title = $heroTitle;
$section_title = 'Explore';
$section_url = 'gallery';

require __DIR__ . '/includes/header.php';
?>
<style>
    .volunteer-hero-card,
    .volunteer-info-card,
    .volunteer-opportunity-card,
    .volunteer-process-card,
    .volunteer-cta-band {
        border-radius: 28px;
        background: #fff;
        box-shadow: 0 24px 60px rgba(49, 35, 30, 0.08);
    }
    .volunteer-hero-card {
        padding: 28px;
    }
    .volunteer-hero-card img {
        width: 100%;
        height: 100%;
        min-height: 360px;
        object-fit: cover;
        border-radius: 24px;
    }
    .volunteer-kicker {
        display: inline-flex;
        align-items: center;
        gap: 14px;
        color: var(--secondary-color);
        font-style: italic;
        font-weight: 700;
        margin-bottom: 18px;
    }
    .volunteer-kicker::before {
        content: "";
        width: 46px;
        height: 2px;
        background: var(--secondary-color);
        display: block;
    }
    .volunteer-display {
        font-family: 'Instrument Serif', serif;
        color: var(--primary-color);
        font-size: clamp(2.5rem, 5vw, 4.4rem);
        line-height: 0.98;
        font-style: italic;
        margin-bottom: 20px;
    }
    .volunteer-lead,
    .volunteer-copy {
        color: #5e6884;
        font-size: 1rem;
        line-height: 1.85;
    }
    .volunteer-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        margin-top: 28px;
    }
    .volunteer-info-card,
    .volunteer-opportunity-card,
    .volunteer-process-card {
        padding: 28px;
        height: 100%;
    }
    .volunteer-opportunity-card i,
    .volunteer-process-card i {
        width: 62px;
        height: 62px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        background: rgba(237, 175, 35, 0.12);
        color: var(--primary-color);
        font-size: 1.55rem;
        margin-bottom: 18px;
    }
    .volunteer-info-card ul {
        list-style: none;
        padding: 0;
        margin: 24px 0 0;
        display: grid;
        gap: 14px;
    }
    .volunteer-info-card li {
        padding: 16px 18px;
        border-radius: 18px;
        background: #f9f6ee;
        color: var(--primary-color);
        font-weight: 600;
    }
    .volunteer-cta-band {
        padding: 36px;
        background: linear-gradient(135deg, #11332a 0%, #1d4d3e 100%);
        color: #fff;
    }
    .volunteer-cta-band h2 {
        color: #fff;
        margin-bottom: 12px;
    }
    .volunteer-cta-band p {
        color: rgba(255,255,255,0.78);
        margin-bottom: 0;
    }
    @media (max-width: 767px) {
        .volunteer-hero-card,
        .volunteer-info-card,
        .volunteer-opportunity-card,
        .volunteer-process-card,
        .volunteer-cta-band {
            padding: 22px;
            border-radius: 22px;
        }
        .volunteer-hero-card img {
            min-height: 260px;
        }
    }
</style>

<section class="wide-tb-100">
    <div class="container">
        <div class="volunteer-hero-card">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <span class="volunteer-kicker"><?php echo Helpers::e($heroLabel); ?></span>
                    <h1 class="volunteer-display"><?php echo Helpers::e($heroTitle); ?></h1>
                    <p class="volunteer-lead"><?php echo Helpers::e($heroDescription); ?></p>
                    <div class="volunteer-actions">
                        <a href="<?php echo Helpers::e($primaryCtaUrl); ?>" class="btn btn-default"><?php echo Helpers::e($primaryCtaLabel); ?></a>
                        <a href="<?php echo Helpers::e($secondaryCtaUrl); ?>" class="btn btn-outline-default"><?php echo Helpers::e($secondaryCtaLabel); ?></a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="<?php echo Helpers::e($heroImage); ?>" alt="<?php echo Helpers::e($heroTitle); ?>">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="wide-tb-100 pt-0">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="volunteer-info-card">
                    <h2 class="heading-main mb-4">
                        <small>Volunteer Page</small>
                    <?php echo Helpers::e($introTitle); ?>
                    </h2>
                    <p class="volunteer-copy"><?php echo Helpers::e($introDescription); ?></p>
                    <?php if ($impactLines): ?>
                        <h4 class="mt-4 mb-3"><?php echo Helpers::e($impactTitle); ?></h4>
                        <ul>
                            <?php foreach ($impactLines as $line): ?>
                                <li><?php echo Helpers::e($line); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="row g-4">
                    <div class="col-12">
                        <h2 class="heading-main mb-2">
                            <small>Opportunities</small>
                            <?php echo Helpers::e($opportunitiesTitle); ?>
                        </h2>
                        <p class="volunteer-copy"><?php echo Helpers::e($opportunitiesIntro); ?></p>
                    </div>
                    <?php
                    $icons = ['charity-love_hearts', 'charity-school_icon', 'charity-gift_box'];
                    foreach ($opportunities as $index => $item):
                    ?>
                        <div class="col-md-4">
                            <div class="volunteer-opportunity-card">
                                <i class="<?php echo Helpers::e($icons[$index] ?? 'charity-love_hearts'); ?>"></i>
                                <h4><?php echo Helpers::e($item['title']); ?></h4>
                                <p class="volunteer-copy mb-0"><?php echo Helpers::e($item['description']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="wide-tb-100 pt-0 bg-light-gray">
    <div class="container">
        <div class="row align-items-end mb-4">
            <div class="col-lg-7">
                <h2 class="heading-main mb-2">
                    <small>Volunteer Journey</small>
                    <?php echo Helpers::e($processTitle); ?>
                </h2>
                <p class="volunteer-copy mb-0"><?php echo Helpers::e($processIntro); ?></p>
            </div>
            <div class="col-lg-5 text-lg-end">
                <a href="<?php echo Helpers::e($primaryCtaUrl); ?>" class="btn btn-outline-default"><?php echo Helpers::e($primaryCtaLabel); ?></a>
            </div>
        </div>
        <div class="row g-4">
            <?php
            $stepIcons = ['icofont-ui-message', 'icofont-users-social', 'icofont-check-circled'];
            foreach ($processSteps as $index => $step):
            ?>
                <div class="col-md-4">
                    <div class="volunteer-process-card">
                        <i class="<?php echo Helpers::e($stepIcons[$index] ?? 'icofont-check-circled'); ?>"></i>
                        <h4><?php echo Helpers::e($step['title']); ?></h4>
                        <p class="volunteer-copy mb-0"><?php echo Helpers::e($step['description']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="wide-tb-100 pt-0">
    <div class="container">
        <div class="volunteer-cta-band">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2><?php echo Helpers::e($finalCtaTitle); ?></h2>
                    <p><?php echo Helpers::e($finalCtaDescription); ?></p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="<?php echo Helpers::e($primaryCtaUrl); ?>" class="btn btn-default"><?php echo Helpers::e($primaryCtaLabel); ?></a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/site-footer.php'; ?>
