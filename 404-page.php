<?php
declare(strict_types=1);

http_response_code(404);
require __DIR__ . "/config/autoload.php";

use App\Helpers;

$page_title = "404 – Page Not Found";
$page_description = "The page you are looking for could not be found.";
$hero_title = "Page Not Found";
$section_title = "Error";
$section_url = "";
$breadcrumb_title = "404 Error";

require __DIR__ . "/includes/header.php";
?>

<section class="wide-tb-100">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12 mb-5 text-center">
                <h1 class="heading-main">
                    <small>404 Error</small>
                    Oops!
                </h1>
                <p>Something is broken, please try again later or go to home page</p>
                <a class="btn btn-primary mt-4" href="<?php echo Helpers::e(Helpers::siteUrl()); ?>">Back to Home</a>
            </div>
            <div class="col-lg-12">
                <div class="text-center error-img">
                    <img src="<?php echo Helpers::e(Helpers::siteUrl("assets/images/404_img.png")); ?>" alt="404">
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . "/includes/footer.php"; ?>
