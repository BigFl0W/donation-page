<?php
require_once __DIR__ . '/config/autoload.php';

use App\Database;
use App\Helpers;

$query = trim((string)($_GET['q'] ?? ''));
$brandName = Helpers::brandName();
$page_title = $query !== '' ? 'Search results for "' . $query . '" | ' . $brandName : 'Search | ' . $brandName;
$page_description = $query !== '' ? 'Search results across blog posts, events, and programmes on ' . $brandName . '.' : 'Search across blog posts, events, and programmes on ' . $brandName . '.';
$breadcrumb_title = 'Search';
$hero_title = 'Search';
$hero_label = 'Site Search';
$section_title = 'Search';
$section_url = 'search.php';

$results = [];

if ($query !== '' && Database::available()) {
    $like = '%' . $query . '%';

    $programmeResults = Database::fetchAll(
        "SELECT id, title, slug, summary, featured_image, category
         FROM programmes
         WHERE status = 'published'
           AND (
               title LIKE :like
               OR category LIKE :like
               OR summary LIKE :like
               OR mission_statement LIKE :like
               OR content LIKE :like
           )
         ORDER BY updated_at DESC
         LIMIT 8",
        ['like' => $like]
    );

    foreach ($programmeResults as $programme) {
        $results[] = [
            'type' => 'Programme',
            'title' => (string)($programme['title'] ?? 'Untitled programme'),
            'summary' => trim((string)($programme['summary'] ?? '')),
            'url' => Helpers::siteUrl('cause/' . rawurlencode((string)($programme['slug'] ?? (string)$programme['id']))),
            'meta' => trim((string)($programme['category'] ?? '')),
            'image' => trim((string)($programme['featured_image'] ?? '')),
        ];
    }

    $eventResults = Database::fetchAll(
        "SELECT id, title, slug, summary, featured_image, city, venue, event_start
         FROM events
         WHERE status = 'published'
           AND (
               title LIKE :like
               OR summary LIKE :like
               OR content LIKE :like
               OR city LIKE :like
               OR venue LIKE :like
           )
         ORDER BY COALESCE(event_start, created_at) DESC
         LIMIT 8",
        ['like' => $like]
    );

    foreach ($eventResults as $event) {
        $eventDate = !empty($event['event_start']) ? date('M j, Y', strtotime((string)$event['event_start'])) : '';
        $location = trim(implode(' • ', array_filter([
            $eventDate,
            (string)($event['city'] ?: $event['venue'] ?? ''),
        ])));

        $results[] = [
            'type' => 'Event',
            'title' => (string)($event['title'] ?? 'Untitled event'),
            'summary' => trim((string)($event['summary'] ?? '')),
            'url' => Helpers::siteUrl('event/' . rawurlencode((string)($event['slug'] ?? (string)$event['id']))),
            'meta' => $location,
            'image' => trim((string)($event['featured_image'] ?? '')),
        ];
    }

    $postResults = Database::fetchAll(
        "SELECT title, slug, category, excerpt, featured_image, published_at, created_at
         FROM posts
         WHERE status = 'published'
           AND (
               title LIKE :like
               OR category LIKE :like
               OR excerpt LIKE :like
               OR content LIKE :like
               OR seo_keywords LIKE :like
           )
         ORDER BY COALESCE(published_at, created_at) DESC
         LIMIT 10",
        ['like' => $like]
    );

    foreach ($postResults as $post) {
        $dateLabel = !empty($post['published_at']) ? date('M j, Y', strtotime((string)$post['published_at'])) : '';
        $category = trim((string)($post['category'] ?? ''));
        $postMeta = trim(implode(' • ', array_filter([$category, $dateLabel])));

        $results[] = [
            'type' => 'Blog Post',
            'title' => (string)($post['title'] ?? 'Untitled story'),
            'summary' => trim((string)($post['excerpt'] ?? '')),
            'url' => Helpers::siteUrl('blog/' . Helpers::slugify($category !== '' ? $category : 'general') . '/' . rawurlencode((string)($post['slug'] ?? ''))),
            'meta' => $postMeta,
            'image' => trim((string)($post['featured_image'] ?? '')),
        ];
    }
}

require __DIR__ . '/includes/header.php';
?>

<section class="wide-tb-100">
    <div class="container">
        <div class="search-page-shell">
            <div class="search-page-intro">
                <div class="search-page-kicker">Site Search</div>
                <h2><?php echo Helpers::e($query !== '' ? 'Search results for "' . $query . '"' : 'Find posts, events, and programmes'); ?></h2>
                <p><?php echo Helpers::e($query !== '' ? 'Browse the most relevant matches from across the website.' : 'Use a keyword to search published blog posts, events, and programmes.'); ?></p>
            </div>

            <form class="search-page-form" action="<?php echo Helpers::e(Helpers::siteUrl('search.php')); ?>" method="get">
                <input type="text" name="q" value="<?php echo Helpers::e($query); ?>" placeholder="Search posts, events, programmes..." aria-label="Search site">
                <button type="submit" class="btn btn-default">Search</button>
            </form>

            <?php if ($query === ''): ?>
                <div class="search-empty-card">
                    <h3>Start with a keyword</h3>
                    <p>Try a topic like outreach, education, event, donation, volunteer, or impact story.</p>
                </div>
            <?php elseif (!$results): ?>
                <div class="search-empty-card">
                    <h3>No results found</h3>
                    <p>We couldn’t find a match for "<?php echo Helpers::e($query); ?>". Try a different keyword or browse the main sections of the site.</p>
                </div>
            <?php else: ?>
                <div class="search-results-grid">
                    <?php foreach ($results as $result): ?>
                        <article class="search-result-card">
                            <div class="search-result-type"><?php echo Helpers::e($result['type']); ?></div>
                            <h3><a href="<?php echo Helpers::e($result['url']); ?>"><?php echo Helpers::e($result['title']); ?></a></h3>
                            <?php if ($result['meta'] !== ''): ?>
                                <div class="search-result-meta"><?php echo Helpers::e($result['meta']); ?></div>
                            <?php endif; ?>
                            <p><?php echo Helpers::e($result['summary'] !== '' ? $result['summary'] : 'Open this result to view the full content.'); ?></p>
                            <a class="search-result-link" href="<?php echo Helpers::e($result['url']); ?>">Open Result</a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
    .search-page-shell {
        display: grid;
        gap: 28px;
    }
    .search-page-kicker {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        color: #4e7a64;
        font-size: 0.8rem;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .search-page-kicker::before {
        content: "";
        width: 30px;
        height: 2px;
        background: #d59b2d;
    }
    .search-page-intro h2 {
        margin-bottom: 10px;
        color: #1f2a44;
        font-size: clamp(2rem, 4vw, 3.1rem);
        line-height: 1.1;
    }
    .search-page-intro p {
        max-width: 64ch;
        margin: 0;
        color: #5f6986;
        font-size: 1rem;
        line-height: 1.8;
    }
    .search-page-form {
        display: flex;
        gap: 14px;
        align-items: center;
    }
    .search-page-form input {
        flex: 1;
        min-height: 62px;
        padding: 0 20px;
        border-radius: 18px;
        border: 1px solid rgba(31, 42, 68, 0.12);
        background: #fff;
        color: #1f2a44;
        font-size: 1rem;
    }
    .search-empty-card,
    .search-result-card {
        padding: 30px;
        border-radius: 28px;
        background: linear-gradient(135deg, #ffffff 0%, #f8f2e7 100%);
        border: 1px solid rgba(213, 155, 45, 0.16);
        box-shadow: 0 20px 48px rgba(17, 51, 42, 0.07);
    }
    .search-empty-card h3,
    .search-result-card h3 {
        margin-bottom: 10px;
        color: #1f2a44;
    }
    .search-empty-card p,
    .search-result-card p {
        margin: 0;
        color: #5f6986;
        line-height: 1.8;
    }
    .search-results-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 22px;
    }
    .search-result-type {
        margin-bottom: 10px;
        color: #4e7a64;
        font-size: 0.8rem;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .search-result-meta {
        margin-bottom: 12px;
        color: #8a6f39;
        font-size: 0.88rem;
        font-weight: 700;
    }
    .search-result-card a {
        color: inherit;
        text-decoration: none;
    }
    .search-result-link {
        display: inline-flex;
        margin-top: 16px;
        color: #d59b2d !important;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        font-size: 0.82rem;
    }
    @media (max-width: 991px) {
        .search-results-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 767px) {
        .search-page-form {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<?php require __DIR__ . '/includes/site-footer.php'; ?>
