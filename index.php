<?php
require_once 'config/autoload.php';
$totalDonations = \App\Payment::getTotalDonations();
$brandName = \App\Helpers::brandName("Gracious Charity");
$brandLogo = \App\Helpers::brandLogoPath("assets/images/logo_dark.svg");
$brandFavicon = \App\Helpers::brandFaviconPath("assets/images/favicon.ico");
$homeMetaTitle = (string)\App\Helpers::setting('home_meta_title', $brandName);
$homeMetaDescription = (string)\App\Helpers::setting('home_meta_description', 'Friends at Heart Welfare Initiative supports children, families and underserved communities through compassionate outreach, practical care and transparent giving.');
$recentCauses = \App\Database::fetchAll("SELECT * FROM programmes WHERE status = 'published' ORDER BY created_at DESC LIMIT 4");
$homeEvents = \App\Database::fetchAll(
    "SELECT e.*, COALESCE(a.full_name, 'Events Desk') AS organizer
     FROM events e
     LEFT JOIN admins a ON a.id = e.created_by
     WHERE e.status = 'published'
     ORDER BY COALESCE(e.event_start, e.created_at) DESC
     LIMIT 3"
) ?: [];
$homePosts = \App\Content::publishedPosts(5);
$homePartners = \App\Database::fetchAll(
    "SELECT name, logo_path, website_url
     FROM partners
     WHERE status = 'published'
     ORDER BY sort_order ASC, name ASC
     LIMIT 12"
) ?: [];
$homeGalleryItems = \App\Database::fetchAll(
    "SELECT title, media_type, media_path, description
     FROM gallery_items
     WHERE status = 'published'
     ORDER BY created_at DESC
     LIMIT 5"
) ?: [];
$homeAboutSettings = [];
$rawHomeAbout = \App\Database::fetchAll("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'about_%'");
foreach ($rawHomeAbout as $settingRow) {
    $homeAboutSettings[$settingRow['setting_key']] = $settingRow['setting_value'];
}
$homeAboutTitleHtml = nl2br(strip_tags((string)($homeAboutSettings['about_hero_title'] ?? 'Building Hope, Restoring Dignity.'), '<br><i><em><strong><span>'));
$homeAboutDesc = trim((string)($homeAboutSettings['about_hero_desc'] ?? 'We are dedicated to creating a world where everyone has the opportunity to thrive.'));
$homeIntroLabel = trim((string)($homeAboutSettings['about_home_intro_label'] ?? 'Friends at Heart Welfare Initiative'));
$homeIntroTitle = trim((string)($homeAboutSettings['about_home_intro_title'] ?? 'Compassion in action for children, families and communities.'));
$homeIntroDesc = trim((string)($homeAboutSettings['about_home_intro_desc'] ?? 'We support children kept out of school by unpaid fees, patients burdened by medical bills, and families facing severe hardship. Every donation helps us restore dignity, protect hope and deliver practical care where it is needed most.'));
$homeIntroStat1Value = trim((string)($homeAboutSettings['about_home_intro_stat_1_value'] ?? '3,750'));
$homeIntroStat1Label = trim((string)($homeAboutSettings['about_home_intro_stat_1_label'] ?? 'Lives Supported'));
$homeIntroStat2Value = trim((string)($homeAboutSettings['about_home_intro_stat_2_value'] ?? '14,800'));
$homeIntroStat2Label = trim((string)($homeAboutSettings['about_home_intro_stat_2_label'] ?? 'Community Donations'));
$homeSliderKicker = trim((string)($homeAboutSettings['about_home_slider_kicker'] ?? 'Restoring Hope'));
$homeSliderTitle = trim((string)($homeAboutSettings['about_home_slider_title'] ?? 'For Children And Families'));
$homeSliderPrimaryLabel = trim((string)($homeAboutSettings['about_home_slider_primary_label'] ?? 'Join Us Now'));
$homeSliderPrimaryUrl = trim((string)($homeAboutSettings['about_home_slider_primary_url'] ?? 'causes-projects'));
$homeSliderVideoLabel = trim((string)($homeAboutSettings['about_home_slider_video_label'] ?? 'Watch the video'));
$homeSliderVideoUrl = trim((string)($homeAboutSettings['about_home_slider_video_url'] ?? 'https://player.vimeo.com/video/7449107'));
$homeSliderImage1 = trim((string)($homeAboutSettings['about_home_slider_image_1'] ?? 'assets/images/slider/slider_home_first_1.jpg'));
$homeSliderImage2 = trim((string)($homeAboutSettings['about_home_slider_image_2'] ?? 'assets/images/slider/slider_home_first_2.jpg'));
$homeSliderImage3 = trim((string)($homeAboutSettings['about_home_slider_image_3'] ?? 'assets/images/slider/slider_home_first_3.jpg'));
$homeSliderImages = [$homeSliderImage1, $homeSliderImage2, $homeSliderImage3];
$homeDonationHighlightValue = trim((string)($homeAboutSettings['about_home_donation_highlight_value'] ?? '100'));
$homeDonationHighlightLabel = trim((string)($homeAboutSettings['about_home_donation_highlight_label'] ?? 'Lives Supported'));
$homeStoryHighlights = [
    "Friends at Heart Welfare Initiative was born from moments that broke our hearts.",
    "We saw the suffering. We felt it. And we chose not to look away.",
    "We are ordinary people with extraordinary compassion, committed to standing with people in their moment of greatest need.",
    "We do not just pay school fees or settle hospital bills; we restore dreams, dignity and the possibility of a better future.",
    "As a registered organisation with the Corporate Affairs Commission and the Nigeria Network of NGOs, we serve with compassion, transparency and accountability."
];
$homeCoreValues = [
    ["title" => "Compassion", "text" => "We show love, empathy and care to individuals and communities in need."],
    ["title" => "Integrity", "text" => "We uphold honesty, accountability and strong moral principles in all we do."],
    ["title" => "Transparency", "text" => "We remain open, trustworthy and responsible in our operations and use of resources."],
    ["title" => "Equality", "text" => "We believe every individual deserves fairness, dignity and equal opportunity."],
    ["title" => "Volunteerism", "text" => "We encourage selfless service, teamwork and community participation."],
    ["title" => "Empowerment", "text" => "We equip people with opportunities, support and resources for a better future."],
    ["title" => "Excellence", "text" => "We pursue professionalism, quality and meaningful service delivery."],
    ["title" => "Inclusion", "text" => "We promote unity, acceptance and equal participation for all."],
    ["title" => "Teamwork", "text" => "We believe collaboration and partnership strengthen our impact."],
    ["title" => "Service To Humanity", "text" => "We are dedicated to improving lives and restoring hope through humanitarian service."]
];
?>
<!doctype html>
<html lang="en">
<head>
    <!-- xxx Basics xxx -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- xxx Change With Your Information xxx -->    
    <meta name="viewport" content="width=device-width, initial-scale=1.0 user-scalable=no">
    <title><?php echo htmlspecialchars($homeMetaTitle); ?></title>
    <meta name="author" content="<?php echo htmlspecialchars($brandName); ?>">     
    <meta name="description" content="<?php echo htmlspecialchars($homeMetaDescription); ?>">
    <meta name="keywords" content="Friends at Heart Welfare Initiative, FAHWI, charity, donation, NGO, community support, school fees support, hospital bill support, outreach, volunteer">
    
    <!-- Favicon -->    
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($brandFavicon); ?>">
    <!-- Animate CSSS -->    
    <link href="assets/library/animate/animate.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="assets/library/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icofont CSS -->
    <link href="assets/library/icofont/icofont.min.css" rel="stylesheet">
    <!-- Owl Carousel CSS -->
    <link href="assets/library/owlcarousel/css/owl.carousel.min.css" rel="stylesheet">
    <!-- Select Dropdown CSS -->
    <link href="assets/library/select2/css/select2.min.css" rel="stylesheet">
    <!-- Magnific Popup CSS -->
    <link href="assets/library/magnific-popup/magnific-popup.css" rel="stylesheet">    
    <!-- Main Theme CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Home SLider CSS -->
    <link rel="stylesheet" href="assets/css/home-main.css">
    <style>
        .home-about-summary {
            background: linear-gradient(180deg, #fffdf8 0%, #ffffff 100%);
        }
        .home-about-summary .summary-copy {
            padding-left: 30px;
        }
        .home-about-summary .summary-kicker {
            display: inline-flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 24px;
            color: var(--secondary-color);
            font-family: var(--font-heading);
            font-style: italic;
            font-weight: 700;
            font-size: 1.2rem;
        }
        .home-about-summary .summary-kicker::before {
            content: "";
            width: 50px;
            height: 2px;
            background: var(--secondary-color);
            display: block;
        }
        .home-about-summary .summary-title {
            font-family: 'Instrument Serif', serif;
            font-size: clamp(3rem, 5vw, 4.6rem);
            line-height: 0.98;
            font-style: italic;
            color: var(--primary-color);
            margin-bottom: 28px;
        }
        .home-about-summary .summary-text {
            font-size: 1.12rem;
            line-height: 1.8;
            color: #58627f;
            max-width: 580px;
            margin-bottom: 30px;
        }
        .home-about-summary .summary-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--secondary-color);
            font-weight: 700;
            letter-spacing: 0.4px;
        }
        .home-about-summary .summary-link i {
            transition: transform 0.25s ease;
        }
        .home-about-summary .summary-link:hover i {
            transform: translateX(4px);
        }
        .home-identity-section {
            background: linear-gradient(180deg, #fff 0%, #f7f3ea 100%);
        }
        .home-identity-section .identity-story-card {
            background: #11332a;
            color: rgba(255, 255, 255, 0.86);
            border-radius: 32px;
            padding: 42px;
            box-shadow: 0 32px 80px rgba(17, 51, 42, 0.16);
            height: 100%;
        }
        .home-identity-section .identity-kicker {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 22px;
            color: #edaf23;
            font-family: var(--font-heading);
            font-style: italic;
            font-weight: 700;
        }
        .home-identity-section .identity-kicker::before {
            content: "";
            width: 46px;
            height: 2px;
            background: #edaf23;
            display: block;
        }
        .home-identity-section .identity-title {
            color: #fff;
            font-family: 'Instrument Serif', serif;
            font-size: clamp(2.6rem, 4.2vw, 4rem);
            line-height: 0.98;
            font-style: italic;
            margin-bottom: 22px;
        }
        .home-identity-section .identity-intro {
            font-size: 1.05rem;
            line-height: 1.9;
            margin-bottom: 24px;
        }
        .home-identity-section .identity-lines {
            display: grid;
            gap: 16px;
            margin-bottom: 28px;
        }
        .home-identity-section .identity-line {
            padding-left: 20px;
            border-left: 2px solid rgba(237, 175, 35, 0.4);
            font-size: 0.98rem;
            line-height: 1.75;
        }
        .home-identity-section .identity-trust {
            border-top: 1px solid rgba(255, 255, 255, 0.14);
            margin-top: 28px;
            padding-top: 22px;
            color: rgba(255, 255, 255, 0.72);
            font-size: 0.92rem;
            line-height: 1.7;
        }
        .home-identity-section .identity-values-wrap {
            padding-left: 28px;
        }
        .home-identity-section .identity-values-header {
            margin-bottom: 24px;
        }
        .home-identity-section .identity-values-header small {
            display: inline-block;
            color: var(--secondary-color);
            font-family: var(--font-heading);
            font-style: italic;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .home-identity-section .identity-values-header h3 {
            color: var(--primary-color);
            font-family: 'Instrument Serif', serif;
            font-size: clamp(2.2rem, 3.4vw, 3.2rem);
            line-height: 1.02;
            font-style: italic;
            margin: 0;
        }
        .home-identity-section .identity-values-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }
        .home-identity-section .value-chip {
            background: #fff;
            border: 1px solid rgba(71, 119, 99, 0.14);
            border-radius: 22px;
            padding: 18px 18px 16px;
            box-shadow: 0 16px 36px rgba(49, 35, 30, 0.06);
            min-height: 100%;
        }
        .home-identity-section .value-chip h4 {
            color: var(--primary-color);
            font-size: 1rem;
            margin-bottom: 8px;
        }
        .home-identity-section .value-chip p {
            color: #5e6884;
            font-size: 0.92rem;
            line-height: 1.65;
            margin: 0;
        }
        .home-blog-post-wrap .blog-section-actions {
            display: flex;
            justify-content: flex-end;
            align-items: flex-end;
        }
        .home-blog-post-wrap .blog-cta {
            white-space: nowrap;
        }
        .home-blog-post-wrap .blog-carousel-shell {
            position: relative;
            padding-top: 34px;
        }
        #home-second-blog-post .owl-nav {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin: 0 0 28px;
            position: relative;
            top: 0;
            right: 0;
        }
        #home-second-blog-post .owl-nav button.owl-prev,
        #home-second-blog-post .owl-nav button.owl-next {
            margin: 0;
            flex: 0 0 auto;
        }
        .home-about-summary .summary-collage {
            position: relative;
            min-height: 660px;
        }
        .home-about-summary .summary-card {
            position: absolute;
            overflow: hidden;
            border-radius: 30px;
            background: #fff;
            box-shadow: 0 32px 80px rgba(49,35,30,0.12);
            border: 1px solid rgba(71,119,99,0.08);
        }
        .home-about-summary .summary-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .home-about-summary .summary-card.main {
            width: 72%;
            height: 540px;
            left: 0;
            top: 0;
        }
        .home-about-summary .summary-card.secondary {
            width: 36%;
            height: 260px;
            right: 0;
            top: 72px;
            transform: rotate(4deg);
        }
        .home-about-summary .summary-card.accent {
            width: 48%;
            height: 280px;
            right: 10%;
            bottom: 0;
            transform: rotate(-3deg);
        }
        @media (max-width: 991px) {
            .home-identity-section .identity-values-wrap {
                padding-left: 0;
                margin-top: 36px;
            }
            .home-blog-post-wrap .blog-section-actions {
                justify-content: flex-start;
                margin-top: 18px;
            }
            .home-about-summary .summary-copy {
                padding-left: 0;
                margin-top: 50px;
            }
            .home-about-summary .summary-collage {
                min-height: 520px;
            }
            .home-about-summary .summary-card.main {
                width: 78%;
                height: 420px;
            }
            .home-about-summary .summary-card.secondary {
                width: 40%;
                height: 210px;
            }
            .home-about-summary .summary-card.accent {
                width: 54%;
                height: 220px;
                right: 4%;
            }
        }
        @media (max-width: 767px) {
            .home-identity-section .identity-story-card {
                padding: 30px 24px;
            }
            .home-identity-section .identity-values-grid {
                grid-template-columns: 1fr;
            }
            .home-blog-post-wrap .blog-carousel-shell {
                padding-top: 18px;
            }
            #home-second-blog-post .owl-nav {
                justify-content: flex-start;
                margin-bottom: 22px;
            }
            .home-about-summary .summary-collage {
                min-height: auto;
                display: grid;
                gap: 18px;
            }
            .home-about-summary .summary-card,
            .home-about-summary .summary-card.main,
            .home-about-summary .summary-card.secondary,
            .home-about-summary .summary-card.accent {
                position: relative;
                width: 100%;
                height: 240px;
                inset: auto;
                transform: none;
            }
            .home-about-summary .summary-title {
                font-size: 2.7rem;
            }
        }
    </style>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->		
</head>
<body>

    <!-- Page loader Start -->
    <div id="pageloader">   
        <div class="loader-item">
            <div class="loader">
                <div class="circle"></div>
                <div class="circle"></div>
                <div class="circle"></div>
                <div class="circle"></div>
              </div>
        </div>
    </div>
    <!-- Page loader End -->

    <!-- Header Start -->
    <header class="header-style-fullwidth">
        <!-- Main Navigation Start -->
        <nav class="navbar navbar-expand-lg header-fullpage">
            <div class="container text-nowrap">
                <div class="d-flex align-items-center w-100 col p-0 logo-brand">
                    <a class="navbar-brand rounded-bottom light-bg" href="<?php echo htmlspecialchars(\App\Helpers::siteUrl()); ?>">
                        <img class="site-logo site-logo--header" src="<?php echo htmlspecialchars($brandLogo); ?>" alt="<?php echo htmlspecialchars($brandName); ?>">
                    </a> 
                </div>
                <!-- Topbar Buttons Start -->
                <div class="d-inline-flex request-btn order-lg-last col-auto p-0 align-items-center"> 
                    <a class="btn-outline-primary btn ms-3" href="#" id="search_home"><i data-feather="search"></i></a>

                    <a class="nav-link btn btn-default ms-3 donate-btn" href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('donation-page')); ?>">Donate</a>

                    <!-- Toggle Button Start -->
                    <button class="navbar-toggler x collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <!-- Toggle Button End -->
                </div>
                <!-- Topbar Buttons End -->
                
                <div class="navbar-collapse">
                    <!-- Mobile Logo -->
                    <div class="offcanvas-header">
                        <a href="<?php echo htmlspecialchars(\App\Helpers::siteUrl()); ?>" class="logo-small">
                            <img class="site-logo site-logo--header" src="<?php echo htmlspecialchars($brandLogo); ?>" alt="<?php echo htmlspecialchars($brandName); ?>">
                        </a>                        
                    </div>
                    <!-- Mobile Logo -->
                    <!-- Mobile Menu -->
                    <div class="offcanvas-body">
                        <ul class="navbar-nav ms-auto">
                                                        <li class="nav-item">
                                <a class="nav-link" href="<?php echo htmlspecialchars(\App\Helpers::siteUrl()); ?>">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('about-us')); ?>">About Us</a>
                            </li>
                            
                                                        <li class="nav-item dropdown">
                                <a class="nav-link has-children" href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('gallery')); ?>" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Explore</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="gallery">Photo & Video Gallery</a></li>
                                    <li><a class="dropdown-item" href="partners-sponsors">Partners & Sponsors</a></li>
                                    <li><a class="dropdown-item" href="causes-projects">Our Causes</a></li>
                                    <li><a class="dropdown-item" href="programme">Programme</a></li>
                                    <li><a class="dropdown-item" href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('volunteer')); ?>">Volunteer</a></li>
                                    <li><a class="dropdown-item" href="faqs">FAQs</a></li>
                                </ul>
                            </li>
                                                        <li class="nav-item">
                                <a class="nav-link" href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('events')); ?>">Events</a>
                            </li>                        
                                                        <li class="nav-item">
                                <a class="nav-link" href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('blog')); ?>">Blog</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('contact-us')); ?>">Contact</a>
                            </li>
                            
                        </ul>
                    </div>
                    <!-- Mobile Menu -->
                    <div class="close-nav"></div>
                    <!-- Main Navigation End -->
                </div>
            </div>
        </nav>
        <!-- Main Navigation End -->
    </header>
    <!-- Header Start -->

    <!-- Banner Start -->
    <section class="main-banner home-style-second">
        <div class="slides-wrap">
            <div class="owl-carousel owl-theme">
                <!--/owl-slide-->
                <div class="owl-slide d-flex align-items-center cover" style="background-image: url(<?php echo htmlspecialchars($homeSliderImages[0]); ?>);">
                    <div class="container">
                        <div class="row justify-content-center justify-content-md-start no-gutters">
                            <div class="col-10 col-md-6 static">
                                <div class="owl-slide-text">
                                    <h3 class="owl-slide-animated owl-slide-title"><?php echo htmlspecialchars($homeSliderKicker); ?></h3>
                                    <h1 class="owl-slide-animated owl-slide-subtitle">
                                        <?php echo nl2br(htmlspecialchars($homeSliderTitle)); ?>
                                    </h1>
                                    <div class="owl-slide-animated owl-slide-cta">                                        
                                        <a class="btn btn-default me-3" href="<?php echo htmlspecialchars($homeSliderPrimaryUrl); ?>" role="button"><?php echo htmlspecialchars($homeSliderPrimaryLabel); ?></a>
                                        <a class="slider-link popup-video" href="<?php echo htmlspecialchars($homeSliderVideoUrl); ?>"><?php echo htmlspecialchars($homeSliderVideoLabel); ?> <i class="charity-play_button"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/owl-slide-->
                
                
                <!--/owl-slide-->
                <div class="owl-slide d-flex align-items-center cover" style="background-image: url(<?php echo htmlspecialchars($homeSliderImages[1]); ?>);">
                    <div class="container">
                        <div class="row justify-content-center justify-content-md-start no-gutters">
                            <div class="col-10 col-md-6 static">
                                <div class="owl-slide-text">
                                    <h3 class="owl-slide-animated owl-slide-title"><?php echo htmlspecialchars($homeSliderKicker); ?></h3>
                                    <h1 class="owl-slide-animated owl-slide-subtitle">
                                        <?php echo nl2br(htmlspecialchars($homeSliderTitle)); ?>
                                    </h1>
                                    <div class="owl-slide-animated owl-slide-cta">                                        
                                        <a class="btn btn-default me-3" href="<?php echo htmlspecialchars($homeSliderPrimaryUrl); ?>" role="button"><?php echo htmlspecialchars($homeSliderPrimaryLabel); ?></a>
                                        <a class="slider-link popup-video" href="<?php echo htmlspecialchars($homeSliderVideoUrl); ?>"><?php echo htmlspecialchars($homeSliderVideoLabel); ?> <i class="charity-play_button"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--/owl-slide-->
                <div class="owl-slide d-flex align-items-center cover" style="background-image: url(<?php echo htmlspecialchars($homeSliderImages[2]); ?>);">
                    <div class="container">
                        <div class="row justify-content-center justify-content-md-start no-gutters">
                            <div class="col-10 col-md-6 static">
                                <div class="owl-slide-text">
                                    <h3 class="owl-slide-animated owl-slide-title"><?php echo htmlspecialchars($homeSliderKicker); ?></h3>
                                    <h1 class="owl-slide-animated owl-slide-subtitle">
                                        <?php echo nl2br(htmlspecialchars($homeSliderTitle)); ?>
                                    </h1>
                                    <div class="owl-slide-animated owl-slide-cta">                                        
                                        <a class="btn btn-default me-3" href="<?php echo htmlspecialchars($homeSliderPrimaryUrl); ?>" role="button"><?php echo htmlspecialchars($homeSliderPrimaryLabel); ?></a>
                                        <a class="slider-link popup-video" href="<?php echo htmlspecialchars($homeSliderVideoUrl); ?>"><?php echo htmlspecialchars($homeSliderVideoLabel); ?> <i class="charity-play_button"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/owl-slide-->
            </div>
            
        </div>
    </section>
    <!-- Banner Start -->

    <!-- Main Body Content Start -->
    <main id="body-content" class="body-non-overflow">

        <!-- Donation Style Start -->
        <section class="bg-white">
            <div class="container">
                <div class="row align-items-center">  
                    <div class="col-lg-5 col-md-12 order-lg-last">
                        <div class="home-second-donation-form">                                                    
                            <div class="funds-committed">
                                <div class="gift-icon">
                                    <i class="charity-gift_box"></i>
                                </div>
                                <small><?php echo \App\Helpers::e($homeDonationHighlightLabel); ?></small>
                                <div class="d-flex justify-content-center align-items-center">
                                    <span class="counter"><?php echo \App\Helpers::e($homeDonationHighlightValue); ?></span>
                                </div>
                            </div>
                            <form class="form-style" action="includes/paystack_initialize.php" method="POST">
                                <h3 class="h3-sm fw-7 txt-white mb-3">Easy Donation</h3>
                                <div class="form-group">
                                    <label for="name"><strong>Full Name</strong></label>
                                    <input type="text" name="first_name" class="form-control form-light" id="name" placeholder="e.g John Doe" required>
                                </div>
                                <div class="form-group">
                                    <label for="email"><strong>Email Address</strong></label>
                                    <input type="email" name="email" class="form-control form-light" id="email" placeholder="e.g example@sitename.com" required>
                                </div>
                                <div class="form-group">
                                    <label for="state"><strong>Select Causes</strong></label>
                                    <select class="theme-combo home-charity" id="state" name="cause" style="height: 400px">
                                        <option value="General">Select Causes</option>
                                        <option value="Charity For Food">Charity For Food</option>
                                        <option value="Charity For Education">Charity For Education</option>
                                        <option value="Charity For Medical">Charity For Medical</option>
                                        <option value="Charity For Water">Charity For Water</option>
                                        <option value="Charity For Natural Disaster">Charity For Natural Disaster</option>
                                    </select>
                                </div>
                                <div class="form-group mb-4">
                                    <div class="mb-2"><label for="amount1"><strong>Select Amount (₦)</strong></label></div>
                                    <div class="donation-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                                        <div class="form-check custom-radio">
                                            <input class="form-check-input" type="radio" name="amount_radio" id="amount1" value="10000" checked>
                                            <label class="form-check-label" for="amount1">10,000</label>
                                        </div>
                                        <div class="form-check custom-radio">
                                            <input class="form-check-input" type="radio" name="amount_radio" id="amount2" value="20000">
                                            <label class="form-check-label" for="amount2">20,000</label>
                                        </div>
                                        <div class="form-check custom-radio">
                                            <input class="form-check-input" type="radio" name="amount_radio" id="amount3" value="50000">
                                            <label class="form-check-label" for="amount3">50,000</label>
                                        </div>
                                        <div class="form-check custom-radio">
                                            <input class="form-check-input" type="radio" name="amount_radio" id="amount4" value="100000">
                                            <label class="form-check-label" for="amount4">100,000</label>
                                        </div>
                                        <div class="form-check custom-radio">
                                            <input class="form-check-input" type="radio" name="amount_radio" id="amount5" value="200000">
                                            <label class="form-check-label" for="amount5">200,000</label>
                                        </div>
                                        <div class="form-check custom-radio">
                                            <input class="form-check-input" type="radio" name="amount_radio" id="amount6" value="500000">
                                            <label class="form-check-label" for="amount6">500,000</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="number" name="amount_custom" class="form-control form-light" id="custom" placeholder="Custom Amount (₦)">
                                </div>
                                <input type="hidden" name="amount" id="final_amount" value="10000">
                                
                                <div class="paystack-footer mt-3 text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2 text-nowrap">
                                        <i class="icofont-lock txt-white fs-6"></i>
                                        <span class="txt-white opacity-75" style="font-size: 12px;">Secured by Paystack. Cards, Transfer & USSD supported.</span>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-default mt-4 btn-block w-100">Donate now</button>
                            </form>
                            
                            <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const finalAmount = document.getElementById('final_amount');
                                const customInput = document.getElementById('custom');
                                const radios = document.querySelectorAll('input[name="amount_radio"]');

                                radios.forEach(radio => {
                                    radio.addEventListener('change', function() {
                                        finalAmount.value = this.value;
                                        customInput.value = '';
                                    });
                                });

                                customInput.addEventListener('input', function() {
                                    if (this.value) {
                                        finalAmount.value = this.value;
                                        radios.forEach(r => r.checked = false);
                                    } else {
                                        // Default back to first radio if custom is cleared
                                        const first = document.getElementById('amount1');
                                        if(first) {
                                            first.checked = true;
                                            finalAmount.value = first.value;
                                        }
                                    }
                                });
                            });
                            </script>
                                
                        </div>
                    </div> 
                    
                    <!-- Spacer For Medium -->
                    <div class="w-100 d-none d-sm-none d-md-block d-lg-none spacer-60"></div>
                    <!-- Spacer For Medium -->

                    <div class="col-lg-7 col-md-12">
                        <div>
                            
                            <h1 class="heading-main">
                                <small><?php echo \App\Helpers::e($homeIntroLabel); ?></small>
                                <?php echo \App\Helpers::e($homeIntroTitle); ?>
                            </h1>
                            <p>That�s 14% of the world�s population. Put another way, that's 1 in 8 people alive today living without hope amongst trash, sewage, drugs, and abuse in unimaginable conditions. Life without secure housing is a life without basic needs being met.</p>                        

                            <div class="row my-5 home-second-welcome">                      
                                <!-- Map Icons Start -->
                                <div class="col-sm-6 mb-md-0">
                                    <div class="icon-box-1">
                                        <i class="charity-volunteer_people"></i>
                                        <div class="text">
                                            <h3><?php echo \App\Helpers::e($homeIntroStat1Value); ?> <br> <span><?php echo \App\Helpers::e($homeIntroStat1Label); ?></span></h3>
                                        </div>
                                    </div>
                                </div>
                                <!-- Map Icons Start -->

                                <!-- Map Icons Start -->
                                <div class="col-sm-6">
                                    <div class="icon-box-1">
                                        <i class="charity-donate_money"></i>
                                        <div class="text">
                                            <h3><?php echo \App\Helpers::e($homeIntroStat2Value); ?> <br> <span><?php echo \App\Helpers::e($homeIntroStat2Label); ?></span></h3>
                                        </div>
                                    </div>
                                </div>
                                <!-- Map Icons Start -->
                            </div>

                            <a href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('volunteer')); ?>" class="btn btn-outline-default">Become a Volunteer</a>
                        </div>
                    </div>

                    <!-- Spacer For Medium -->
                    <div class="w-100 d-none d-sm-none d-md-block d-lg-none spacer-60"></div>
                    <!-- Spacer For Medium -->

                    
                </div>
            </div>
        </section>
        <!-- Donation Style Start -->

        <script>
        document.addEventListener('DOMContentLoaded', function () {
            var homepageIntroCopy = <?php echo json_encode($homeIntroDesc); ?>;
            var donationSectionParagraph = document.querySelector('.home-second-welcome')?.previousElementSibling;
            if (donationSectionParagraph && donationSectionParagraph.tagName === 'P') {
                donationSectionParagraph.textContent = homepageIntroCopy;
            }

            var greenIntroParagraph = document.querySelector('.wide-tb-100.pb-5.bg-green p');
            if (greenIntroParagraph) {
                greenIntroParagraph.textContent = homepageIntroCopy;
            }
        });
        </script>

        <!-- Counter Style 2 -->
        <section class="wide-tb-100 p-0">
            <div class="container">
                <div class="row d-flex align-items-center">
                    <!-- Counter Col Start -->
                    <div class="col col-12 col-lg-4 col-md-6">
                        <div class="counter-style-box small-box">              
                            <div class="counter-txt"><span class="counter">180</span>+</div>
                            <div>Featured<br> Campaign</div>
                        </div>
                    </div>
                    <!-- Counter Col End -->

                    <!-- Counter Col Start -->
                    <div class="col col-12 col-lg-4 col-md-6">
                        <div class="counter-style-box small-box">              
                            <div class="counter-txt"><span class="counter">280</span>+</div>
                            <div>Dedicated<br> Volunteers</div>
                        </div>
                    </div>
                    <!-- Counter Col End -->

                    <!-- Counter Col Start -->
                    <div class="col col-12 col-lg-4 col-md-6">
                        <div class="counter-style-box small-box">              
                            <div class="counter-txt"><span class="counter">1560</span>+</div>
                            <div>People Helped<br> Happily</div>
                        </div>
                    </div>
                    <!-- Counter Col End -->
                </div>
            </div>
        </section>
        <!-- Counter Style 2 -->

        <!-- Causes Grid Start -->
        <section id="media-gallery" class="wide-tb-100">
            <div class="container">
                
                <div class="row justify-content-between align-items-end">
                    <div class="col-lg-4 col-md-6">
                        <h1 class="heading-main">
                            <small>Help Us Now</small>
                            More Recent Causes
                        </h1>
                    </div>
                    <div class="col-lg-8 col-md-6 text-md-end btn-team">
                        <a href="causes-projects" class="btn btn-outline-dark">View All Causes</a>
                    </div>
                </div>

                <div class="owl-carousel owl-theme" id="home-second-causes">
                    <?php if (!empty($recentCauses)): ?>
                        <?php foreach ($recentCauses as $cause): ?>
                            <?php 
                                $goal = (float)$cause['goal_amount'] > 0 ? (float)$cause['goal_amount'] : 1;
                                $raised = (float)$cause['raised_amount'];
                                $percent = min(100, round(($raised / $goal) * 100));
                                $image = !empty($cause['featured_image']) ? $cause['featured_image'] : 'assets/images/causes/causes_img_1.jpg';
                                $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
                                $isVideo = in_array($ext, ['mp4', 'webm', 'ogg', 'mov']);
                            ?>
                            <!-- Causes Wrap -->
                            <div class="item">
                                <div class="causes-wrap">
                                    <div class="img-wrap">
                                        <a href="cause/<?php echo !empty($cause['slug']) ? $cause['slug'] : $cause['id']; ?>">
                                            <?php if ($isVideo): ?>
                                                <video src="<?php echo htmlspecialchars($image); ?>" autoplay loop muted playsinline style="width: 100%; height: 250px; object-fit: cover; border-radius: 6px 6px 0 0;"></video>
                                            <?php else: ?>
                                                <img src="<?php echo htmlspecialchars($image); ?>" alt="" style="width: 100%; height: 250px; object-fit: cover; border-radius: 6px 6px 0 0;">
                                            <?php endif; ?>
                                        </a>
                                        <div class="raised-progress">
                                            <div class="skillbar-wrap">
                                                <div class="clearfix">
                                                    ₦<?php echo number_format($raised); ?> raised of ₦<?php echo number_format($goal); ?>
                                                </div>
                                                <div class="skillbar" data-percent="<?php echo $percent; ?>%">
                                                    <div class="skillbar-percent"><?php echo $percent; ?>%</div>
                                                    <div class="skillbar-bar"></div>
                                                </div>             
                                            </div>
                                        </div>
                                    </div>

                                    <div class="content-wrap">
                                        <span class="tag"><?php echo htmlspecialchars($cause['category'] ?? 'General'); ?></span>
                                        <h3><a href="cause/<?php echo !empty($cause['slug']) ? $cause['slug'] : $cause['id']; ?>"><?php echo htmlspecialchars($cause['title']); ?></a></h3>
                                        <p><?php echo htmlspecialchars(substr($cause['summary'] ?? '', 0, 100)); ?><?php if(strlen($cause['summary'] ?? '') > 100) echo '...'; ?></p>
                                        <div class="btn-wrap">
                                            <a class="btn-primary btn" href="cause/<?php echo !empty($cause['slug']) ? $cause['slug'] : $cause['id']; ?>">Donate Now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Causes Wrap -->
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center" style="padding: 50px 0; color: var(--muted); width: 100%;">
                            <p>No causes have been published yet. Please check back later.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <!-- Causes Grid Start -->
        

        <!-- Callout Style Start -->
        <section class="wide-tb-100 bg-scroll bg-img-1 pos-rel callout-style-1">
            <div class="bg-overlay black opacity-50"></div>
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <h1 class="heading-main light-mode orange">
                            <small>Help Other People</small>
                            We Dream to Create A Bright Future Of The Underprivileged Children
                        </h1>
                        <a href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('donation-page')); ?>" class="btn btn-default">Donate Now</a>
                    </div>
                </div>
            </div>
        </section>
        <!-- Callout Style End -->

        <!-- Images Gallery Style Start -->
        <section id="media-gallery" class="wide-tb-100">
            <div class="container">
                <div class="row img-gallery">
                    <div class="col-lg-4">
                        <h1 class="heading-main mb-lg-0">
                            <small>Photo & Video Gallery</small>
                            Moments From Our Work
                        </h1>
                        <div style="margin-top:24px;">
                            <button type="button" class="btn btn-outline-dark gallery-page-link" onclick="window.location.href='<?php echo \App\Helpers::e(\App\Helpers::siteUrl('gallery')); ?>';">View More Images</button>
                        </div>
                    </div>
                    <?php if ($homeGalleryItems): ?>
                        <?php foreach ($homeGalleryItems as $galleryItem): ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="img-gallery-item">
                                    <a class="gallery-media-trigger" href="<?php echo \App\Helpers::e($galleryItem['media_path']); ?>" title="<?php echo \App\Helpers::e($galleryItem['title']); ?>" <?php echo ($galleryItem['media_type'] ?? 'photo') === 'video' ? '' : 'data-fancybox="home-gallery"'; ?>>
                                        <div class="gallery-content">
                                            <div class="tag"><span><?php echo ($galleryItem['media_type'] ?? 'photo') === 'video' ? 'Video' : 'Photo'; ?></span></div>
                                            <h3><?php echo \App\Helpers::e($galleryItem['title']); ?></h3>
                                            <div class="img-open">
                                                <i data-feather="<?php echo ($galleryItem['media_type'] ?? 'photo') === 'video' ? 'play-circle' : 'plus-circle'; ?>"></i>
                                            </div>
                                        </div>
                                        <?php if (($galleryItem['media_type'] ?? 'photo') === 'video'): ?>
                                            <video src="<?php echo \App\Helpers::e($galleryItem['media_path']); ?>" muted playsinline style="width:100%; height:100%; object-fit:cover;"></video>
                                        <?php else: ?>
                                            <img src="<?php echo \App\Helpers::e($galleryItem['media_path']); ?>" alt="<?php echo \App\Helpers::e($galleryItem['title']); ?>">
                                        <?php endif; ?>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-lg-8 col-md-12">
                            <div class="explore-intro-card">
                                <h3 class="mb-2">No gallery items published yet.</h3>
                                <p class="mb-0">Upload and publish gallery items from the admin dashboard and the latest five will appear here automatically.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <!-- Images Gallery Style End -->

        <section class="wide-tb-100 home-identity-section">
            <div class="container">
                <div class="row align-items-start">
                    <div class="col-lg-6">
                        <div class="identity-story-card">
                            <div class="identity-kicker">Who We Are</div>
                            <h2 class="identity-title">Love that answers the call.</h2>
                            <p class="identity-intro">
                                Friends at Heart Welfare Initiative exists for children sent home over unpaid school fees, patients detained by medical bills, and families carrying hardship in silence. We step into those moments with practical help, dignity and hope.
                            </p>
                            <div class="identity-lines">
                                <?php foreach ($homeStoryHighlights as $storyLine): ?>
                                    <div class="identity-line"><?php echo \App\Helpers::e($storyLine); ?></div>
                                <?php endforeach; ?>
                            </div>
                            <div class="identity-trust">
                                Until no child loses access to school because of unpaid fees, no patient is trapped by hospital bills, and no underserved community feels forgotten, our hearts will continue to answer the call.
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="identity-values-wrap">
                            <div class="identity-values-header">
                                <small>Our Core Values</small>
                                <h3>The principles behind every act of service.</h3>
                            </div>
                            <div class="identity-values-grid">
                                <?php foreach ($homeCoreValues as $value): ?>
                                    <div class="value-chip">
                                        <h4><?php echo \App\Helpers::e($value['title']); ?></h4>
                                        <p><?php echo \App\Helpers::e($value['text']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div style="margin-top:24px;">
                                <a class="summary-link" href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('about-us')); ?>">
                                    Discover our story <i data-feather="arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Event Style Start -->
        <section id="media-gallery" class="wide-tb-100">
            <div class="container">
                <div class="row justify-content-between align-items-end">
                    <div class="col-lg-6 col-md-8">
                        <h1 class="heading-main">
                            <small>Join Us</small>
                            Reach Out & Help In Our Latest Events
                        </h1>
                    </div>
                    
                </div>
                <?php if ($homeEvents): ?>
                <div class="owl-carousel owl-theme" id="home-second-events">
                    <?php foreach ($homeEvents as $homeEvent): ?>
                    <div class="item">
                        <div class="event-wrap-alternate">
                            <div class="date-box">
                                <?php echo \App\Helpers::e(date("d", strtotime($homeEvent["event_start"] ?? "now"))); ?>
                                <span><?php echo \App\Helpers::e(date("M", strtotime($homeEvent["event_start"] ?? "now"))); ?></span>
                            </div>
                            <div class="img-wrap">
                                <a href="<?php echo \App\Helpers::e(\App\Helpers::siteUrl('event/' . rawurlencode((string)($homeEvent["slug"] ?? '')))); ?>"><img src="<?php echo \App\Helpers::e($homeEvent["featured_image"] ?: "assets/images/events/event_alternate_img_1.jpg"); ?>" alt="<?php echo \App\Helpers::e($homeEvent["title"] ?? "Event"); ?>"></a>
                                <div class="content-wrap">
                                    <h3><a href="<?php echo \App\Helpers::e(\App\Helpers::siteUrl('event/' . rawurlencode((string)($homeEvent["slug"] ?? '')))); ?>"><?php echo \App\Helpers::e($homeEvent["title"] ?? "Upcoming Event"); ?></a></h3>
                                    <div class="event-details">
                                        <div><i data-feather="clock"></i> <?php echo \App\Helpers::e(\App\Content::formatEventTime($homeEvent["event_start"] ?? "")); ?></div>
                                        <div><i data-feather="map-pin"></i> <?php echo \App\Helpers::e(($homeEvent["city"] ?: $homeEvent["venue"]) ?? "Venue TBA"); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="card text-center p-5">
                    <h3 class="mb-3">No published events yet</h3>
                    <p class="mb-0">Your newest published events will appear here automatically.</p>
                </div>
                <?php endif; ?>

                <div class="text-center mt-5">
                    <a href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('events')); ?>" class="btn btn-outline-dark">View All Events</a>
                </div>
            </div>
        </section>
        <!-- Event Style End -->

        <!-- Team Member Style Start -->
        <section class="wide-tb-100 team-bg bg-green mb-spacer-md">
            <div class="container">
                <div class="row justify-content-between align-items-end">
                    <div class="col-lg-4 col-md-6">
                        <h1 class="heading-main">
                            <small>Team Member</small>
                            Our Expert Volunteer
                        </h1>
                    </div>
                    <div class="col-lg-8 col-md-6 text-md-end btn-team">
                        <a href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('volunteer')); ?>" class="btn btn-outline-dark">View All Members</a>
                    </div>
                </div>

                <div class="row">
                    <!-- Team Column One -->
                    <div class="col-12 col-lg-3 col-sm-6">
                        <div class="team-section-wrap mb-0">
                            <div class="img green">
                                <div class="social-icons">
                                    <a href="#"><i class="icofont-facebook"></i></a>
                                    <a href="#"><i class="icofont-twitter"></i></a>
                                    <a href="#"><i class="icofont-instagram"></i></a>
                                </div>
                                <img src="assets/images/team/team-1.jpg" alt="" class="rounded-circle">
                            </div>
                            <h4>Adams Hobes</h4>
                            <h5>Volunteer</h5>
                            <div class="text-md-end">
                                <a href="javascript:" class="read-more-line"><span>Read More</span></a>
                            </div>
                        </div>
                    </div>
                    <!-- Team Column One -->

                    <!-- Team Column One -->
                    <div class="col-12 col-lg-3 col-sm-6">
                        <div class="team-section-wrap mb-0">
                            <div class="img navy-blue">
                                <div class="social-icons">
                                    <a href="#"><i class="icofont-facebook"></i></a>
                                    <a href="#"><i class="icofont-twitter"></i></a>
                                    <a href="#"><i class="icofont-instagram"></i></a>
                                </div>
                                <img src="assets/images/team/team-2.jpg" alt="" class="rounded-circle">
                            </div>
                            <h4>Natasha Gamble</h4>
                            <h5>Volunteer</h5>
                            <div class="text-md-end">
                                <a href="javascript:" class="read-more-line"><span>Read More</span></a>
                            </div>
                        </div>
                    </div>
                    <!-- Team Column One -->

                    <!-- Spacer For Medium -->
                    <div class="w-100 d-none d-sm-block d-lg-none spacer-60"></div>
                    <!-- Spacer For Medium -->

                    <!-- Team Column One -->
                    <div class="col-12 col-lg-3 col-sm-6">
                        <div class="team-section-wrap mb-0">
                            <div class="img orange">
                                <div class="social-icons">
                                    <a href="#"><i class="icofont-facebook"></i></a>
                                    <a href="#"><i class="icofont-twitter"></i></a>
                                    <a href="#"><i class="icofont-instagram"></i></a>
                                </div>
                                <img src="assets/images/team/team-3.jpg" alt="" class="rounded-circle">
                            </div>
                            <h4>James Evans</h4>
                            <h5>Volunteer</h5>
                            <div class="text-md-end">
                                <a href="javascript:" class="read-more-line"><span>Read More</span></a>
                            </div>
                        </div>
                    </div>
                    <!-- Team Column One -->

                    <!-- Team Column One -->
                    <div class="col-12 col-lg-3 col-sm-6">
                        <div class="team-section-wrap mb-0">
                            <div class="img beige">
                                <div class="social-icons">
                                    <a href="#"><i class="icofont-facebook"></i></a>
                                    <a href="#"><i class="icofont-twitter"></i></a>
                                    <a href="#"><i class="icofont-instagram"></i></a>
                                </div>
                                <img src="assets/images/team/team-4.jpg" alt="" class="rounded-circle">
                            </div>
                            <h4>Rick Dalton</h4>
                            <h5>Volunteer</h5>
                            <div class="text-md-end">
                                <a href="javascript:" class="read-more-line"><span>Read More</span></a>
                            </div>
                        </div>
                    </div>
                    <!-- Team Column One -->
                </div>
            </div>
        </section>
        <!-- Team Member Style End -->        

        <!-- Testimonials Style Start -->
        <section class="wide-tb-100 pattern-green pb-0 home-second-testimonials-wrap">
            <div class="container">
                <h1 class="heading-main light-mode green">
                    <small>Our Testimonials</small>
                    What People Say
                </h1>
                <div class="owl-carousel owl-theme nav-light" id="home-second-testimonials">
                        
                    <!-- Client Testimonials Alternate Slider Item -->
                    <div class="item">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-8 col-md-11 mx-auto">
                                    <div class="client-testimonial-alternate">
                                        <div class="client-inner-content">
                                            <i class="charity-quotes"></i>
                                            <p>Gracious is a nonpro?t organization supported by community leaders, corporate sponsors, churches,
                                                helpless etc. and concerned citizens</p>
                                        </div>
                                        <div class="client-testimonial-icon">
                                            <img src="assets/images/team_1.jpg" alt="">
                                            <div class="text">
                                                <div class="name">Josefin Fashkin</div>
                                                <div class="post">Senior Activist</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                    </div>
                    <!-- Client Testimonials Alternate Slider Item -->
                
                    <!-- Client Testimonials Alternate Slider Item -->
                    <div class="item">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-8 col-md-11 mx-auto">
                                    <div class="client-testimonial-alternate">
                                        <div class="client-inner-content">
                                            <i class="charity-quotes"></i>
                                            <p>Gracious is a nonpro?t organization supported by community leaders, corporate sponsors, churches,
                                                helpless etc. and concerned citizens</p>
                                        </div>
                                        <div class="client-testimonial-icon">
                                            <img src="assets/images/team_2.jpg" alt="">
                                            <div class="text">
                                                <div class="name">Josefin Fashkin</div>
                                                <div class="post">Senior Activist</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                    </div>
                    <!-- Client Testimonials Alternate Slider Item -->
                
                    <!-- Client Testimonials Alternate Slider Item -->
                    <div class="item">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-8 col-md-11 mx-auto">
                                    <div class="client-testimonial-alternate">
                                        <div class="client-inner-content">
                                            <i class="charity-quotes"></i>
                                            <p>Gracious is a nonpro?t organization supported by community leaders, corporate sponsors, churches,
                                                helpless etc. and concerned citizens</p>
                                        </div>
                                        <div class="client-testimonial-icon">
                                            <img src="assets/images/team_1.jpg" alt="">
                                            <div class="text">
                                                <div class="name">Josefin Fashkin</div>
                                                <div class="post">Senior Activist</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                    </div>
                    <!-- Client Testimonials Alternate Slider Item -->
                
                </div>
            </div>
        </section>
        <!-- Testimonials Style End -->          

        <!-- Blog Style Start -->
        <section class="wide-tb-100 pb-0 home-blog-post-wrap">
            <div class="container">
                <div class="row align-items-end">
                    <div class="col-md-8 col-lg-6 col-9">
                        <h1 class="heading-main">
                            <small>News & Blogs</small>
                            Some Of Our Recent Stories & News Blog
                        </h1>
                    </div>
                    <div class="col-md-4 col-lg-6 col-12 blog-section-actions">
                        <a href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('blog')); ?>" class="btn btn-default d-inline-flex align-items-center px-4 blog-cta">View All Posts</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="blog-carousel-shell">
                        <div class="owl-carousel owl-theme" id="home-second-blog-post">
                            <?php if ($homePosts): ?>
                                <?php foreach ($homePosts as $homePost): ?>
                                    <?php
                                        $postLink = \App\Helpers::postPublicUrl($homePost);
                                        $postImage = (string)($homePost['featured_image'] ?? '');
                                        if ($postImage === '') {
                                            $postImage = 'assets/images/blogs/blog_img_1.jpg';
                                        }
                                    ?>
                                    <div class="item">
                                        <div class="post-wrap">
                                            <div class="post-img">
                                                <a href="<?php echo \App\Helpers::e($postLink); ?>"><img src="<?php echo \App\Helpers::e($postImage); ?>" alt="<?php echo \App\Helpers::e((string)($homePost['title'] ?? 'Blog story')); ?>"></a>
                                            </div>
                                            <div class="post-content">
                                                <div class="post-date"><?php echo \App\Helpers::e(date('d, M, Y', strtotime((string)($homePost['published_at'] ?? $homePost['created_at'] ?? 'now')))); ?></div>
                                                <h3 class="post-title"><a href="<?php echo \App\Helpers::e($postLink); ?>"><?php echo \App\Helpers::e((string)($homePost['title'] ?? 'Untitled story')); ?></a></h3>
                                                <div class="post-category"><?php echo \App\Helpers::e((string)($homePost['category'] ?: 'News')); ?></div>
                                                <div class="text-md-end">
                                                    <a href="<?php echo \App\Helpers::e($postLink); ?>" class="read-more-line"><span>Read More</span></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="item">
                                    <div class="post-wrap">
                                        <div class="post-content">
                                            <div class="post-date">Editorial Desk</div>
                                            <h3 class="post-title"><a href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('blog')); ?>">No published stories yet</a></h3>
                                            <div class="post-category">Blog</div>
                                            <div class="text-md-end">
                                                <a href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('blog')); ?>" class="read-more-line"><span>Open Blog</span></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Blog Style End -->   
        
        <!-- Google Map Style Start -->   
        <section class="wide-tb-100 pb-0">
            <div class="map-frame">
                <iframe src="https://www.google.com/maps?q=137%20Market%20Road%2C%20Aba%2C%20Abia%20State%2C%20Nigeria&z=15&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <div class="container">
                <div class="row">
                    <!-- Callout Section Side Image -->
                    <div class="col-sm-12">
                        <div class="callout-style-side-img d-lg-flex align-items-center top-broken-grid">
                            <div class="img-callout">
                                <img src="assets/images/callout_side_img.jpg" alt="">
                            </div>
                            <div class="text-callout">
                                <div class="d-sm-flex align-items-center">                                   
                                    <div class="heading">
                                        <h2>Let Us Come Together To Make A Difference</h2>
                                    </div>
                                    <div class="icon">
                                        <a href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('donation-page')); ?>" class="btn btn-default">Donate Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Callout Section Side Image -->
                </div>
            </div>
        </section>
        <!-- Google Map Style End -->   

        <!-- Our Partners Start -->
        <section id="partners-sponsors" class="wide-tb-100 pt-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <h1 class="heading-main">
                            <small>Global Providers</small>
                            Our World Wide Partner
                        </h1>
                    </div>
                    <div class="col-sm-12">
                        <div class="owl-carousel owl-theme" id="home-clients">
                            <?php if ($homePartners): ?>
                                <?php foreach ($homePartners as $partner): ?>
                                    <div class="item">
                                        <div class="clients-logo">
                                            <?php if (!empty($partner['website_url'])): ?>
                                                <a href="<?php echo htmlspecialchars($partner['website_url']); ?>" target="_blank" rel="noopener">
                                                    <img src="<?php echo htmlspecialchars($partner['logo_path'] ?: 'assets/images/clients/client1.png'); ?>" alt="<?php echo htmlspecialchars($partner['name'] ?: 'Partner'); ?>">
                                                </a>
                                            <?php else: ?>
                                                <img src="<?php echo htmlspecialchars($partner['logo_path'] ?: 'assets/images/clients/client1.png'); ?>" alt="<?php echo htmlspecialchars($partner['name'] ?: 'Partner'); ?>">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="item">
                                    <div class="clients-logo">
                                        <img src="assets/images/clients/client1.png" alt="Partner placeholder">
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Our Partners End -->
        
           
    <?php require __DIR__ . "/includes/site-footer.php"; ?>
