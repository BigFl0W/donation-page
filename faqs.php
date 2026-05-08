<?php
require __DIR__ . "/config/autoload.php";
$page_title = "FAQs";
$breadcrumb_title = "FAQs";
$hero_title = "Frequently Asked Questions";
require __DIR__ . "/includes/header.php";
?>

<section class="wide-tb-100">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="explore-faq-card">
                    <div class="explore-kicker">Helpful Answers</div>
                    <h2 class="mb-4">Clear information for donors, volunteers, partners, and beneficiaries.</h2>
                    <div class="theme-collapse">
                        <div class="toggle arrow-down">
                            <span class="icon"><i class="icofont-plus"></i></span> How can someone support the organisation?
                        </div>
                        <div class="collapse show">
                            <div class="content">
                                Support can come through donations, sponsorships, volunteering, media partnerships, or programme collaboration. This layout is ready for the admin to replace placeholder answers with real policy-driven guidance later.
                            </div>
                        </div>

                        <div class="toggle">
                            <span class="icon"><i class="icofont-plus"></i></span> Can partners sponsor a specific project or campaign?
                        </div>
                        <div class="collapse">
                            <div class="content">
                                Yes. The Explore section now has dedicated pages that make it easier to direct sponsors toward projects, programmes, and media visibility opportunities.
                            </div>
                        </div>

                        <div class="toggle">
                            <span class="icon"><i class="icofont-plus"></i></span> How will updates be shared with supporters?
                        </div>
                        <div class="collapse">
                            <div class="content">
                                Updates can be published through the gallery, programme pages, news posts, and partner announcements, giving the platform a more transparent structure.
                            </div>
                        </div>

                        <div class="toggle">
                            <span class="icon"><i class="icofont-plus"></i></span> Can the admin edit this section later?
                        </div>
                        <div class="collapse">
                            <div class="content">
                                Yes. This page was redesigned to be a clean starting point, so future admin edits can focus on real content rather than redesigning the layout from scratch.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="explore-contact-card mb-4">
                    <div class="explore-kicker">Need More Help?</div>
                    <h3>Speak with the team directly.</h3>
                    <p>Use this side panel for contact details, response windows, or escalation instructions later.</p>
                    <a href="contact-us.php" class="btn btn-default">Contact Us</a>
                </div>
                <div class="explore-contact-card">
                    <div class="explore-kicker">Popular Topics</div>
                    <ul class="explore-faq-list">
                        <li><i class="icofont-check"></i><span>Donations and receipts</span></li>
                        <li><i class="icofont-check"></i><span>Volunteering and onboarding</span></li>
                        <li><i class="icofont-check"></i><span>Partnership enquiries</span></li>
                        <li><i class="icofont-check"></i><span>Programme eligibility questions</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . "/includes/footer.php"; ?>
