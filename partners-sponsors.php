<?php
require_once 'config/autoload.php';

// Fetch all published partners/sponsors
$allPartners = \App\Database::fetchAll("SELECT * FROM partners WHERE status = 'published' ORDER BY sort_order ASC, name ASC");

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
                    <small style="color: #4e7a64;">Strategic Alliances</small>
                    Leveraging collective strength for sustainable community impact.
                </h1>
                <p class="mb-4" style="font-size: 1.1rem; line-height: 1.7; color: #555;">At Gracious Charity, we believe that systemic change is only possible through deep-rooted institutional partnerships. By aligning with global sponsors and local collaborators, we bridge the gap between resource mobilization and community-led solutions, ensuring every intervention is both scalable and sustainable.</p>
                
                <ul class="explore-check-list mb-4">
                    <li><i class="icofont-check-circled" style="color: #d59b2d;"></i><span>Scale your CSR initiatives through our transparent, field-tested intervention frameworks.</span></li>
                    <li><i class="icofont-check-circled" style="color: #d59b2d;"></i><span>Gain institutional visibility across our multi-channel advocacy and impact reporting.</span></li>
                    <li><i class="icofont-check-circled" style="color: #d59b2d;"></i><span>Access real-time data and field insights to measure the social return on your commitment.</span></li>
                </ul>
                
                <a href="contact-us.php" class="btn btn-default" style="background-color: #d59b2d; border-color: #d59b2d;">Become a Partner</a>
            </div>
            
            <div class="col-lg-6 mt-4 mt-lg-0">
                <div class="explore-accent-block p-4" style="background: transparent; border: none;">
                    <div class="text-center mb-2">
                        <small class="text-uppercase fw-bold" style="letter-spacing: 1px; font-size: 0.7rem; color: #4e7a64;">Institutional Partners</small>
                    </div>
                    
                    <!-- Partner Logo Carousel -->
                    <div class="partner-side-carousel">
                        <div class="partner-side-track is-animated">
                            <?php 
                            if (!empty($allPartners)):
                                // Duplicate items enough to ensure a seamless infinite scroll loop even with 1 partner
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
                                // Fallback placeholders
                                for($i=1; $i<=6; $i++):
                            ?>
                                <div class="partner-side-item">
                                    <div class="d-flex align-items-center justify-content-center" style="width: 110px; height: 90px; margin: 0 8px; background: transparent; border: 1px dashed #ccc; opacity: 0.1; border-radius: 10px;">
                                        <i class="icofont-building-alt" style="font-size: 1.5rem;"></i>
                                    </div>
                                </div>
                            <?php 
                                endfor;
                            endif; ?>
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
                    <p class="mb-0 text-muted">By moving beyond static templates, we provide our partners with a structured framework for visibility and accountability. Every contribution is handled with extreme administrative precision, allowing your organization to focus on what matters: the social return on your investment.</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="explore-stat-card text-white p-5 rounded-4 text-center" style="background-color: #4e7a64;">
                    <span class="display-4 fw-bold d-block mb-2">3</span>
                    <p class="mb-0 fw-bold">Strategic tiers designed for maximum alignment and operational scale.</p>
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
</style>

<?php require_once 'includes/footer.php'; ?>
