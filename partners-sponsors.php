<?php
require_once 'config/autoload.php';

// Fetch all published partners/sponsors
$allPartners = \App\Database::fetchAll("SELECT * FROM partners WHERE status = 'published' ORDER BY sort_order ASC, name ASC");
$rawPartnerSettings = \App\Database::fetchAll("SELECT setting_key, setting_value FROM settings WHERE setting_group = 'partners'") ?: [];
$partnerSettings = [];
foreach ($rawPartnerSettings as $settingRow) {
    $partnerSettings[(string)($settingRow['setting_key'] ?? '')] = (string)($settingRow['setting_value'] ?? '');
}
$partnerSetting = static function (string $key, string $default = '') use ($partnerSettings): string {
    $value = trim((string)($partnerSettings['partners_' . $key] ?? ''));
    return $value !== '' ? $value : $default;
};
$registrationBenefits = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $partnerSetting(
    'registration_benefits_list',
    "Build credibility through recognised NGO membership.\nAccess networking, advocacy and sector learning opportunities.\nUnderstand the available membership categories before applying.\nGive prospective partners one trusted registration path."
)) ?: [])));
$registrationResources = [];
for ($resourceIndex = 1; $resourceIndex <= 4; $resourceIndex++) {
    $resourceDefaults = [
        1 => ['label' => 'Membership Overview', 'url' => 'https://nnngo.org/membership-2/'],
        2 => ['label' => 'Membership Benefits', 'url' => 'https://nnngo.org/membership-benefits/'],
        3 => ['label' => 'Membership Category', 'url' => 'https://nnngo.org/membership-category/'],
        4 => ['label' => 'Join Now', 'url' => 'https://nnngo.org/join-now/'],
    ];
    $label = $partnerSetting("registration_resource_{$resourceIndex}_label", $resourceDefaults[$resourceIndex]['label']);
    $url = $partnerSetting("registration_resource_{$resourceIndex}_url", $resourceDefaults[$resourceIndex]['url']);
    if ($label !== '' && $url !== '') {
        $registrationResources[] = ['label' => $label, 'url' => $url];
    }
}
$trackedLink = static function (string $url, string $source): string {
    return 'partner-link.php?source=' . rawurlencode($source) . '&url=' . rawurlencode($url);
};

$page_title = "Partners & Sponsors";
$breadcrumb_title = "Partners & Sponsors";
$hero_title = "Partners & Sponsors";

require_once 'includes/header.php';
?>

<section class="wide-tb-100">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="heading-main">
                    <small style="color: #4e7a64;"><?php echo htmlspecialchars($partnerSetting('registration_kicker', 'Partner Registration')); ?></small>
                    <?php echo htmlspecialchars($partnerSetting('registration_title', 'Register with the Nigeria Network of NGOs through the official membership pathway.')); ?>
                </h1>
                <p class="mb-4" style="font-size: 1.1rem; line-height: 1.7; color: #555;"><?php echo nl2br(htmlspecialchars($partnerSetting('registration_description', 'We encourage prospective institutional partners to register through the Nigeria Network of NGOs so the relationship begins on a foundation of credibility, accountability and sector-wide collaboration.'))); ?></p>

                <div class="explore-kicker fw-bold mb-3" style="font-size: 0.9rem; letter-spacing: 0.4px; color: #1f3b2f;"><?php echo htmlspecialchars($partnerSetting('registration_benefits_title', 'Why register')); ?></div>
                <ul class="explore-check-list mb-4">
                    <?php foreach ($registrationBenefits as $benefit): ?>
                    <li><i class="icofont-check-circled" style="color: #d59b2d;"></i><span><?php echo htmlspecialchars($benefit); ?></span></li>
                    <?php endforeach; ?>
                </ul>

                <div style="display:flex; flex-wrap:wrap; gap:14px; align-items:center;">
                    <a href="<?php echo htmlspecialchars($trackedLink($partnerSetting('registration_primary_url', 'https://nnngo.org/join-now/'), 'primary_cta')); ?>" class="btn btn-default" style="background-color: #d59b2d; border-color: #d59b2d;" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars($partnerSetting('registration_primary_label', 'Join NNNGO')); ?></a>
                    <a href="<?php echo htmlspecialchars($trackedLink($partnerSetting('registration_secondary_url', 'https://nnngo.org/membership-benefits/'), 'secondary_cta')); ?>" class="btn btn-outline-dark" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars($partnerSetting('registration_secondary_label', 'View Membership Benefits')); ?></a>
                </div>
            </div>
            
            <div class="col-lg-6 mt-4 mt-lg-0">
                <div class="explore-accent-block p-4 rounded-4 shadow-sm" style="background: #fff; border: 1px solid #f0ece5;">
                    <div class="text-center mb-3">
                        <small class="text-uppercase fw-bold" style="letter-spacing: 1px; font-size: 0.7rem; color: #4e7a64;"><?php echo htmlspecialchars($partnerSetting('registration_resources_title', 'Helpful registration links')); ?></small>
                    </div>

                    <div class="row g-3">
                        <?php foreach ($registrationResources as $resource): ?>
                        <div class="col-sm-6">
                            <a href="<?php echo htmlspecialchars($trackedLink($resource['url'], $resource['label'])); ?>" target="_blank" rel="noopener noreferrer" class="partner-resource-card">
                                <span><?php echo htmlspecialchars($resource['label']); ?></span>
                                <i class="icofont-rounded-right"></i>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="text-center my-4">
                        <small class="text-uppercase fw-bold" style="letter-spacing: 1px; font-size: 0.7rem; color: #4e7a64;">Institutional Partners</small>
                    </div>

                    <div class="partner-side-carousel">
                        <div class="partner-side-track is-animated">
                            <?php 
                            if (!empty($allPartners)):
                                $displayItems = $allPartners;
                                if (count($allPartners) < 10) {
                                    $displayItems = array_merge($allPartners, $allPartners, $allPartners, $allPartners, $allPartners);
                                }
                                foreach ($displayItems as $p):
                            ?>
                            <div class="partner-side-item">
                                <div class="d-flex align-items-center justify-content-center p-1" style="width: 110px; height: 90px; margin: 0 8px; background: transparent;">
                                    <img src="<?php echo htmlspecialchars($p['logo_path'] ?: 'assets/images/clients/client1.png'); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                </div>
                            </div>
                            <?php
                                endforeach;
                            else:
                                for($i = 1; $i <= 6; $i++):
                            ?>
                            <div class="partner-side-item">
                                <div class="d-flex align-items-center justify-content-center" style="width: 110px; height: 90px; margin: 0 8px; background: transparent; border: 1px dashed #ccc; opacity: 0.1; border-radius: 10px;">
                                    <i class="icofont-building-alt" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                            <?php
                                endfor;
                            endif;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tiers Section -->
<section class="wide-tb-100 bg-light-gray">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="explore-tier-card h-100 bg-white p-5 rounded-4 shadow-sm border-0">
                    <div class="explore-kicker fw-bold text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 1px; color: #d59b2d;">Lead Sponsors</div>
                    <h4 class="fw-bold mb-3">Strategic visibility with measurable impact.</h4>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="icofont-check me-2" style="color: #4e7a64;"></i> Brand placement on flagship campaigns</li>
                        <li class="mb-2"><i class="icofont-check me-2" style="color: #4e7a64;"></i> Impact storytelling in media materials</li>
                        <li class="mb-2"><i class="icofont-check me-2" style="color: #4e7a64;"></i> Priority recognition at major events</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="explore-tier-card h-100 bg-white p-5 rounded-4 shadow-sm border-0">
                    <div class="explore-kicker fw-bold text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 1px; color: #d59b2d;">Programme Partners</div>
                    <h4 class="fw-bold mb-3">Long-term collaboration around social outcomes.</h4>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="icofont-check me-2" style="color: #4e7a64;"></i> Joint project delivery and reporting</li>
                        <li class="mb-2"><i class="icofont-check me-2" style="color: #4e7a64;"></i> Shared field storytelling and updates</li>
                        <li class="mb-2"><i class="icofont-check me-2" style="color: #4e7a64;"></i> Community visibility and recognition</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="explore-tier-card h-100 bg-white p-5 rounded-4 shadow-sm border-0">
                    <div class="explore-kicker fw-bold text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 1px; color: #d59b2d;">Community Sponsors</div>
                    <h4 class="fw-bold mb-3">Support specific campaigns with a clear story.</h4>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="icofont-check me-2" style="color: #4e7a64;"></i> Campaign-by-campaign visibility</li>
                        <li class="mb-2"><i class="icofont-check me-2" style="color: #4e7a64;"></i> Social media acknowledgment</li>
                        <li class="mb-2"><i class="icofont-check me-2" style="color: #4e7a64;"></i> Simple onboarding for new sponsors</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why It Works Section -->
<section class="wide-tb-100">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <div class="explore-contact-card p-5 rounded-4" style="background: #fff; border: 1px solid #f0f0f0;">
                    <div class="explore-kicker text-muted text-uppercase mb-2" style="font-size: 0.7rem; letter-spacing: 1px;">Institutional Credibility</div>
                    <h3 class="fw-bold mb-3">Professional stewardship ensures long-term mission success.</h3>
                    <p class="mb-0 text-muted">We guide prospective partners through a credible registration path, communicate expectations clearly and build every collaboration on transparency, accountability and shared social impact.</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="explore-stat-card text-white p-5 rounded-4 text-center" style="background-color: #4e7a64;">
                    <span class="display-4 fw-bold d-block mb-2">3</span>
                    <p class="mb-0 fw-bold">Helpful membership resources for partner onboarding.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .partner-side-carousel {
        overflow: hidden;
        width: 100%;
        position: relative;
        padding: 15px 0;
    }
    .partner-side-track {
        display: flex;
        width: max-content;
        align-items: center;
    }
    .partner-side-track.is-animated {
        animation: sideScroll 40s linear infinite;
    }
    .partner-side-track.is-animated:hover {
        animation-play-state: paused;
    }
    @keyframes sideScroll {
        0% { transform: translateX(0); }
        100% { transform: translateX(-40%); }
    }
    .heading-main small {
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 2px;
        display: block;
        margin-bottom: 10px;
    }
    .explore-check-list {
        list-style: none;
        padding: 0;
    }
    .explore-check-list li {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 15px;
        font-weight: 500;
        color: #444;
    }
    .explore-check-list li i {
        font-size: 1.4rem;
        margin-top: 2px;
    }
    .partner-resource-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 18px;
        border-radius: 16px;
        background: #f8f5ef;
        border: 1px solid #efe6d8;
        color: #2f2f2f;
        font-weight: 600;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .partner-resource-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
        color: #2f2f2f;
    }
    .partner-resource-card i {
        color: #d59b2d;
        font-size: 1rem;
    }
</style>

<?php require_once 'includes/footer.php'; ?>
