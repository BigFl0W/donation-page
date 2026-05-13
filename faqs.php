<?php
require __DIR__ . "/config/autoload.php";

use App\Helpers;

$page_title = trim((string) Helpers::setting('faq_page_title', 'FAQs'));
$breadcrumb_title = "FAQs";
$hero_title = trim((string) Helpers::setting('faq_hero_title', 'Frequently Asked Questions'));
$heroKicker = trim((string) Helpers::setting('faq_hero_kicker', 'Helpful Answers'));
$heroIntro = trim((string) Helpers::setting('faq_hero_intro', 'Clear information for donors, volunteers, partners, and beneficiaries.'));
$contactKicker = trim((string) Helpers::setting('faq_contact_kicker', 'Need More Help?'));
$contactTitle = trim((string) Helpers::setting('faq_contact_title', 'Speak with the team directly.'));
$contactText = trim((string) Helpers::setting('faq_contact_text', 'Still need clarity? Reach out and our team will guide you directly.'));
$contactButtonLabel = trim((string) Helpers::setting('faq_contact_button_label', 'Contact Us'));
$contactButtonUrl = trim((string) Helpers::setting('faq_contact_button_url', 'contact-us'));
$topicsKicker = trim((string) Helpers::setting('faq_topics_kicker', 'Popular Topics'));

$faqItems = [];
for ($i = 1; $i <= 6; $i++) {
    $question = trim((string) Helpers::setting("faq_item_{$i}_question", ''));
    $answer = trim((string) Helpers::setting("faq_item_{$i}_answer", ''));
    if ($question !== '' && $answer !== '') {
        $faqItems[] = ['question' => $question, 'answer' => $answer];
    }
}
if (!$faqItems) {
    $faqItems = [
        ['question' => 'How can someone support the organisation?', 'answer' => 'Support can come through donations, sponsorships, volunteering, media partnerships, or programme collaboration.'],
        ['question' => 'Can partners sponsor a specific project or campaign?', 'answer' => 'Yes. We welcome aligned partners who want to support specific interventions, campaigns, or beneficiary groups.'],
        ['question' => 'How will updates be shared with supporters?', 'answer' => 'Updates can be shared through the gallery, blog posts, programme pages, newsletters, and direct communication.'],
        ['question' => 'Do donors receive receipts after giving?', 'answer' => 'Yes. Successful donors receive an email receipt automatically once the payment is confirmed.'],
    ];
}

$topics = [];
for ($i = 1; $i <= 5; $i++) {
    $topic = trim((string) Helpers::setting("faq_topic_{$i}", ''));
    if ($topic !== '') {
        $topics[] = $topic;
    }
}
if (!$topics) {
    $topics = [
        'Donations and receipts',
        'Volunteering and onboarding',
        'Partnership enquiries',
        'Programme eligibility questions',
        'Support response timelines',
    ];
}

require __DIR__ . "/includes/header.php";
?>

<section class="wide-tb-100">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="explore-faq-card">
                    <div class="explore-kicker"><?php echo Helpers::e($heroKicker); ?></div>
                    <h2 class="mb-4"><?php echo Helpers::e($heroIntro); ?></h2>
                    <div class="theme-collapse">
                        <?php foreach ($faqItems as $index => $item): ?>
                        <div class="toggle<?php echo $index === 0 ? ' arrow-down' : ''; ?>">
                            <span class="icon"><i class="icofont-plus"></i></span> <?php echo Helpers::e($item['question']); ?>
                        </div>
                        <div class="collapse<?php echo $index === 0 ? ' show' : ''; ?>">
                            <div class="content">
                                <?php echo nl2br(Helpers::e($item['answer'])); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="explore-contact-card mb-4">
                    <div class="explore-kicker"><?php echo Helpers::e($contactKicker); ?></div>
                    <h3><?php echo Helpers::e($contactTitle); ?></h3>
                    <p><?php echo Helpers::e($contactText); ?></p>
                    <a href="<?php echo Helpers::e(Helpers::siteUrl($contactButtonUrl)); ?>" class="btn btn-default"><?php echo Helpers::e($contactButtonLabel); ?></a>
                </div>
                <div class="explore-contact-card">
                    <div class="explore-kicker"><?php echo Helpers::e($topicsKicker); ?></div>
                    <ul class="explore-faq-list">
                        <?php foreach ($topics as $topic): ?>
                        <li><i class="icofont-check"></i><span><?php echo Helpers::e($topic); ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . "/includes/footer.php"; ?>
