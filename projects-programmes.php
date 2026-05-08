<?php
require __DIR__ . "/config/autoload.php";
$page_title = "Projects & Programmes";
$breadcrumb_title = "Projects & Programmes";
$hero_title = "Projects & Programmes";
require __DIR__ . "/includes/header.php";
?>

<section class="wide-tb-100">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="explore-intro-card">
                    <div class="explore-kicker">What We Do</div>
                    <h2 class="mb-3">A clearer way to present interventions, priorities, and measurable outcomes.</h2>
                    <p class="mb-4">This page replaces generic template content with a proper programmes overview. It helps donors, partners, and beneficiaries understand what the organization is actively working on.</p>
                    <a href="donation-page.php" class="btn btn-default">Support a Programme</a>
                </div>
            </div>
            <div class="col-lg-6 mt-4 mt-lg-0">
                <div class="explore-mini-grid">
                    <div class="explore-stat-card">
                        <span class="number">12</span>
                        <p>Community projects tracked through one polished page.</p>
                    </div>
                    <div class="explore-stat-card">
                        <span class="number">4</span>
                        <p>Core focus areas that are easy for admins to update later.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="wide-tb-100 bg-light-gray">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="explore-programme-card h-100">
                    <div class="explore-kicker">Education Access</div>
                    <h3>Scholarships, learning resources, and classroom support.</h3>
                    <p>Use this card for one of your flagship programmes and update it over time with milestones, locations, or success indicators.</p>
                    <ul class="explore-programme-list">
                        <li><i class="icofont-check"></i><span>School materials for underserved children</span></li>
                        <li><i class="icofont-check"></i><span>Teacher support and classroom improvement</span></li>
                        <li><i class="icofont-check"></i><span>Community-led mentorship opportunities</span></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="explore-programme-card h-100">
                    <div class="explore-kicker">Health & Nutrition</div>
                    <h3>Preventive care, food support, and local outreach.</h3>
                    <p>This structure works well for programme pages where the admin may later add metrics, stories, and updated needs.</p>
                    <ul class="explore-programme-list">
                        <li><i class="icofont-check"></i><span>Food and nutrition drives</span></li>
                        <li><i class="icofont-check"></i><span>Community health awareness sessions</span></li>
                        <li><i class="icofont-check"></i><span>Support for vulnerable households</span></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="explore-programme-card h-100">
                    <div class="explore-kicker">Youth Empowerment</div>
                    <h3>Skills, mentorship, and pathways to self-reliance.</h3>
                    <p>Great for vocational training, leadership cohorts, or entrepreneurship support programmes.</p>
                    <ul class="explore-programme-list">
                        <li><i class="icofont-check"></i><span>Skill-building workshops</span></li>
                        <li><i class="icofont-check"></i><span>Youth leadership initiatives</span></li>
                        <li><i class="icofont-check"></i><span>Career readiness support</span></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="explore-programme-card h-100">
                    <div class="explore-kicker">Emergency Response</div>
                    <h3>Prepared support for urgent community needs.</h3>
                    <p>This gives space for disaster response, emergency relief, and seasonal intervention updates.</p>
                    <ul class="explore-programme-list">
                        <li><i class="icofont-check"></i><span>Rapid-response distributions</span></li>
                        <li><i class="icofont-check"></i><span>Volunteer mobilization support</span></li>
                        <li><i class="icofont-check"></i><span>Transparent field reporting</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="wide-tb-100">
    <div class="container">
        <div class="explore-accent-block">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h3 class="mb-2">Why this is better than keeping the old template page</h3>
                    <p class="mb-0">It gives visitors a clear operational picture, keeps the design consistent, and creates a strong base that admins can later expand with live content.</p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <a href="contact-us.php" class="btn btn-default">Talk to Our Team</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . "/includes/footer.php"; ?>
