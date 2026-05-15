<?php
$page_title = $page_title ?? "Explore";
$page_description = $page_description ?? "Gracious charity platform";
$meta_keywords = $meta_keywords ?? "";
$canonical_url = $canonical_url ?? \App\Helpers::siteUrl();
$brand_name = \App\Helpers::brandName("Gracious Charity");
$brand_logo = \App\Helpers::brandLogoPath("assets/images/logo_dark.svg");
$brand_favicon = \App\Helpers::brandFaviconPath("assets/images/favicon.ico");
$share_image = $share_image ?? \App\Helpers::siteUrl($brand_logo);
$structured_data = $structured_data ?? "";
$breadcrumb_title = $breadcrumb_title ?? $page_title;
$hero_title = $hero_title ?? $page_title;
$hero_label = $hero_label ?? "Explore";
$section_title = $section_title ?? "Explore";
$section_url = $section_url ?? "gallery";
$default_inner_page_banner = \App\Helpers::setting("inner_page_banner_image", "assets/images/breadcrumbs_bg.jpg");
$hero_bg_image = $hero_bg_image ?? $default_inner_page_banner;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?php echo htmlspecialchars(\App\Helpers::siteUrl()); ?>/">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="author" content="<?php echo htmlspecialchars($brand_name); ?>">
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <?php if ($meta_keywords !== ""): ?>
        <meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords); ?>">
    <?php endif; ?>
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($share_image); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($share_image); ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo htmlspecialchars($brand_favicon); ?>">
    <link href="assets/library/animate/animate.min.css" rel="stylesheet">
    <link href="assets/library/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/library/icofont/icofont.min.css" rel="stylesheet">
    <link href="assets/library/owlcarousel/css/owl.carousel.min.css" rel="stylesheet">
    <link href="assets/library/select2/css/select2.min.css" rel="stylesheet">
    <link href="assets/library/magnific-popup/magnific-popup.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/explore-pages.css" rel="stylesheet">
    <?php if ($structured_data !== ""): ?>
        <script type="application/ld+json"><?php echo $structured_data; ?></script>
    <?php endif; ?>
</head>
<body>
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

    <header>
        <nav class="navbar navbar-expand-lg header-fullpage">
            <div class="container text-nowrap">
                <div class="d-flex align-items-center w-100 col p-0 logo-brand">
                    <a class="navbar-brand rounded-bottom light-bg" href="<?php echo htmlspecialchars(\App\Helpers::siteUrl()); ?>">
                        <img class="site-logo site-logo--header" src="<?php echo htmlspecialchars($brand_logo); ?>" alt="<?php echo htmlspecialchars($brand_name); ?>">
                    </a>
                </div>
                <div class="d-inline-flex request-btn order-lg-last col-auto p-0 align-items-center">
                    <a class="btn-outline-primary btn ms-3" href="#" id="search_home"><i data-feather="search"></i></a>
                    <a class="nav-link btn btn-default ms-3 donate-btn" href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('donation-page')); ?>">Donate</a>
                    <button class="navbar-toggler x collapsed" type="button" data-bs-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>

                <div class="navbar-collapse">
                    <div class="offcanvas-header">
                        <a href="<?php echo htmlspecialchars(\App\Helpers::siteUrl()); ?>" class="logo-small">
                            <img class="site-logo site-logo--header" src="<?php echo htmlspecialchars($brand_logo); ?>" alt="<?php echo htmlspecialchars($brand_name); ?>">
                        </a>
                    </div>
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
                                    <li><a class="dropdown-item" href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('gallery')); ?>">Photo &amp; Video Gallery</a></li>
                                    <li><a class="dropdown-item" href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('partners-sponsors')); ?>">Partners &amp; Sponsors</a></li>
                                    <li><a class="dropdown-item" href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('causes-projects')); ?>">Our Causes</a></li>
                                    <li><a class="dropdown-item" href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('programme')); ?>">Programme</a></li>
                                    <li><a class="dropdown-item" href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('volunteer')); ?>">Volunteer</a></li>
                                    <li><a class="dropdown-item" href="<?php echo htmlspecialchars(\App\Helpers::siteUrl('faqs')); ?>">FAQs</a></li>
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
                    <div class="close-nav"></div>
                </div>
            </div>
        </nav>
    </header>

    <section class="breadcrumbs-page-wrap">
        <div class="bg-fixed pos-rel breadcrumbs-page" style="background-image: url('<?php echo htmlspecialchars($hero_bg_image); ?>');">
            <div class="container">
                <h1><?php echo htmlspecialchars($hero_title); ?></h1>
                <nav aria-label="breadcrumb" class="breadcrumb-wrap">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo htmlspecialchars(\App\Helpers::siteUrl()); ?>">Home</a></li>
                        <?php if ($section_title !== "" && $section_title !== $breadcrumb_title): ?>
                            <li class="breadcrumb-item"><a href="<?php echo htmlspecialchars(\App\Helpers::siteUrl($section_url)); ?>"><?php echo htmlspecialchars($section_title); ?></a></li>
                        <?php endif; ?>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($breadcrumb_title); ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>

    <main id="body-content">
