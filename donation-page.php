<?php
require_once __DIR__ . '/config/autoload.php';
$brandName = \App\Helpers::brandName();
$brandLogo = \App\Helpers::brandLogoPath("assets/images/logo_dark.svg");
$brandFavicon = \App\Helpers::brandFaviconPath("assets/images/favicon.ico");
$innerPageBanner = (string)\App\Helpers::setting('inner_page_banner_image', 'assets/images/breadcrumbs_bg.jpg');
$donationMetaTitle = (string)\App\Helpers::setting('donation_meta_title', 'Donate | ' . $brandName);
$donationMetaDescription = (string)\App\Helpers::setting('donation_meta_description', 'Support Friends at Heart Welfare Initiative by donating to children, families and community care programmes that restore dignity and hope.');
$publishedPartners = \App\Database::fetchAll("SELECT * FROM partners WHERE status = 'published' ORDER BY sort_order ASC, name ASC") ?: [];
$donationPartnerLogos = array_values(array_filter($publishedPartners, static function ($partner) {
    return trim((string)($partner['logo_path'] ?? '')) !== '';
}));
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
    <title><?php echo htmlspecialchars($donationMetaTitle); ?></title>
    <meta name="author" content="<?php echo htmlspecialchars($brandName); ?>">     
    <meta name="description" content="<?php echo htmlspecialchars($donationMetaDescription); ?>">
    <meta name="keywords" content="donate to Friends at Heart Welfare Initiative, FAHWI donation, charity donation, NGO donation, support families, support children">
    
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
    <header>
        <!-- Main Navigation Start -->
        <nav class="navbar navbar-expand-lg header-fullpage">
            <div class="container text-nowrap">
                <div class="d-flex align-items-center w-100 col p-0 logo-brand">
                    <a class="navbar-brand rounded-bottom light-bg" href="index.php">
                        <img class="site-logo site-logo--header" src="<?php echo htmlspecialchars($brandLogo); ?>" alt="<?php echo htmlspecialchars($brandName); ?>">
                    </a> 
                </div>
                <!-- Topbar Buttons Start -->
                <div class="d-inline-flex request-btn order-lg-last col-auto p-0 align-items-center"> 
                    <a class="btn-outline-primary btn ms-3" href="#" id="search_home"><i data-feather="search"></i></a>

                    <a class="nav-link btn btn-default ms-3 donate-btn" href="donation-page.php">Donate</a>

                    <!-- Toggle Button Start -->
                    <button class="navbar-toggler x collapsed" type="button" data-bs-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
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
                        <a href="index.php" class="logo-small">
                            <img class="site-logo site-logo--header" src="<?php echo htmlspecialchars($brandLogo); ?>" alt="<?php echo htmlspecialchars($brandName); ?>">
                        </a>                        
                    </div>
                    <!-- Mobile Logo -->
                    <!-- Mobile Menu -->
                    <div class="offcanvas-body">
                        <ul class="navbar-nav ms-auto">
                                                        <li class="nav-item">
                                <a class="nav-link" href="index.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="about-us.php">About Us</a>
                            </li>
                            
                                                        <li class="nav-item dropdown">
                                <a class="nav-link has-children" href="index.php" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Explore</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="gallery.php">Photo & Video Gallery</a></li>
                                    <li><a class="dropdown-item" href="partners-sponsors.php">Partners & Sponsors</a></li>
                                    <li><a class="dropdown-item" href="projects-programmes.php">Projects & Programmes</a></li>
                                    <li><a class="dropdown-item" href="faqs.php">FAQs</a></li>
                                </ul>
                            </li>
                                                        <li class="nav-item">
                                <a class="nav-link" href="events.php">Events</a>
                            </li>                        
                                                        <li class="nav-item">
                                <a class="nav-link" href="blog.php">Blog</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="contact-us.php">Contact</a>
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

    <!-- Page Breadcrumbs Start -->
    <section class="breadcrumbs-page-wrap">
        <div class="bg-fixed pos-rel breadcrumbs-page" style="background-image: url('<?php echo htmlspecialchars($innerPageBanner); ?>');">
            <div class="container">
                <h1>Donation</h1>
                <nav aria-label="breadcrumb" class="breadcrumb-wrap">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Donation</li>
                    </ol>
                </nav>  
            </div>
        </div>
    </section>
    <!-- Page Breadcrumbs End -->

    <!-- Main Body Content Start -->
    <main id="body-content">

        <!-- About Us Style Start -->
        <section class="wide-tb-100">
            <div class="container">
                <div class="row">                    
                    <div class="col-lg-8 col-md-12">
                        <h1 class="heading-main">
                            <small>Donation</small>
                            Don't Let Poverty Destroy Someone's Dreams
                        </h1>

                        <p>The secret to happiness lies in helping others. Never underestimate the difference YOU can make in the lives of the poor, the abused and the helpless. Spread sunshine in their lives no matter what the weather may be.</p>

                        <form action="includes/paystack_initialize.php" method="POST" id="donationForm">
                            <div class="donation-wrap">
                                <h3 class="h3-sm fw-5 txt-blue mb-3">Select Your Donation Amount</h3>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input form-light" type="radio" name="amount_radio" id="amount1" value="10000" checked>
                                                <label class="form-check-label label-dark" for="amount1">₦10,000</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input form-light" type="radio" name="amount_radio" id="amount2" value="20000">
                                                <label class="form-check-label label-dark" for="amount2">₦20,000</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input form-light" type="radio" name="amount_radio" id="amount4" value="50000">
                                                <label class="form-check-label label-dark" for="amount4">₦50,000</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input form-light" type="radio" name="amount_radio" id="amount5" value="100000">
                                                <label class="form-check-label label-dark" for="amount5">₦100,000</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input form-light" type="radio" name="amount_radio" id="amount6" value="200000">
                                                <label class="form-check-label label-dark" for="amount6">₦200,000</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input form-light" type="radio" name="amount_radio" id="amount7" value="500000">
                                                <label class="form-check-label label-dark" for="amount7">₦500,000</label>
                                            </div>
                                            <div class="mt-3">
                                                <input type="number" class="form-control" name="amount_custom" id="custom" placeholder="Custom Amount (₦)">
                                            </div>
                                            <input type="hidden" name="amount" id="final_amount" value="10000">
                                        </div>
                                        <div class="paystack-footer mt-2 mb-4 text-start">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="icofont-lock text-muted small"></i>
                                                <span class="text-muted" style="font-size: 12px;">Secured by Paystack. Cards, Transfer & USSD supported.</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <p class="text-primary">Your donation helps provide clean water, food, and education to those in need.</p>
                                        <div class="border-top mb-4"></div>                                    
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" name="first_name" class="form-control" id="name" placeholder="First Name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" name="last_name" class="form-control" id="last_name" placeholder="Last Name" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="email" name="email" class="form-control" id="email" placeholder="Your Email" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="text" name="zip" class="form-control" id="zip" placeholder="Zip Code">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <select class="form-control" name="country" data-no-select2="true">
                                                <option value="">Select Country</option>
                                                <option value="Nigeria">Nigeria</option>
                                                <option value="United States">United States</option>
                                                <option value="United Kingdom">United Kingdom</option>
                                                <option value="Ghana">Ghana</option>
                                                <option value="Kenya">Kenya</option>
                                                <option value="Canada">Canada</option>
                                                <option value="Others">Others</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-default"><i data-feather="heart"></i> Donate Now</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const finalAmount = document.getElementById('final_amount');
                            const customInput = document.getElementById('custom');
                            const radios = document.querySelectorAll('input[name="amount_radio"]');
                            const countrySelect = document.querySelector('select[name="country"][data-no-select2="true"]');

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
                                    first.checked = true;
                                    finalAmount.value = first.value;
                                }
                            });

                            if (countrySelect && window.jQuery) {
                                const $countrySelect = window.jQuery(countrySelect);
                                if ($countrySelect.hasClass('select2-hidden-accessible')) {
                                    $countrySelect.select2('destroy');
                                }
                                window.jQuery(countrySelect)
                                    .siblings('.select2')
                                    .remove();
                            }
                        });
                        </script>

                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="faqs-sidebar pos-rel">
                            <div class="bg-overlay blue opacity-80"></div>
                            <div id="donationContactSuccess"></div>
                            <form action="#" method="post" id="contact_form" data-success-target="#donationContactSuccess" novalidate="novalidate">
                                <h3 class="h3-sm fw-7 txt-white mb-3">Have any Question?</h3>
                                <div class="form-group">
                                    <label for="donation_contact_name"><strong>Full Name</strong></label>
                                    <input type="text" class="form-control form-light" id="donation_contact_name" name="name">
                                </div>
                                <div class="form-group">
                                    <label for="donation_contact_email"><strong>Email Address</strong></label>
                                    <input type="email" class="form-control form-light" id="donation_contact_email" name="email">
                                </div>
                                <div class="form-group">
                                    <label for="donation_contact_comment"><strong>How can help you?</strong></label>
                                    <textarea class="form-control form-light" rows="5" id="donation_contact_comment" name="comment"></textarea>
                                </div>
                                <input type="hidden" name="lastname" value="Donation Enquiry">
                                <input type="hidden" name="phone" value="">
                                <input type="hidden" name="subject" value="Donation page enquiry">
                                <button type="submit" class="btn btn-default mt-3">Ask It Now</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- About Us Style Start -->

        <!-- Callout Style Start -->
        <section class="wide-tb-150 bg-scroll bg-img-6 pos-rel callout-style-1">
            <div class="bg-overlay blue opacity-80"></div>
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-7">
                        <h1 class="heading-main light-mode">
                            <small>Help Other People</small>
                            We Dream to Create A Bright Future Of The Underprivileged Children
                        </h1>
                    </div>
                    <div class="col-sm-12 text-md-end">
                        <a href="donation-page.php" class="btn btn-default">Donate Now</a>
                    </div>
                </div>
            </div>
        </section>
        <!-- Callout Style End -->

        <!-- Our Partners Start -->
        <section class="wide-tb-100">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <h1 class="heading-main">
                            <small>Partnership Network</small>
                            Organisations and supporters helping us extend our reach
                        </h1>
                    </div>
                    <div class="col-sm-12">
                        <?php if ($donationPartnerLogos): ?>
                        <div class="owl-carousel owl-theme" id="home-clients">
                                <?php foreach ($donationPartnerLogos as $partner): ?>
                                    <div class="item">
                                        <div class="clients-logo">
                                            <?php if (!empty($partner['website_url'])): ?>
                                                <a href="<?php echo htmlspecialchars((string)$partner['website_url']); ?>" target="_blank" rel="noopener">
                                                    <img
                                                        src="<?php echo htmlspecialchars((string)$partner['logo_path']); ?>"
                                                        alt="<?php echo htmlspecialchars((string)($partner['name'] ?: 'Partner')); ?>"
                                                    >
                                                </a>
                                            <?php else: ?>
                                                <img
                                                    src="<?php echo htmlspecialchars((string)$partner['logo_path']); ?>"
                                                    alt="<?php echo htmlspecialchars((string)($partner['name'] ?: 'Partner')); ?>"
                                                >
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="explore-accent-block partner-fallback-block" style="padding:40px 42px; border-radius:30px;">
                            <div class="row align-items-center g-4">
                                <div class="col-lg-8">
                                    <div class="explore-kicker">Partnerships and Institutional Alignment</div>
                                    <h3 class="mb-3">We are building a strong network of individuals, organisations and mission-aligned supporters.</h3>
                                    <p class="mb-0">As partnerships are confirmed and logo permissions are received, approved partner identities will appear here. Until then, this section reflects our active commitment to credible collaboration and responsible growth.</p>
                                </div>
                                <div class="col-lg-4">
                                    <div class="partner-fallback-actions">
                                        <a href="partners-sponsors.php" class="btn btn-default">Become a Partner</a>
                                        <a href="contact-us.php" class="btn btn-outline-dark">Contact Us</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        <!-- Our Partners End -->

        <style>
            .partner-fallback-block .partner-fallback-actions{
                display:flex;
                flex-direction:column;
                align-items:flex-end;
                gap:16px;
            }
            .partner-fallback-block .partner-fallback-actions .btn{
                min-width:260px;
                justify-content:center;
                text-align:center;
            }
            .faqs-sidebar #donationContactSuccess,
            .faqs-sidebar form{
                position: relative;
                z-index: 2;
            }
            .faqs-sidebar #donationContactSuccess .alert{
                border: 0 !important;
                border-radius: 16px !important;
                padding: 18px 20px !important;
                margin-bottom: 18px !important;
                font-size: 1rem !important;
                line-height: 1.65 !important;
                font-weight: 600 !important;
                box-shadow: 0 14px 30px rgba(8, 15, 31, 0.18) !important;
            }
            .faqs-sidebar #donationContactSuccess .alert-success{
                background: #ecf9f2 !important;
                color: #113b2c !important;
            }
            .faqs-sidebar #donationContactSuccess .alert-danger{
                background: #ffefef !important;
                color: #7a1f1f !important;
            }
            @media (max-width: 991px) {
                .partner-fallback-block .partner-fallback-actions{
                    align-items:flex-start;
                }
            }
        </style>
        
           
    </main>
    <?php require __DIR__ . "/includes/site-footer.php"; ?>
