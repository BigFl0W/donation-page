<<<<<<< HEAD
<?php
declare(strict_types=1);
require_once __DIR__ . "/../config/autoload.php";

use App\Auth;
use App\Helpers;
use App\Database;
use App\Content;

Auth::requireLogin();

$admin = Auth::current();
$adminName = $admin["name"] ?? "Admin";
$adminEmail = $admin["email"] ?? "";
$adminRole = $admin["role"] ?? "admin";
$adminInitials = "";
foreach (array_slice(preg_split('/\s+/', trim($adminName)) ?: [], 0, 2) as $p) {
    if ($p !== "") $adminInitials .= strtoupper($p[0]);
}
$adminInitials = $adminInitials ?: "AD";

// ─── DASHBOARD DATA ───────────────────────────────
$dbAvail = Database::available();

$totalDonationsYear = 0; $totalDonationsCurrency = "USD";
$totalDonationsAll = 0; $totalTxCount = 0;
$pendingReview = 0; $failedCount = 0;
$totalAdmins = 0; $activeAdmins = 0; $suspendedAdmins = 0;
$publishedPosts = 0; $draftPosts = 0;
$publishedEvents = 0; $upcomingEvents = [];
$activePartners = 0; $publishedProgrammes = 0;
$recentDonations = []; $recentPosts = []; $recentActivity = [];
$monthlyDonationData = []; $gatewayMix = [];
$partnersList = []; $galleryItems = []; $adminUsers = []; $recentLogins = []; $settings = [];
$programmes = []; $allDonations = [];

if ($dbAvail) {
    $r = Database::fetchOne("SELECT COALESCE(SUM(amount),0) AS t, COALESCE(MAX(currency),'USD') AS c FROM donations WHERE status='successful' AND YEAR(COALESCE(paid_at,created_at))=YEAR(CURRENT_DATE())");
    if ($r) { $totalDonationsYear = (float)$r["t"]; $totalDonationsCurrency = (string)$r["c"]; }

    $r = Database::fetchOne("SELECT COALESCE(SUM(amount),0) AS t FROM donations WHERE status='successful'");
    if ($r) $totalDonationsAll = (float)$r["t"];

    $r = Database::fetchOne("SELECT COUNT(*) AS t FROM donations");
    if ($r) $totalTxCount = (int)$r["t"];

    $r = Database::fetchOne("SELECT COUNT(*) AS t FROM donations WHERE status='pending'");
    if ($r) $pendingReview = (int)$r["t"];

    $r = Database::fetchOne("SELECT COUNT(*) AS t FROM donations WHERE status IN ('failed','refunded')");
    if ($r) $failedCount = (int)$r["t"];

    $r = Database::fetchOne("SELECT COUNT(*) AS t FROM admins");
    if ($r) $totalAdmins = (int)$r["t"];

    $r = Database::fetchOne("SELECT COUNT(*) AS t FROM admins WHERE status='active'");
    if ($r) $activeAdmins = (int)$r["t"];

    $r = Database::fetchOne("SELECT COUNT(*) AS t FROM admins WHERE status='suspended'");
    if ($r) $suspendedAdmins = (int)$r["t"];

    $r = Database::fetchOne("SELECT COUNT(*) AS t FROM posts WHERE status='published'");
    if ($r) $publishedPosts = (int)$r["t"];
    $r = Database::fetchOne("SELECT COUNT(*) AS t FROM posts WHERE status='draft'");
    if ($r) $draftPosts = (int)$r["t"];

    $r = Database::fetchOne("SELECT COUNT(*) AS t FROM events WHERE status='published'");
    if ($r) $publishedEvents = (int)$r["t"];

    $recentDonations = Database::fetchAll("SELECT donor_name,amount,currency,gateway,status,COALESCE(paid_at,created_at) AS dt FROM donations ORDER BY dt DESC LIMIT 5") ?: [];

    $recentPosts = Database::fetchAll("SELECT p.title,p.status,p.published_at,p.created_at,COALESCE(a.full_name,p.author_name,'Admin Team') AS author FROM posts p LEFT JOIN admins a ON a.id=p.author_id ORDER BY COALESCE(p.published_at,p.updated_at) DESC LIMIT 6") ?: [];

    $upcomingEvents = Database::fetchAll("SELECT title,venue,city,event_start,status FROM events WHERE status='published' AND event_start>=NOW() ORDER BY event_start LIMIT 5") ?: [];

    $r = Database::fetchOne("SELECT COUNT(*) AS t FROM partners WHERE status='published'");
    if ($r) $activePartners = (int)$r["t"];

    $r = Database::fetchOne("SELECT COUNT(*) AS t FROM programmes WHERE status='published'");
    if ($r) $publishedProgrammes = (int)$r["t"];

    // Monthly donation chart data (last 12 months)
    $rawMonthly = Database::fetchAll("SELECT DATE_FORMAT(COALESCE(paid_at,created_at),'%Y-%m') AS mo, SUM(amount) AS total FROM donations WHERE status='successful' AND COALESCE(paid_at,created_at)>=DATE_SUB(CURRENT_DATE(),INTERVAL 12 MONTH) GROUP BY mo ORDER BY mo ASC") ?: [];
    $monthlyLookup = [];
    foreach ($rawMonthly as $m) { $monthlyLookup[$m["mo"]] = (float)$m["total"]; }
    for ($i = 11; $i >= 0; $i--) {
        $mo = date("Y-m", strtotime("-{$i} month"));
        $monthlyDonationData[] = [
            "label" => date("M", strtotime($mo . "-01")),
            "total" => $monthlyLookup[$mo] ?? 0,
        ];
    }
    $maxMonthly = $monthlyDonationData ? max(1, ...array_column($monthlyDonationData, "total")) : 1;

    // Gateway mix
    $gwRaw = Database::fetchAll("SELECT gateway,COUNT(*) AS cnt,COALESCE(SUM(amount),0) AS total FROM donations GROUP BY gateway") ?: [];
    foreach ($gwRaw as $g) {
        $gatewayMix[] = ["name" => ucfirst((string)$g["gateway"]), "count" => (int)$g["cnt"], "total" => (float)$g["total"]];
    }
    $maxGwCount = $gatewayMix ? max(array_column($gatewayMix, "count")) : 1;

    // Recent activity (posts + donations)
    $actPosts = Database::fetchAll("SELECT p.title AS item,'post' AS type,p.updated_at AS dt FROM posts p ORDER BY p.updated_at DESC LIMIT 3") ?: [];
    $actDonations = Database::fetchAll("SELECT CONCAT('Donation: ',COALESCE(donor_name,'Anonymous')) AS item,'donation' AS type,COALESCE(paid_at,created_at) AS dt FROM donations ORDER BY dt DESC LIMIT 3") ?: [];
    $merged = array_merge($actPosts, $actDonations);
    usort($merged, fn($a, $b) => strtotime((string)($b["dt"] ?? "")) <=> strtotime((string)($a["dt"] ?? "")));
    $recentActivity = array_slice($merged, 0, 5);

    // Partners list
    $partnersList = Database::fetchAll("SELECT name,partner_type,description,status,created_at FROM partners WHERE status='published' ORDER BY created_at DESC LIMIT 6") ?: [];

    // Gallery items
    $galleryItems = Database::fetchAll("SELECT title,media_type,description,created_at FROM gallery_items WHERE status='published' ORDER BY created_at DESC LIMIT 8") ?: [];

    // Admin users
    $adminUsers = Database::fetchAll("SELECT a.id,a.full_name,a.email,a.status,a.last_login_at,a.created_at,r.name AS role_name FROM admins a LEFT JOIN roles r ON r.id=a.role_id ORDER BY a.created_at DESC LIMIT 10") ?: [];

    // Settings
    $settings = [];
    $rawSettings = Database::fetchAll("SELECT setting_key,setting_value FROM settings") ?: [];
    foreach ($rawSettings as $s) { $settings[$s["setting_key"]] = $s["setting_value"]; }

    // Security - recent admin logins
    $recentLogins = Database::fetchAll("SELECT full_name,email,last_login_at,status FROM admins ORDER BY last_login_at DESC LIMIT 5") ?: [];
}

// ─── HANDLE FORM SUBMISSIONS ───────────────────────
$flashMsg = ""; $flashType = "";

$csrfError = "";
if ($dbAvail && $_SERVER["REQUEST_METHOD"] === "POST") {
    // CSRF validation
    $submittedToken = (string)($_POST["_csrf_token"] ?? "");
    $sessionToken = $_SESSION["_csrf_token"] ?? "";
    if ($submittedToken === "" || !hash_equals($sessionToken, $submittedToken)) {
        $csrfError = "Invalid or expired form token. Please reload and try again.";
    }

    $action = (string) ($_POST["_action"] ?? "");

    if (!$csrfError):
    if ($action === "create_post") {
        $title = trim((string) ($_POST["title"] ?? ""));
        $slug = Helpers::slugify($title);
        $content = (string) ($_POST["content"] ?? "");
        $excerpt = (string) ($_POST["excerpt"] ?? "");
        $category = (string) ($_POST["category"] ?? "General");
        $authorName = (string) ($_POST["author_name"] ?? $adminName);
        $status = (string) ($_POST["status"] ?? "draft");
        $catSlug = Helpers::slugify($category);
        $permalink = "blog/" . $catSlug . "/" . $slug;

        if ($title !== "") {
            $exists = Database::fetchOne("SELECT id FROM posts WHERE slug = :slug", ["slug" => $slug]);
            if (!$exists) {
                Database::execute(
                    "INSERT INTO posts (title,slug,permalink_path,content,excerpt,category,author_name,status,published_at,created_at)
                     VALUES (:title,:slug,:permalink,:content,:excerpt,:category,:author_name,:status,:published_at,NOW())",
                    ["title" => $title, "slug" => $slug, "permalink" => $permalink, "content" => $content,
                     "excerpt" => $excerpt, "category" => $category, "author_name" => $authorName,
                     "status" => $status, "published_at" => $status === "published" ? date("Y-m-d H:i:s") : null]
                );
                $flashMsg = "Post created successfully"; $flashType = "success";
            } else { $flashMsg = "A post with this title already exists"; $flashType = "danger"; }
        } else { $flashMsg = "Title is required"; $flashType = "danger"; }
    }

    if ($action === "update_post") {
        $id = (int) ($_POST["id"] ?? 0);
        $title = trim((string) ($_POST["title"] ?? ""));
        $slug = Helpers::slugify($title);
        $content = (string) ($_POST["content"] ?? "");
        $excerpt = (string) ($_POST["excerpt"] ?? "");
        $category = (string) ($_POST["category"] ?? "General");
        $authorName = (string) ($_POST["author_name"] ?? $adminName);
        $status = (string) ($_POST["status"] ?? "draft");
        $catSlug = Helpers::slugify($category);
        $permalink = "blog/" . $catSlug . "/" . $slug;

        if ($title !== "" && $id > 0) {
            Database::execute(
                "UPDATE posts SET title=:title,slug=:slug,permalink_path=:permalink,content=:content,excerpt=:excerpt,
                 category=:category,author_name=:author_name,status=:status,
                 published_at=IF(:status='published' AND published_at IS NULL,NOW(),published_at)
                 WHERE id=:id",
                ["title" => $title, "slug" => $slug, "permalink" => $permalink, "content" => $content,
                 "excerpt" => $excerpt, "category" => $category, "author_name" => $authorName,
                 "status" => $status, "id" => $id]
            );
            $flashMsg = "Post updated successfully"; $flashType = "success";
        } else { $flashMsg = "Invalid request"; $flashType = "danger"; }
    }

    if ($action === "delete_post") {
        $id = (int) ($_POST["id"] ?? 0);
        if ($id > 0) { Database::execute("DELETE FROM posts WHERE id=:id", ["id" => $id]); $flashMsg = "Post deleted"; $flashType = "success"; }
    }

    if ($action === "create_event") {
        $title = trim((string) ($_POST["title"] ?? ""));
        $slug = Helpers::slugify($title);
        $summary = (string) ($_POST["summary"] ?? "");
        $content = (string) ($_POST["content"] ?? "");
        $venue = (string) ($_POST["venue"] ?? "");
        $city = (string) ($_POST["city"] ?? "");
        $eventStart = (string) ($_POST["event_start"] ?? "");
        $eventEnd = (string) ($_POST["event_end"] ?? "");
        $status = (string) ($_POST["status"] ?? "draft");

        if ($title !== "" && $eventStart !== "") {
            $exists = Database::fetchOne("SELECT id FROM events WHERE slug = :slug", ["slug" => $slug]);
            if (!$exists) {
                Database::execute(
                    "INSERT INTO events (title,slug,summary,content,venue,city,event_start,event_end,status,created_by,created_at)
                     VALUES (:title,:slug,:summary,:content,:venue,:city,:event_start,:event_end,:status,:created_by,NOW())",
                    ["title" => $title, "slug" => $slug, "summary" => $summary, "content" => $content,
                     "venue" => $venue, "city" => $city, "event_start" => $eventStart,
                     "event_end" => $eventEnd ?: null, "status" => $status, "created_by" => (int)($admin["id"] ?? 0)]
                );
                $flashMsg = "Event created successfully"; $flashType = "success";
            } else { $flashMsg = "An event with this title already exists"; $flashType = "danger"; }
        } else { $flashMsg = "Title and start date are required"; $flashType = "danger"; }
    }

    if ($action === "update_event") {
        $id = (int) ($_POST["id"] ?? 0);
        $title = trim((string) ($_POST["title"] ?? ""));
        $slug = Helpers::slugify($title);
        $summary = (string) ($_POST["summary"] ?? "");
        $content = (string) ($_POST["content"] ?? "");
        $venue = (string) ($_POST["venue"] ?? "");
        $city = (string) ($_POST["city"] ?? "");
        $eventStart = (string) ($_POST["event_start"] ?? "");
        $eventEnd = (string) ($_POST["event_end"] ?? "");
        $status = (string) ($_POST["status"] ?? "draft");

        if ($title !== "" && $eventStart !== "" && $id > 0) {
            Database::execute(
                "UPDATE events SET title=:title,slug=:slug,summary=:summary,content=:content,
                 venue=:venue,city=:city,event_start=:event_start,event_end=:event_end,status=:status
                 WHERE id=:id",
                ["title" => $title, "slug" => $slug, "summary" => $summary, "content" => $content,
                 "venue" => $venue, "city" => $city, "event_start" => $eventStart,
                 "event_end" => $eventEnd ?: null, "status" => $status, "id" => $id]
            );
            $flashMsg = "Event updated successfully"; $flashType = "success";
        } else { $flashMsg = "Invalid request"; $flashType = "danger"; }
    }

    if ($action === "delete_event") {
        $id = (int) ($_POST["id"] ?? 0);
        if ($id > 0) { Database::execute("DELETE FROM events WHERE id=:id", ["id" => $id]); $flashMsg = "Event deleted"; $flashType = "success"; }
    }

    if ($action === "create_gallery") {
        $title = trim((string) ($_POST["title"] ?? ""));
        $mediaType = (string) ($_POST["media_type"] ?? "photo");
        $mediaPath = (string) ($_POST["media_path"] ?? "");
        $description = (string) ($_POST["description"] ?? "");
        $status = (string) ($_POST["status"] ?? "draft");

        if ($title !== "" && $mediaPath !== "") {
            Database::execute(
                "INSERT INTO gallery_items (title,media_type,media_path,thumbnail_path,description,status,created_at)
                 VALUES (:title,:media_type,:media_path,:thumbnail,:description,:status,NOW())",
                ["title" => $title, "media_type" => $mediaType, "media_path" => $mediaPath,
                 "thumbnail" => $mediaPath, "description" => $description, "status" => $status]
            );
            $flashMsg = "Gallery item created"; $flashType = "success";
        } else { $flashMsg = "Title and media path are required"; $flashType = "danger"; }
    }

    if ($action === "update_gallery") {
        $id = (int) ($_POST["id"] ?? 0);
        $title = trim((string) ($_POST["title"] ?? ""));
        $mediaType = (string) ($_POST["media_type"] ?? "photo");
        $mediaPath = (string) ($_POST["media_path"] ?? "");
        $description = (string) ($_POST["description"] ?? "");
        $status = (string) ($_POST["status"] ?? "draft");

        if ($title !== "" && $mediaPath !== "" && $id > 0) {
            Database::execute(
                "UPDATE gallery_items SET title=:title,media_type=:media_type,media_path=:media_path,
                 description=:description,status=:status WHERE id=:id",
                ["title" => $title, "media_type" => $mediaType, "media_path" => $mediaPath,
                 "description" => $description, "status" => $status, "id" => $id]
            );
            $flashMsg = "Gallery item updated"; $flashType = "success";
        } else { $flashMsg = "Invalid request"; $flashType = "danger"; }
    }

    if ($action === "delete_gallery") {
        $id = (int) ($_POST["id"] ?? 0);
        if ($id > 0) { Database::execute("DELETE FROM gallery_items WHERE id=:id", ["id" => $id]); $flashMsg = "Gallery item deleted"; $flashType = "success"; }
    }

    if ($action === "update_admin") {
        $id = (int) ($_POST["id"] ?? 0);
        $fullName = trim((string) ($_POST["full_name"] ?? ""));
        $email = trim((string) ($_POST["email"] ?? ""));
        $role = (string) ($_POST["role"] ?? "admin");
        $status = (string) ($_POST["status"] ?? "active");
        $password = (string) ($_POST["password"] ?? "");

        if ($fullName !== "" && $email !== "" && $id > 0) {
            $roleRow = Database::fetchOne("SELECT id FROM roles WHERE name = :name", ["name" => $role]);
            if ($roleRow) {
                $sql = "UPDATE admins SET full_name=:name, email=:email, role_id=:role_id, status=:status WHERE id=:id";
                $params = ["name" => $fullName, "email" => $email, "role_id" => $roleRow["id"], "status" => $status, "id" => $id];
                if ($password !== "") {
                    $sql = "UPDATE admins SET full_name=:name, email=:email, role_id=:role_id, status=:status, password_hash=:hash WHERE id=:id";
                    $params["hash"] = password_hash($password, PASSWORD_DEFAULT);
                }
                Database::execute($sql, $params);
                $flashMsg = "Admin user updated"; $flashType = "success";
            } else { $flashMsg = "Invalid role"; $flashType = "danger"; }
        } else { $flashMsg = "Name and email are required"; $flashType = "danger"; }
    }

    if ($action === "delete_admin") {
        $id = (int) ($_POST["id"] ?? 0);
        $currentId = (int)($admin["id"] ?? 0);
        if ($id > 0 && $id !== $currentId) {
            Database::execute("DELETE FROM admins WHERE id=:id", ["id" => $id]);
            $flashMsg = "Admin user deleted"; $flashType = "success";
        } elseif ($id === $currentId) {
            $flashMsg = "You cannot delete your own account"; $flashType = "danger";
        } else { $flashMsg = "Invalid request"; $flashType = "danger"; }
    }

    endif; // !$csrfError

    // Redirect to avoid form resubmission (include _page to stay on same tab)
    if ($flashMsg) {
        $page = (string)($_POST["_page"] ?? "dashboard");
        header("Location: " . Helpers::adminUrl("index.php?page=" . urlencode($page) . "&msg=" . urlencode($flashMsg) . "&type=" . $flashType));
        exit;
    }
}

// Handle flash messages from redirect
$flashMsg = (string) ($_GET["msg"] ?? "");
$flashType = (string) ($_GET["type"] ?? "success");

// AJAX endpoint for fetching a single item's data (used by edit modal)
if ($dbAvail && isset($_GET["ajax"]) && $_GET["ajax"] === "get_item") {
    $type = (string)($_GET["type"] ?? "");
    $id = (int)($_GET["id"] ?? 0);
    header("Content-Type: application/json");
    $item = null;
    if ($id > 0) {
        if ($type === "post") {
            $item = Database::fetchOne("SELECT * FROM posts WHERE id = :id", ["id" => $id]);
        } elseif ($type === "event") {
            $item = Database::fetchOne("SELECT * FROM events WHERE id = :id", ["id" => $id]);
        } elseif ($type === "gallery") {
            $item = Database::fetchOne("SELECT * FROM gallery_items WHERE id = :id", ["id" => $id]);
        } elseif ($type === "admin") {
            $row = Database::fetchOne("SELECT a.id, a.full_name, a.email, a.status, r.name AS role FROM admins a LEFT JOIN roles r ON r.id=a.role_id WHERE a.id = :id", ["id" => $id]);
            if ($row) $item = $row;
        }
    }
    echo json_encode($item);
    exit;
}

// Fetch all data for management pages
$allEvents = []; $allPosts = []; $allGalleryItems = [];
if ($dbAvail) {
    $allEvents = Database::fetchAll(
        "SELECT e.*, COALESCE(a.full_name,'Events Desk') AS organizer
         FROM events e
         LEFT JOIN admins a ON a.id=e.created_by
         ORDER BY e.event_start DESC LIMIT 20"
    ) ?: [];
    $allPosts = Database::fetchAll(
        "SELECT p.*, COALESCE(a.full_name,p.author_name,'Admin Team') AS author_name
         FROM posts p
         LEFT JOIN admins a ON a.id=p.author_id
         ORDER BY COALESCE(p.published_at,p.updated_at) DESC LIMIT 20"
    ) ?: [];
    $allGalleryItems = Database::fetchAll(
        "SELECT * FROM gallery_items ORDER BY created_at DESC LIMIT 20"
    ) ?: [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>HopeConnect NGO — Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

:root{
  --brand:#0f766e;
  --brand-light:#14b8a6;
  --brand-dim:#ccfbf1;
  --brand-bg:#f0fdfa;
  --amber:#d97706;
  --amber-l:#fbbf24;
  --amber-bg:#fffbeb;
  --rose:#dc2626;
  --rose-bg:#fef2f2;
  --blue:#2563eb;
  --blue-bg:#eff6ff;
  --violet:#7c3aed;
  --violet-bg:#f5f3ff;
  --dark:#0c1220;
  --mid:#374151;
  --muted:#6b7280;
  --soft:#9ca3af;
  --border:#e5e7eb;
  --surface:#f9fafb;
  --white:#ffffff;
  --sidebar-w:265px;
  --header-h:66px;
  --radius:14px;
  --shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.06);
  --shadow-md:0 4px 20px rgba(0,0,0,.1);
  --shadow-lg:0 8px 32px rgba(0,0,0,.12);
}

body{
  font-family:'Plus Jakarta Sans',sans-serif;
  background:var(--surface);
  color:var(--dark);
  min-height:100vh;
  overflow-x:hidden;
  font-size:14px;
  line-height:1.5;
}

::-webkit-scrollbar{width:4px;height:4px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:#d1d5db;border-radius:99px}
::-webkit-scrollbar-thumb:hover{background:#9ca3af}

/* ═══════ LAYOUT ═══════ */
.app{display:flex;min-height:100vh}

/* ═══════ OVERLAY (mobile) ═══════ */
.sidebar-overlay{
  display:none;
  position:fixed;inset:0;
  background:rgba(0,0,0,.5);
  z-index:199;
  backdrop-filter:blur(2px);
}
.sidebar-overlay.show{display:block}

=======
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>HopeConnect NGO — Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

:root{
  --brand:#0f766e;
  --brand-light:#14b8a6;
  --brand-dim:#ccfbf1;
  --brand-bg:#f0fdfa;
  --amber:#d97706;
  --amber-l:#fbbf24;
  --amber-bg:#fffbeb;
  --rose:#dc2626;
  --rose-bg:#fef2f2;
  --blue:#2563eb;
  --blue-bg:#eff6ff;
  --violet:#7c3aed;
  --violet-bg:#f5f3ff;
  --dark:#0c1220;
  --mid:#374151;
  --muted:#6b7280;
  --soft:#9ca3af;
  --border:#e5e7eb;
  --surface:#f9fafb;
  --white:#ffffff;
  --sidebar-w:265px;
  --header-h:66px;
  --radius:14px;
  --shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.06);
  --shadow-md:0 4px 20px rgba(0,0,0,.1);
}

body{
  font-family:'Plus Jakarta Sans',sans-serif;
  background:var(--surface);
  color:var(--dark);
  min-height:100vh;
  overflow-x:hidden;
  font-size:14px;
  line-height:1.5;
}

::-webkit-scrollbar{width:4px;height:4px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:#d1d5db;border-radius:99px}
::-webkit-scrollbar-thumb:hover{background:#9ca3af}

/* ═══════ LAYOUT ═══════ */
.app{display:flex;min-height:100vh}

/* ═══════ OVERLAY (mobile) ═══════ */
.sidebar-overlay{
  display:none;
  position:fixed;inset:0;
  background:rgba(0,0,0,.5);
  z-index:199;
  backdrop-filter:blur(2px);
}
.sidebar-overlay.show{display:block}

>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
/* ═══════ SIDEBAR ═══════ */
.sidebar{
  width:var(--sidebar-w);
  background:var(--dark);
  display:flex;flex-direction:column;
  position:fixed;top:0;left:0;
  height:100vh;z-index:200;
  transition:transform .3s cubic-bezier(.4,0,.2,1),width .3s cubic-bezier(.4,0,.2,1);
  overflow:hidden;
}
<<<<<<< HEAD
=======

/* Collapsed (desktop) */
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
.sidebar.collapsed{width:72px}
.sidebar.collapsed .nav-text,
.sidebar.collapsed .brand-text,
.sidebar.collapsed .nav-section,
.sidebar.collapsed .user-text,
.sidebar.collapsed .footer-links,
.sidebar.collapsed .nav-badge{display:none!important}
.sidebar.collapsed .brand{padding:18px;justify-content:center}
.sidebar.collapsed .nav-item{padding:12px;justify-content:center}
.sidebar.collapsed .nav-item i.nav-icon{margin:0}
.sidebar.collapsed .sidebar-footer{padding:14px;justify-content:center}
.sidebar.collapsed .user-ava{margin:0}

<<<<<<< HEAD
=======
/* Mobile hidden */
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
@media(max-width:1023px){
  .sidebar{transform:translateX(-100%)}
  .sidebar.mobile-open{transform:translateX(0)}
  .sidebar.collapsed{width:var(--sidebar-w);transform:translateX(-100%)}
  .sidebar.collapsed.mobile-open{transform:translateX(0)}
}

<<<<<<< HEAD
=======
/* Brand */
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
.brand{
  display:flex;align-items:center;gap:12px;
  padding:20px 18px 16px;
  border-bottom:1px solid rgba(255,255,255,.07);
  flex-shrink:0;
}
.brand-logo{
  width:38px;height:38px;border-radius:10px;
  background:linear-gradient(135deg,var(--brand-light),var(--brand));
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:.9rem;flex-shrink:0;
}
<<<<<<< HEAD
=======
.brand-text{}
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
.brand-name{
  font-family:'Instrument Serif',serif;
  font-size:1.1rem;color:#fff;white-space:nowrap;line-height:1.2;
}
.brand-sub{font-size:.68rem;color:#6b7280;white-space:nowrap;margin-top:1px;letter-spacing:.3px}

<<<<<<< HEAD
=======
/* Nav scroll */
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
.nav-scroll{flex:1;overflow-y:auto;overflow-x:hidden;padding:10px 0}
.nav-section{
  font-size:.65rem;font-weight:700;letter-spacing:1.6px;
  text-transform:uppercase;color:#4b5563;
  padding:14px 18px 5px;white-space:nowrap;
}
.nav-item{
  display:flex;align-items:center;gap:11px;
  padding:10px 18px;cursor:pointer;
  border-left:2px solid transparent;
  transition:all .18s ease;white-space:nowrap;
  position:relative;
}
.nav-item:hover{background:rgba(255,255,255,.05)}
.nav-item.active{
  background:rgba(20,184,166,.12);
  border-left-color:var(--brand-light);
}
.nav-item.active .nav-icon{color:var(--brand-light)}
.nav-item.active .nav-text{color:#f1f5f9;font-weight:600}
.nav-icon{
  font-size:.9rem;color:#6b7280;
  transition:color .18s;flex-shrink:0;
  width:18px;text-align:center;
}
.nav-text{font-size:.83rem;color:#9ca3af;transition:color .18s;flex:1}
.nav-badge{
  font-size:.62rem;font-weight:700;
  padding:2px 7px;border-radius:99px;
  background:var(--rose);color:#fff;
}
.nav-badge.green{background:#059669}
.nav-badge.amber{background:var(--amber)}

<<<<<<< HEAD
=======
/* Sidebar footer */
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
.sidebar-footer{
  border-top:1px solid rgba(255,255,255,.07);
  padding:14px 18px;
  display:flex;align-items:center;gap:10px;
  flex-shrink:0;
}
.user-ava{
  width:34px;height:34px;border-radius:50%;flex-shrink:0;
  background:linear-gradient(135deg,var(--amber-l),var(--amber));
  display:flex;align-items:center;justify-content:center;
  font-size:.72rem;font-weight:700;color:#fff;
}
<<<<<<< HEAD
=======
.user-text{}
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
.user-name{font-size:.8rem;font-weight:700;color:#f1f5f9}
.user-role{font-size:.68rem;color:#4b5563;margin-top:1px}
.footer-links{
  border-top:1px solid rgba(255,255,255,.07);
  padding:10px 18px;display:flex;gap:14px;flex-shrink:0;
}
.footer-links a{
  font-size:.7rem;color:#4b5563;text-decoration:none;transition:color .18s;
}
.footer-links a:hover{color:var(--brand-light)}
.footer-links a.danger:hover{color:#f87171}

/* ═══════ MAIN ═══════ */
.main{
  margin-left:var(--sidebar-w);flex:1;
  transition:margin-left .3s cubic-bezier(.4,0,.2,1);
  min-height:100vh;display:flex;flex-direction:column;
}
.main.collapsed{margin-left:72px}
@media(max-width:1023px){
  .main,.main.collapsed{margin-left:0}
}

/* ═══════ TOPBAR ═══════ */
.topbar{
  height:var(--header-h);background:var(--white);
  border-bottom:1px solid var(--border);
  display:flex;align-items:center;gap:14px;
  padding:0 24px;position:sticky;top:0;z-index:100;
}
.menu-btn{
  width:36px;height:36px;border-radius:9px;
  border:none;background:none;cursor:pointer;
  display:flex;flex-direction:column;align-items:center;
  justify-content:center;gap:4.5px;flex-shrink:0;
  transition:background .18s;
}
.menu-btn:hover{background:var(--surface)}
.menu-btn span{
  display:block;width:18px;height:1.8px;
  background:var(--mid);border-radius:2px;transition:all .28s;
}
<<<<<<< HEAD
=======
.page-heading{}
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
.page-title{font-size:.95rem;font-weight:700;color:var(--dark);line-height:1}
.breadcrumb{
  font-size:.72rem;color:var(--muted);
  display:flex;align-items:center;gap:5px;margin-top:2px;
}
.breadcrumb i{font-size:.55rem}

.topbar-search{
  flex:1;max-width:340px;margin-left:auto;position:relative;
}
.topbar-search input{
  width:100%;padding:9px 14px 9px 36px;
  border:1px solid var(--border);border-radius:10px;
  font-family:'Plus Jakarta Sans',sans-serif;font-size:.82rem;
  background:var(--surface);color:var(--dark);outline:none;
  transition:border .18s,box-shadow .18s;
}
.topbar-search input:focus{border-color:var(--brand-light);box-shadow:0 0 0 3px rgba(20,184,166,.1)}
.topbar-search i{
  position:absolute;left:12px;top:50%;transform:translateY(-50%);
  color:var(--soft);font-size:.8rem;
}
.topbar-right{display:flex;align-items:center;gap:6px;margin-left:10px}
.tb-btn{
  width:36px;height:36px;border-radius:9px;
  border:1px solid var(--border);background:var(--white);cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  font-size:.88rem;color:var(--mid);position:relative;transition:all .18s;
}
.tb-btn:hover{background:var(--surface);border-color:var(--brand-light);color:var(--brand)}
.tb-dot{
  position:absolute;top:5px;right:5px;
  width:7px;height:7px;border-radius:50%;
  background:var(--rose);border:2px solid var(--white);
}
.tb-avatar{
  width:34px;height:34px;border-radius:50%;
  background:linear-gradient(135deg,var(--brand-light),var(--brand));
  display:flex;align-items:center;justify-content:center;
  font-size:.7rem;font-weight:700;color:#fff;cursor:pointer;
  border:2px solid var(--brand-dim);flex-shrink:0;
}

/* ═══════ CONTENT ═══════ */
.content{flex:1;padding:26px;display:none}
.content.active{display:block;animation:pageIn .28s ease}
@keyframes pageIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}

/* ═══════ STATS GRID ═══════ */
.stats-grid{
  display:grid;grid-template-columns:repeat(4,1fr);
  gap:16px;margin-bottom:22px;
}
<<<<<<< HEAD
.stats-grid.cols-3{grid-template-columns:repeat(3,1fr)}
@media(max-width:1279px){.stats-grid,.stats-grid.cols-3{grid-template-columns:repeat(2,1fr)}}
@media(max-width:767px){.stats-grid,.stats-grid.cols-3{grid-template-columns:repeat(2,1fr)}}
@media(max-width:479px){.stats-grid,.stats-grid.cols-3{grid-template-columns:1fr}}
=======
@media(max-width:1279px){.stats-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:479px){.stats-grid{grid-template-columns:1fr}}
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43

.stat-card{
  background:var(--white);border-radius:var(--radius);
  padding:20px;border:1px solid var(--border);
  box-shadow:var(--shadow);position:relative;overflow:hidden;
  transition:transform .2s,box-shadow .2s;
}
.stat-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-md)}
.stat-card::after{
  content:'';position:absolute;right:-16px;top:-16px;
  width:80px;height:80px;border-radius:50%;opacity:.07;
}
.stat-card.t1::after{background:var(--brand)}
.stat-card.t2::after{background:var(--amber)}
.stat-card.t3::after{background:var(--rose)}
.stat-card.t4::after{background:var(--blue)}
<<<<<<< HEAD
.stat-card.t5::after{background:var(--violet)}
=======
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43

.stat-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px}
.stat-icon-wrap{
  width:42px;height:42px;border-radius:11px;
  display:flex;align-items:center;justify-content:center;
  font-size:.95rem;
}
.t1 .stat-icon-wrap{background:var(--brand-bg);color:var(--brand)}
.t2 .stat-icon-wrap{background:var(--amber-bg);color:var(--amber)}
.t3 .stat-icon-wrap{background:var(--rose-bg);color:var(--rose)}
.t4 .stat-icon-wrap{background:var(--blue-bg);color:var(--blue)}
.t5 .stat-icon-wrap{background:var(--violet-bg);color:var(--violet)}

.stat-trend{
  font-size:.7rem;font-weight:600;
  padding:3px 8px;border-radius:99px;
  display:flex;align-items:center;gap:4px;
}
.stat-trend.up{background:#dcfce7;color:#15803d}
.stat-trend.down{background:var(--rose-bg);color:var(--rose)}
.stat-trend.neutral{background:var(--surface);color:var(--muted)}

.stat-value{font-size:1.75rem;font-weight:800;color:var(--dark);line-height:1;letter-spacing:-1px}
.stat-label{font-size:.75rem;color:var(--muted);margin-top:4px;font-weight:500}
.stat-sub{font-size:.72rem;color:var(--soft);margin-top:10px}

<<<<<<< HEAD
.stat-skeleton{
  height:20px;background:linear-gradient(90deg,var(--border) 25%,#f3f4f6 50%,var(--border) 75%);
  background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:6px;margin-bottom:8px;
}
@keyframes shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}

=======
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
/* ═══════ CARD ═══════ */
.card{
  background:var(--white);border-radius:var(--radius);
  border:1px solid var(--border);padding:20px 22px;
  box-shadow:var(--shadow);
}
.card-hd{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:18px;gap:10px;flex-wrap:wrap;
}
<<<<<<< HEAD
=======
.card-hd-left{}
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
.card-title{font-size:.9rem;font-weight:700;color:var(--dark)}
.card-sub{font-size:.75rem;color:var(--muted);margin-top:2px}
.card-link{
  font-size:.76rem;font-weight:600;color:var(--brand);
  padding:6px 12px;border-radius:8px;border:1px solid var(--brand-dim);
  background:var(--brand-bg);cursor:pointer;transition:all .18s;
  text-decoration:none;white-space:nowrap;
}
.card-link:hover{background:var(--brand-dim)}

/* ═══════ CHARTS ROW ═══════ */
.charts-row{
  display:grid;grid-template-columns:1.65fr 1fr;gap:16px;margin-bottom:22px;
}
@media(max-width:1199px){.charts-row{grid-template-columns:1fr}}

<<<<<<< HEAD
.bar{transition:height .6s cubic-bezier(.4,0,.2,1)}
.bar-chart {
  display:flex;align-items:flex-end;gap:8px;
  height:200px;padding-top:8px;
}
.bar-col{flex:1;display:flex;flex-direction:column;align-items:center;gap:5px}
.bar-stack{
  width:100%;display:flex;align-items:flex-end;gap:2px;height:170px;
  position:relative;
}
.bar{
  flex:1;border-radius:4px 4px 0 0;min-width:6px;
  transition:height .6s cubic-bezier(.4,0,.2,1),opacity .18s;cursor:pointer;
  position:relative;
}
.bar:hover{opacity:.75}
.bar .bar-tooltip{
  position:absolute;top:-28px;left:50%;transform:translateX(-50%);
  font-size:.62rem;font-weight:600;color:var(--dark);
  background:var(--white);padding:2px 6px;border-radius:4px;
  border:1px solid var(--border);white-space:nowrap;
  opacity:0;transition:opacity .18s;pointer-events:none;
}
.bar:hover .bar-tooltip{opacity:1}
=======
/* Bar chart */
.bar-chart-wrap{display:flex;flex-direction:column;gap:8px}
.bar-chart{
  display:flex;align-items:flex-end;gap:8px;
  height:130px;padding-top:8px;
}
.bar-col{flex:1;display:flex;flex-direction:column;align-items:center;gap:5px}
.bar-stack{width:100%;display:flex;align-items:flex-end;gap:2px;height:105px}
.bar{
  flex:1;border-radius:4px 4px 0 0;min-width:6px;
  transition:opacity .18s;cursor:pointer;
}
.bar:hover{opacity:.75}
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
.bar.primary{background:var(--brand)}
.bar.secondary{background:var(--brand-dim)}
.bar-lbl{font-size:.62rem;color:var(--soft);font-weight:500}
.chart-legend{
  display:flex;gap:16px;margin-top:6px;
}
.legend-item{display:flex;align-items:center;gap:6px;font-size:.72rem;color:var(--muted)}
.legend-dot{width:8px;height:8px;border-radius:2px;flex-shrink:0}

<<<<<<< HEAD
=======
/* Donut */
.donut-wrap{display:flex;align-items:center;gap:20px;flex-wrap:wrap}
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
.donut-legend{display:flex;flex-direction:column;gap:9px;min-width:120px}
.dl-item{display:flex;align-items:center;gap:8px;font-size:.78rem}
.dl-dot{width:9px;height:9px;border-radius:3px;flex-shrink:0}
.dl-lbl{color:var(--muted);flex:1}
.dl-val{font-weight:700;color:var(--dark)}

<<<<<<< HEAD
=======
/* Mini stats below donut */
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
.mini-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:16px}
.mini-stat{
  background:var(--surface);border-radius:9px;
  padding:11px 13px;border:1px solid var(--border);text-align:center;
}
.mini-stat .v{font-size:1.1rem;font-weight:800;color:var(--dark)}
.mini-stat .l{font-size:.68rem;color:var(--muted);margin-top:2px}

/* ═══════ TWO COL ═══════ */
.two-col{display:grid;grid-template-columns:1.3fr 1fr;gap:16px}
@media(max-width:1199px){.two-col{grid-template-columns:1fr}}

/* ═══════ FEED / LIST ═══════ */
.feed-list{display:flex;flex-direction:column}
.feed-row{
  display:flex;align-items:center;gap:11px;
  padding:11px 0;border-bottom:1px solid var(--border);
}
.feed-row:last-child{border-bottom:none}
.feed-ava{
  width:34px;height:34px;border-radius:50%;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  font-size:.72rem;font-weight:700;color:#fff;
}
.feed-info{flex:1;min-width:0}
.feed-name{font-size:.82rem;font-weight:600;color:var(--dark);
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.feed-sub{font-size:.72rem;color:var(--muted)}
.feed-amt{font-size:.88rem;font-weight:700;color:var(--brand);white-space:nowrap}

<<<<<<< HEAD
=======
/* Patient status */
.ps-list{display:flex;flex-direction:column}
.ps-row{
  display:flex;align-items:center;gap:10px;
  padding:10px 0;border-bottom:1px solid var(--border);
}
.ps-row:last-child{border-bottom:none}
.ps-bar{width:3px;height:36px;border-radius:2px;flex-shrink:0}
.ps-info{flex:1;min-width:0}
.ps-name{font-size:.82rem;font-weight:600;color:var(--dark)}
.ps-detail{font-size:.71rem;color:var(--muted);margin-top:1px}

>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
/* ═══════ BADGES ═══════ */
.badge{
  display:inline-flex;align-items:center;gap:4px;
  padding:3px 9px;border-radius:99px;
  font-size:.7rem;font-weight:600;white-space:nowrap;
}
.badge i{font-size:.6rem}
.badge.success{background:#dcfce7;color:#15803d}
.badge.warning{background:#fef3c7;color:#92400e}
.badge.danger{background:#fee2e2;color:#991b1b}
.badge.info{background:#dbeafe;color:#1e40af}
.badge.neutral{background:var(--surface);color:var(--mid)}
.badge.violet{background:#ede9fe;color:#5b21b6}
.badge.teal{background:var(--brand-dim);color:var(--brand)}
.badge.dark{background:var(--dark);color:#fff}

/* ═══════ TABLE ═══════ */
.toolbar{
  display:flex;align-items:center;gap:8px;
  margin-bottom:16px;flex-wrap:wrap;
}
.search-box{
  position:relative;display:flex;
}
.search-box input{
  padding:8px 12px 8px 34px;
  border:1px solid var(--border);border-radius:9px;
  font-family:'Plus Jakarta Sans',sans-serif;font-size:.81rem;
  background:var(--surface);color:var(--dark);
  outline:none;transition:border .18s;width:200px;
}
.search-box input:focus{border-color:var(--brand-light)}
.search-box i{
  position:absolute;left:11px;top:50%;transform:translateY(-50%);
  color:var(--soft);font-size:.78rem;
}
.filter-btn{
  padding:8px 13px;border:1px solid var(--border);border-radius:9px;
  background:var(--white);font-family:'Plus Jakarta Sans',sans-serif;
  font-size:.8rem;color:var(--mid);cursor:pointer;
  display:flex;align-items:center;gap:6px;transition:all .18s;
}
.filter-btn i{font-size:.8rem;color:var(--soft)}
<<<<<<< HEAD
.filter-btn:hover,.filter-btn.on{border-color:var(--brand-light);color:var(--brand)}
=======
.filter-btn:hover{border-color:var(--brand-light);color:var(--brand)}
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
.btn-primary{
  padding:8px 16px;border:none;border-radius:9px;
  background:var(--brand);color:#fff;
  font-family:'Plus Jakarta Sans',sans-serif;font-size:.8rem;font-weight:600;
  cursor:pointer;display:flex;align-items:center;gap:7px;
  transition:background .18s;white-space:nowrap;
}
.btn-primary i{font-size:.8rem}
.btn-primary:hover{background:var(--brand-light)}
.btn-primary.ml{margin-left:auto}
.btn-secondary{
  padding:8px 14px;border:1px solid var(--border);border-radius:9px;
  background:var(--white);color:var(--mid);
  font-family:'Plus Jakarta Sans',sans-serif;font-size:.8rem;font-weight:500;
  cursor:pointer;display:flex;align-items:center;gap:7px;transition:all .18s;
}
.btn-secondary:hover{border-color:var(--brand-light);color:var(--brand)}

.data-table{width:100%;border-collapse:collapse}
<<<<<<< HEAD
@media(max-width:767px){.data-table thead{display:none}.data-table tr{display:block;margin-bottom:12px;border:1px solid var(--border);border-radius:10px;padding:10px}.data-table td{display:flex;justify-content:space-between;align-items:center;padding:8px 6px;border-bottom:1px solid var(--border);gap:8px}.data-table td:last-child{border-bottom:none}.data-table td::before{content:attr(data-label);font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);white-space:nowrap;flex-shrink:0}.data-table td .action-btns{flex-shrink:0}.data-table .action-btn{width:34px;height:34px;font-size:.85rem}}
=======
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
.data-table th{
  text-align:left;font-size:.67rem;font-weight:700;
  letter-spacing:.7px;text-transform:uppercase;
  color:var(--muted);padding:9px 13px;
  border-bottom:1px solid var(--border);
  white-space:nowrap;background:var(--surface);
}
.data-table th:first-child{border-radius:8px 0 0 0}
.data-table th:last-child{border-radius:0 8px 0 0}
.data-table td{
  padding:12px 13px;font-size:.82rem;
  border-bottom:1px solid var(--border);
  vertical-align:middle;
}
.data-table tr:last-child td{border-bottom:none}
.data-table tbody tr{transition:background .15s}
.data-table tbody tr:hover td{background:var(--surface)}

.cell-user{display:flex;align-items:center;gap:9px}
.cell-ava{
  width:32px;height:32px;border-radius:50%;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  font-size:.7rem;font-weight:700;color:#fff;
}
.cell-name{font-size:.81rem;font-weight:600;color:var(--dark);display:block}
.cell-sub{font-size:.7rem;color:var(--muted)}

.action-btns{display:flex;gap:5px}
.action-btn{
  width:28px;height:28px;border-radius:7px;
  border:1px solid var(--border);background:var(--white);
  cursor:pointer;display:flex;align-items:center;
  justify-content:center;font-size:.75rem;color:var(--muted);
  transition:all .18s;
}
.action-btn:hover.view{border-color:var(--blue);color:var(--blue);background:var(--blue-bg)}
.action-btn:hover.edit{border-color:var(--brand);color:var(--brand);background:var(--brand-bg)}
.action-btn:hover.del{border-color:var(--rose);color:var(--rose);background:var(--rose-bg)}

<<<<<<< HEAD
=======
/* Pagination */
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
.pagination{
  display:flex;align-items:center;gap:5px;
  margin-top:16px;justify-content:flex-end;flex-wrap:wrap;
}
.page-info{font-size:.74rem;color:var(--muted);margin-right:auto}
.page-btn{
  width:30px;height:30px;border-radius:7px;
  border:1px solid var(--border);background:var(--white);
  cursor:pointer;font-size:.78rem;font-weight:500;
  display:flex;align-items:center;justify-content:center;
  transition:all .18s;color:var(--mid);
}
.page-btn:hover,.page-btn.on{background:var(--brand);color:#fff;border-color:var(--brand)}

<<<<<<< HEAD
=======
/* Tabs */
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
.tabs{display:flex;gap:4px;margin-bottom:18px;flex-wrap:wrap}
.tab-btn{
  padding:7px 14px;border-radius:8px;
  border:1px solid var(--border);background:var(--white);
  font-family:'Plus Jakarta Sans',sans-serif;font-size:.78rem;font-weight:500;
  color:var(--muted);cursor:pointer;transition:all .18s;
}
.tab-btn:hover{border-color:var(--brand-light);color:var(--brand)}
.tab-btn.on{background:var(--brand);color:#fff;border-color:var(--brand)}

<<<<<<< HEAD
=======
/* Progress bar */
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
.prog-wrap{width:100%;background:var(--border);border-radius:99px;height:5px;margin:6px 0}
.prog-bar{height:100%;border-radius:99px;background:linear-gradient(90deg,var(--brand),var(--brand-light))}
.prog-bar.urgent{background:linear-gradient(90deg,#f87171,var(--rose))}

<<<<<<< HEAD
=======
/* Section header */
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
.section-hd{
  display:flex;align-items:flex-start;justify-content:space-between;
  margin-bottom:18px;flex-wrap:wrap;gap:10px;
}
.section-hd h2{font-size:.95rem;font-weight:700;color:var(--dark)}
.section-hd p{font-size:.76rem;color:var(--muted);margin-top:2px}

/* ═══════ CAMPAIGNS ═══════ */
.campaign-grid{
  display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:18px;
}
@media(max-width:899px){.campaign-grid{grid-template-columns:1fr 1fr}}
@media(max-width:599px){.campaign-grid{grid-template-columns:1fr}}

.campaign-card{
  border:1px solid var(--border);border-radius:11px;padding:16px;
  transition:box-shadow .2s;
}
.campaign-card:hover{box-shadow:var(--shadow-md)}
.cmp-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px}
.cmp-name{font-size:.84rem;font-weight:700;color:var(--dark)}
.cmp-date{font-size:.7rem;color:var(--muted);margin-top:2px}
.cmp-nums{display:flex;justify-content:space-between;font-size:.74rem;color:var(--muted);margin-bottom:5px}
.cmp-pct{font-size:.71rem;font-weight:700;margin-top:4px}

/* ═══════ PARTNERS ═══════ */
.partners-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
@media(max-width:899px){.partners-grid{grid-template-columns:1fr 1fr}}
@media(max-width:599px){.partners-grid{grid-template-columns:1fr}}

.partner-card{
  background:var(--white);border:1px solid var(--border);
  border-radius:11px;padding:17px;
  display:flex;align-items:center;gap:13px;
  transition:all .2s;cursor:pointer;
}
.partner-card:hover{border-color:var(--brand-light);box-shadow:0 4px 14px rgba(15,118,110,.1)}
.partner-logo{
  width:46px;height:46px;border-radius:10px;
  background:var(--surface);border:1px solid var(--border);
  display:flex;align-items:center;justify-content:center;
  font-size:.9rem;color:var(--mid);flex-shrink:0;
}
.partner-info{flex:1;min-width:0}
.partner-name{font-size:.83rem;font-weight:700;color:var(--dark)}
.partner-type{font-size:.71rem;color:var(--muted);margin-top:1px}
.partner-since{font-size:.68rem;color:var(--brand);font-weight:600;margin-top:5px}

/* ═══════ BLOG ═══════ */
.blog-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
@media(max-width:1099px){.blog-grid{grid-template-columns:1fr 1fr}}
@media(max-width:599px){.blog-grid{grid-template-columns:1fr}}

.blog-card{
  background:var(--white);border:1px solid var(--border);
  border-radius:12px;overflow:hidden;
  transition:all .22s;cursor:pointer;
}
.blog-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-md)}
.blog-thumb{
  height:130px;display:flex;align-items:center;justify-content:center;font-size:2.4rem;
}
.blog-body{padding:14px}
.blog-tag{
  font-size:.65rem;font-weight:700;letter-spacing:.8px;
  text-transform:uppercase;color:var(--brand);margin-bottom:6px;
}
.blog-title{font-size:.85rem;font-weight:700;color:var(--dark);line-height:1.4;margin-bottom:5px}
<<<<<<< HEAD
.blog-excerpt{font-size:.75rem;color:var(--muted);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
=======
.blog-excerpt{font-size:.75rem;color:var(--muted);line-height:1.5}
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
.blog-meta{
  display:flex;align-items:center;gap:8px;
  font-size:.7rem;color:var(--muted);margin-top:11px;flex-wrap:wrap;
}
.blog-meta span{display:flex;align-items:center;gap:4px}

/* ═══════ GALLERY ═══════ */
.gallery-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
@media(max-width:1099px){.gallery-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:699px){.gallery-grid{grid-template-columns:repeat(2,1fr)}}

.gallery-item{
  border-radius:10px;overflow:hidden;aspect-ratio:1;
  position:relative;cursor:pointer;
}
.g-thumb{
  width:100%;height:100%;display:flex;align-items:center;
  justify-content:center;font-size:2.2rem;transition:transform .3s;
}
.gallery-item:hover .g-thumb{transform:scale(1.06)}
.g-overlay{
  position:absolute;inset:0;
  background:linear-gradient(to top,rgba(12,18,32,.75),transparent);
  opacity:0;transition:opacity .28s;
  display:flex;flex-direction:column;justify-content:space-between;padding:10px;
}
.gallery-item:hover .g-overlay{opacity:1}
.g-actions{display:flex;gap:5px;justify-content:flex-end}
.g-btn{
  width:26px;height:26px;border-radius:6px;
  background:rgba(255,255,255,.92);border:none;cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  font-size:.72rem;color:var(--mid);transition:all .18s;
}
.g-btn:hover{background:#fff}
.g-caption{font-size:.72rem;color:#fff;font-weight:500}

/* ═══════ SECURITY ═══════ */
.security-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
@media(max-width:899px){.security-grid{grid-template-columns:1fr}}

.activity-list{display:flex;flex-direction:column}
.act-row{
  display:flex;align-items:flex-start;gap:11px;
  padding:11px 0;border-bottom:1px solid var(--border);
}
.act-row:last-child{border-bottom:none}
.act-icon{
  width:30px;height:30px;border-radius:8px;
  display:flex;align-items:center;justify-content:center;
  font-size:.78rem;flex-shrink:0;margin-top:1px;
}
.act-icon.login{background:var(--brand-bg);color:var(--brand)}
.act-icon.warn{background:var(--amber-bg);color:var(--amber)}
.act-icon.danger{background:var(--rose-bg);color:var(--rose)}
.act-icon.info{background:var(--blue-bg);color:var(--blue)}
.act-body{flex:1;min-width:0}
.act-title{font-size:.8rem;font-weight:600;color:var(--dark);display:block}
.act-desc{font-size:.71rem;color:var(--muted);margin-top:1px}
.act-time{font-size:.68rem;color:var(--soft);white-space:nowrap;padding-top:2px}

.sys-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.sys-stat{
  background:var(--surface);border-radius:9px;padding:12px 14px;border:1px solid var(--border);
}
.sys-val{font-size:1.2rem;font-weight:800;color:var(--dark)}
.sys-lbl{font-size:.7rem;color:var(--muted);margin-top:2px}

/* ═══════ SETTINGS FORM ═══════ */
.form-field{margin-bottom:14px}
.form-label{font-size:.76rem;font-weight:600;color:var(--mid);margin-bottom:5px;display:block}
.form-input{
  width:100%;padding:9px 13px;
  border:1px solid var(--border);border-radius:9px;
  font-family:'Plus Jakarta Sans',sans-serif;font-size:.83rem;
  color:var(--dark);outline:none;transition:border .18s,box-shadow .18s;
}
.form-input:focus{border-color:var(--brand-light);box-shadow:0 0 0 3px rgba(20,184,166,.1)}

.gateway-row{
  display:flex;align-items:center;justify-content:space-between;
  padding:12px 14px;border:1px solid var(--border);border-radius:10px;
  margin-bottom:8px;transition:border .18s;
}
.gateway-row:hover{border-color:var(--brand-light)}
.gw-left{display:flex;align-items:center;gap:10px}
.gw-icon{width:34px;height:34px;border-radius:8px;background:var(--surface);
  border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:.9rem}
.gw-name{font-size:.82rem;font-weight:600;color:var(--dark)}
.gw-desc{font-size:.7rem;color:var(--muted)}

.toggle-switch{
  width:38px;height:20px;background:var(--brand);border-radius:99px;
  position:relative;cursor:pointer;flex-shrink:0;
}
.toggle-switch::after{
  content:'';position:absolute;right:3px;top:3px;
  width:14px;height:14px;border-radius:50%;background:#fff;
  transition:all .2s;
}
.toggle-switch.off{background:var(--border)}
.toggle-switch.off::after{right:auto;left:3px}

.notif-row{
  display:flex;justify-content:space-between;align-items:center;
  padding:12px 0;border-bottom:1px solid var(--border);
}
.notif-row:last-child{border-bottom:none}
.notif-label{font-size:.82rem;font-weight:600;color:var(--dark)}
.notif-desc{font-size:.71rem;color:var(--muted);margin-top:2px}

<<<<<<< HEAD
/* ═══════ RESPONSIVE ═══════ */
=======
/* ═══════ RESPONSIVE HELPERS ═══════ */
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
.hide-sm{display:block}
@media(max-width:767px){
  .hide-sm{display:none}
  .content{padding:16px}
  .topbar{padding:0 16px;gap:10px}
  .topbar-search{max-width:none;flex:1}
  .stat-value{font-size:1.45rem}
}
@media(max-width:479px){
  .topbar-search{display:none}
  .card{padding:16px}
<<<<<<< HEAD
}

.mono{font-family:'Courier New',monospace;font-size:.78rem;color:var(--muted)}

=======
  .data-table th:nth-child(n+4),.data-table td:nth-child(n+4){display:none}
}

/* ═══════ EMPTY / MONO ═══════ */
.mono{font-family:'Courier New',monospace;font-size:.78rem;color:var(--muted)}

/* ═══════ ALERT BANNER ═══════ */
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
.alert-banner{
  display:flex;align-items:center;gap:10px;
  padding:11px 16px;border-radius:10px;
  margin-bottom:16px;font-size:.8rem;
}
.alert-banner.warn{background:#fffbeb;border:1px solid #fde68a;color:#92400e}
.alert-banner.danger{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}
.alert-banner i{flex-shrink:0}

<<<<<<< HEAD
/* ─── EMPTY STATE ─── */
.empty-state{
  text-align:center;padding:40px 20px;color:var(--muted);
}
.empty-state i{font-size:2.5rem;color:var(--soft);margin-bottom:12px;display:block}
.empty-state p{font-size:.85rem;margin-top:6px}
.empty-state .sub{font-size:.75rem;color:var(--soft);margin-top:4px}

/* ─── MODAL ─── */
.modal-overlay{
  display:none;position:fixed;inset:0;background:rgba(12,18,32,.6);
  z-index:300;backdrop-filter:blur(3px);
  align-items:center;justify-content:center;padding:1rem;
  animation:fadeIn .2s ease;
}
.modal-overlay.show{display:flex}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.modal{
  background:var(--white);border-radius:18px;width:100%;max-width:620px;
  max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2);
  animation:modalIn .25s cubic-bezier(.4,0,.2,1);
}
@keyframes modalIn{from{opacity:0;transform:scale(.96) translateY(12px)}to{opacity:1;transform:scale(1) translateY(0)}}
.modal-header{
  display:flex;align-items:center;justify-content:space-between;
  padding:18px 22px 14px;border-bottom:1px solid var(--border);
}
.modal-title{font-size:.95rem;font-weight:700;color:var(--dark)}
.modal-close{
  width:32px;height:32px;border-radius:8px;border:none;background:none;
  cursor:pointer;display:flex;align-items:center;justify-content:center;
  font-size:1.1rem;color:var(--muted);transition:all .18s;
}
.modal-close:hover{background:var(--surface);color:var(--dark)}
.modal-body{padding:20px 22px}
.modal-footer{
  display:flex;align-items:center;justify-content:flex-end;gap:8px;
  padding:14px 22px 18px;border-top:1px solid var(--border);
}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px}
.form-group{margin-bottom:14px}
.form-group:last-child{margin-bottom:0}
.form-group label{display:block;font-size:.76rem;font-weight:600;color:var(--mid);margin-bottom:5px}
.form-group .hint{font-size:.68rem;color:var(--soft);margin-top:3px}
.form-control{
  width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:9px;
  font-family:'Plus Jakarta Sans',sans-serif;font-size:.83rem;color:var(--dark);
  outline:none;transition:border .18s,box-shadow .18s;background:var(--white);
}
.form-control:focus{border-color:var(--brand-light);box-shadow:0 0 0 3px rgba(20,184,166,.1)}
.form-control::placeholder{color:var(--soft)}
textarea.form-control{min-height:120px;resize:vertical;line-height:1.6}
select.form-control{cursor:pointer;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;padding-right:32px}
.flash-msg{
  display:flex;align-items:center;gap:8px;padding:11px 16px;border-radius:10px;
  margin-bottom:16px;font-size:.82rem;animation:slideDown .25s ease;
}
@keyframes slideDown{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}
.flash-msg.success{background:#dcfce7;border:1px solid #bbf7d0;color:#15803d}
.flash-msg.danger{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}
.flash-msg i{flex-shrink:0}

/* ─── TOAST ─── */
.toast-container{
  position:fixed;top:20px;right:20px;z-index:9999;
  display:flex;flex-direction:column;gap:10px;
  pointer-events:none;
}
.toast{
  display:flex;align-items:center;gap:10px;
  padding:13px 17px;border-radius:11px;
  background:var(--white);border-left:4px solid var(--muted);
  box-shadow:0 4px 24px rgba(0,0,0,.13);
  font-size:.83rem;font-weight:500;color:var(--dark);
  animation:toastIn .3s cubic-bezier(.4,0,.2,1);
  min-width:320px;max-width:440px;
  pointer-events:auto;
}
.toast.success{border-left-color:#059669}
.toast.danger{border-left-color:#dc2626}
.toast i.fa-check-circle{color:#059669}
.toast i.fa-exclamation-circle{color:#dc2626}
.toast.removing{animation:toastOut .25s ease forwards}
@keyframes toastIn{from{opacity:0;transform:translateX(50px)}to{opacity:1;transform:translateX(0)}}
@keyframes toastOut{from{opacity:1;transform:translateX(0)}to{opacity:0;transform:translateX(50px)}}
.toast-close{
  background:none;border:none;cursor:pointer;
  color:var(--soft);font-size:.85rem;padding:2px;margin-left:auto;
  transition:color .15s;
}
.toast-close:hover{color:var(--mid)}

/* ─── SPINNER ─── */
.spinner{display:flex;flex-direction:column;align-items:center;gap:8px;padding:40px 20px;color:var(--muted)}
.spinner i{font-size:2rem;color:var(--brand-light)}
.spinner span{font-size:.82rem}
.spinner.hidden{display:none}

/* ─── CONFIRM MODAL ─── */
.modal-sm{max-width:420px}
#confirmBody{padding:12px 22px 6px}
#confirmBody p{font-size:.88rem;color:var(--mid);line-height:1.6}

=======
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
</style>
</head>
<body>
<div class="app">

<!-- OVERLAY -->
<div class="sidebar-overlay" id="overlay" onclick="closeMobile()"></div>

<!-- ══════════ SIDEBAR ══════════ -->
<aside class="sidebar" id="sidebar">
<<<<<<< HEAD
  <div class="brand">
    <img src="assets/images/logo_white.svg" alt="HopeConnect" class="brand-logo-img">
=======

  <div class="brand">
    <div class="brand-logo"><i class="fas fa-hands-holding-heart"></i></div>
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
    <div class="brand-text">
      <div class="brand-name">HopeConnect</div>
      <div class="brand-sub">NGO Admin Portal</div>
    </div>
  </div>

  <nav class="nav-scroll">
    <div class="nav-section">Overview</div>
    <div class="nav-item active" onclick="showPage('dashboard',this)">
      <i class="fas fa-chart-pie nav-icon"></i>
      <span class="nav-text">Dashboard</span>
    </div>

    <div class="nav-section">Management</div>
    <div class="nav-item" onclick="showPage('donations',this)">
      <i class="fas fa-hand-holding-dollar nav-icon"></i>
      <span class="nav-text">Donations</span>
<<<<<<< HEAD
      <span class="nav-badge"><?php echo Helpers::e((string)$pendingReview); ?></span>
=======
      <span class="nav-badge">12</span>
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
    </div>
    <div class="nav-item" onclick="showPage('users',this)">
      <i class="fas fa-users nav-icon"></i>
      <span class="nav-text">Users</span>
<<<<<<< HEAD
      <span class="nav-badge green"><?php echo Helpers::e((string)$totalAdmins); ?></span>
    </div>
    <div class="nav-item" onclick="showPage('programmes',this)">
      <i class="fas fa-seedling nav-icon"></i>
      <span class="nav-text">Programmes</span>
      <span class="nav-badge amber"><?php echo Helpers::e((string)$publishedProgrammes); ?></span>
=======
      <span class="nav-badge green">248</span>
    </div>
    <div class="nav-item" onclick="showPage('patients',this)">
      <i class="fas fa-hospital-user nav-icon"></i>
      <span class="nav-text">Patients</span>
      <span class="nav-badge amber">5</span>
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
    </div>
    <div class="nav-item" onclick="showPage('partners',this)">
      <i class="fas fa-handshake nav-icon"></i>
      <span class="nav-text">Partners</span>
    </div>

    <div class="nav-section">Content</div>
    <div class="nav-item" onclick="showPage('blog',this)">
      <i class="fas fa-newspaper nav-icon"></i>
      <span class="nav-text">Blog &amp; News</span>
    </div>
<<<<<<< HEAD
    <div class="nav-item" onclick="showPage('events',this)">
      <i class="fas fa-calendar-days nav-icon"></i>
      <span class="nav-text">Events</span>
    </div>
=======
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
    <div class="nav-item" onclick="showPage('gallery',this)">
      <i class="fas fa-images nav-icon"></i>
      <span class="nav-text">Gallery</span>
    </div>

    <div class="nav-section">System</div>
    <div class="nav-item" onclick="showPage('security',this)">
      <i class="fas fa-shield-halved nav-icon"></i>
      <span class="nav-text">Security</span>
<<<<<<< HEAD
=======
      <span class="nav-badge">3</span>
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
    </div>
    <div class="nav-item" onclick="showPage('settings',this)">
      <i class="fas fa-gear nav-icon"></i>
      <span class="nav-text">Settings</span>
    </div>
  </nav>

  <div class="sidebar-footer">
<<<<<<< HEAD
    <div class="user-ava"><?php echo Helpers::e($adminInitials); ?></div>
    <div class="user-text">
      <div class="user-name"><?php echo Helpers::e($adminName); ?></div>
      <div class="user-role"><?php echo Helpers::e(ucwords(str_replace("_", " ", $adminRole))); ?></div>
    </div>
  </div>
  <div class="footer-links">
    <a href="<?php echo Helpers::e(Helpers::siteUrl()); ?>" target="_blank">View Site</a>
    <a href="<?php echo Helpers::e(Helpers::adminUrl("logout.php")); ?>" class="danger">Logout</a>
=======
    <div class="user-ava">SA</div>
    <div class="user-text">
      <div class="user-name">Super Admin</div>
      <div class="user-role">Administrator</div>
    </div>
  </div>
  <div class="footer-links">
    <a href="#">Help</a>
    <a href="#">Privacy</a>
    <a href="#" class="danger" style="color:#4b5563">Logout</a>
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
  </div>
</aside>

<!-- ══════════ MAIN ══════════ -->
<div class="main" id="main">

  <!-- TOPBAR -->
  <header class="topbar">
    <button class="menu-btn" id="menuBtn" onclick="toggleSidebar()" aria-label="Toggle menu">
      <span></span><span></span><span></span>
    </button>
    <div class="page-heading">
      <div class="page-title" id="pageTitle">Dashboard</div>
      <div class="breadcrumb">
        <span>HopeConnect</span>
        <i class="fas fa-chevron-right"></i>
        <span id="breadSub">Overview</span>
      </div>
    </div>
    <div class="topbar-search">
      <i class="fas fa-search"></i>
      <input type="text" placeholder="Search anything…" aria-label="Search"/>
    </div>
    <div class="topbar-right">
      <button class="tb-btn" title="Notifications" aria-label="Notifications">
        <i class="fas fa-bell"></i>
        <span class="tb-dot"></span>
      </button>
      <button class="tb-btn hide-sm" title="Messages" aria-label="Messages">
        <i class="fas fa-envelope"></i>
      </button>
<<<<<<< HEAD
      <div class="tb-avatar" title="Profile"><?php echo Helpers::e($adminInitials); ?></div>
    </div>
  </header>

  <!-- ════════════════════════════════════════════
       DASHBOARD
  ════════════════════════════════════════════ -->
  <div class="content active" id="page-dashboard">

=======
      <div class="tb-avatar" title="Profile">SA</div>
    </div>
  </header>

  <!-- ══════════════════════════════
       DASHBOARD
  ══════════════════════════════ -->
  <div class="content active" id="page-dashboard">

    <div class="alert-banner warn">
      <i class="fas fa-triangle-exclamation"></i>
      <span><strong>3 security alerts</strong> require your attention — <a href="#" onclick="showPage('security',document.querySelector('[onclick*=security]'))" style="color:inherit;font-weight:700;text-decoration:underline">Review now</a></span>
    </div>

>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
    <div class="stats-grid">
      <div class="stat-card t1">
        <div class="stat-top">
          <div class="stat-icon-wrap"><i class="fas fa-dollar-sign"></i></div>
<<<<<<< HEAD
          <span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i> All Time</span>
        </div>
        <div class="stat-value"><?php echo Helpers::e(Helpers::fmt($totalDonationsAll)); ?></div>
        <div class="stat-label">Total Donations</div>
        <div class="stat-sub"><i class="far fa-clock" style="margin-right:4px"></i><?php echo Helpers::e(Helpers::fmt($totalDonationsYear)); ?> this year</div>
      </div>
      <div class="stat-card t4">
        <div class="stat-top">
          <div class="stat-icon-wrap"><i class="fas fa-users"></i></div>
          <span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i><?php echo Helpers::e($activeAdmins); ?> active</span>
        </div>
        <div class="stat-value"><?php echo Helpers::e($totalAdmins); ?></div>
        <div class="stat-label">Admin Users</div>
        <div class="stat-sub"><i class="far fa-clock" style="margin-right:4px"></i><?php echo Helpers::e($suspendedAdmins); ?> suspended</div>
      </div>
      <div class="stat-card t2">
        <div class="stat-top">
          <div class="stat-icon-wrap"><i class="fas fa-seedling"></i></div>
          <span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i><?php echo Helpers::e($publishedProgrammes); ?> active</span>
        </div>
        <div class="stat-value"><?php echo Helpers::e($publishedProgrammes); ?></div>
        <div class="stat-label">Active Programmes</div>
        <div class="stat-sub"><i class="far fa-clock" style="margin-right:4px"></i>Published programmes</div>
      </div>
      <div class="stat-card t5">
        <div class="stat-top">
          <div class="stat-icon-wrap"><i class="fas fa-handshake"></i></div>
          <span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i><?php echo Helpers::e($activePartners); ?> partners</span>
        </div>
        <div class="stat-value"><?php echo Helpers::e($activePartners); ?></div>
        <div class="stat-label">Active Partners</div>
        <div class="stat-sub"><i class="far fa-clock" style="margin-right:4px"></i>Supporting our mission</div>
=======
          <span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i>18.4%</span>
        </div>
        <div class="stat-value">$248,500</div>
        <div class="stat-label">Total Donations This Year</div>
        <div class="stat-sub"><i class="far fa-clock" style="margin-right:4px"></i>Updated just now</div>
      </div>
      <div class="stat-card t2">
        <div class="stat-top">
          <div class="stat-icon-wrap"><i class="fas fa-users"></i></div>
          <span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i>6.2%</span>
        </div>
        <div class="stat-value">2,481</div>
        <div class="stat-label">Registered Users</div>
        <div class="stat-sub"><i class="far fa-clock" style="margin-right:4px"></i>+154 this week</div>
      </div>
      <div class="stat-card t3">
        <div class="stat-top">
          <div class="stat-icon-wrap"><i class="fas fa-hospital-user"></i></div>
          <span class="stat-trend down"><i class="fas fa-arrow-trend-down"></i>2 today</span>
        </div>
        <div class="stat-value">312</div>
        <div class="stat-label">Active Patients</div>
        <div class="stat-sub"><i class="far fa-clock" style="margin-right:4px"></i>2 discharged today</div>
      </div>
      <div class="stat-card t4">
        <div class="stat-top">
          <div class="stat-icon-wrap"><i class="fas fa-handshake"></i></div>
          <span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i>3 new</span>
        </div>
        <div class="stat-value">47</div>
        <div class="stat-label">Active Partners</div>
        <div class="stat-sub"><i class="far fa-clock" style="margin-right:4px"></i>+3 this month</div>
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
      </div>
    </div>

    <div class="charts-row">
      <div class="card">
        <div class="card-hd">
          <div class="card-hd-left">
            <div class="card-title">Donation Overview</div>
<<<<<<< HEAD
            <div class="card-sub">Monthly donations (last 12 months)</div>
          </div>
        </div>
        <div class="bar-chart-wrap">
          <div class="bar-chart" id="barChart">
            <?php $maxMonthly = $monthlyDonationData ? max(1, ...array_column($monthlyDonationData, "total")) : 1; ?>
            <?php foreach ($monthlyDonationData as $m): ?>
            <div class="bar-col">
              <div class="bar-stack">
                <div class="bar primary" style="height:<?php echo Helpers::e(max(4, (int)(($m["total"] / $maxMonthly) * 100))); ?>%">
                  <span class="bar-tooltip">$<?php echo Helpers::e(number_format($m["total"], 0)); ?></span>
                </div>
              </div>
              <div class="bar-lbl"><?php echo Helpers::e($m["label"]); ?></div>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="chart-legend">
            <div class="legend-item"><div class="legend-dot" style="background:var(--brand)"></div>Donations Received</div>
=======
            <div class="card-sub">Monthly donations — 2025</div>
          </div>
          <a class="card-link" onclick="showPage('donations',null)"><i class="fas fa-arrow-right"></i> View All</a>
        </div>
        <div class="bar-chart-wrap">
          <div class="bar-chart">
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:55%"></div><div class="bar secondary" style="height:38%"></div></div><div class="bar-lbl">Jan</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:70%"></div><div class="bar secondary" style="height:52%"></div></div><div class="bar-lbl">Feb</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:45%"></div><div class="bar secondary" style="height:30%"></div></div><div class="bar-lbl">Mar</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:80%"></div><div class="bar secondary" style="height:65%"></div></div><div class="bar-lbl">Apr</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:90%"></div><div class="bar secondary" style="height:72%"></div></div><div class="bar-lbl">May</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:60%"></div><div class="bar secondary" style="height:44%"></div></div><div class="bar-lbl">Jun</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:75%"></div><div class="bar secondary" style="height:58%"></div></div><div class="bar-lbl">Jul</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:100%"></div><div class="bar secondary" style="height:80%"></div></div><div class="bar-lbl">Aug</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:82%"></div><div class="bar secondary" style="height:66%"></div></div><div class="bar-lbl">Sep</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:68%"></div><div class="bar secondary" style="height:50%"></div></div><div class="bar-lbl">Oct</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:55%"></div><div class="bar secondary" style="height:40%"></div></div><div class="bar-lbl">Nov</div></div>
            <div class="bar-col"><div class="bar-stack"><div class="bar primary" style="height:40%"></div><div class="bar secondary" style="height:28%"></div></div><div class="bar-lbl">Dec</div></div>
          </div>
          <div class="chart-legend">
            <div class="legend-item"><div class="legend-dot" style="background:var(--brand)"></div>Received</div>
            <div class="legend-item"><div class="legend-dot" style="background:var(--brand-dim)"></div>Disbursed</div>
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-hd">
          <div class="card-hd-left">
<<<<<<< HEAD
            <div class="card-title">Gateway Distribution</div>
            <div class="card-sub">Payment method breakdown</div>
          </div>
        </div>
        <?php if ($gatewayMix): ?>
        <div class="donut-wrap" style="display:flex;align-items:center;gap:20px;flex-wrap:wrap">
          <div style="position:relative;width:110px;height:110px;flex-shrink:0">
            <svg width="110" height="110" viewBox="0 0 110 110">
              <circle cx="55" cy="55" r="40" fill="none" stroke="#e5e7eb" stroke-width="16"/>
              <?php
              $totalGw = array_sum(array_column($gatewayMix, "count")) ?: 1;
              $offset = 0;
              $gwColors = ["#0f766e","#d97706","#2563eb","#7c3aed","#dc2626"];
              foreach ($gatewayMix as $i => $gw):
                $pct = ($gw["count"] / $totalGw) * 251;
                $color = $gwColors[$i % count($gwColors)];
              ?>
              <circle cx="55" cy="55" r="40" fill="none" stroke="<?php echo Helpers::e($color); ?>" stroke-width="16" stroke-dasharray="<?php echo Helpers::e(max(1, $pct)); ?> 251" stroke-dashoffset="-<?php echo Helpers::e($offset); ?>" transform="rotate(-90 55 55)"/>
              <?php $offset += $pct; endforeach; ?>
              <text x="55" y="50" text-anchor="middle" font-size="13" font-weight="800" fill="#0c1220" font-family="Plus Jakarta Sans,sans-serif"><?php echo Helpers::e($totalGw); ?></text>
              <text x="55" y="63" text-anchor="middle" font-size="8" fill="#6b7280" font-family="Plus Jakarta Sans,sans-serif">transactions</text>
            </svg>
          </div>
          <div class="donut-legend">
            <?php foreach ($gatewayMix as $i => $gw): $color = $gwColors[$i % count($gwColors)]; ?>
            <div class="dl-item"><div class="dl-dot" style="background:<?php echo Helpers::e($color); ?>"></div><span class="dl-lbl"><?php echo Helpers::e($gw["name"]); ?></span><span class="dl-val"><?php echo Helpers::e(Helpers::fmt($gw["total"])); ?></span></div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php else: ?>
        <div class="empty-state"><i class="fas fa-credit-card"></i><p>No payment data yet</p><div class="sub">Donations will appear here once processed</div></div>
        <?php endif; ?>
        <div class="mini-grid">
          <div class="mini-stat"><div class="v"><?php echo Helpers::e($totalTxCount); ?></div><div class="l">Transactions</div></div>
          <div class="mini-stat"><div class="v"><?php echo Helpers::e($publishedPosts); ?></div><div class="l">Blog Posts</div></div>
          <div class="mini-stat"><div class="v"><?php echo Helpers::e($publishedEvents); ?></div><div class="l">Events</div></div>
          <div class="mini-stat"><div class="v"><?php echo Helpers::e((int)($publishedProgrammes) + (int)($activePartners)); ?></div><div class="l">Total Items</div></div>
=======
            <div class="card-title">Patients by Category</div>
            <div class="card-sub">Current distribution</div>
          </div>
        </div>
        <div class="donut-wrap">
          <svg width="110" height="110" viewBox="0 0 110 110" style="flex-shrink:0">
            <circle cx="55" cy="55" r="40" fill="none" stroke="#e5e7eb" stroke-width="16"/>
            <circle cx="55" cy="55" r="40" fill="none" stroke="#0f766e" stroke-width="16" stroke-dasharray="100 151" stroke-dashoffset="0" transform="rotate(-90 55 55)"/>
            <circle cx="55" cy="55" r="40" fill="none" stroke="#fbbf24" stroke-width="16" stroke-dasharray="60 191" stroke-dashoffset="-100" transform="rotate(-90 55 55)"/>
            <circle cx="55" cy="55" r="40" fill="none" stroke="#dc2626" stroke-width="16" stroke-dasharray="40 211" stroke-dashoffset="-160" transform="rotate(-90 55 55)"/>
            <circle cx="55" cy="55" r="40" fill="none" stroke="#2563eb" stroke-width="16" stroke-dasharray="51 200" stroke-dashoffset="-200" transform="rotate(-90 55 55)"/>
            <text x="55" y="50" text-anchor="middle" font-size="13" font-weight="800" fill="#0c1220" font-family="Plus Jakarta Sans,sans-serif">312</text>
            <text x="55" y="63" text-anchor="middle" font-size="8" fill="#6b7280" font-family="Plus Jakarta Sans,sans-serif">patients</text>
          </svg>
          <div class="donut-legend">
            <div class="dl-item"><div class="dl-dot" style="background:#0f766e"></div><span class="dl-lbl">Medical Aid</span><span class="dl-val">40%</span></div>
            <div class="dl-item"><div class="dl-dot" style="background:#fbbf24"></div><span class="dl-lbl">Nutrition</span><span class="dl-val">24%</span></div>
            <div class="dl-item"><div class="dl-dot" style="background:#dc2626"></div><span class="dl-lbl">Mental Health</span><span class="dl-val">16%</span></div>
            <div class="dl-item"><div class="dl-dot" style="background:#2563eb"></div><span class="dl-lbl">Emergency</span><span class="dl-val">20%</span></div>
          </div>
        </div>
        <div class="mini-grid">
          <div class="mini-stat"><div class="v">89%</div><div class="l">Recovery Rate</div></div>
          <div class="mini-stat"><div class="v">14d</div><div class="l">Avg. Stay</div></div>
          <div class="mini-stat"><div class="v">97</div><div class="l">Discharged</div></div>
          <div class="mini-stat"><div class="v">6</div><div class="l">Critical</div></div>
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
        </div>
      </div>
    </div>

    <div class="two-col">
      <div class="card">
        <div class="card-hd">
          <div class="card-hd-left">
            <div class="card-title">Recent Donations</div>
<<<<<<< HEAD
            <div class="card-sub">Latest transactions</div>
          </div>
          <a class="card-link" onclick="showPage('donations',null)"><i class="fas fa-arrow-right"></i> View All</a>
        </div>
        <?php if ($recentDonations): ?>
        <div class="feed-list">
          <?php foreach ($recentDonations as $d): ?>
          <div class="feed-row">
            <div class="feed-ava" style="background:<?php echo Helpers::e(Helpers::bc($d["donor_name"] ?? "A")); ?>"><?php echo Helpers::e(Helpers::initls($d["donor_name"] ?? "AN")); ?></div>
            <div class="feed-info">
              <div class="feed-name"><?php echo Helpers::e($d["donor_name"] ?: "Anonymous Donor"); ?></div>
              <div class="feed-sub"><i class="fas fa-credit-card" style="margin-right:3px"></i><?php echo Helpers::e(ucfirst((string)($d["gateway"] ?? "manual"))); ?> · <?php echo Helpers::e(Helpers::ta($d["dt"] ?? null)); ?></div>
            </div>
            <?php $status = strtolower((string)($d["status"] ?? "pending")); ?>
            <span class="badge <?php echo Helpers::e($status === "successful" ? "success" : ($status === "pending" ? "warning" : ($status === "failed" ? "danger" : "neutral"))); ?>">
              <i class="fas fa-<?php echo Helpers::e($status === "successful" ? "check" : ($status === "pending" ? "clock" : ($status === "failed" ? "xmark" : "circle"))); ?>"></i>
              <?php echo Helpers::e(ucfirst($status)); ?>
            </span>
            <div class="feed-amt"><?php echo Helpers::e(Helpers::fmt((float)($d["amount"] ?? 0))); ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state"><i class="fas fa-inbox"></i><p>No donations yet</p><div class="sub">When donors contribute, their transactions will show here</div></div>
        <?php endif; ?>
=======
            <div class="card-sub">Last 24 hours</div>
          </div>
          <a class="card-link" onclick="showPage('donations',null)"><i class="fas fa-arrow-right"></i> View All</a>
        </div>
        <div class="feed-list">
          <div class="feed-row">
            <div class="feed-ava" style="background:#0f766e">AM</div>
            <div class="feed-info"><div class="feed-name">Amara Osei</div><div class="feed-sub"><i class="fas fa-credit-card" style="margin-right:3px"></i>One-time · Credit Card</div></div>
            <span class="badge success"><i class="fas fa-check"></i>Completed</span>
            <div class="feed-amt">$500</div>
          </div>
          <div class="feed-row">
            <div class="feed-ava" style="background:#2563eb">KC</div>
            <div class="feed-info"><div class="feed-name">Kwame Asante</div><div class="feed-sub"><i class="fas fa-building-columns" style="margin-right:3px"></i>Monthly · Bank Transfer</div></div>
            <span class="badge success"><i class="fas fa-check"></i>Completed</span>
            <div class="feed-amt">$1,200</div>
          </div>
          <div class="feed-row">
            <div class="feed-ava" style="background:#d97706">FN</div>
            <div class="feed-info"><div class="feed-name">Fatima Njoku</div><div class="feed-sub"><i class="fas fa-mobile-screen" style="margin-right:3px"></i>One-time · Mobile Money</div></div>
            <span class="badge warning"><i class="fas fa-clock"></i>Pending</span>
            <div class="feed-amt">$250</div>
          </div>
          <div class="feed-row">
            <div class="feed-ava" style="background:#7c3aed">EM</div>
            <div class="feed-info"><div class="feed-name">Emmanuel Mensah</div><div class="feed-sub"><i class="fas fa-building-columns" style="margin-right:3px"></i>Annual · Wire Transfer</div></div>
            <span class="badge success"><i class="fas fa-check"></i>Completed</span>
            <div class="feed-amt">$5,000</div>
          </div>
          <div class="feed-row">
            <div class="feed-ava" style="background:#dc2626">CI</div>
            <div class="feed-info"><div class="feed-name">Corporate: Intex Ltd</div><div class="feed-sub"><i class="fas fa-building-columns" style="margin-right:3px"></i>One-time · Bank Transfer</div></div>
            <span class="badge danger"><i class="fas fa-xmark"></i>Failed</span>
            <div class="feed-amt">$10,000</div>
          </div>
        </div>
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
      </div>

      <div class="card">
        <div class="card-hd">
          <div class="card-hd-left">
<<<<<<< HEAD
            <div class="card-title">Recent Activity</div>
            <div class="card-sub">Latest updates across the platform</div>
          </div>
        </div>
        <?php if ($recentActivity): ?>
        <div class="activity-list">
          <?php foreach ($recentActivity as $act): ?>
          <div class="act-row">
            <div class="act-icon <?php echo Helpers::e($act["type"] === "post" ? "info" : "login"); ?>"><i class="fas fa-<?php echo Helpers::e($act["type"] === "post" ? "newspaper" : "heart"); ?>"></i></div>
            <div class="act-body">
              <span class="act-title"><?php echo Helpers::e((string)$act["item"]); ?></span>
              <span class="act-desc"><?php echo Helpers::e(ucfirst((string)$act["type"])); ?></span>
            </div>
            <div class="act-time"><?php echo Helpers::e(Helpers::ta($act["dt"] ?? null)); ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state"><i class="fas fa-clock"></i><p>No recent activity</p></div>
        <?php endif; ?>
=======
            <div class="card-title">Patient Admissions</div>
            <div class="card-sub">Today's status</div>
          </div>
        </div>
        <div class="ps-list">
          <div class="ps-row">
            <div class="ps-bar" style="background:#0f766e"></div>
            <div class="ps-info"><div class="ps-name">Chidinma A. — F/28</div><div class="ps-detail"><i class="fas fa-bed" style="margin-right:3px"></i>Medical Aid · Ward B</div></div>
            <span class="badge teal">Stable</span>
          </div>
          <div class="ps-row">
            <div class="ps-bar" style="background:#dc2626"></div>
            <div class="ps-info"><div class="ps-name">Musa Ibrahim — M/45</div><div class="ps-detail"><i class="fas fa-heart-pulse" style="margin-right:3px"></i>Emergency · ICU</div></div>
            <span class="badge danger">Critical</span>
          </div>
          <div class="ps-row">
            <div class="ps-bar" style="background:#fbbf24"></div>
            <div class="ps-info"><div class="ps-name">Grace Eze — F/12</div><div class="ps-detail"><i class="fas fa-apple-alt" style="margin-right:3px"></i>Nutrition · Ward A</div></div>
            <span class="badge warning">Monitoring</span>
          </div>
          <div class="ps-row">
            <div class="ps-bar" style="background:#2563eb"></div>
            <div class="ps-info"><div class="ps-name">Tunde Bakare — M/33</div><div class="ps-detail"><i class="fas fa-brain" style="margin-right:3px"></i>Mental Health · Clinic C</div></div>
            <span class="badge info">In Session</span>
          </div>
          <div class="ps-row">
            <div class="ps-bar" style="background:#059669"></div>
            <div class="ps-info"><div class="ps-name">Adaeze Nwosu — F/19</div><div class="ps-detail"><i class="fas fa-bed" style="margin-right:3px"></i>Medical Aid · Ward B</div></div>
            <span class="badge success">Discharged</span>
          </div>
        </div>
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
      </div>
    </div>

  </div>

<<<<<<< HEAD
  <!-- ════════════════════════════════════════════
       DONATIONS
  ════════════════════════════════════════════ -->
  <div class="content" id="page-donations">
    <div class="stats-grid">
      <div class="stat-card t1">
        <div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-dollar-sign"></i></div><span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i>All time</span></div>
        <div class="stat-value"><?php echo Helpers::e(Helpers::fmt($totalDonationsAll)); ?></div><div class="stat-label">Total Raised</div>
      </div>
      <div class="stat-card t4">
        <div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-receipt"></i></div><span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i><?php echo Helpers::e($totalTxCount); ?> total</span></div>
        <div class="stat-value"><?php echo Helpers::e($totalTxCount); ?></div><div class="stat-label">Total Transactions</div>
      </div>
      <div class="stat-card t2">
        <div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-hourglass-half"></i></div><span class="stat-trend neutral">Needs action</span></div>
        <div class="stat-value"><?php echo Helpers::e($pendingReview); ?></div><div class="stat-label">Pending Review</div>
      </div>
      <div class="stat-card t3">
        <div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-circle-xmark"></i></div><span class="stat-trend down"><i class="fas fa-arrow-down"></i><?php echo Helpers::e($failedCount); ?> txns</span></div>
        <div class="stat-value"><?php echo Helpers::e($failedCount); ?></div><div class="stat-label">Failed / Reversed</div>
=======
  <!-- ══════════════════════════════
       DONATIONS
  ══════════════════════════════ -->
  <div class="content" id="page-donations">
    <div class="stats-grid">
      <div class="stat-card t1">
        <div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-dollar-sign"></i></div><span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i>18.4%</span></div>
        <div class="stat-value">$248.5K</div><div class="stat-label">Total Raised</div>
      </div>
      <div class="stat-card t4">
        <div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-receipt"></i></div><span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i>54 this week</span></div>
        <div class="stat-value">1,842</div><div class="stat-label">Total Transactions</div>
      </div>
      <div class="stat-card t2">
        <div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-hourglass-half"></i></div><span class="stat-trend neutral">Needs action</span></div>
        <div class="stat-value">12</div><div class="stat-label">Pending Review</div>
      </div>
      <div class="stat-card t3">
        <div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-circle-xmark"></i></div><span class="stat-trend down"><i class="fas fa-arrow-down"></i>3 txns</span></div>
        <div class="stat-value">$3,200</div><div class="stat-label">Failed / Reversed</div>
      </div>
    </div>

    <div class="card" style="margin-bottom:18px">
      <div class="card-hd">
        <div class="card-hd-left"><div class="card-title">Active Campaigns</div></div>
        <button class="btn-primary"><i class="fas fa-plus"></i> New Campaign</button>
      </div>
      <div class="campaign-grid">
        <div class="campaign-card">
          <div class="cmp-top"><div><div class="cmp-name">Medical Equipment Fund</div><div class="cmp-date"><i class="far fa-calendar" style="margin-right:3px"></i>Ends Dec 31, 2025</div></div><span class="badge success">Active</span></div>
          <div class="cmp-nums"><span>$42,000 raised</span><span>$60,000 goal</span></div>
          <div class="prog-wrap"><div class="prog-bar" style="width:70%"></div></div>
          <div class="cmp-pct" style="color:var(--brand)">70% funded</div>
        </div>
        <div class="campaign-card">
          <div class="cmp-top"><div><div class="cmp-name">Child Nutrition Program</div><div class="cmp-date"><i class="far fa-calendar" style="margin-right:3px"></i>Ends Mar 15, 2026</div></div><span class="badge success">Active</span></div>
          <div class="cmp-nums"><span>$18,500 raised</span><span>$25,000 goal</span></div>
          <div class="prog-wrap"><div class="prog-bar" style="width:74%"></div></div>
          <div class="cmp-pct" style="color:var(--brand)">74% funded</div>
        </div>
        <div class="campaign-card">
          <div class="cmp-top"><div><div class="cmp-name">Emergency Relief 2025</div><div class="cmp-date"><i class="far fa-clock" style="margin-right:3px"></i>Ongoing</div></div><span class="badge warning">Urgent</span></div>
          <div class="cmp-nums"><span>$8,200 raised</span><span>$50,000 goal</span></div>
          <div class="prog-wrap"><div class="prog-bar urgent" style="width:16%"></div></div>
          <div class="cmp-pct" style="color:var(--rose)">16% funded</div>
        </div>
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
      </div>
    </div>

    <div class="card">
      <div class="toolbar">
        <div class="search-box"><i class="fas fa-search"></i><input placeholder="Search donations…"/></div>
        <button class="filter-btn"><i class="far fa-calendar"></i> Date Range</button>
        <button class="filter-btn"><i class="fas fa-credit-card"></i> Method</button>
        <button class="filter-btn"><i class="fas fa-circle-half-stroke"></i> Status</button>
        <button class="btn-primary ml"><i class="fas fa-download"></i> Export CSV</button>
      </div>
      <div style="overflow-x:auto">
        <table class="data-table">
<<<<<<< HEAD
          <thead><tr><th>Donor</th><th>Amount</th><th>Gateway</th><th>Reference</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
            <?php
            $allDonations = $dbAvail ? (Database::fetchAll("SELECT donor_name,amount,currency,gateway,payment_reference,status,COALESCE(paid_at,created_at) AS dt FROM donations ORDER BY dt DESC LIMIT 20") ?: []) : [];
            ?>
            <?php if ($allDonations): ?>
              <?php foreach ($allDonations as $d): ?>
              <?php $st = strtolower((string)($d["status"] ?? "pending")); ?>
              <tr>
                <td data-label="Donor"><div class="cell-user"><div class="cell-ava" style="background:<?php echo Helpers::e(Helpers::bc($d["donor_name"] ?? "A")); ?>"><?php echo Helpers::e(Helpers::initls($d["donor_name"] ?? "AN")); ?></div><div><span class="cell-name"><?php echo Helpers::e($d["donor_name"] ?: "Anonymous"); ?></span><span class="cell-sub">via <?php echo Helpers::e(ucfirst((string)($d["gateway"] ?? "manual"))); ?></span></div></div></td>
                <td data-label="Amount"><strong><?php echo Helpers::e(Helpers::fmt((float)($d["amount"] ?? 0))); ?></strong></td>
                <td data-label="Gateway"><span class="badge info"><?php echo Helpers::e(ucfirst((string)($d["gateway"] ?? "manual"))); ?></span></td>
                <td data-label="Reference" class="mono"><?php echo Helpers::e((string)($d["payment_reference"] ?? "—")); ?></td>
                <td data-label="Date" class="mono"><?php echo Helpers::e(Helpers::ta($d["dt"] ?? null)); ?></td>
                <td data-label="Status"><span class="badge <?php echo Helpers::e($st === "successful" ? "success" : ($st === "pending" ? "warning" : ($st === "failed" ? "danger" : "neutral"))); ?>"><i class="fas fa-<?php echo Helpers::e($st === "successful" ? "check" : ($st === "pending" ? "clock" : ($st === "failed" ? "xmark" : "circle"))); ?>"></i><?php echo Helpers::e(ucfirst($st)); ?></span></td>
                <td data-label="Actions"><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button></div></td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7"><div class="empty-state"><i class="fas fa-inbox"></i><p>No donation records</p></div></td></tr>
            <?php endif; ?>
=======
          <thead>
            <tr><th>Donor</th><th>Amount</th><th>Campaign</th><th>Method</th><th>Date</th><th>Status</th><th>Actions</th></tr>
          </thead>
          <tbody>
            <tr>
              <td><div class="cell-user"><div class="cell-ava" style="background:#0f766e">AM</div><div><span class="cell-name">Amara Osei</span><span class="cell-sub">amara@email.com</span></div></div></td>
              <td><strong>$500.00</strong></td><td>Medical Equipment</td>
              <td><span class="badge info"><i class="fas fa-credit-card"></i>Card</span></td>
              <td class="mono">May 7, 2025</td>
              <td><span class="badge success"><i class="fas fa-check"></i>Completed</span></td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button></div></td>
            </tr>
            <tr>
              <td><div class="cell-user"><div class="cell-ava" style="background:#2563eb">KC</div><div><span class="cell-name">Kwame Asante</span><span class="cell-sub">kwame@corp.com</span></div></div></td>
              <td><strong>$1,200.00</strong></td><td>Child Nutrition</td>
              <td><span class="badge neutral"><i class="fas fa-building-columns"></i>Bank</span></td>
              <td class="mono">May 7, 2025</td>
              <td><span class="badge success"><i class="fas fa-check"></i>Completed</span></td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button></div></td>
            </tr>
            <tr>
              <td><div class="cell-user"><div class="cell-ava" style="background:#d97706">FN</div><div><span class="cell-name">Fatima Njoku</span><span class="cell-sub">f.njoku@mail.ng</span></div></div></td>
              <td><strong>$250.00</strong></td><td>Emergency Relief</td>
              <td><span class="badge violet"><i class="fas fa-mobile-screen"></i>Mobile</span></td>
              <td class="mono">May 6, 2025</td>
              <td><span class="badge warning"><i class="fas fa-clock"></i>Pending</span></td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button><button class="action-btn del"><i class="fas fa-trash"></i></button></div></td>
            </tr>
            <tr>
              <td><div class="cell-user"><div class="cell-ava" style="background:#7c3aed">EM</div><div><span class="cell-name">Emmanuel Mensah</span><span class="cell-sub">e.mensah@org.gh</span></div></div></td>
              <td><strong>$5,000.00</strong></td><td>Medical Equipment</td>
              <td><span class="badge neutral"><i class="fas fa-building-columns"></i>Wire</span></td>
              <td class="mono">May 5, 2025</td>
              <td><span class="badge success"><i class="fas fa-check"></i>Completed</span></td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button></div></td>
            </tr>
            <tr>
              <td><div class="cell-user"><div class="cell-ava" style="background:#dc2626">CI</div><div><span class="cell-name">Intex Ltd</span><span class="cell-sub">giving@intex.com</span></div></div></td>
              <td><strong>$10,000.00</strong></td><td>General Fund</td>
              <td><span class="badge neutral"><i class="fas fa-building-columns"></i>Bank</span></td>
              <td class="mono">May 4, 2025</td>
              <td><span class="badge danger"><i class="fas fa-xmark"></i>Failed</span></td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button><button class="action-btn del"><i class="fas fa-trash"></i></button></div></td>
            </tr>
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
          </tbody>
        </table>
      </div>
      <div class="pagination">
<<<<<<< HEAD
        <span class="page-info">Showing <?php echo Helpers::e(min(count($allDonations), 20)); ?> of <?php echo Helpers::e($totalTxCount); ?> entries</span>
        <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
        <button class="page-btn on">1</button>
=======
        <span class="page-info">Showing 1–5 of 1,842 entries</span>
        <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
        <button class="page-btn on">1</button><button class="page-btn">2</button><button class="page-btn">3</button>
        <button class="page-btn">…</button><button class="page-btn">368</button>
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
        <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
      </div>
    </div>
  </div>

<<<<<<< HEAD
  <!-- ════════════════════════════════════════════
       USERS
  ════════════════════════════════════════════ -->
  <div class="content" id="page-users">
    <div class="stats-grid">
      <div class="stat-card t1"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-users"></i></div><span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i>Total</span></div><div class="stat-value"><?php echo Helpers::e($totalAdmins); ?></div><div class="stat-label">Total Admin Users</div></div>
      <div class="stat-card t4"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-user-check"></i></div><span class="stat-trend up"><?php echo Helpers::e($totalAdmins > 0 ? round(($activeAdmins/$totalAdmins)*100) : 0); ?>%</span></div><div class="stat-value"><?php echo Helpers::e($activeAdmins); ?></div><div class="stat-label">Active</div></div>
      <div class="stat-card t2"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-user-clock"></i></div><span class="stat-trend neutral">Inactive</span></div><div class="stat-value"><?php echo Helpers::e(max(0, $totalAdmins - $activeAdmins - $suspendedAdmins)); ?></div><div class="stat-label">Inactive</div></div>
      <div class="stat-card t3"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-user-slash"></i></div><span class="stat-trend down"><i class="fas fa-arrow-down"></i><?php echo Helpers::e($suspendedAdmins); ?></span></div><div class="stat-value"><?php echo Helpers::e($suspendedAdmins); ?></div><div class="stat-label">Suspended</div></div>
=======
  <!-- ══════════════════════════════
       USERS
  ══════════════════════════════ -->
  <div class="content" id="page-users">
    <div class="stats-grid">
      <div class="stat-card t1"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-users"></i></div><span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i>6.2%</span></div><div class="stat-value">2,481</div><div class="stat-label">Total Users</div></div>
      <div class="stat-card t4"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-user-check"></i></div><span class="stat-trend up">84.8%</span></div><div class="stat-value">2,104</div><div class="stat-label">Verified</div></div>
      <div class="stat-card t2"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-user-clock"></i></div><span class="stat-trend neutral">10% of total</span></div><div class="stat-value">248</div><div class="stat-label">Pending Verification</div></div>
      <div class="stat-card t3"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-user-slash"></i></div><span class="stat-trend down"><i class="fas fa-arrow-down"></i>2 this week</span></div><div class="stat-value">129</div><div class="stat-label">Suspended</div></div>
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
    </div>
    <div class="card">
      <div class="toolbar">
        <div class="search-box"><i class="fas fa-search"></i><input placeholder="Search users…"/></div>
        <button class="filter-btn"><i class="fas fa-tag"></i> Role</button>
        <button class="filter-btn"><i class="fas fa-circle-half-stroke"></i> Status</button>
<<<<<<< HEAD
        <button class="btn-primary ml"><i class="fas fa-user-plus"></i> Add User</button>
      </div>
      <div style="overflow-x:auto">
        <table class="data-table">
          <thead><tr><th>User</th><th>Role</th><th>Status</th><th>Last Login</th><th>Joined</th><th>Actions</th></tr></thead>
          <tbody>
            <?php if (isset($adminUsers) && $adminUsers): ?>
              <?php foreach ($adminUsers as $u): ?>
              <?php $role = (string)($u["role_name"] ?? "admin"); ?>
              <tr>
                <td data-label="User"><div class="cell-user"><div class="cell-ava" style="background:<?php echo Helpers::e(Helpers::bc($u["full_name"] ?? "A")); ?>"><?php echo Helpers::e(Helpers::initls($u["full_name"] ?? "AD")); ?></div><div><span class="cell-name"><?php echo Helpers::e($u["full_name"] ?? "Admin"); ?></span><span class="cell-sub"><?php echo Helpers::e($u["email"] ?? ""); ?></span></div></div></td>
                <td data-label="Role"><span class="badge <?php echo Helpers::e($role === "super_admin" ? "danger" : ($role === "admin" ? "violet" : "info")); ?>"><i class="fas fa-<?php echo Helpers::e($role === "super_admin" ? "crown" : ($role === "admin" ? "shield" : "user")); ?>"></i><?php echo Helpers::e(ucwords(str_replace("_", " ", $role))); ?></span></td>
                <td data-label="Status"><span class="badge <?php echo Helpers::e(($u["status"] ?? "active") === "active" ? "success" : (($u["status"] ?? "") === "suspended" ? "danger" : "warning")); ?>"><?php echo Helpers::e(ucfirst($u["status"] ?? "active")); ?></span></td>
                <td data-label="Last Login" class="mono"><?php echo Helpers::e(Helpers::ta($u["last_login_at"] ?? null)); ?></td>
                <td data-label="Joined" class="mono"><?php echo Helpers::e(date("M j, Y", strtotime($u["created_at"] ?? "now"))); ?></td>
                <td data-label="Actions"><div class="action-btns">
                  <button class="action-btn view" onclick="openModal('admin',<?php echo Helpers::e((int)($u["id"] ?? 0)); ?>)"><i class="fas fa-eye"></i></button>
                  <button class="action-btn edit" onclick="openModal('admin',<?php echo Helpers::e((int)($u["id"] ?? 0)); ?>)"><i class="fas fa-pen"></i></button>
                  <button class="action-btn del" onclick="deleteItem('admin',<?php echo Helpers::e((int)($u["id"] ?? 0)); ?>)"><i class="fas fa-trash"></i></button>
                </div></td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6"><div class="empty-state"><i class="fas fa-users"></i><p>No users found</p></div></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ════════════════════════════════════════════
       PROGRAMMES
  ════════════════════════════════════════════ -->
  <div class="content" id="page-programmes">
    <?php
    $programmes = $dbAvail ? (Database::fetchAll("SELECT title,category,summary,status,start_date,end_date FROM programmes ORDER BY created_at DESC LIMIT 12") ?: []) : [];
    $programmesPublished = count(array_filter($programmes, fn($p) => ($p["status"] ?? "") === "published"));
    $programmesCompleted = count(array_filter($programmes, fn($p) => ($p["status"] ?? "") === "completed"));
    ?>
    <div class="stats-grid">
      <div class="stat-card t1"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-seedling"></i></div><span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i>Running</span></div><div class="stat-value"><?php echo Helpers::e($programmesPublished); ?></div><div class="stat-label">Active Programmes</div></div>
      <div class="stat-card t4"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-check-circle"></i></div><span class="stat-trend up">Completed</span></div><div class="stat-value"><?php echo Helpers::e($programmesCompleted); ?></div><div class="stat-label">Completed</div></div>
      <div class="stat-card t2"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-list"></i></div><span class="stat-trend neutral">All</span></div><div class="stat-value"><?php echo Helpers::e(count($programmes)); ?></div><div class="stat-label">Total Programmes</div></div>
      <div class="stat-card t3"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-hourglass"></i></div><span class="stat-trend neutral">Draft</span></div><div class="stat-value"><?php echo Helpers::e(max(0, count($programmes) - $programmesPublished - $programmesCompleted)); ?></div><div class="stat-label">In Draft</div></div>
    </div>
    <div class="card">
      <div class="section-hd">
        <div><h2>All Programmes</h2><p>Projects and initiatives managed by the organization</p></div>
        <button class="btn-primary"><i class="fas fa-plus"></i> New Programme</button>
      </div>
      <?php if ($programmes): ?>
      <div class="campaign-grid">
        <?php foreach ($programmes as $p): ?>
        <?php $pStatus = strtolower((string)($p["status"] ?? "draft")); ?>
        <div class="campaign-card">
          <div class="cmp-top">
            <div>
              <div class="cmp-name"><?php echo Helpers::e($p["title"] ?? "Untitled"); ?></div>
              <div class="cmp-date"><i class="far fa-calendar" style="margin-right:3px"></i><?php echo Helpers::e($p["start_date"] ? date("M j, Y", strtotime($p["start_date"])) : "TBD"); ?></div>
            </div>
            <span class="badge <?php echo Helpers::e($pStatus === "published" ? "success" : ($pStatus === "completed" ? "info" : ($pStatus === "draft" ? "warning" : "neutral"))); ?>"><?php echo Helpers::e(ucfirst($pStatus)); ?></span>
          </div>
          <?php if ($p["category"]): ?><div style="font-size:.72rem;color:var(--muted);margin-bottom:6px"><?php echo Helpers::e($p["category"]); ?></div><?php endif; ?>
            <?php if ($p["summary"]): ?><div style="font-size:.75rem;color:var(--muted);line-height:1.5"><?php echo Helpers::e(substr($p["summary"], 0, 100)); ?><?php if (strlen($p["summary"] ?? "") > 100): ?>…<?php endif; ?></div><?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div class="empty-state"><i class="fas fa-seedling"></i><p>No programmes created yet</p><div class="sub">Create your first programme to start tracking projects</div></div>
      <?php endif; ?>
    </div>
  </div>

  <!-- ════════════════════════════════════════════
       PARTNERS
  ════════════════════════════════════════════ -->
  <div class="content" id="page-partners">
    <div class="stats-grid cols-3">
      <div class="stat-card t1"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-handshake"></i></div><span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i>Active</span></div><div class="stat-value"><?php echo Helpers::e($activePartners); ?></div><div class="stat-label">Active Partners</div></div>
      <div class="stat-card t4"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-building"></i></div></div><div class="stat-value"><?php echo Helpers::e(count($partnersList)); ?></div><div class="stat-label">Total Organizations</div></div>
      <div class="stat-card t2"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-sack-dollar"></i></div></div><div class="stat-value"><?php echo Helpers::e(Helpers::fmt($totalDonationsAll)); ?></div><div class="stat-label">Total Contributions</div></div>
    </div>
    <div class="card">
      <div class="section-hd">
        <div><h2>Partner Organizations</h2><p>Organizations supporting our mission</p></div>
        <button class="btn-primary"><i class="fas fa-plus"></i> Add Partner</button>
      </div>
      <?php if ($partnersList): ?>
      <div class="partners-grid">
        <?php foreach ($partnersList as $p): ?>
        <div class="partner-card">
          <div class="partner-logo"><i class="fas fa-building-columns"></i></div>
          <div class="partner-info">
            <div class="partner-name"><?php echo Helpers::e($p["name"] ?? "Partner"); ?></div>
            <div class="partner-type"><?php echo Helpers::e(ucfirst((string)($p["partner_type"] ?? "partner"))); ?></div>
            <div class="partner-since"><i class="far fa-calendar" style="margin-right:3px"></i>Added <?php echo Helpers::e(date("M Y", strtotime($p["created_at"] ?? "now"))); ?></div>
          </div>
          <span class="badge success">Active</span>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div class="empty-state"><i class="fas fa-handshake"></i><p>No partners yet</p><div class="sub">Partner organizations will appear here once added</div></div>
      <?php endif; ?>
    </div>
  </div>

  <!-- ════════════════════════════════════════════
       BLOG
  ════════════════════════════════════════════ -->
  <div class="content" id="page-blog">
    <div class="section-hd">
      <div><h2>Blog &amp; News</h2><p>Manage your published content</p></div>
      <button class="btn-primary" onclick="openModal('post')"><i class="fas fa-pen-to-square"></i> New Post</button>
    </div>
    <?php if ($allPosts): ?>
    <div class="blog-grid">
      <?php foreach ($allPosts as $p): ?>
      <?php
        $cats = ["Healthcare","Nutrition","Mental Health","Emergency","Education","Partnerships","General"];
        $cat = $p["category"] ?: $cats[crc32($p["title"] ?? "x") % count($cats)];
        $icons = ["fa-kit-medical","fa-apple-whole","fa-brain","fa-house-flood-water","fa-graduation-cap","fa-handshake","fa-newspaper"];
        $idx = array_search($cat, $cats);
        $icon = $idx !== false ? $icons[$idx] : $icons[6];
        $colors = [["var(--brand-bg)","var(--brand-dim)"],["var(--amber-bg)","#fde68a"],["#ede9fe","#ddd6fe"],["#fee2e2","#fecaca"],["#dbeafe","#bfdbfe"],["#dcfce7","#bbf7d0"],["#f3f4f6","#e5e7eb"]];
        $bg = $colors[$idx !== false ? $idx : 6];
        $pStatus = strtolower((string)($p["status"] ?? "draft"));
      ?>
      <div class="blog-card">
        <div class="blog-thumb" style="background:linear-gradient(135deg,<?php echo Helpers::e($bg[0]); ?>,<?php echo Helpers::e($bg[1]); ?>)"><i class="fas <?php echo Helpers::e($icon); ?>" style="color:<?php echo Helpers::e(Helpers::bc($cat)); ?>;font-size:2.5rem"></i></div>
        <div class="blog-body">
          <div class="blog-tag"><?php echo Helpers::e($cat); ?></div>
          <div class="blog-title"><?php echo Helpers::e($p["title"] ?? "Untitled"); ?></div>
          <div class="blog-meta">
            <span><i class="fas fa-user"></i><?php echo Helpers::e($p["author_name"] ?? "Admin"); ?></span>
            <span><i class="far fa-calendar"></i><?php echo Helpers::e(date("M j, Y", strtotime($p["published_at"] ?? $p["created_at"] ?? "now"))); ?></span>
            <span class="badge <?php echo Helpers::e($pStatus === "published" ? "success" : ($pStatus === "draft" ? "warning" : "neutral")); ?>"><?php echo Helpers::e(ucfirst($pStatus)); ?></span>
            <div class="action-btns" style="margin-left:auto">
              <button class="action-btn edit" onclick="openModal('post',<?php echo Helpers::e((int)($p["id"] ?? 0)); ?>)"><i class="fas fa-pen"></i></button>
              <button class="action-btn del" onclick="deleteItem('post',<?php echo Helpers::e((int)($p["id"] ?? 0)); ?>)"><i class="fas fa-trash"></i></button>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="card">
      <div class="empty-state"><i class="fas fa-newspaper"></i><p>No blog posts yet</p><div class="sub">Create your first post to start sharing news and stories</div></div>
    </div>
    <?php endif; ?>
  </div>

  <!-- ════════════════════════════════════════════
       EVENTS
  ════════════════════════════════════════════ -->
  <div class="content" id="page-events">
    <div class="section-hd">
      <div><h2>Events</h2><p>Manage your organization's events</p></div>
      <button class="btn-primary" onclick="openModal('event')"><i class="fas fa-plus"></i> New Event</button>
    </div>
    <?php
      $evtPublished = count(array_filter($allEvents, fn($e) => ($e["status"] ?? "") === "published"));
      $evtUpcoming = count(array_filter($allEvents, fn($e) => ($e["status"] ?? "") === "published" && strtotime($e["event_start"] ?? "") > time()));
      $evtCompleted = count(array_filter($allEvents, fn($e) => ($e["status"] ?? "") === "completed"));
    ?>
    <div class="stats-grid">
      <div class="stat-card t1"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-calendar"></i></div><span class="stat-trend up">Total</span></div><div class="stat-value"><?php echo Helpers::e(count($allEvents)); ?></div><div class="stat-label">All Events</div></div>
      <div class="stat-card t4"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-check-circle"></i></div><span class="stat-trend up">Published</span></div><div class="stat-value"><?php echo Helpers::e($evtPublished); ?></div><div class="stat-label">Published</div></div>
      <div class="stat-card t2"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-arrow-trend-up"></i></div><span class="stat-trend up">Upcoming</span></div><div class="stat-value"><?php echo Helpers::e($evtUpcoming); ?></div><div class="stat-label">Upcoming</div></div>
      <div class="stat-card t3"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-flag-checkered"></i></div><span class="stat-trend neutral">Done</span></div><div class="stat-value"><?php echo Helpers::e($evtCompleted); ?></div><div class="stat-label">Completed</div></div>
    </div>
    <div class="card">
      <div class="toolbar">
        <div class="search-box"><i class="fas fa-search"></i><input placeholder="Search events…"/></div>
        <button class="filter-btn"><i class="far fa-calendar"></i> Date</button>
        <button class="filter-btn"><i class="fas fa-circle-half-stroke"></i> Status</button>
        <button class="btn-primary ml" onclick="openModal('event')"><i class="fas fa-plus"></i> New Event</button>
      </div>
      <div style="overflow-x:auto">
        <table class="data-table">
          <thead><tr><th>Title</th><th>Venue</th><th>Date</th><th>Organizer</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
            <?php if ($allEvents): ?>
              <?php foreach ($allEvents as $ev): ?>
              <?php $es = strtolower((string)($ev["status"] ?? "draft")); ?>
              <tr>
                <td data-label="Title"><strong><?php echo Helpers::e($ev["title"] ?? "Untitled"); ?></strong></td>
                <td data-label="Venue"><span style="color:var(--muted)"><?php echo Helpers::e($ev["venue"] ?? "—"); ?>, <?php echo Helpers::e($ev["city"] ?? "—"); ?></span></td>
                <td data-label="Date" class="mono"><?php echo Helpers::e($ev["event_start"] ? date("M j, Y", strtotime($ev["event_start"])) : "TBD"); ?></td>
                <td data-label="Organizer" style="color:var(--muted)"><?php echo Helpers::e($ev["organizer"] ?? "Events Desk"); ?></td>
                <td data-label="Status"><span class="badge <?php echo Helpers::e($es === "published" ? "success" : ($es === "completed" ? "info" : ($es === "cancelled" ? "danger" : "warning"))); ?>"><?php echo Helpers::e(ucfirst($es)); ?></span></td>
                <td data-label="Actions"><div class="action-btns">
                  <button class="action-btn view" onclick="openModal('event',<?php echo Helpers::e((int)($ev["id"] ?? 0)); ?>)"><i class="fas fa-eye"></i></button>
                  <button class="action-btn edit" onclick="openModal('event',<?php echo Helpers::e((int)($ev["id"] ?? 0)); ?>)"><i class="fas fa-pen"></i></button>
                  <button class="action-btn del" onclick="deleteItem('event',<?php echo Helpers::e((int)($ev["id"] ?? 0)); ?>)"><i class="fas fa-trash"></i></button>
                </div></td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6"><div class="empty-state"><i class="fas fa-calendar"></i><p>No events yet</p><div class="sub">Create your first event to get started</div></div></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ════════════════════════════════════════════
       GALLERY
  ════════════════════════════════════════════ -->
=======
        <button class="filter-btn"><i class="far fa-calendar"></i> Joined</button>
        <button class="btn-primary ml"><i class="fas fa-user-plus"></i> Add User</button>
      </div>
      <div class="tabs">
        <button class="tab-btn on">All Users</button>
        <button class="tab-btn">Admins</button>
        <button class="tab-btn">Volunteers</button>
        <button class="tab-btn">Donors</button>
        <button class="tab-btn">Staff</button>
      </div>
      <div style="overflow-x:auto">
        <table class="data-table">
          <thead><tr><th>User</th><th>Role</th><th>Location</th><th>Joined</th><th>Last Active</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
            <tr>
              <td><div class="cell-user"><div class="cell-ava" style="background:#0f766e">SA</div><div><span class="cell-name">Super Admin</span><span class="cell-sub">admin@hopeconnect.org</span></div></div></td>
              <td><span class="badge danger"><i class="fas fa-crown"></i>Super Admin</span></td>
              <td><i class="fas fa-location-dot" style="color:var(--soft);margin-right:4px"></i>Lagos, NG</td>
              <td class="mono">Jan 1, 2023</td><td class="mono">Just now</td>
              <td><span class="badge success"><i class="fas fa-circle"></i>Online</span></td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button></div></td>
            </tr>
            <tr>
              <td><div class="cell-user"><div class="cell-ava" style="background:#7c3aed">AK</div><div><span class="cell-name">Aisha Kamara</span><span class="cell-sub">a.kamara@hopeconnect.org</span></div></div></td>
              <td><span class="badge violet"><i class="fas fa-shield"></i>Admin</span></td>
              <td><i class="fas fa-location-dot" style="color:var(--soft);margin-right:4px"></i>Accra, GH</td>
              <td class="mono">Mar 12, 2023</td><td class="mono">2h ago</td>
              <td><span class="badge success"><i class="fas fa-circle"></i>Online</span></td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button><button class="action-btn del"><i class="fas fa-trash"></i></button></div></td>
            </tr>
            <tr>
              <td><div class="cell-user"><div class="cell-ava" style="background:#2563eb">OT</div><div><span class="cell-name">Oluwaseun Taiwo</span><span class="cell-sub">o.taiwo@volunteer.org</span></div></div></td>
              <td><span class="badge info"><i class="fas fa-person-digging"></i>Volunteer</span></td>
              <td><i class="fas fa-location-dot" style="color:var(--soft);margin-right:4px"></i>Abuja, NG</td>
              <td class="mono">Jun 5, 2024</td><td class="mono">1d ago</td>
              <td><span class="badge teal"><i class="fas fa-circle"></i>Active</span></td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button><button class="action-btn del"><i class="fas fa-trash"></i></button></div></td>
            </tr>
            <tr>
              <td><div class="cell-user"><div class="cell-ava" style="background:#d97706">MB</div><div><span class="cell-name">Miriam Boateng</span><span class="cell-sub">m.boateng@email.com</span></div></div></td>
              <td><span class="badge neutral"><i class="fas fa-heart"></i>Donor</span></td>
              <td><i class="fas fa-location-dot" style="color:var(--soft);margin-right:4px"></i>Kumasi, GH</td>
              <td class="mono">Sep 18, 2024</td><td class="mono">5d ago</td>
              <td><span class="badge warning"><i class="fas fa-circle"></i>Away</span></td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button><button class="action-btn del"><i class="fas fa-trash"></i></button></div></td>
            </tr>
            <tr>
              <td><div class="cell-user"><div class="cell-ava" style="background:#dc2626">XY</div><div><span class="cell-name">Xavier Yeboah</span><span class="cell-sub">x.yeboah@staff.org</span></div></div></td>
              <td><span class="badge teal"><i class="fas fa-id-badge"></i>Staff</span></td>
              <td><i class="fas fa-location-dot" style="color:var(--soft);margin-right:4px"></i>Nairobi, KE</td>
              <td class="mono">Feb 2, 2025</td><td class="mono">3w ago</td>
              <td><span class="badge danger"><i class="fas fa-ban"></i>Suspended</span></td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button><button class="action-btn del"><i class="fas fa-trash"></i></button></div></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="pagination">
        <span class="page-info">Showing 1–5 of 2,481 users</span>
        <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
        <button class="page-btn on">1</button><button class="page-btn">2</button><button class="page-btn">3</button>
        <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════
       PATIENTS
  ══════════════════════════════ -->
  <div class="content" id="page-patients">
    <div class="stats-grid">
      <div class="stat-card t1"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-hospital-user"></i></div><span class="stat-trend neutral">Active</span></div><div class="stat-value">312</div><div class="stat-label">Total Active Patients</div></div>
      <div class="stat-card t3"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-triangle-exclamation"></i></div><span class="stat-trend down">Urgent</span></div><div class="stat-value">6</div><div class="stat-label">Critical Cases</div></div>
      <div class="stat-card t4"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-circle-check"></i></div><span class="stat-trend up">This month</span></div><div class="stat-value">97</div><div class="stat-label">Discharged (Month)</div></div>
      <div class="stat-card t2"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-clipboard-list"></i></div><span class="stat-trend neutral">Queued</span></div><div class="stat-value">42</div><div class="stat-label">Awaiting Admission</div></div>
    </div>
    <div class="card">
      <div class="toolbar">
        <div class="search-box"><i class="fas fa-search"></i><input placeholder="Search patients…"/></div>
        <button class="filter-btn"><i class="fas fa-bed"></i> Ward</button>
        <button class="filter-btn"><i class="fas fa-stethoscope"></i> Category</button>
        <button class="filter-btn"><i class="fas fa-circle-half-stroke"></i> Status</button>
        <button class="btn-primary ml"><i class="fas fa-user-plus"></i> Admit Patient</button>
      </div>
      <div style="overflow-x:auto">
        <table class="data-table">
          <thead><tr><th>Patient ID</th><th>Name</th><th>Age/Sex</th><th>Category</th><th>Ward</th><th>Admitted</th><th>Status</th><th>Assigned To</th><th>Actions</th></tr></thead>
          <tbody>
            <tr>
              <td><span class="mono">#PT-0042</span></td>
              <td><div class="cell-user"><div class="cell-ava" style="background:#0f766e">CA</div><div><span class="cell-name">Chidinma Agu</span></div></div></td>
              <td>28 / F</td><td><span class="badge teal"><i class="fas fa-kit-medical"></i>Medical Aid</span></td>
              <td>Ward B</td><td class="mono">May 1, 2025</td>
              <td><span class="badge success">Stable</span></td>
              <td style="color:var(--muted)">Dr. Obi</td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button></div></td>
            </tr>
            <tr>
              <td><span class="mono">#PT-0067</span></td>
              <td><div class="cell-user"><div class="cell-ava" style="background:#dc2626">MI</div><div><span class="cell-name">Musa Ibrahim</span></div></div></td>
              <td>45 / M</td><td><span class="badge danger"><i class="fas fa-siren"></i>Emergency</span></td>
              <td>ICU</td><td class="mono">May 7, 2025</td>
              <td><span class="badge danger">Critical</span></td>
              <td style="color:var(--muted)">Dr. Afolabi</td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button></div></td>
            </tr>
            <tr>
              <td><span class="mono">#PT-0091</span></td>
              <td><div class="cell-user"><div class="cell-ava" style="background:#fbbf24">GE</div><div><span class="cell-name">Grace Eze</span></div></div></td>
              <td>12 / F</td><td><span class="badge warning"><i class="fas fa-apple-whole"></i>Nutrition</span></td>
              <td>Ward A</td><td class="mono">Apr 22, 2025</td>
              <td><span class="badge warning">Monitoring</span></td>
              <td style="color:var(--muted)">Nurse Bello</td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button></div></td>
            </tr>
            <tr>
              <td><span class="mono">#PT-0112</span></td>
              <td><div class="cell-user"><div class="cell-ava" style="background:#2563eb">TB</div><div><span class="cell-name">Tunde Bakare</span></div></div></td>
              <td>33 / M</td><td><span class="badge info"><i class="fas fa-brain"></i>Mental Health</span></td>
              <td>Clinic C</td><td class="mono">Mar 15, 2025</td>
              <td><span class="badge info">In Session</span></td>
              <td style="color:var(--muted)">Dr. Uche</td>
              <td><div class="action-btns"><button class="action-btn view"><i class="fas fa-eye"></i></button><button class="action-btn edit"><i class="fas fa-pen"></i></button></div></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="pagination">
        <span class="page-info">Showing 1–4 of 312 patients</span>
        <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
        <button class="page-btn on">1</button><button class="page-btn">2</button>
        <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════
       PARTNERS
  ══════════════════════════════ -->
  <div class="content" id="page-partners">
    <div class="stats-grid" style="grid-template-columns:repeat(3,1fr)">
      <div class="stat-card t1"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-handshake"></i></div><span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i>+3</span></div><div class="stat-value">47</div><div class="stat-label">Active Partners</div></div>
      <div class="stat-card t4"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-earth-africa"></i></div></div><div class="stat-value">18</div><div class="stat-label">Countries Covered</div></div>
      <div class="stat-card t2"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-sack-dollar"></i></div></div><div class="stat-value">$1.2M</div><div class="stat-label">Partner Contributions</div></div>
    </div>
    <div class="card">
      <div class="section-hd">
        <div><h2>All Partners</h2><p>Organizations supporting HopeConnect</p></div>
        <button class="btn-primary"><i class="fas fa-plus"></i> Add Partner</button>
      </div>
      <div class="partners-grid">
        <div class="partner-card">
          <div class="partner-logo"><i class="fas fa-building-columns"></i></div>
          <div class="partner-info"><div class="partner-name">UNICEF West Africa</div><div class="partner-type">International Organization</div><div class="partner-since"><i class="far fa-calendar" style="margin-right:3px"></i>Since 2021 · $480,000 contributed</div></div>
          <span class="badge success">Active</span>
        </div>
        <div class="partner-card">
          <div class="partner-logo"><i class="fas fa-hospital"></i></div>
          <div class="partner-info"><div class="partner-name">Lagos State Hospital</div><div class="partner-type">Healthcare Facility</div><div class="partner-since"><i class="far fa-calendar" style="margin-right:3px"></i>Since 2022 · Medical resources</div></div>
          <span class="badge success">Active</span>
        </div>
        <div class="partner-card">
          <div class="partner-logo"><i class="fas fa-landmark"></i></div>
          <div class="partner-info"><div class="partner-name">First Bank Foundation</div><div class="partner-type">Financial Institution</div><div class="partner-since"><i class="far fa-calendar" style="margin-right:3px"></i>Since 2023 · $250,000/yr</div></div>
          <span class="badge success">Active</span>
        </div>
        <div class="partner-card">
          <div class="partner-logo"><i class="fas fa-leaf"></i></div>
          <div class="partner-info"><div class="partner-name">Green Earth NGO</div><div class="partner-type">Environmental Organization</div><div class="partner-since"><i class="far fa-calendar" style="margin-right:3px"></i>Since 2024 · Joint programs</div></div>
          <span class="badge teal">Active</span>
        </div>
        <div class="partner-card">
          <div class="partner-logo"><i class="fas fa-graduation-cap"></i></div>
          <div class="partner-info"><div class="partner-name">EduAfrica Trust</div><div class="partner-type">Education Nonprofit</div><div class="partner-since"><i class="far fa-calendar" style="margin-right:3px"></i>Since 2023 · Scholarship fund</div></div>
          <span class="badge warning">Renewal</span>
        </div>
        <div class="partner-card">
          <div class="partner-logo"><i class="fas fa-plane"></i></div>
          <div class="partner-info"><div class="partner-name">Air Peace Foundation</div><div class="partner-type">Corporate CSR</div><div class="partner-since"><i class="far fa-calendar" style="margin-right:3px"></i>Since 2024 · Logistics support</div></div>
          <span class="badge success">Active</span>
        </div>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════
       BLOG
  ══════════════════════════════ -->
  <div class="content" id="page-blog">
    <div class="section-hd">
      <div><h2>Blog &amp; News Management</h2><p>Create, manage and publish posts</p></div>
      <button class="btn-primary"><i class="fas fa-pen-to-square"></i> New Post</button>
    </div>
    <div class="tabs">
      <button class="tab-btn on">All Posts</button>
      <button class="tab-btn">Published</button>
      <button class="tab-btn">Drafts</button>
      <button class="tab-btn">Scheduled</button>
      <button class="tab-btn">Archived</button>
    </div>
    <div class="blog-grid">
      <div class="blog-card">
        <div class="blog-thumb" style="background:linear-gradient(135deg,var(--brand-bg),var(--brand-dim))"><i class="fas fa-earth-africa" style="color:var(--brand);font-size:2.5rem"></i></div>
        <div class="blog-body">
          <div class="blog-tag"><i class="fas fa-kit-medical" style="margin-right:3px"></i>Healthcare</div>
          <div class="blog-title">How We Treated 200 Children in Remote Communities This Quarter</div>
          <div class="blog-excerpt">Our medical teams reached the most isolated villages, providing essential care to over 200 children...</div>
          <div class="blog-meta">
            <span><i class="fas fa-user"></i>Admin</span>
            <span><i class="far fa-calendar"></i>May 6, 2025</span>
            <span><i class="fas fa-eye"></i>1.2k</span>
            <span class="badge success" style="margin-left:auto">Published</span>
          </div>
        </div>
      </div>
      <div class="blog-card">
        <div class="blog-thumb" style="background:linear-gradient(135deg,var(--amber-bg),#fde68a)"><i class="fas fa-apple-whole" style="color:var(--amber);font-size:2.5rem"></i></div>
        <div class="blog-body">
          <div class="blog-tag"><i class="fas fa-apple-whole" style="margin-right:3px"></i>Nutrition</div>
          <div class="blog-title">Launching Our New Child Nutrition Program in 5 States</div>
          <div class="blog-excerpt">Malnutrition affects millions of children across West Africa. Here's how our new program addresses root causes...</div>
          <div class="blog-meta">
            <span><i class="fas fa-user"></i>Aisha K.</span>
            <span><i class="far fa-calendar"></i>May 3, 2025</span>
            <span><i class="fas fa-eye"></i>845</span>
            <span class="badge success" style="margin-left:auto">Published</span>
          </div>
        </div>
      </div>
      <div class="blog-card">
        <div class="blog-thumb" style="background:linear-gradient(135deg,#ede9fe,#ddd6fe)"><i class="fas fa-brain" style="color:#7c3aed;font-size:2.5rem"></i></div>
        <div class="blog-body">
          <div class="blog-tag"><i class="fas fa-brain" style="margin-right:3px"></i>Mental Health</div>
          <div class="blog-title">Breaking the Stigma: Mental Health Outreach in Urban Areas</div>
          <div class="blog-excerpt">Our counselors are working to bring mental health support to communities where it was once taboo...</div>
          <div class="blog-meta">
            <span><i class="fas fa-user"></i>Dr. Uche</span>
            <span><i class="far fa-calendar"></i>Apr 28, 2025</span>
            <span><i class="fas fa-eye"></i>622</span>
            <span class="badge warning" style="margin-left:auto">Draft</span>
          </div>
        </div>
      </div>
      <div class="blog-card">
        <div class="blog-thumb" style="background:linear-gradient(135deg,#fee2e2,#fecaca)"><i class="fas fa-house-flood-water" style="color:#dc2626;font-size:2.5rem"></i></div>
        <div class="blog-body">
          <div class="blog-tag"><i class="fas fa-siren" style="margin-right:3px"></i>Emergency</div>
          <div class="blog-title">Flood Response 2025: How Your Donations Made a Difference</div>
          <div class="blog-excerpt">When floods devastated three communities in April, your generosity enabled us to respond within hours...</div>
          <div class="blog-meta">
            <span><i class="fas fa-user"></i>Admin</span>
            <span><i class="far fa-calendar"></i>Apr 20, 2025</span>
            <span><i class="fas fa-eye"></i>3.4k</span>
            <span class="badge success" style="margin-left:auto">Published</span>
          </div>
        </div>
      </div>
      <div class="blog-card">
        <div class="blog-thumb" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe)"><i class="fas fa-graduation-cap" style="color:#2563eb;font-size:2.5rem"></i></div>
        <div class="blog-body">
          <div class="blog-tag"><i class="fas fa-graduation-cap" style="margin-right:3px"></i>Education</div>
          <div class="blog-title">50 Scholarships Awarded — Meet This Year's Recipients</div>
          <div class="blog-excerpt">This year we surpassed our scholarship target, awarding 50 full bursaries to exceptional students...</div>
          <div class="blog-meta">
            <span><i class="fas fa-user"></i>Olumide S.</span>
            <span><i class="far fa-calendar"></i>May 10 (Sched.)</span>
            <span class="badge info" style="margin-left:auto">Scheduled</span>
          </div>
        </div>
      </div>
      <div class="blog-card">
        <div class="blog-thumb" style="background:linear-gradient(135deg,#dcfce7,#bbf7d0)"><i class="fas fa-handshake" style="color:#059669;font-size:2.5rem"></i></div>
        <div class="blog-body">
          <div class="blog-tag"><i class="fas fa-handshake" style="margin-right:3px"></i>Partnerships</div>
          <div class="blog-title">New Partnership with UNICEF West Africa Announced</div>
          <div class="blog-excerpt">We are proud to announce a landmark partnership that will expand our reach to 3 new countries...</div>
          <div class="blog-meta">
            <span><i class="fas fa-user"></i>Admin</span>
            <span><i class="far fa-calendar"></i>Apr 14, 2025</span>
            <span><i class="fas fa-eye"></i>2.1k</span>
            <span class="badge success" style="margin-left:auto">Published</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ══════════════════════════════
       GALLERY
  ══════════════════════════════ -->
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
  <div class="content" id="page-gallery">
    <div class="section-hd">
      <div><h2>Media Gallery</h2><p>Photos and videos from our programs</p></div>
      <div style="display:flex;gap:8px">
<<<<<<< HEAD
        <button class="btn-primary" onclick="openModal('gallery')"><i class="fas fa-upload"></i> Upload Media</button>
      </div>
    </div>
    <?php if ($allGalleryItems): ?>
    <div class="gallery-grid">
      <?php foreach ($allGalleryItems as $item): ?>
      <?php
        $gColors = [["#0f766e","#14b8a6"],["#d97706","#fbbf24"],["#1d4ed8","#3b82f6"],["#7c3aed","#a78bfa"],["#dc2626","#f87171"],["#059669","#34d399"],["#0e7490","#22d3ee"],["#92400e","#fbbf24"]];
        $gc = $gColors[crc32($item["title"] ?? "x") % count($gColors)];
        $gIcon = $item["media_type"] === "video" ? "fa-video" : "fa-image";
      ?>
      <div class="gallery-item">
        <div class="g-thumb" style="background:linear-gradient(135deg,<?php echo Helpers::e($gc[0]); ?>,<?php echo Helpers::e($gc[1]); ?>)"><i class="fas <?php echo Helpers::e($gIcon); ?>" style="color:rgba(255,255,255,.8)"></i></div>
        <div class="g-overlay">
          <div class="g-actions">
            <button class="g-btn" onclick="deleteItem('gallery',<?php echo Helpers::e((int)($item["id"] ?? 0)); ?>)"><i class="fas fa-trash"></i></button>
          </div>
          <div class="g-caption"><?php echo Helpers::e($item["title"] ?? "Untitled"); ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="card">
      <div class="empty-state"><i class="fas fa-images"></i><p>Gallery is empty</p><div class="sub">Upload photos and videos to showcase your programs</div></div>
    </div>
    <?php endif; ?>
  </div>

  <!-- ════════════════════════════════════════════
       SECURITY
  ════════════════════════════════════════════ -->
  <div class="content" id="page-security">
    <div class="stats-grid">
      <div class="stat-card t1"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-server"></i></div><span class="stat-trend up">Healthy</span></div><div class="stat-value">99.8%</div><div class="stat-label">System Uptime</div></div>
      <div class="stat-card t4"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-shield-halved"></i></div><span class="stat-trend up">Secure</span></div><div class="stat-value"><?php echo Helpers::e(count($recentLogins)); ?></div><div class="stat-label">Recent Logins</div></div>
      <div class="stat-card t2"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-key"></i></div></div><div class="stat-value"><?php echo Helpers::e($totalAdmins); ?></div><div class="stat-label">Admin Accounts</div></div>
      <div class="stat-card t5"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-clock"></i></div></div><div class="stat-value"><?php echo Helpers::e(date("h:i A")); ?></div><div class="stat-label">Server Time</div></div>
=======
        <button class="btn-secondary"><i class="fas fa-folder-plus"></i> New Album</button>
        <button class="btn-primary"><i class="fas fa-upload"></i> Upload Media</button>
      </div>
    </div>
    <div class="tabs">
      <button class="tab-btn on">All Media</button>
      <button class="tab-btn">Healthcare</button>
      <button class="tab-btn">Nutrition</button>
      <button class="tab-btn">Events</button>
      <button class="tab-btn">Videos</button>
    </div>
    <div class="gallery-grid">
      <div class="gallery-item">
        <div class="g-thumb" style="background:linear-gradient(135deg,#0f766e,#14b8a6)"><i class="fas fa-hospital" style="color:rgba(255,255,255,.8)"></i></div>
        <div class="g-overlay">
          <div class="g-actions"><button class="g-btn"><i class="fas fa-pen"></i></button><button class="g-btn"><i class="fas fa-trash"></i></button></div>
          <div class="g-caption">Medical Camp — March 2025</div>
        </div>
      </div>
      <div class="gallery-item">
        <div class="g-thumb" style="background:linear-gradient(135deg,#d97706,#fbbf24)"><i class="fas fa-apple-whole" style="color:rgba(255,255,255,.8)"></i></div>
        <div class="g-overlay">
          <div class="g-actions"><button class="g-btn"><i class="fas fa-pen"></i></button><button class="g-btn"><i class="fas fa-trash"></i></button></div>
          <div class="g-caption">Nutrition Drive — Kano</div>
        </div>
      </div>
      <div class="gallery-item">
        <div class="g-thumb" style="background:linear-gradient(135deg,#1d4ed8,#3b82f6)"><i class="fas fa-graduation-cap" style="color:rgba(255,255,255,.8)"></i></div>
        <div class="g-overlay">
          <div class="g-actions"><button class="g-btn"><i class="fas fa-pen"></i></button><button class="g-btn"><i class="fas fa-trash"></i></button></div>
          <div class="g-caption">Scholarship Ceremony 2025</div>
        </div>
      </div>
      <div class="gallery-item">
        <div class="g-thumb" style="background:linear-gradient(135deg,#7c3aed,#a78bfa)"><i class="fas fa-handshake" style="color:rgba(255,255,255,.8)"></i></div>
        <div class="g-overlay">
          <div class="g-actions"><button class="g-btn"><i class="fas fa-pen"></i></button><button class="g-btn"><i class="fas fa-trash"></i></button></div>
          <div class="g-caption">UNICEF Partnership Signing</div>
        </div>
      </div>
      <div class="gallery-item">
        <div class="g-thumb" style="background:linear-gradient(135deg,#dc2626,#f87171)"><i class="fas fa-truck-medical" style="color:rgba(255,255,255,.8)"></i></div>
        <div class="g-overlay">
          <div class="g-actions"><button class="g-btn"><i class="fas fa-pen"></i></button><button class="g-btn"><i class="fas fa-trash"></i></button></div>
          <div class="g-caption">Flood Response — April 2025</div>
        </div>
      </div>
      <div class="gallery-item">
        <div class="g-thumb" style="background:linear-gradient(135deg,#059669,#34d399)"><i class="fas fa-seedling" style="color:rgba(255,255,255,.8)"></i></div>
        <div class="g-overlay">
          <div class="g-actions"><button class="g-btn"><i class="fas fa-pen"></i></button><button class="g-btn"><i class="fas fa-trash"></i></button></div>
          <div class="g-caption">Community Garden Project</div>
        </div>
      </div>
      <div class="gallery-item">
        <div class="g-thumb" style="background:linear-gradient(135deg,#0e7490,#22d3ee)"><i class="fas fa-droplet" style="color:rgba(255,255,255,.8)"></i></div>
        <div class="g-overlay">
          <div class="g-actions"><button class="g-btn"><i class="fas fa-pen"></i></button><button class="g-btn"><i class="fas fa-trash"></i></button></div>
          <div class="g-caption">Clean Water Initiative</div>
        </div>
      </div>
      <div class="gallery-item">
        <div class="g-thumb" style="background:linear-gradient(135deg,#92400e,#fbbf24)"><i class="fas fa-star" style="color:rgba(255,255,255,.8)"></i></div>
        <div class="g-overlay">
          <div class="g-actions"><button class="g-btn"><i class="fas fa-pen"></i></button><button class="g-btn"><i class="fas fa-trash"></i></button></div>
          <div class="g-caption">Annual Gala 2024</div>
        </div>
      </div>
    </div>
    <div class="pagination" style="margin-top:18px">
      <span class="page-info">Showing 1–8 of 246 media files</span>
      <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
      <button class="page-btn on">1</button><button class="page-btn">2</button><button class="page-btn">3</button>
      <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
    </div>
  </div>

  <!-- ══════════════════════════════
       SECURITY
  ══════════════════════════════ -->
  <div class="content" id="page-security">
    <div class="stats-grid">
      <div class="stat-card t1"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-server"></i></div><span class="stat-trend up">Healthy</span></div><div class="stat-value">99.8%</div><div class="stat-label">System Uptime</div></div>
      <div class="stat-card t3"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-bell"></i></div><span class="stat-trend down">Review needed</span></div><div class="stat-value">3</div><div class="stat-label">Active Alerts</div></div>
      <div class="stat-card t4"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-shield-halved"></i></div><span class="stat-trend up">This month</span></div><div class="stat-value">1,248</div><div class="stat-label">Blocked Attempts</div></div>
      <div class="stat-card t2"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-key"></i></div></div><div class="stat-value">18</div><div class="stat-label">Active Sessions</div></div>
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
    </div>
    <div class="security-grid">
      <div class="card">
        <div class="card-hd">
<<<<<<< HEAD
          <div class="card-hd-left"><div class="card-title"><i class="fas fa-history" style="color:var(--brand);margin-right:6px"></i>Admin Login Activity</div></div>
          <span class="badge success"><i class="fas fa-circle-check"></i>Monitored</span>
        </div>
        <?php if ($recentLogins): ?>
        <div class="activity-list">
          <?php foreach ($recentLogins as $i => $lg): ?>
          <div class="act-row">
            <div class="act-icon <?php echo Helpers::e($lg["last_login_at"] ? "login" : "warn"); ?>"><i class="fas fa-<?php echo Helpers::e($lg["last_login_at"] ? "check" : "eye"); ?>"></i></div>
            <div class="act-body">
              <span class="act-title"><?php echo Helpers::e($lg["full_name"] ?? "Unknown"); ?></span>
              <span class="act-desc"><?php echo Helpers::e($lg["email"] ?? ""); ?> · Status: <?php echo Helpers::e(ucfirst($lg["status"] ?? "active")); ?></span>
            </div>
            <div class="act-time"><?php echo Helpers::e($lg["last_login_at"] ? Helpers::ta($lg["last_login_at"]) : "Never"); ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state"><i class="fas fa-shield"></i><p>No login records</p></div>
        <?php endif; ?>
=======
          <div class="card-hd-left"><div class="card-title"><i class="fas fa-bell" style="color:var(--rose);margin-right:6px"></i>Security Alerts</div></div>
          <span class="badge danger">3 Active</span>
        </div>
        <div class="activity-list">
          <div class="act-row">
            <div class="act-icon danger"><i class="fas fa-triangle-exclamation"></i></div>
            <div class="act-body"><span class="act-title">Multiple Failed Login Attempts</span><span class="act-desc">IP: 197.210.84.12 — 14 attempts in 5 minutes</span></div>
            <div class="act-time">2m ago</div>
          </div>
          <div class="act-row">
            <div class="act-icon warn"><i class="fas fa-eye"></i></div>
            <div class="act-body"><span class="act-title">Unusual Admin Access</span><span class="act-desc">Admin accessed from new device in Kenya</span></div>
            <div class="act-time">1h ago</div>
          </div>
          <div class="act-row">
            <div class="act-icon warn"><i class="fas fa-gauge-high"></i></div>
            <div class="act-body"><span class="act-title">API Rate Limit Exceeded</span><span class="act-desc">Donations API — 500 req/min exceeded</span></div>
            <div class="act-time">3h ago</div>
          </div>
          <div class="act-row">
            <div class="act-icon info"><i class="fas fa-certificate"></i></div>
            <div class="act-body"><span class="act-title">SSL Certificate Renewal</span><span class="act-desc">Certificate expires in 14 days — auto-renew on</span></div>
            <div class="act-time">1d ago</div>
          </div>
          <div class="act-row">
            <div class="act-icon login"><i class="fas fa-fire-flame-simple"></i></div>
            <div class="act-body"><span class="act-title">Firewall Rules Updated</span><span class="act-desc">IP blocklist refreshed — 48 IPs added</span></div>
            <div class="act-time">2d ago</div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fas fa-key" style="color:var(--amber);margin-right:6px"></i>Recent Login Activity</div>
        </div>
        <div class="activity-list">
          <div class="act-row">
            <div class="act-icon login"><i class="fas fa-check"></i></div>
            <div class="act-body"><span class="act-title">Super Admin — Lagos, NG</span><span class="act-desc"><i class="fab fa-chrome" style="margin-right:3px"></i>Chrome · Windows · 197.210.10.4</span></div>
            <div class="act-time">Just now</div>
          </div>
          <div class="act-row">
            <div class="act-icon login"><i class="fas fa-check"></i></div>
            <div class="act-body"><span class="act-title">Aisha Kamara — Accra, GH</span><span class="act-desc"><i class="fab fa-safari" style="margin-right:3px"></i>Safari · macOS · 154.68.22.1</span></div>
            <div class="act-time">2h ago</div>
          </div>
          <div class="act-row">
            <div class="act-icon danger"><i class="fas fa-xmark"></i></div>
            <div class="act-body"><span class="act-title">Unknown User — Failed Login</span><span class="act-desc"><i class="fab fa-firefox" style="margin-right:3px"></i>Firefox · Linux · 185.220.101.4</span></div>
            <div class="act-time">4h ago</div>
          </div>
          <div class="act-row">
            <div class="act-icon login"><i class="fas fa-check"></i></div>
            <div class="act-body"><span class="act-title">Olumide Taiwo — Abuja, NG</span><span class="act-desc"><i class="fab fa-android" style="margin-right:3px"></i>Chrome · Android · 105.112.4.21</span></div>
            <div class="act-time">1d ago</div>
          </div>
        </div>
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
      </div>

      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fas fa-display" style="color:var(--brand);margin-right:6px"></i>System Health</div>
          <span class="badge success"><i class="fas fa-circle-check"></i>All Systems Go</span>
        </div>
        <div class="sys-grid">
<<<<<<< HEAD
          <div class="sys-stat"><div class="sys-val"><?php echo Helpers::e(round(memory_get_usage(true) / 1024 / 1024, 1)); ?> MB</div><div class="sys-lbl">PHP Memory</div></div>
          <div class="sys-stat"><div class="sys-val"><?php echo Helpers::e(phpversion()); ?></div><div class="sys-lbl">PHP Version</div></div>
          <div class="sys-stat"><div class="sys-val"><?php echo Helpers::e($dbAvail ? "Connected" : "Offline"); ?></div><div class="sys-lbl">Database</div></div>
          <div class="sys-stat"><div class="sys-val">v3.5.0</div><div class="sys-lbl">App Version</div></div>
        </div>
        <div style="margin-top:16px;padding:13px;background:var(--brand-bg);border-radius:9px;border:1px solid var(--brand-dim)">
          <div style="font-size:.8rem;font-weight:700;color:var(--brand);margin-bottom:3px"><i class="fas fa-lock" style="margin-right:5px"></i>Security Status</div>
          <div style="font-size:.75rem;color:var(--mid)">All admin accounts are active. <?php echo Helpers::e($totalAdmins); ?> total administrators.</div>
=======
          <div class="sys-stat"><div class="sys-val">62%</div><div class="sys-lbl">CPU Usage</div></div>
          <div class="sys-stat"><div class="sys-val">4.2 GB</div><div class="sys-lbl">RAM (of 8 GB)</div></div>
          <div class="sys-stat"><div class="sys-val">248 GB</div><div class="sys-lbl">Storage Used</div></div>
          <div class="sys-stat"><div class="sys-val">24ms</div><div class="sys-lbl">Avg Response</div></div>
          <div class="sys-stat"><div class="sys-val">TLS 1.3</div><div class="sys-lbl">Encryption</div></div>
          <div class="sys-stat"><div class="sys-val">v3.4.1</div><div class="sys-lbl">App Version</div></div>
        </div>
        <div style="margin-top:16px;padding:13px;background:var(--brand-bg);border-radius:9px;border:1px solid var(--brand-dim)">
          <div style="font-size:.8rem;font-weight:700;color:var(--brand);margin-bottom:3px"><i class="fas fa-lock" style="margin-right:5px"></i>2-Factor Authentication</div>
          <div style="font-size:.75rem;color:var(--mid)">Enabled for all admin accounts. Last audit: Apr 30, 2025.</div>
        </div>
      </div>

      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fas fa-shield-halved" style="color:var(--violet);margin-right:6px"></i>Roles &amp; Permissions</div>
          <button class="btn-secondary"><i class="fas fa-pen"></i> Edit Roles</button>
        </div>
        <div style="overflow-x:auto">
          <table class="data-table" style="font-size:.76rem">
            <thead><tr><th>Role</th><th>Users</th><th>Donations</th><th>Patients</th><th>Security</th></tr></thead>
            <tbody>
              <tr><td><span class="badge danger">Super Admin</span></td><td><i class="fas fa-check" style="color:var(--brand)"></i> Full</td><td><i class="fas fa-check" style="color:var(--brand)"></i> Full</td><td><i class="fas fa-check" style="color:var(--brand)"></i> Full</td><td><i class="fas fa-check" style="color:var(--brand)"></i> Full</td></tr>
              <tr><td><span class="badge violet">Admin</span></td><td><i class="fas fa-check" style="color:var(--brand)"></i> Full</td><td><i class="fas fa-check" style="color:var(--brand)"></i> Full</td><td><i class="fas fa-check" style="color:var(--brand)"></i> Full</td><td><i class="fas fa-eye" style="color:var(--blue)"></i> View</td></tr>
              <tr><td><span class="badge info">Staff</span></td><td><i class="fas fa-eye" style="color:var(--blue)"></i> View</td><td><i class="fas fa-eye" style="color:var(--blue)"></i> View</td><td><i class="fas fa-check" style="color:var(--brand)"></i> Full</td><td><i class="fas fa-xmark" style="color:var(--soft)"></i> None</td></tr>
              <tr><td><span class="badge neutral">Volunteer</span></td><td><i class="fas fa-xmark" style="color:var(--soft)"></i> None</td><td><i class="fas fa-xmark" style="color:var(--soft)"></i> None</td><td><i class="fas fa-eye" style="color:var(--blue)"></i> View</td><td><i class="fas fa-xmark" style="color:var(--soft)"></i> None</td></tr>
              <tr><td><span class="badge teal">Donor</span></td><td><i class="fas fa-xmark" style="color:var(--soft)"></i> None</td><td><i class="fas fa-rotate" style="color:var(--amber)"></i> Own</td><td><i class="fas fa-xmark" style="color:var(--soft)"></i> None</td><td><i class="fas fa-xmark" style="color:var(--soft)"></i> None</td></tr>
            </tbody>
          </table>
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
        </div>
      </div>
    </div>
  </div>

<<<<<<< HEAD
  <!-- ════════════════════════════════════════════
       SETTINGS
  ════════════════════════════════════════════ -->
=======
  <!-- ══════════════════════════════
       SETTINGS
  ══════════════════════════════ -->
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
  <div class="content" id="page-settings">
    <div class="two-col">
      <div style="display:flex;flex-direction:column;gap:18px">
        <div class="card">
          <div class="card-hd">
            <div class="card-hd-left"><div class="card-title"><i class="fas fa-building" style="margin-right:6px;color:var(--muted)"></i>Organization Profile</div></div>
            <button class="btn-primary"><i class="fas fa-floppy-disk"></i> Save</button>
          </div>
<<<<<<< HEAD
          <div class="form-field">
            <label class="form-label">Organization Name</label>
            <input class="form-input" value="<?php echo Helpers::e($settings["site_name"] ?? "HopeConnect NGO"); ?>"/>
          </div>
          <div class="form-field">
            <label class="form-label">Contact Email</label>
            <input class="form-input" value="<?php echo Helpers::e($settings["contact_email"] ?? $adminEmail); ?>"/>
          </div>
          <div class="form-field">
            <label class="form-label">Phone</label>
            <input class="form-input" value="<?php echo Helpers::e($settings["contact_phone"] ?? "+234 800 000 0000"); ?>"/>
          </div>
=======
          <div class="form-field"><label class="form-label">Organization Name</label><input class="form-input" value="HopeConnect NGO"/></div>
          <div class="form-field"><label class="form-label">Contact Email</label><input class="form-input" value="info@hopeconnect.org"/></div>
          <div class="form-field"><label class="form-label">Phone</label><input class="form-input" value="+234 800 000 0000"/></div>
          <div class="form-field"><label class="form-label">Headquarters</label><input class="form-input" value="14 Victoria Island, Lagos, Nigeria"/></div>
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
        </div>
        <div class="card">
          <div class="card-hd"><div class="card-title"><i class="fas fa-credit-card" style="margin-right:6px;color:var(--muted)"></i>Payment Gateways</div></div>
          <div class="gateway-row">
            <div class="gw-left"><div class="gw-icon"><i class="fas fa-bolt" style="color:var(--brand)"></i></div><div><div class="gw-name">Paystack</div><div class="gw-desc">West Africa payments</div></div></div>
<<<<<<< HEAD
            <span class="badge <?php echo Helpers::e(($settings["paystack_public_key"] ?? "") ? "success" : "warning"); ?>"><i class="fas fa-<?php echo Helpers::e(($settings["paystack_public_key"] ?? "") ? "plug" : "clock"); ?>"></i><?php echo Helpers::e(($settings["paystack_public_key"] ?? "") ? "Connected" : "Not configured"); ?></span>
          </div>
          <div class="gateway-row">
            <div class="gw-left"><div class="gw-icon"><i class="fab fa-stripe-s" style="color:#6772e5"></i></div><div><div class="gw-name">Stripe</div><div class="gw-desc">International cards</div></div></div>
            <span class="badge <?php echo Helpers::e(($settings["stripe_public_key"] ?? "") ? "success" : "warning"); ?>"><i class="fas fa-<?php echo Helpers::e(($settings["stripe_public_key"] ?? "") ? "plug" : "clock"); ?>"></i><?php echo Helpers::e(($settings["stripe_public_key"] ?? "") ? "Connected" : "Not configured"); ?></span>
=======
            <span class="badge success"><i class="fas fa-plug"></i>Connected</span>
          </div>
          <div class="gateway-row">
            <div class="gw-left"><div class="gw-icon"><i class="fab fa-stripe-s" style="color:#6772e5"></i></div><div><div class="gw-name">Stripe</div><div class="gw-desc">International cards</div></div></div>
            <span class="badge success"><i class="fas fa-plug"></i>Connected</span>
          </div>
          <div class="gateway-row">
            <div class="gw-left"><div class="gw-icon"><i class="fas fa-wave-square" style="color:var(--amber)"></i></div><div><div class="gw-name">Flutterwave</div><div class="gw-desc">Mobile money</div></div></div>
            <span class="badge warning"><i class="fas fa-clock"></i>Pending</span>
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
          </div>
        </div>
      </div>

      <div style="display:flex;flex-direction:column;gap:18px">
        <div class="card">
<<<<<<< HEAD
          <div class="card-hd"><div class="card-title"><i class="fas fa-bell" style="margin-right:6px;color:var(--muted)"></i>Notification Preferences</div></div>
=======
          <div class="card-hd"><div class="card-title"><i class="fas fa-bell" style="margin-right:6px;color:var(--muted)"></i>Notification Settings</div></div>
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
          <div class="notif-row">
            <div><div class="notif-label">New Donation Alerts</div><div class="notif-desc">Get notified for every donation</div></div>
            <div class="toggle-switch"></div>
          </div>
          <div class="notif-row">
<<<<<<< HEAD
=======
            <div><div class="notif-label">Patient Admissions</div><div class="notif-desc">Critical case alerts</div></div>
            <div class="toggle-switch"></div>
          </div>
          <div class="notif-row">
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
            <div><div class="notif-label">Security Alerts</div><div class="notif-desc">Suspicious activity warnings</div></div>
            <div class="toggle-switch"></div>
          </div>
          <div class="notif-row">
            <div><div class="notif-label">Weekly Reports</div><div class="notif-desc">Email digest every Monday</div></div>
            <div class="toggle-switch off"></div>
          </div>
        </div>
        <div class="card">
<<<<<<< HEAD
          <div class="card-hd"><div class="card-title"><i class="fas fa-user-circle" style="margin-right:6px;color:var(--muted)"></i>Account</div></div>
          <div class="form-field"><label class="form-label">Your Name</label><input class="form-input" value="<?php echo Helpers::e($adminName); ?>"/></div>
          <div class="form-field"><label class="form-label">Email</label><input class="form-input" value="<?php echo Helpers::e($adminEmail); ?>"/></div>
          <div class="form-field"><label class="form-label">Role</label><input class="form-input" value="<?php echo Helpers::e(ucwords(str_replace("_", " ", $adminRole))); ?>" disabled style="opacity:.6"/></div>
=======
          <div class="card-hd"><div class="card-title"><i class="fas fa-globe" style="margin-right:6px;color:var(--muted)"></i>Website Integration</div></div>
          <div class="form-field">
            <label class="form-label">Public API Key</label>
            <div style="display:flex;gap:8px">
              <input class="form-input" value="pk_live_••••••••••••••••••••" style="font-family:monospace;flex:1"/>
              <button class="btn-secondary"><i class="fas fa-copy"></i></button>
            </div>
          </div>
          <div class="form-field">
            <label class="form-label">Webhook URL</label>
            <input class="form-input" value="https://hopeconnect.org/api/webhook"/>
          </div>
          <button class="btn-primary" style="margin-top:4px"><i class="fas fa-rotate"></i> Regenerate Keys</button>
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
        </div>
      </div>
    </div>
  </div>

</div><!-- /main -->
</div><!-- /app -->

<<<<<<< HEAD
<!-- ═══════ TOAST CONTAINER ═══════ -->
<div id="toastContainer" class="toast-container"></div>

<!-- ═══════ MODAL ═══════ -->
<div class="modal-overlay" id="modalOverlay" onclick="if(event.target===this)closeModal()">
  <div class="modal" id="modal">
    <div class="modal-header">
      <div class="modal-title" id="modalTitle">New Item</div>
      <button class="modal-close" onclick="closeModal()"><i class="fas fa-xmark"></i></button>
    </div>
    <div class="spinner hidden" id="modalSpinner"><i class="fas fa-circle-notch fa-spin"></i><span>Loading data…</span></div>
    <form method="post" id="modalForm">
      <div class="modal-body" id="modalBody">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
        <button type="submit" class="btn-primary" id="modalSubmit"><i class="fas fa-floppy-disk"></i> Save</button>
      </div>
    </form>
  </div>
</div>

<!-- ═══════ CONFIRM MODAL ═══════ -->
<div class="modal-overlay" id="confirmOverlay" onclick="if(event.target===this)closeConfirm()">
  <div class="modal modal-sm">
    <div class="modal-header">
      <div class="modal-title" id="confirmTitle">Confirm Action</div>
      <button class="modal-close" onclick="closeConfirm()"><i class="fas fa-xmark"></i></button>
    </div>
    <div class="modal-body" id="confirmBody">
      <p id="confirmMessage" style="font-size:.88rem;color:var(--mid);line-height:1.6"></p>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn-secondary" onclick="closeConfirm()">Cancel</button>
      <button type="button" class="btn-primary" id="confirmDeleteBtn" style="background:var(--rose)"><i class="fas fa-trash"></i> Delete</button>
    </div>
  </div>
</div>

<script>
const CSRF_TOKEN = '<?php echo $_SESSION["_csrf_token"] ?? ""; ?>';
const PAGES = {
  dashboard:'Dashboard',donations:'Donations',users:'Users',
  programmes:'Programmes',partners:'Partners',blog:'Blog & News',
  events:'Events',gallery:'Gallery',security:'Security',settings:'Settings'
=======
<script>
const PAGES = {
  dashboard:'Dashboard',donations:'Donations',users:'Users',
  patients:'Patients',partners:'Partners',blog:'Blog & News',
  gallery:'Gallery',security:'Security',settings:'Settings'
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
};

function showPage(id, el) {
  document.querySelectorAll('.content').forEach(c => c.classList.remove('active'));
  const pg = document.getElementById('page-' + id);
  if (pg) pg.classList.add('active');
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  if (el) el.classList.add('active');
  else {
    document.querySelectorAll('.nav-item').forEach(n => {
      if (n.getAttribute('onclick') && n.getAttribute('onclick').includes("'"+id+"'")) n.classList.add('active');
    });
  }
  document.getElementById('pageTitle').textContent = PAGES[id] || id;
  document.getElementById('breadSub').textContent = PAGES[id] || id;
<<<<<<< HEAD
  // Persist current page in URL so refresh stays on the same tab
  const url = new URL(window.location);
  if (url.searchParams.get('page') !== id) {
    url.searchParams.set('page', id);
    window.history.replaceState({page: id}, '', url);
  }
=======
  // Close mobile sidebar
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
  if (window.innerWidth < 1024) closeMobile();
}

let isCollapsed = false;
let mobileOpen = false;
<<<<<<< HEAD
let editId = 0;
=======
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43

function toggleSidebar() {
  const sb = document.getElementById('sidebar');
  const mn = document.getElementById('main');
  const ov = document.getElementById('overlay');
  if (window.innerWidth < 1024) {
    mobileOpen = !mobileOpen;
    sb.classList.toggle('mobile-open', mobileOpen);
    ov.classList.toggle('show', mobileOpen);
  } else {
    isCollapsed = !isCollapsed;
    sb.classList.toggle('collapsed', isCollapsed);
    mn.classList.toggle('collapsed', isCollapsed);
  }
}

function closeMobile() {
  mobileOpen = false;
  document.getElementById('sidebar').classList.remove('mobile-open');
  document.getElementById('overlay').classList.remove('show');
}

<<<<<<< HEAD
// ─── MODAL ──────────────────────────────────────
const MODAL_FORMS = {
  post: {
    title: 'Blog Post',
    action: 'create_post',
    fields: [
      {name:'_action',type:'hidden'},
      {name:'id',type:'hidden'},
      {name:'title',label:'Title',type:'text',required:true,placeholder:'Enter post title'},
      {name:'category',label:'Category',type:'select',options:['Impact Stories','News','Announcements','Healthcare','Education','Partnerships','General']},
      {name:'author_name',label:'Author',type:'text',placeholder:'Author name'},
      {name:'status',label:'Status',type:'select',options:['draft','published','archived']},
      {name:'excerpt',label:'Excerpt',type:'textarea',placeholder:'Brief summary…',rows:3},
      {name:'content',label:'Content',type:'textarea',placeholder:'Write your post content here…',rows:8},
    ]
  },
  event: {
    title: 'Event',
    action: 'create_event',
    fields: [
      {name:'_action',type:'hidden'},
      {name:'id',type:'hidden'},
      {name:'title',label:'Title',type:'text',required:true,placeholder:'Event title'},
      {name:'venue',label:'Venue',type:'text',placeholder:'Event venue'},
      {name:'city',label:'City',type:'text',placeholder:'City'},
      {name:'event_start',label:'Start Date',type:'datetime-local',required:true},
      {name:'event_end',label:'End Date',type:'datetime-local'},
      {name:'status',label:'Status',type:'select',options:['draft','published','cancelled','completed']},
      {name:'summary',label:'Summary',type:'textarea',placeholder:'Brief description…',rows:3},
      {name:'content',label:'Full Description',type:'textarea',placeholder:'Detailed description…',rows:6},
    ]
  },
  admin: {
    title: 'Admin User',
    action: 'create_admin',
    fields: [
      {name:'_action',type:'hidden'},
      {name:'id',type:'hidden'},
      {name:'full_name',label:'Full Name',type:'text',required:true,placeholder:'Full name'},
      {name:'email',label:'Email',type:'email',required:true,placeholder:'admin@example.org'},
      {name:'role',label:'Role',type:'select',options:['super_admin','admin','editor','finance']},
      {name:'status',label:'Status',type:'select',options:['active','suspended']},
      {name:'password',label:'New Password (leave blank to keep current)',type:'password',placeholder:'Min 8 characters'},
    ]
  },
  gallery: {
    title: 'Gallery Item',
    action: 'create_gallery',
    fields: [
      {name:'_action',type:'hidden'},
      {name:'id',type:'hidden'},
      {name:'title',label:'Title',type:'text',required:true,placeholder:'Image or video title'},
      {name:'media_type',label:'Media Type',type:'select',options:['photo','video']},
      {name:'media_path',label:'Media URL / Path',type:'text',required:true,placeholder:'/assets/images/uploads/file.jpg'},
      {name:'description',label:'Description',type:'textarea',placeholder:'Optional description…',rows:3},
      {name:'status',label:'Status',type:'select',options:['draft','published']},
    ]
  }
};

function openModal(type, id) {
  editId = id || 0;
  const currentPage = document.querySelector('.content.active')?.id?.replace('page-', '') || 'dashboard';
  const config = MODAL_FORMS[type];
  if (!config) return;

  const isEdit = editId > 0;
  document.getElementById('modalTitle').textContent = (isEdit ? 'Edit ' : 'New ') + config.title;
  document.getElementById('modalSubmit').innerHTML = '<i class="fas fa-floppy-disk"></i> ' + (isEdit ? 'Update' : 'Save');

  let html = '';
  let actionValue = config.action.replace('create_', isEdit ? 'update_' : 'create_');

  for (const f of config.fields) {
    if (f.type === 'hidden') {
      if (f.name === '_action') html += '<input type="hidden" name="_action" value="' + actionValue + '"/>';
      else if (f.name === 'id') html += '<input type="hidden" name="id" value="' + editId + '"/>';
      continue;
    }
    html += '<div class="form-group">';
    if (f.label) html += '<label>' + f.label + (f.required ? ' <span style="color:var(--rose)">*</span>' : '') + '</label>';

    if (f.type === 'select') {
      html += '<select class="form-control" name="' + f.name + '"' + (f.required ? ' required' : '') + '>';
      for (const opt of f.options) {
        html += '<option value="' + opt + '">' + opt.charAt(0).toUpperCase() + opt.slice(1) + '</option>';
      }
      html += '</select>';
    } else if (f.type === 'textarea') {
      html += '<textarea class="form-control" name="' + f.name + '" placeholder="' + (f.placeholder||'') + '" rows="' + (f.rows||4) + '"' + (f.required ? ' required' : '') + '></textarea>';
    } else {
      html += '<input class="form-control" type="' + f.type + '" name="' + f.name + '" placeholder="' + (f.placeholder||'') + '"' + (f.required ? ' required' : '') + '/>';
    }
    html += '</div>';
  }

  html += '<input type="hidden" name="_csrf_token" value="' + CSRF_TOKEN + '"/>';
  html += '<input type="hidden" name="_page" value="' + currentPage + '"/>';
  document.getElementById('modalBody').innerHTML = html;
  document.getElementById('modalOverlay').classList.add('show');
  document.getElementById('modalForm').reset();

  // If editing, show spinner and fetch data
  if (isEdit) {
    document.getElementById('modalForm').style.display = 'none';
    document.getElementById('modalSpinner').classList.remove('hidden');
    fetchModalData(type, editId);
  } else {
    document.getElementById('modalForm').style.display = '';
    document.getElementById('modalSpinner').classList.add('hidden');
  }
}

function fetchModalData(type, id) {
  fetch('?ajax=get_item&type=' + type + '&id=' + id)
    .then(r => { if (!r.ok) throw new Error('Network error'); return r.json(); })
    .then(data => {
      document.getElementById('modalSpinner').classList.add('hidden');
      document.getElementById('modalForm').style.display = '';
      if (!data) { showToast('Could not load item data', 'danger'); return; }
      const form = document.getElementById('modalForm');
      // Store original values for change detection
      form.dataset.originalStatus = data.status || '';
      for (const [key, value] of Object.entries(data)) {
        if (value === null || value === undefined) continue;
        const el = form.elements[key];
        if (!el) continue;
        if (el.type === 'datetime-local') {
          const d = value.replace(' ', 'T');
          el.value = d.substring(0, 16);
        } else {
          el.value = value;
        }
      }
    })
    .catch(() => {
      document.getElementById('modalSpinner').classList.add('hidden');
      document.getElementById('modalForm').style.display = '';
      showToast('Failed to load edit data', 'danger');
    });
}

function closeModal() {
  document.getElementById('modalOverlay').classList.remove('show');
  editId = 0;
  delete document.getElementById('modalForm').dataset.originalStatus;
}

// Close modals on Escape key
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    const confirmOpen = document.getElementById('confirmOverlay').classList.contains('show');
    if (confirmOpen) { closeConfirm(); return; }
    closeModal();
  }
});

// ─── CONFIRM MODAL ────────────────────────────────
let pendingConfirm = null;

function showConfirm(message, onConfirm) {
  document.getElementById('confirmMessage').textContent = message;
  pendingConfirm = onConfirm;
  document.getElementById('confirmOverlay').classList.add('show');
}

function closeConfirm() {
  document.getElementById('confirmOverlay').classList.remove('show');
  pendingConfirm = null;
}

// ─── DELETE ITEM ─────────────────────────────────
function deleteItem(type, id) {
  const labels = {admin:'admin user',post:'blog post',event:'event',gallery:'gallery item'};
  showConfirm('Are you sure you want to delete this ' + (labels[type] || type) + '? This action cannot be undone.', () => {
    const currentPage = document.querySelector('.content.active')?.id?.replace('page-', '') || 'dashboard';
    const form = document.createElement('form');
    form.method = 'post';
    form.style.display = 'none';
    form.innerHTML = '<input type="hidden" name="_csrf_token" value="' + CSRF_TOKEN + '"/><input type="hidden" name="_action" value="delete_' + type + '"/><input type="hidden" name="id" value="' + id + '"/><input type="hidden" name="_page" value="' + currentPage + '"/>';
    document.body.appendChild(form);
    form.submit();
  });
}

// ─── TOAST NOTIFICATION ─────────────────────────
function showToast(message, type) {
  const container = document.getElementById('toastContainer');
  const toast = document.createElement('div');
  toast.className = 'toast ' + (type || 'info');
  const icons = {success:'check-circle', danger:'exclamation-circle'};
  toast.innerHTML = '<i class="fas fa-' + (icons[type] || 'info-circle') + '"></i><span style="flex:1">' + message + '</span><button class="toast-close" onclick="this.parentElement.classList.add(\'removing\');setTimeout(()=>this.parentElement.remove(),300)"><i class="fas fa-xmark"></i></button>';
  container.appendChild(toast);
  setTimeout(() => {
    toast.classList.add('removing');
    setTimeout(() => toast.remove(), 300);
  }, 5000);
}

// ─── PAGE LOAD ───────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  const page = params.get('page');
  const msg = params.get('msg');
  const type = params.get('type');

  if (page) {
    const nav = document.querySelector('.nav-item[onclick*="' + page + '"]');
    showPage(page, nav);
  }

  if (msg) {
    showToast(msg, type || 'info');
    const url = new URL(window.location);
    url.searchParams.delete('msg');
    url.searchParams.delete('type');
    url.searchParams.delete('page');
    window.history.replaceState({}, '', url);
  }

  // ─── TABLE SEARCH ──────────────────────────────
  document.querySelectorAll('.search-box input').forEach(input => {
    input.addEventListener('input', function () {
      const q = this.value.toLowerCase();
      const table = this.closest('.card')?.querySelector('.data-table');
      if (!table) return;
      table.querySelectorAll('tbody tr').forEach(row => {
        const match = Array.from(row.querySelectorAll('td')).some(td => td.textContent.toLowerCase().includes(q));
        row.style.display = match ? '' : 'none';
      });
    });
  });

  // ─── FILTER BUTTONS ────────────────────────────
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      this.classList.toggle('on');
    });
  });

  // Confirm status change on edit modal submit
  document.getElementById('modalForm').addEventListener('submit', (e) => {
    if (editId > 0) {
      const originalStatus = e.target.dataset.originalStatus || '';
      const newStatus = e.target.elements['status']?.value;
      if (newStatus && originalStatus && newStatus !== originalStatus) {
        e.preventDefault();
        showConfirm('Are you sure you want to change the status from "' + originalStatus + '" to "' + newStatus + '"?', () => {
          e.target.submit();
        });
      }
    }
  });

  // Confirm delete button
  document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
    if (typeof pendingConfirm === 'function') pendingConfirm();
    closeConfirm();
  });

  // Animate bars
  document.querySelectorAll('.bar.primary').forEach(bar => {
    const h = bar.style.height;
    bar.style.height = '0%';
    setTimeout(() => { bar.style.height = h; }, 100);
  });
});

=======
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
// Tabs
document.querySelectorAll('.tabs').forEach(group => {
  group.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      group.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('on'));
      btn.classList.add('on');
    });
  });
});

<<<<<<< HEAD
// Toggle switches
=======
// Toggle switches (settings)
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
document.querySelectorAll('.toggle-switch').forEach(sw => {
  sw.addEventListener('click', () => sw.classList.toggle('off'));
});

<<<<<<< HEAD
// Resize handler
=======
// Responsive: reset sidebar on resize
>>>>>>> 593b0d370d66b70eb994a9fec89ffbfb79cb7a43
window.addEventListener('resize', () => {
  if (window.innerWidth >= 1024) {
    document.getElementById('sidebar').classList.remove('mobile-open');
    document.getElementById('overlay').classList.remove('show');
    mobileOpen = false;
  }
});
</script>
</body>
</html>
