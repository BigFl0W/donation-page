<?php
declare(strict_types=1);

use App\Database;
use App\Helpers;

$brand_name = Helpers::brandName("Gracious Charity");
$brand_logo_footer = Helpers::siteUrl(Helpers::brandLogoPath("assets/images/logo_white.svg"));

$footerSettings = [];
if (class_exists(Database::class) && Database::available()) {
    $rawFooter = Database::fetchAll("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'footer_%'") ?: [];
    foreach ($rawFooter as $setting) {
        $footerSettings[(string)$setting["setting_key"]] = (string)($setting["setting_value"] ?? "");
    }
}

$footerLabel = $footerSettings["footer_newsletter_label"] ?? "Stay Connected";
$footerTitle = $footerSettings["footer_newsletter_title"] ?? "Get updates on outreach, events, and impact stories.";
$footerButton = $footerSettings["footer_newsletter_button"] ?? "Join Newsletter";
$footerBrandName = trim((string)($footerSettings["footer_brand_name"] ?? ""));
$footerBrandText = $footerSettings["footer_brand_text"] ?? "We create credible programmes, visible impact, and trusted partnerships that supporters can follow with confidence.";
$footerAddress = $footerSettings["footer_address"] ?? "13 Charity Avenue, Lagos, Nigeria";
$footerPhone = $footerSettings["footer_phone"] ?? ($footerSettings["footer_cta_phone"] ?? "+1234567899");
$footerEmail = $footerSettings["footer_email"] ?? "info@graciouscharity.org";
$footerHours = $footerSettings["footer_hours"] ?? "Mon-Fri / 9:00 AM - 6:00 PM";
$footerLinksTitle = $footerSettings["footer_links_title"] ?? "Quick Links";
$footerNoteTitle = $footerSettings["footer_note_title"] ?? "Support the Mission";
$footerNoteText = $footerSettings["footer_note_text"] ?? "Support our programmes, follow new stories, and stay close to the work happening in communities.";
$footerNoteButton = $footerSettings["footer_note_button"] ?? "Donate Now";
$footerNoteUrl = $footerSettings["footer_note_url"] ?? "donation-page.php";
$footerCtaTitle = $footerSettings["footer_cta_title"] ?? "Give us a call";
$footerCtaPhone = $footerSettings["footer_cta_phone"] ?? $footerPhone;
$footerCopyright = $footerSettings["footer_copyright"] ?? "Gracious Charity";

$quickLinks = [];
for ($i = 1; $i <= 4; $i++) {
    $label = trim((string)($footerSettings["footer_link_{$i}_label"] ?? ""));
    $url = trim((string)($footerSettings["footer_link_{$i}_url"] ?? ""));
    if ($label !== "" && $url !== "") {
        $quickLinks[] = ["label" => $label, "url" => $url];
    }
}
if ($quickLinks === []) {
    $quickLinks = [
        ["label" => "About Us", "url" => "about-us.php"],
        ["label" => "Events", "url" => "events.php"],
        ["label" => "Blog", "url" => "blog.php"],
        ["label" => "Contact", "url" => "contact-us.php"],
    ];
}

$bottomLinks = [];
for ($i = 1; $i <= 3; $i++) {
    $label = trim((string)($footerSettings["footer_bottom_link_{$i}_label"] ?? ""));
    $url = trim((string)($footerSettings["footer_bottom_link_{$i}_url"] ?? ""));
    if ($label !== "" && $url !== "") {
        $bottomLinks[] = ["label" => $label, "url" => $url];
    }
}
if ($bottomLinks === []) {
    $bottomLinks = [
        ["label" => "About", "url" => "about-us.php"],
        ["label" => "Contact", "url" => "contact-us.php"],
        ["label" => "FAQs", "url" => "faqs.php"],
    ];
}

$socials = [
    ["icon" => "facebook", "url" => trim((string)($footerSettings["footer_social_facebook"] ?? ""))],
    ["icon" => "twitter", "url" => trim((string)($footerSettings["footer_social_twitter"] ?? ""))],
    ["icon" => "instagram", "url" => trim((string)($footerSettings["footer_social_instagram"] ?? ""))],
    ["icon" => "youtube-play", "url" => trim((string)($footerSettings["footer_social_youtube"] ?? ""))],
];
?>
    </main>

    <style>
        .site-footer-v2 {
            background: #11332a; /* Deep Forest Green */
            color: rgba(255, 255, 255, 0.7);
            padding-top: 80px;
            margin-top: 0;
            position: relative;
        }
        .site-footer-v2 .footer-newsletter-row {
            padding-bottom: 60px;
            margin-bottom: 60px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 40px;
        }
        .site-footer-v2 .newsletter-text h2 {
            color: #fff;
            font-family: var(--font-heading);
            font-size: 2.2rem;
            line-height: 1.1;
            margin: 0 0 10px;
            font-weight: 400;
        }
        .site-footer-v2 .newsletter-text p {
            margin: 0;
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.95rem;
        }
        .site-footer-v2 .newsletter-form {
            display: flex;
            width: 100%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 6px;
        }
        .site-footer-v2 .newsletter-form input {
            flex: 1;
            background: transparent;
            border: none;
            color: #fff;
            padding: 10px 16px;
            outline: none;
            font-size: 0.9rem;
        }
        .site-footer-v2 .newsletter-form button {
            background: #edaf23; /* Site Primary Yellow */
            color: #11332a;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 800;
            transition: all 0.3s ease;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
        }
        .site-footer-v2 .newsletter-form button:hover {
            background: #f6c96c;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(237, 175, 35, 0.3);
        }
        .site-footer-v2 .footer-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr 0.8fr 1.1fr;
            gap: 40px;
            padding-bottom: 60px;
        }
        .site-footer-v2 h3.footer-heading {
            color: #fff;
            font-size: 0.82rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .site-footer-v2 h3.footer-heading::after {
            content: "";
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.08);
        }
        .site-footer-v2 .footer-brand-lockup {
            display: flex;
            align-items: center;
            margin-bottom: 22px;
        }
        .site-footer-v2 .footer-brand img {
            margin-bottom: 0;
            flex-shrink: 0;
        }
        .site-footer-v2 .footer-brand-title {
            color: #fff;
            font-family: var(--font-heading);
            font-size: 1.1rem;
            line-height: 1.2;
            font-weight: 700;
            margin: 0 0 0 16px;
            max-width: 220px;
        }
        .site-footer-v2 .footer-brand p {
            font-size: 0.9rem;
            line-height: 1.75;
            margin-bottom: 28px;
        }
        .site-footer-v2 .footer-contact ul,
        .site-footer-v2 .footer-menu ul,
        .site-footer-v2 .footer-socials ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .site-footer-v2 .footer-contact li {
            display: flex;
            gap: 15px;
            margin-bottom: 22px;
            font-size: 0.9rem;
        }
        .site-footer-v2 .footer-contact i {
            color: #edaf23; /* Yellow Accent */
            font-size: 1.1rem;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .site-footer-v2 .footer-contact a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .site-footer-v2 .footer-contact a:hover {
            color: #edaf23;
        }
        .site-footer-v2 .footer-menu li {
            margin-bottom: 14px;
        }
        .site-footer-v2 .footer-menu a {
            color: inherit;
            text-decoration: none;
            font-size: 0.92rem;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }
        .site-footer-v2 .footer-menu a i {
            font-size: 0.6rem;
            color: rgba(255, 255, 255, 0.15);
        }
        .site-footer-v2 .footer-menu a:hover {
            color: #fff;
            padding-left: 8px;
        }
        .site-footer-v2 .footer-note-clean {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .site-footer-v2 .footer-note-clean h4 {
            color: #fff;
            font-size: 1.05rem;
            margin: 0;
            font-weight: 700;
        }
        .site-footer-v2 .footer-note-clean p {
            font-size: 0.88rem;
            line-height: 1.7;
            margin: 0;
        }
        .site-footer-v2 .footer-note-clean .footer-btn {
            background: #edaf23;
            color: #11332a;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            width: fit-content;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.3s ease;
        }
        .site-footer-v2 .footer-note-clean .footer-btn:hover {
            background: #fff;
            color: #11332a;
            transform: translateY(-2px);
        }
        .site-footer-v2 .footer-call-clean {
            margin-top: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: #fff;
        }
        .site-footer-v2 .footer-call-clean .icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(237, 175, 35, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #edaf23;
            flex-shrink: 0;
            font-size: 1.2rem;
        }
        .site-footer-v2 .footer-call-clean small {
            display: block;
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 2px;
        }
        .site-footer-v2 .footer-call-clean strong {
            font-size: 1.2rem;
            font-weight: 700;
        }
        .site-footer-v2 .footer-socials {
            margin-top: 25px;
        }
        .site-footer-v2 .footer-socials ul {
            display: flex;
            gap: 12px;
        }
        .site-footer-v2 .footer-socials a {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        .site-footer-v2 .footer-socials a:hover {
            background: #edaf23;
            color: #11332a;
            transform: translateY(-3px);
        }
        .site-footer-v2 .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding: 30px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.35);
        }
        .site-footer-v2 .footer-bottom-links {
            display: flex;
            gap: 24px;
        }
        .site-footer-v2 .footer-bottom-links a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .site-footer-v2 .footer-bottom-links a:hover {
            color: #fff;
        }
        @media (max-width: 991px) {
            .site-footer-v2 .footer-newsletter-row {
                flex-direction: column;
                align-items: flex-start;
                text-align: left;
            }
            .site-footer-v2 .footer-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
        @media (max-width: 767px) {
            .site-footer-v2 .footer-grid {
                grid-template-columns: 1fr;
            }
            .site-footer-v2 .footer-brand-title {
                font-size: 1rem;
                max-width: none;
            }
            .site-footer-v2 .footer-bottom {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
    </style>

    <footer class="site-footer-v2 wide-tb-70 pb-0">
        <div class="container">
            <div class="footer-newsletter-row">
                <div class="newsletter-text">
                    <h2><?php echo Helpers::e($footerTitle); ?></h2>
                    <p>Join our mission and stay updated with stories of impact and change.</p>
                </div>
                <form class="newsletter-form" onsubmit="event.preventDefault();">
                    <input type="email" placeholder="Enter your email address" aria-label="Email address">
                    <button type="submit"><?php echo Helpers::e($footerButton); ?></button>
                </form>
            </div>

            <div class="footer-grid">
                <div class="footer-col footer-brand">
                    <div class="footer-brand-lockup">
                        <img class="site-logo site-logo--footer" src="<?php echo Helpers::e($brand_logo_footer); ?>" alt="<?php echo Helpers::e($brand_name); ?>">
                        <?php if ($footerBrandName !== ""): ?>
                            <h3 class="footer-brand-title"><?php echo Helpers::e($footerBrandName); ?></h3>
                        <?php endif; ?>
                    </div>
                    <p><?php echo Helpers::e($footerBrandText); ?></p>
                    <div class="footer-socials">
                        <ul>
                            <?php foreach ($socials as $social): ?>
                                <?php if ($social["url"] !== ""): ?>
                                    <li><a href="<?php echo Helpers::e($social["url"]); ?>" target="_blank" rel="noopener"><i class="icofont-<?php echo Helpers::e($social["icon"]); ?>"></i></a></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="footer-col footer-contact">
                    <h3 class="footer-heading">Contact Info</h3>
                    <ul>
                        <li><i data-feather="map-pin"></i><span><?php echo Helpers::e($footerAddress); ?></span></li>
                        <li><i data-feather="phone"></i><a href="tel:<?php echo Helpers::e(preg_replace('/\s+/', '', $footerPhone)); ?>"><?php echo Helpers::e($footerPhone); ?></a></li>
                        <li><i data-feather="mail"></i><a href="mailto:<?php echo Helpers::e($footerEmail); ?>"><?php echo Helpers::e($footerEmail); ?></a></li>
                        <li><i data-feather="clock"></i><span><?php echo Helpers::e($footerHours); ?></span></li>
                    </ul>
                </div>

                <div class="footer-col footer-menu">
                    <h3 class="footer-heading"><?php echo Helpers::e($footerLinksTitle); ?></h3>
                    <ul>
                        <?php foreach ($quickLinks as $link): ?>
                            <li><a href="<?php echo Helpers::e($link["url"]); ?>"><i class="icofont-simple-right"></i> <?php echo Helpers::e($link["label"]); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="footer-col">
                    <div class="footer-note-clean">
                        <h4><?php echo Helpers::e($footerNoteTitle); ?></h4>
                        <p><?php echo Helpers::e($footerNoteText); ?></p>
                        <a href="<?php echo Helpers::e($footerNoteUrl); ?>" class="footer-btn"><?php echo Helpers::e($footerNoteButton); ?></a>
                    </div>
                    <div class="footer-call-clean">
                        <span class="icon"><i data-feather="phone-call"></i></span>
                        <div>
                            <small><?php echo Helpers::e($footerCtaTitle); ?></small>
                            <strong><?php echo Helpers::e($footerCtaPhone); ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <div>Copyright <?php echo Helpers::e($footerCopyright); ?> <span id="yearText"></span></div>
                <div class="footer-bottom-links">
                    <?php foreach ($bottomLinks as $link): ?>
                        <a href="<?php echo Helpers::e($link["url"]); ?>"><?php echo Helpers::e($link["label"]); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </footer>

    <div class="overlay overlay-hugeinc">
        <form class="form-inline mt-2 mt-md-0">
            <div class="form-inner">
                <div class="form-inner-div d-inline-flex align-items-center no-gutters">
                    <div class="col-auto">
                        <i class="icofont-search"></i>
                    </div>
                    <div class="col">
                        <input class="form-control w-100 p-0" type="text" placeholder="Search" aria-label="Search">
                    </div>
                    <div class="col-auto">
                        <a href="#" class="overlay-close"><i class="icofont-close-line"></i></a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <a id="mkdf-back-to-top" href="#" class="off"><i data-feather="corner-right-up"></i></a>

    <script src="<?php echo Helpers::e(Helpers::siteUrl('assets/library/jquery/jquery.min.js')); ?>"></script>
    <script src="<?php echo Helpers::e(Helpers::siteUrl('assets/library/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo Helpers::e(Helpers::siteUrl('assets/library/feather-icons/feather.min.js')); ?>"></script>
    <script src="<?php echo Helpers::e(Helpers::siteUrl('assets/library/owlcarousel/js/owl.carousel.min.js')); ?>"></script>
    <script src="<?php echo Helpers::e(Helpers::siteUrl('assets/library/select2/js/select2.min.js')); ?>"></script>
    <script src="<?php echo Helpers::e(Helpers::siteUrl('assets/library/magnific-popup/jquery.magnific-popup.min.js')); ?>"></script>
    <script src="<?php echo Helpers::e(Helpers::siteUrl('assets/library/jflickrfeed/jflickrfeed.min.js')); ?>"></script>
    <script src="<?php echo Helpers::e(Helpers::siteUrl('assets/library/jquery-waypoints/jquery.waypoints.min.js')); ?>"></script>
    <script src="<?php echo Helpers::e(Helpers::siteUrl('assets/library/countdown/jquery.countdown.min.js')); ?>"></script>
    <script src="<?php echo Helpers::e(Helpers::siteUrl('assets/library/jquery-appear/jquery.appear.js')); ?>"></script>
    <script src="<?php echo Helpers::e(Helpers::siteUrl('assets/library/jquery-easing/jquery.easing.min.js')); ?>"></script>
    <script src="<?php echo Helpers::e(Helpers::siteUrl('assets/library/jquery.counterup/jquery.counterup.min.js')); ?>"></script>
    <script src="<?php echo Helpers::e(Helpers::siteUrl('assets/library/jquery-validate/jquery.validate.min.js')); ?>"></script>
    <script src="<?php echo Helpers::e(Helpers::siteUrl('assets/js/site-custom.js')); ?>"></script>
</body>
</html>
