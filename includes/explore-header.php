<?php
$page_title = $page_title ?? "Explore";
$breadcrumb_title = $breadcrumb_title ?? $page_title;
$hero_title = $hero_title ?? $page_title;
$hero_label = $hero_label ?? "Explore";
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0 user-scalable=no">
    <title><?php echo htmlspecialchars($page_title); ?> | Gracious Charity</title>
    <meta name="author" content="Mannat Studio">
    <meta name="description" content="Gracious charity platform">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico">
    <link href="assets/library/animate/animate.min.css" rel="stylesheet">
    <link href="assets/library/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/library/icofont/icofont.min.css" rel="stylesheet">
    <link href="assets/library/owlcarousel/css/owl.carousel.min.css" rel="stylesheet">
    <link href="assets/library/select2/css/select2.min.css" rel="stylesheet">
    <link href="assets/library/magnific-popup/magnific-popup.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/explore-pages.css" rel="stylesheet">
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
                    <a class="navbar-brand rounded-bottom light-bg" href="index.php">
                        <img src="assets/images/logo_dark.svg" alt="Gracious">
                    </a>
                </div>
                <div class="d-inline-flex request-btn order-lg-last col-auto p-0 align-items-center">
                    <a class="btn-outline-primary btn ms-3" href="#" id="search_home"><i data-feather="search"></i></a>
                    <a class="nav-link btn btn-default ms-3 donate-btn" href="donation-page.php">Donate</a>
                    <button class="navbar-toggler x collapsed" type="button" data-bs-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>

                <div class="navbar-collapse">
                    <div class="offcanvas-header">
                        <a href="index.php" class="logo-small">
                            <img src="assets/images/logo_dark.svg" alt="Gracious">
                        </a>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="index.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="about-us.php">About Us</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link has-children" href="index.php" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Causes</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="causes-list.php">Causes List</a></li>
                                    <li><a class="dropdown-item" href="causes-single.php">Causes Single</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link has-children" href="gallery.php" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Explore</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="gallery.php">Photo &amp; Video Gallery</a></li>
                                    <li><a class="dropdown-item" href="partners-sponsors.php">Partners &amp; Sponsors</a></li>
                                    <li><a class="dropdown-item" href="projects-programmes.php">Projects &amp; Programmes</a></li>
                                    <li><a class="dropdown-item" href="faqs.php">FAQs</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link has-children" href="index.php" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Events</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="events-list.php">Events List</a></li>
                                    <li><a class="dropdown-item" href="events-alternate.php">Events Alternate</a></li>
                                    <li><a class="dropdown-item" href="events-single.php">Events Single</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link has-children" href="index.php" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Blog</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="blog-standard.php">Blog Standard</a></li>
                                    <li><a class="dropdown-item" href="blog-grid.php">Blog Grid</a></li>
                                    <li><a class="dropdown-item" href="blog-single.php">Blog Single</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="contact-us.php">Contact</a>
                            </li>
                        </ul>
                    </div>
                    <div class="close-nav"></div>
                </div>
            </div>
        </nav>
    </header>

    <section class="breadcrumbs-page-wrap">
        <div class="bg-fixed pos-rel breadcrumbs-page">
            <div class="container">
                <h1><?php echo htmlspecialchars($hero_title); ?></h1>
                <nav aria-label="breadcrumb" class="breadcrumb-wrap">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="gallery.php">Explore</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($breadcrumb_title); ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>

    <main id="body-content">
