<?php
require_once 'config/autoload.php';

// Fetch all published causes
$allCauses = \App\Database::fetchAll("SELECT * FROM programmes WHERE status = 'published' ORDER BY created_at DESC");

$page_title = "Causes & Projects";
$breadcrumb_title = "Causes & Projects";
$hero_title = "Causes & Projects";
$hero_label = "What We Do";

require_once 'includes/header.php';
?>

<section class="wide-tb-100 pb-0">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <div class="mb-5">
                    <div class="explore-kicker" style="color: var(--primary-color); font-weight: 700; text-transform: uppercase; letter-spacing: 2px; font-size: 0.85rem; margin-bottom: 15px;">Our Mission in Action</div>
                    <h2 class="fw-bold mb-3" style="font-size: 2.5rem; color: #1c2339;">Explore Our Active Causes & Projects</h2>
                    <p class="text-muted mx-auto" style="max-width: 700px; font-size: 1.05rem; line-height: 1.6;">Every project here represents a community in need and a pathway to sustainable change. Your support directly fuels these interventions, providing measurable outcomes for those who need it most.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="wide-tb-100 bg-light-gray" style="background-color: #f8f9fa; padding: 80px 0;">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <?php if (!empty($allCauses)): ?>
                <?php foreach ($allCauses as $cause): ?>
                    <?php 
                        $goal = (float)$cause['goal_amount'] > 0 ? (float)$cause['goal_amount'] : 1;
                        $raised = (float)$cause['raised_amount'];
                        $percent = min(100, round(($raised / $goal) * 100));
                        $image = !empty($cause['featured_image']) ? $cause['featured_image'] : 'assets/images/causes/causes_img_1.jpg';
                        $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
                        $isVideo = in_array($ext, ['mp4', 'webm', 'ogg', 'mov']);
                    ?>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white" style="transition: all 0.3s ease-in-out;">
                            <div class="position-relative overflow-hidden" style="height: 220px;">
                                <a href="cause/<?php echo !empty($cause['slug']) ? $cause['slug'] : $cause['id']; ?>">
                                    <?php if ($isVideo): ?>
                                        <video src="<?php echo htmlspecialchars($image); ?>" autoplay loop muted playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                                    <?php else: ?>
                                        <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($cause['title']); ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                                    <?php endif; ?>
                                </a>
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.7rem; font-weight: 600; background-color: var(--primary-color, #D59B2D) !important;">
                                        <?php echo htmlspecialchars($cause['category'] ?? 'General'); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="card-body p-4 d-flex flex-column">
                                <h4 class="card-title mb-3" style="font-size: 1.25rem; font-weight: 800; line-height: 1.4;">
                                    <a href="cause/<?php echo !empty($cause['slug']) ? $cause['slug'] : $cause['id']; ?>" class="text-dark text-decoration-none" style="transition: color 0.3s ease;">
                                        <?php echo htmlspecialchars($cause['title']); ?>
                                    </a>
                                </h4>
                                <p class="card-text text-muted mb-4 small" style="line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?php echo htmlspecialchars($cause['summary'] ?? ''); ?>
                                </p>
                                
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="small fw-bold">
                                            <span class="text-primary" style="color: var(--primary-color, #D59B2D) !important;">₦<?php echo number_format($raised); ?></span>
                                            <span class="text-muted ms-1">Raised</span>
                                        </div>
                                        <div class="small text-muted fw-bold">
                                            Target: ₦<?php echo number_format($goal); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="progress mb-3" style="height: 8px; border-radius: 10px; background-color: #f1f3f5; overflow: hidden;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                                             style="width: <?php echo $percent; ?>%; background-color: var(--primary-color, #D59B2D) !important;" 
                                             aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.65rem; font-weight: 700; border-color: #dee2e6 !important;">
                                            <?php echo $percent; ?>% Complete
                                        </span>
                                        <a href="cause/<?php echo !empty($cause['slug']) ? $cause['slug'] : $cause['id']; ?>" class="btn btn-primary px-4 py-2 fw-bold" style="border-radius: 10px; font-size: 0.85rem;">Donate Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="mb-4">
                        <i class="icofont-exclamation-circle text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h4>No active causes found at the moment.</h4>
                    <p class="text-muted">Please check back later or contact us to learn more about our upcoming projects.</p>
                    <a href="index.php" class="btn btn-default mt-3">Return to Home</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
    .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.12) !important;
    }
    .card:hover img {
        transform: scale(1.05);
    }
    .card-title a:hover {
        color: var(--primary-color, #D59B2D) !important;
    }
    @media (max-width: 576px) {
        .card-body {
            padding: 1.5rem !important;
        }
    }
</style>

<?php require_once 'includes/footer.php'; ?>
