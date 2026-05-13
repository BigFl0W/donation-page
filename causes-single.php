<?php
require_once 'config/autoload.php';

$id = $_GET['id'] ?? '';
$slug = $_GET['slug'] ?? '';

if (!empty($slug)) {
    $cause = \App\Database::fetchOne("SELECT * FROM programmes WHERE slug = :slug AND status != 'draft'", ['slug' => $slug]);
} elseif (!empty($id)) {
    $cause = \App\Database::fetchOne("SELECT * FROM programmes WHERE id = :id AND status != 'draft'", ['id' => (int)$id]);
} else {
    $cause = null;
}

if (!$cause) {
    header("Location: index.php");
    exit;
}

// Redirect to pretty URL if accessed via ID or direct PHP filename
$current_uri = $_SERVER['REQUEST_URI'];
$pretty_url = "cause/" . ($cause['slug'] ?: $cause['id']);
if (!empty($id) || strpos($current_uri, 'causes-single.php') !== false) {
    header("Location: " . \App\Helpers::siteUrl($pretty_url), true, 301);
    exit;
}

$page_title = $cause['title'];
$breadcrumb_title = $cause['title'];
$hero_title = $cause['title'];
$section_title = "Causes & Projects";
$section_url = "causes.php";

require_once 'includes/header.php';

$goal = (float)$cause['goal_amount'] > 0 ? (float)$cause['goal_amount'] : 1;
$raised = (float)$cause['raised_amount'];
$percent = min(100, round(($raised / $goal) * 100));
$image = !empty($cause['featured_image']) ? $cause['featured_image'] : 'assets/images/causes/causes_img_1.jpg';
$ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
$isVideo = in_array($ext, ['mp4', 'webm', 'ogg', 'mov']);
?>

<!-- Cause Details Section Start -->
<section class="wide-tb-100 bg-white">
    <div class="container">
        <div class="row g-5">
            <!-- Left Content Area -->
            <div class="col-lg-8 col-md-12">
                <div class="cause-details-wrap">
                    <!-- Media Wrap -->
                    <div class="img-wrap mb-4 shadow-sm overflow-hidden" style="border-radius: 12px; border: 1px solid #f0f0f0;">
                        <?php if ($isVideo): ?>
                            <video src="<?php echo htmlspecialchars($image); ?>" autoplay loop muted playsinline style="width: 100%; display: block; max-height: 450px; object-fit: cover;"></video>
                        <?php else: ?>
                            <img src="<?php echo htmlspecialchars($image); ?>" alt="" style="width: 100%; display: block; max-height: 450px; object-fit: cover;">
                        <?php endif; ?>
                    </div>
                    
                    <!-- Meta Info -->
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge bg-primary rounded-pill px-3 py-2" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;"><?php echo htmlspecialchars($cause['category'] ?? 'General'); ?></span>
                        <span class="text-muted small"><i class="icofont-calendar me-1"></i> <?php echo date('F d, Y', strtotime($cause['created_at'])); ?></span>
                    </div>

                    <!-- Title -->
                    <h2 class="mb-4 fw-bold" style="font-size: 1.85rem; color: #1a1a1a; letter-spacing: -0.5px;"><?php echo htmlspecialchars($cause['title']); ?></h2>
                    
                    <!-- Content -->
                    <div class="cause-content" style="font-size: 0.95rem; line-height: 1.75; color: #444;">
                        <?php echo nl2br(htmlspecialchars($cause['content'] ?? $cause['summary'] ?? '')); ?>
                    </div>
                </div>
            </div>
            
            <!-- Right Sidebar Area -->
            <div class="col-lg-4 col-md-12 mt-5 mt-lg-0">
                <div class="sidebar-widget donation-widget p-4 border-0 rounded-4 shadow-sm bg-white sticky-lg-top" style="top: 100px; z-index: 10; border: 1px solid #f0f0f0 !important;">
                    
                    <!-- Progress Section (Top of Sidebar) -->
                    <div class="progress-section mb-4 pb-4 border-bottom">
                        <h5 class="mb-3 fw-bold text-dark" style="font-size: 1.1rem;">Fundraising Progress</h5>
                        <div class="d-flex justify-content-between mb-2" style="font-size: 0.85rem;">
                            <span class="fw-bold text-primary">Raised: ₦<?php echo number_format($raised); ?></span>
                            <span class="text-muted">Goal: ₦<?php echo number_format($goal); ?></span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 20px; background-color: #ebebeb; overflow: hidden; border: 1px solid #e0e0e0;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                                 style="width: <?php echo $percent; ?>%; background-color: var(--primary-color, #D59B2D) !important; transition: width 1.5s ease-in-out;" 
                                 aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="mt-2 text-end">
                            <span class="badge rounded-pill bg-light text-primary border" style="font-size: 0.75rem;"><?php echo $percent; ?>% Completed</span>
                        </div>
                    </div>
                    
                    <!-- Donation Form -->
                    <div class="donation-form-wrap">
                        <?php if ($raised >= $goal): ?>
                            <div class="goal-achieved-box text-center p-4 rounded-3 shadow-sm" style="background-color: #e8f5e9; border: 1px solid #c8e6c9;">
                                <div class="mb-3">
                                    <i class="icofont-check-circled text-success" style="font-size: 3rem;"></i>
                                </div>
                                <h5 class="fw-bold text-success mb-2">Goal Achieved!</h5>
                                <p class="text-muted small mb-0">This project is fully funded. Thank you to everyone who contributed!</p>
                                <div class="mt-4">
                                    <a href="causes.php" class="btn btn-outline-success btn-sm px-4 rounded-pill">Explore Other Causes</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <h5 class="mb-3 fw-bold text-dark" style="font-size: 1.1rem;">Make a Donation</h5>
                            
                            <div class="mb-3">
                                <label class="form-label small text-muted mb-2">Select or Enter Amount (₦)</label>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <button class="btn btn-outline-secondary btn-sm preset-amount flex-fill py-2" data-amount="1000" style="font-size: 0.75rem; border-radius: 8px;">₦1,000</button>
                                    <button class="btn btn-outline-secondary btn-sm preset-amount flex-fill py-2" data-amount="5000" style="font-size: 0.75rem; border-radius: 8px;">₦5,000</button>
                                    <button class="btn btn-outline-secondary btn-sm preset-amount flex-fill py-2" data-amount="10000" style="font-size: 0.75rem; border-radius: 8px;">₦10,000</button>
                                </div>
                                <div class="input-group mb-3 border rounded-3 overflow-hidden shadow-sm">
                                    <span class="input-group-text border-0 bg-light fw-bold" style="font-size: 0.85rem;">₦</span>
                                    <input type="number" id="donation_amount" class="form-control border-0" placeholder="Custom Amount" style="font-size: 0.9rem; padding: 12px;">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label small text-muted mb-2">Your Email Address</label>
                                <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                    <span class="input-group-text border-0 bg-light"><i class="icofont-envelope text-muted"></i></span>
                                    <input type="email" id="donor_email" class="form-control border-0" placeholder="email@example.com" style="font-size: 0.9rem; padding: 12px;">
                                </div>
                            </div>
                            
                            <button class="btn btn-primary w-100 py-3 fw-bold shadow-sm" id="pay_button" style="border-radius: 12px; font-size: 1rem; transition: all 0.3s ease;">
                                <i class="icofont-heart me-2"></i>Donate Now
                            </button>
                            
                            <div class="text-center mt-4">
                                <div class="d-flex align-items-center justify-content-center gap-2 text-muted" style="font-size: 0.7rem; opacity: 0.8;">
                                    <i class="icofont-shield-alt fs-6"></i>
                                    <span>Secured by <strong>Paystack</strong> Payment Gateway</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
        </div>
    </div>
</section>

<style>
    .breadcrumb-item + .breadcrumb-item::before { content: "•"; color: var(--brand); font-weight: bold; }
    .breadcrumb-item.active { color: var(--brand); font-weight: 600; font-size: 0.85rem; }
    .breadcrumb { background: transparent; padding: 0; margin: 0; list-style: none; display: flex; flex-wrap: wrap; align-items: center; }
    .breadcrumb-item { list-style: none; font-size: 0.85rem; }
    .breadcrumb-item a { color: #666; text-decoration: none; transition: color 0.2s; }
    .breadcrumb-item a:hover { color: var(--brand); }
    .donation-widget { transition: all 0.3s ease; }
    .donation-widget:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important; }
    .preset-amount:hover, .preset-amount.active { background-color: var(--brand) !important; color: white !important; border-color: var(--brand) !important; }
    .input-group-text { color: var(--brand); }
    .cause-content p { margin-bottom: 1.25rem; }
    @media (max-width: 991px) {
        .sticky-lg-top { position: static !important; }
    }
</style>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
document.querySelectorAll('.preset-amount').forEach(btn => {
    btn.onclick = () => {
        document.querySelectorAll('.preset-amount').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('donation_amount').value = btn.dataset.amount;
    };
});

const payBtn = document.getElementById('pay_button');
if (payBtn) {
    payBtn.onclick = function(e) {
        e.preventDefault();
        const amount = document.getElementById('donation_amount').value;
        const email = document.getElementById('donor_email').value;
        
        if (!amount || amount < 100) {
            alert('Please enter a valid amount (minimum ₦100)');
            return;
        }
        if (!email) {
            alert('Please enter your email');
            return;
        }

        // Paystack integration
        const handler = PaystackPop.setup({
        key: '<?php echo $_ENV['PAYSTACK_PUBLIC_KEY'] ?? ''; ?>',
        email: email,
        amount: amount * 100, // Amount in kobo
        currency: 'NGN',
        ref: 'DON_' + Math.floor((Math.random() * 1000000000) + 1),
        metadata: {
            cause_id: <?php echo $cause['id']; ?>,
            custom_fields: [
                {
                    display_name: "Cause",
                    variable_name: "cause",
                    value: "<?php echo addslashes($cause['title']); ?>"
                }
            ]
        },
        callback: function(response) {
            // This is where you verify the payment on your server
            // and update the database
            fetch('ajax_handler.php?action=verify_payment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    reference: response.reference,
                    cause_id: <?php echo $cause['id']; ?>,
                    amount: amount
                })
            }).then(res => res.json()).then(data => {
                if (data.status === 'success') {
                    const successUrl = new URL('<?php echo \App\Helpers::siteUrl('success'); ?>');
                    successUrl.searchParams.set('amount', amount);
                    successUrl.searchParams.set('currency', 'NGN');
                    successUrl.searchParams.set('ref', response.reference);
                    successUrl.searchParams.set('campaign', '<?php echo addslashes($cause['title']); ?>');
                    window.location.href = successUrl.toString();
                } else {
                    alert('Payment verification failed.');
                }
            });
        },
        onClose: function() {
            console.log('Paystack window closed.');
        }
    });
    handler.openIframe();
    };
}
</script>

<?php require_once 'includes/footer.php'; ?>
