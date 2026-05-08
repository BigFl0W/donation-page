<?php
declare(strict_types=1);
require_once __DIR__ . "/../config/autoload.php";

use App\Auth;
use App\Helpers;
use App\Database;
use App\Content;

Auth::requireLogin();

$admin = Auth::current();
$dbAvail = Database::available();
$adminId = $admin["id"] ?? 0;
$adminName = $admin["name"] ?? "Admin";
$adminEmail = $admin["email"] ?? "";
$adminRole = $admin["role"] ?? "admin";
$adminAvatar = $admin["avatar"] ?? null;

// Re-fetch latest from DB
if ($dbAvail && $adminId) {
    $fresh = Database::fetchOne("SELECT avatar, full_name, email FROM admins WHERE id = :id", ["id" => $adminId]);
    if ($fresh) {
        $adminAvatar = $fresh["avatar"];
        $adminName = $fresh["full_name"];
        $adminEmail = $fresh["email"];
    }
}

$adminInitials = "";
foreach (array_slice(preg_split('/\s+/', trim($adminName)) ?: [], 0, 2) as $p) {
    if ($p !== "") $adminInitials .= strtoupper($p[0]);
}
$adminInitials = $adminInitials ?: "AD";

// ─── DASHBOARD DATA ───────────────────────────────

// ─── LOAD SETTINGS FIRST (BEFORE THEY'RE USED) ────────────────────────────────
$settings = [];
if ($dbAvail) {
    $rawSettings = Database::fetchAll("SELECT setting_key,setting_value FROM settings") ?: [];
    foreach ($rawSettings as $s) { $settings[$s["setting_key"]] = $s["setting_value"]; }
}

// ─── DEFAULT VARIABLES (NOW $SETTINGS IS AVAILABLE) ────────────────────────────────
$adminBrandLogo = Helpers::brandLogoPath();
$siteName = Helpers::brandName();
$adminFavicon = Helpers::brandFaviconPath();

$totalDonationsYear = 0; $totalDonationsCurrency = "USD";
$totalDonationsAll = 0; $totalTxCount = 0;
$pendingReview = 0; $failedCount = 0;
$totalAdmins = 0; $activeAdmins = 0; $suspendedAdmins = 0;
$publishedPosts = 0; $draftPosts = 0;
$publishedEvents = 0; $upcomingEvents = [];
$activePartners = 0; $publishedProgrammes = 0;
$recentDonations = []; $recentPosts = []; $recentActivity = [];
$monthlyDonationData = []; $gatewayMix = [];
$partnersList = []; $galleryItems = []; $adminUsers = []; $recentLogins = [];
$programmes = []; $allDonations = [];

// ─── NOTIFICATIONS & MESSAGES (PROFESSIONAL) ───────
$unreadNotifCount = 0;
$recentNotifications = [];
$unreadMsgCount = 0;
$recentMessages = [];

if ($dbAvail) {
    // Notifications for current admin
    $unreadNotifCount = (int)(Database::fetchOne("SELECT COUNT(*) as t FROM admin_notifications WHERE (admin_id IS NULL OR admin_id = ?) AND is_read = 0", [$admin['id']])['t'] ?? 0);
    $recentNotifications = Database::fetchAll("SELECT * FROM admin_notifications WHERE (admin_id IS NULL OR admin_id = ?) ORDER BY created_at DESC LIMIT 5", [$admin['id']]) ?: [];

    // Messages from contact form
    $unreadMsgCount = (int)(Database::fetchOne("SELECT COUNT(*) as t FROM contact_messages WHERE status = 'unread'")['t'] ?? 0);
    $recentMessages = Database::fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5") ?: [];
}

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

    if ($action === "update_donation") {
        $id = (int) ($_POST["id"] ?? 0);
        $status = (string) ($_POST["status"] ?? "pending");
        $paidAt = (string) ($_POST["paid_at"] ?? null);
        
        if ($id > 0) {
            Database::execute(
                "UPDATE donations SET status = :status, paid_at = :paid_at WHERE id = :id",
                ["status" => $status, "paid_at" => $paidAt ?: null, "id" => $id]
            );
            $flashMsg = "Donation status updated"; $flashType = "success";
        }
    }

    // ─── BRANDING SETTINGS (LOGO & FAVICON UPLOAD) ───────────────────────────
    if ($action === "save_branding_settings") {
        $siteName = trim((string) ($_POST["site_name"] ?? ""));
        $contactEmail = trim((string) ($_POST["contact_email"] ?? ""));
        $contactPhone = trim((string) ($_POST["contact_phone"] ?? ""));
        
        // Create upload directory if it doesn't exist
        $uploadsDir = __DIR__ . "/../assets/uploads/branding";
        if (!is_dir($uploadsDir)) {
            if (!@mkdir($uploadsDir, 0755, true)) {
                $flashMsg = "Failed to create upload directory. Check server permissions."; 
                $flashType = "danger";
            }
        }
        
        // Verify directory is writable before attempting uploads
        if ($flashMsg === "" && !is_writable($uploadsDir)) {
            $flashMsg = "Upload directory is not writable. Please check permissions."; 
            $flashType = "danger";
        }
        
        // Handle logo upload
        $logoPath = $settings['brand_logo'] ?? 'assets/images/logo.png';
        if ($flashMsg === "" && isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
            $logoFile = $_FILES['site_logo'];
            $allowedMimes = ['image/svg+xml', 'image/png', 'image/jpeg', 'image/gif', 'image/webp'];
            $allowedExts = ['svg', 'png', 'jpg', 'jpeg', 'gif', 'webp'];
            
            // Check file size (max 2MB)
            if ($logoFile['size'] > 2097152) {
                $flashMsg = "Logo file too large. Maximum 2MB allowed."; 
                $flashType = "danger";
            } else {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $logoFile['tmp_name']);
                finfo_close($finfo);
                
                $fileExt = strtolower(pathinfo($logoFile['name'], PATHINFO_EXTENSION));
                
                if (in_array($mimeType, $allowedMimes) && in_array($fileExt, $allowedExts)) {
                    $newLogoName = 'logo_' . time() . '.' . $fileExt;
                    $newLogoPath = $uploadsDir . '/' . $newLogoName;
                    
                    if (move_uploaded_file($logoFile['tmp_name'], $newLogoPath)) {
                        // Verify file was actually created
                        if (file_exists($newLogoPath)) {
                            $logoPath = 'assets/uploads/branding/' . $newLogoName;
                            if ($dbAvail) {
                                Database::execute(
                                    "INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value",
                                    ['key' => 'brand_logo', 'value' => $logoPath]
                                );
                            }
                            $settings['brand_logo'] = $logoPath;
                            $adminBrandLogo = $logoPath;
                            $flashMsg = "Logo updated successfully"; 
                            $flashType = "success";
                        } else {
                            $flashMsg = "Logo file was not saved correctly."; 
                            $flashType = "danger";
                        }
                    } else {
                        $flashMsg = "Failed to upload logo file. Check directory permissions."; 
                        $flashType = "danger";
                    }
                } else {
                    $flashMsg = "Invalid logo format. Use SVG, PNG, JPG, GIF, or WebP"; 
                    $flashType = "danger";
                }
            }
        }
        
        // Handle favicon upload
        $faviconPath = $settings['site_favicon'] ?? 'assets/images/favicon.ico';
        if ($flashMsg === "" && isset($_FILES['site_favicon']) && $_FILES['site_favicon']['error'] === UPLOAD_ERR_OK) {
            $faviconFile = $_FILES['site_favicon'];
            $faviconAllowedMimes = ['image/x-icon', 'image/png', 'image/svg+xml'];
            $faviconAllowedExts = ['ico', 'png', 'svg'];
            
            // Check file size (max 1MB)
            if ($faviconFile['size'] > 1048576) {
                $flashMsg = "Favicon file too large. Maximum 1MB allowed."; 
                $flashType = "danger";
            } else {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $faviconFile['tmp_name']);
                finfo_close($finfo);
                
                $fileExt = strtolower(pathinfo($faviconFile['name'], PATHINFO_EXTENSION));
                
                if (in_array($mimeType, $faviconAllowedMimes) && in_array($fileExt, $faviconAllowedExts)) {
                    $newFaviconName = 'favicon_' . time() . '.' . $fileExt;
                    $newFaviconPath = $uploadsDir . '/' . $newFaviconName;
                    
                    if (move_uploaded_file($faviconFile['tmp_name'], $newFaviconPath)) {
                        // Verify file was actually created
                        if (file_exists($newFaviconPath)) {
                            $faviconPath = 'assets/uploads/branding/' . $newFaviconName;
                            if ($dbAvail) {
                                Database::execute(
                                    "INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value",
                                    ['key' => 'site_favicon', 'value' => $faviconPath]
                                );
                            }
                            $settings['site_favicon'] = $faviconPath;
                            $adminFavicon = $faviconPath;
                            if ($flashMsg === "") {
                                $flashMsg = "Favicon updated successfully"; 
                                $flashType = "success";
                            }
                        } else {
                            if ($flashMsg === "") {
                                $flashMsg = "Favicon file was not saved correctly."; 
                                $flashType = "danger";
                            }
                        }
                    } else {
                        if ($flashMsg === "") {
                            $flashMsg = "Failed to upload favicon file. Check directory permissions."; 
                            $flashType = "danger";
                        }
                    }
                } else {
                    if ($flashMsg === "") {
                        $flashMsg = "Invalid favicon format. Use ICO, PNG, or SVG"; 
                        $flashType = "danger";
                    }
                }
            }
        }
        
        // Update organization settings in database
        if ($siteName !== "" && $dbAvail) {
            Database::execute(
                "INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value",
                ['key' => 'site_name', 'value' => $siteName]
            );
            $settings['site_name'] = $siteName;
            $siteName = $siteName;
        }
        
        if ($contactEmail !== "" && $dbAvail) {
            Database::execute(
                "INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value",
                ['key' => 'contact_email', 'value' => $contactEmail]
            );
            $settings['contact_email'] = $contactEmail;
        }
        
        if ($contactPhone !== "" && $dbAvail) {
            Database::execute(
                "INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value",
                ['key' => 'contact_phone', 'value' => $contactPhone]
            );
            $settings['contact_phone'] = $contactPhone;
        }
    }

    // ─── UPDATE PROFILE (PROFESSIONAL) ──────────────────────────────────────
    if ($action === "update_profile") {
        $fullName = trim((string) ($_POST["full_name"] ?? ""));
        $email = trim((string) ($_POST["email"] ?? ""));
        $password = trim((string) ($_POST["password"] ?? ""));
        $adminId = (int)($admin["id"] ?? 0);

        if ($fullName !== "" && $email !== "") {
            $params = ["name" => $fullName, "email" => $email, "id" => $adminId];
            $sql = "UPDATE admins SET full_name = :name, email = :email";

            if ($password !== "") {
                $sql .= ", password_hash = :hash";
                $params["hash"] = password_hash($password, PASSWORD_DEFAULT);
            }

            // Handle Avatar Upload
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $upDir = __DIR__ . "/../assets/uploads/avatars";
                if (!is_dir($upDir)) @mkdir($upDir, 0755, true);
                
                $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $newName = 'ava_' . $adminId . '_' . time() . '.' . $ext;
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upDir . '/' . $newName)) {
                        $avaPath = 'assets/uploads/avatars/' . $newName;
                        $sql .= ", avatar = :avatar";
                        $params["avatar"] = $avaPath;
                    }
                }
            }

            $sql .= " WHERE id = :id";
            if (Database::execute($sql, $params)) {
                $flashMsg = "Profile updated successfully";
                $flashType = "success";
                // Refresh local data
                $adminName = $fullName;
                $adminEmail = $email;
                if (isset($params["avatar"])) $adminAvatar = $params["avatar"];
            } else {
                $flashMsg = "Failed to update profile";
                $flashType = "danger";
            }
        } else {
            $flashMsg = "Name and email are required";
            $flashType = "danger";
        }
    }
        
        if ($flashMsg === "" || strpos($flashMsg, "updated") === false) {
            $flashMsg = "Settings updated successfully"; $flashType = "success";
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
        if ($type === "donation") {
            $item = Database::fetchOne("SELECT * FROM donations WHERE id = :id", ["id" => $id]);
        }
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

/* ═══════ SIDEBAR ═══════ */
.sidebar{
  width:var(--sidebar-w);
  background:var(--white);
  border-right:1px solid var(--border);
  display:flex;flex-direction:column;
  position:fixed;top:0;left:0;
  height:100vh;z-index:200;
  transition:transform .3s cubic-bezier(.4,0,.2,1),width .3s cubic-bezier(.4,0,.2,1);
  overflow:hidden;
}
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

@media(max-width:1023px){
  .sidebar{transform:translateX(-100%)}
  .sidebar.mobile-open{transform:translateX(0)}
  .sidebar.collapsed{width:var(--sidebar-w);transform:translateX(-100%)}
  .sidebar.collapsed.mobile-open{transform:translateX(0)}
}

.brand{
  display:flex;align-items:center;gap:12px;
  padding:20px 18px 16px;
  border-bottom:1px solid var(--border);
  flex-shrink:0;
}
.brand-logo-img{
  display:block;
  width:min(100%, 190px);
  max-width:190px;
  height:auto;
  max-height:72px;
  object-fit:contain;
}
.brand-logo{
  width:38px;height:38px;border-radius:10px;
  background:linear-gradient(135deg,var(--brand-light),var(--brand));
  display:flex;align-items:center;justify-content:center;
  color:#fff;font-size:.9rem;flex-shrink:0;
}
.brand-name{
  font-family:'Instrument Serif',serif;
  font-size:1.1rem;color:var(--dark);white-space:nowrap;line-height:1.2;
}
.brand-sub{font-size:.68rem;color:var(--muted);white-space:nowrap;margin-top:1px;letter-spacing:.3px}

.nav-scroll{flex:1;overflow-y:auto;overflow-x:hidden;padding:10px 0}
.nav-section{
  font-size:.65rem;font-weight:700;letter-spacing:1.6px;
  text-transform:uppercase;color:var(--muted);
  padding:14px 18px 5px;white-space:nowrap;
}
.nav-item{
  display:flex;align-items:center;gap:11px;
  padding:10px 18px;cursor:pointer;
  border-left:2px solid transparent;
  transition:all .18s ease;white-space:nowrap;
  position:relative;
}
.nav-item:hover{background:var(--surface)}
.nav-item.active{
  background:rgba(20,184,166,.08);
  border-left-color:var(--brand);
}
.nav-item.active .nav-icon{color:var(--brand)}
.nav-item.active .nav-text{color:var(--dark);font-weight:600}
.nav-icon{
  font-size:.9rem;color:var(--muted);
  transition:color .18s;flex-shrink:0;
  width:18px;text-align:center;
}
.nav-text{font-size:.83rem;color:var(--mid);transition:color .18s;flex:1}
.nav-badge{
  font-size:.62rem;font-weight:700;
  padding:2px 7px;border-radius:99px;
  background:var(--rose);color:#fff;
}
.nav-badge.green{background:#059669}
.nav-badge.amber{background:var(--amber)}

.sidebar-footer{
  border-top:1px solid var(--border);
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
.user-name{font-size:.8rem;font-weight:700;color:var(--dark)}
.user-role{font-size:.68rem;color:var(--muted);margin-top:1px}
.footer-links{
  border-top:1px solid var(--border);
  padding:10px 18px;display:flex;gap:14px;flex-shrink:0;
}
.footer-links a{
  font-size:.7rem;color:var(--muted);text-decoration:none;transition:color .18s;
}
.footer-links a:hover{color:var(--brand)}
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
  .brand{
    padding:18px 16px 14px;
  }
  .brand-logo-img{
    width:min(100%, 168px);
    max-width:168px;
    max-height:64px;
  }
}

@media(max-width:640px){
  .brand{
    padding:16px 14px 12px;
  }
  .brand-logo-img{
    width:min(100%, 150px);
    max-width:150px;
    max-height:56px;
  }
}

/* ═══════ TOPBAR ═══════ */
.topbar{
  height:var(--header-h);background:var(--white);
  border-bottom:1px solid var(--border);
  display:flex;align-items:center;gap:14px;
  padding:0 24px;position:sticky;top:0;z-index:100;
}
.menu-btn{
  width:38px;height:38px;border-radius:10px;
  border:1px solid var(--border);background:var(--white);cursor:pointer;
  display:flex;flex-direction:column;align-items:center;
  justify-content:center;gap:4px;flex-shrink:0;
  transition:all .22s ease;
  box-shadow:0 1px 3px rgba(0,0,0,.04);
}
.menu-btn:hover{
  background:var(--surface);
  border-color:var(--brand);
  box-shadow:0 2px 8px rgba(0,0,0,.06);
}
.menu-btn span{
  display:block;height:2px;
  background:var(--mid);border-radius:2px;
  transition:all .25s cubic-bezier(.4,0,.2,1);
}
.menu-btn span:nth-child(1){width:18px}
.menu-btn span:nth-child(2){width:14px}
.menu-btn span:nth-child(3){width:10px}
.menu-btn:hover span{background:var(--brand)}
.menu-btn:hover span:nth-child(1){width:18px}
.menu-btn:hover span:nth-child(2){width:18px}
.menu-btn:hover span:nth-child(3){width:18px}
.page-title{font-size:.95rem;font-weight:700;color:var(--dark);line-height:1}
.breadcrumb{
  font-size:.72rem;color:var(--muted);
  display:flex;align-items:center;gap:5px;margin-top:2px;
}
.breadcrumb span, .breadcrumb strong{color:var(--dark)}
.breadcrumb i{font-size:.55rem;color:var(--muted)}

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

/* ═══════ DROPDOWNS ═══════ */
.tb-dropdown-wrap { position: relative; }
.tb-dropdown {
  position: absolute; top: calc(100% + 12px); right: 0;
  width: 320px; background: var(--white); border-radius: 14px;
  border: 1px solid var(--border); box-shadow: 0 10px 40px rgba(0,0,0,0.12);
  display: none; flex-direction: column; z-index: 1000;
  animation: dropdownIn .2s ease; transform-origin: top right;
}
.tb-dropdown.active { display: flex; }
@keyframes dropdownIn { from { opacity: 0; transform: scale(0.95) translateY(-5px); } to { opacity: 1; transform: scale(1) translateY(0); } }

.dd-header {
  padding: 14px 18px; border-bottom: 1px solid var(--border);
  display: flex; align-items: center; justify-content: space-between;
}
.dd-header h4 { font-size: 0.85rem; font-weight: 700; color: var(--dark); margin: 0; }
.dd-body { max-height: 380px; overflow-y: auto; }
.dd-item {
  display: flex; gap: 12px; padding: 12px 18px; text-decoration: none;
  transition: background .15s; border-bottom: 1px solid var(--surface);
}
.dd-item:hover { background: var(--bg); }
.dd-icon {
  width: 36px; height: 36px; border-radius: 9px;
  display: flex; align-items: center; justify-content: center;
  font-size: 0.85rem; background: var(--surface); color: var(--mid);
}
.dd-content { flex: 1; min-width: 0; }
.dd-title { font-size: 0.82rem; font-weight: 700; color: var(--dark); margin-bottom: 2px; }
.dd-text { font-size: 0.76rem; color: var(--soft); line-height: 1.4; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.dd-time { font-size: 0.68rem; color: var(--muted); margin-top: 4px; }
.dd-footer { padding: 12px; text-align: center; border-top: 1px solid var(--border); }
.dd-footer a { font-size: 0.78rem; font-weight: 600; color: var(--brand); text-decoration: none; }

/* Profile Dropdown Specific */
.dd-profile { width: 220px; }
.dd-user-info { padding: 18px; text-align: center; background: var(--bg); border-radius: 14px 14px 0 0; }
.user-ava-lg { width: 60px; height: 60px; border-radius: 50%; background: var(--brand-gradient); margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem; font-weight: 800; }
.dd-link { display: flex; align-items: center; gap: 10px; padding: 10px 18px; font-size: 0.82rem; color: var(--mid); text-decoration: none; transition: all .15s; }
.dd-link:hover { background: var(--bg); color: var(--dark); }
.dd-link.danger:hover { background: #fff1f2; color: var(--rose); }

/* ═══════ CONTENT ═══════ */
.content{flex:1;padding:26px;display:none}
.content.active{display:block;animation:pageIn .28s ease}
@keyframes pageIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}

/* ═══════ STATS GRID ═══════ */
.stats-grid{
  display:grid;grid-template-columns:repeat(4,1fr);
  gap:16px;margin-bottom:22px;
}
.stats-grid.cols-3{grid-template-columns:repeat(3,1fr)}
@media(max-width:1279px){.stats-grid,.stats-grid.cols-3{grid-template-columns:repeat(2,1fr)}}
@media(max-width:767px){.stats-grid,.stats-grid.cols-3{grid-template-columns:repeat(2,1fr)}}
@media(max-width:479px){.stats-grid,.stats-grid.cols-3{grid-template-columns:1fr}}

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
.stat-card.t5::after{background:var(--violet)}

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

.stat-skeleton{
  height:20px;background:linear-gradient(90deg,var(--border) 25%,#f3f4f6 50%,var(--border) 75%);
  background-size:200% 100%;animation:shimmer 1.5s infinite;border-radius:6px;margin-bottom:8px;
}
@keyframes shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}

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
.bar.primary{background:var(--brand)}
.bar.secondary{background:var(--brand-dim)}
.bar-lbl{font-size:.62rem;color:var(--soft);font-weight:500}
.chart-legend{
  display:flex;gap:16px;margin-top:6px;
}
.legend-item{display:flex;align-items:center;gap:6px;font-size:.72rem;color:var(--muted)}
.legend-dot{width:8px;height:8px;border-radius:2px;flex-shrink:0}

.donut-legend{display:flex;flex-direction:column;gap:9px;min-width:120px}
.dl-item{display:flex;align-items:center;gap:8px;font-size:.78rem}
.dl-dot{width:9px;height:9px;border-radius:3px;flex-shrink:0}
.dl-lbl{color:var(--muted);flex:1}
.dl-val{font-weight:700;color:var(--dark)}

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
.filter-btn:hover,.filter-btn.on{border-color:var(--brand-light);color:var(--brand)}
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
@media(max-width:767px){.data-table thead{display:none}.data-table tr{display:block;margin-bottom:12px;border:1px solid var(--border);border-radius:10px;padding:10px}.data-table td{display:flex;justify-content:space-between;align-items:center;padding:8px 6px;border-bottom:1px solid var(--border);gap:8px}.data-table td:last-child{border-bottom:none}.data-table td::before{content:attr(data-label);font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--muted);white-space:nowrap;flex-shrink:0}.data-table td .action-btns{flex-shrink:0}.data-table .action-btn{width:34px;height:34px;font-size:.85rem}}
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

.tabs{display:flex;gap:4px;margin-bottom:18px;flex-wrap:wrap}
.tab-btn{
  padding:7px 14px;border-radius:8px;
  border:1px solid var(--border);background:var(--white);
  font-family:'Plus Jakarta Sans',sans-serif;font-size:.78rem;font-weight:500;
  color:var(--muted);cursor:pointer;transition:all .18s;
}
.tab-btn:hover{border-color:var(--brand-light);color:var(--brand)}
.tab-btn.on{background:var(--brand);color:#fff;border-color:var(--brand)}

.prog-wrap{width:100%;background:var(--border);border-radius:99px;height:5px;margin:6px 0}
.prog-bar{height:100%;border-radius:99px;background:linear-gradient(90deg,var(--brand),var(--brand-light))}
.prog-bar.urgent{background:linear-gradient(90deg,#f87171,var(--rose))}

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
.blog-excerpt{font-size:.75rem;color:var(--muted);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
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

/* ═══════ PROFILE PAGE ═══════ */
.profile-card { overflow: hidden; position: relative; padding: 0 !important; }
.profile-cover {
  height: 120px; background: linear-gradient(135deg, var(--brand-bg), var(--brand-dim));
  border-radius: 14px 14px 0 0; position: relative;
}
.profile-avatar-container {
  margin-top: -55px; display: flex; flex-direction: column; align-items: center;
  position: relative; z-index: 2; margin-bottom: 24px;
}
.profile-avatar-wrap {
  width: 110px; height: 110px; border-radius: 50%; border: 4px solid var(--white);
  box-shadow: 0 4px 15px rgba(0,0,0,0.1); position: relative; overflow: hidden;
  background: var(--brand-gradient); cursor: pointer;
  transition: transform .2s;
}
.profile-avatar-wrap:active { transform: scale(0.95); }
.profile-avatar-wrap img { width: 100%; height: 100%; object-fit: cover; }
.profile-avatar-wrap .initials {
  width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
  font-size: 2.2rem; font-weight: 800; color: white;
}
.profile-avatar-overlay {
  position: absolute; inset: 0; background: rgba(0,0,0,0.4);
  display: flex; align-items: center; justify-content: center;
  color: white; font-size: 1.2rem; opacity: 0; transition: opacity .2s;
}
.profile-avatar-wrap:hover .profile-avatar-overlay { opacity: 1; }
.profile-info { text-align: center; margin-top: 10px; }
.profile-info h3 { font-size: 1.25rem; font-weight: 700; color: var(--dark); margin: 0; }
.profile-info p { font-size: 0.82rem; color: var(--brand); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px; }
.profile-body { padding: 24px 32px 32px; }

/* ═══════ RESPONSIVE ═══════ */
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
}

.mono{font-family:'Courier New',monospace;font-size:.78rem;color:var(--muted)}

.alert-banner{
  display:flex;align-items:center;gap:10px;
  padding:11px 16px;border-radius:10px;
  margin-bottom:16px;font-size:.8rem;
}
.alert-banner.warn{background:#fffbeb;border:1px solid #fde68a;color:#92400e}
.alert-banner.danger{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}
.alert-banner i{flex-shrink:0}

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
.toolbar{display:flex;align-items:center;gap:12px;padding:18px;background:#fdfdfd;border-bottom:1px solid var(--border);flex-wrap:wrap}
.search-box{position:relative;flex:1;min-width:240px}
.search-box i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:.85rem;pointer-events:none}
.search-box input{width:100%;padding:10px 14px 10px 38px;border:1.5px solid var(--border);border-radius:10px;font-size:.85rem;outline:none;transition:all .2s;font-family:inherit}
.search-box input:focus{border-color:var(--brand);box-shadow:0 0 0 4px rgba(15,118,110,.08)}
.filter-select{height:42px;padding:0 35px 0 15px;border:1.5px solid var(--border);border-radius:10px;background:#fff;font-size:.82rem;color:var(--mid);cursor:pointer;transition:all .2s;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center}
.filter-select:hover{border-color:var(--brand-light);color:var(--brand)}
.ml-auto{margin-left:auto}
@media(max-width:991px){.ml-auto{margin-left:0;width:100%}.toolbar > *{flex:1 1 100%}}
.data-table tr:hover td{background:#f8fafb}
.badge{padding:5px 10px;border-radius:6px;font-size:.72rem;font-weight:600;display:inline-flex;align-items:center;gap:5px}
.badge.success{background:#ecfdf5;color:#059669;border:1px solid #d1fae5}
.badge.info{background:#eff6ff;color:#2563eb;border:1px solid #dbeafe}
.badge.warning{background:#fffbeb;color:#d97706;border:1px solid #fef3c7}
.badge.danger{background:#fef2f2;color:#dc2626;border:1px solid #fee2e2}
.card{background:var(--white);border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.05),0 1px 2px rgba(0,0,0,.03);border:1px solid var(--border);margin-bottom:20px;overflow:hidden}
.stat-card{padding:22px;border-radius:15px;background:#fff;border:1px solid var(--border);box-shadow:0 4px 15px rgba(0,0,0,.02);transition:transform .2s}
.stat-card:hover{transform:translateY(-3px);box-shadow:0 10px 25px rgba(0,0,0,.05)}
.stat-value{font-size:1.4rem;font-weight:800;color:var(--dark);margin-top:12px;letter-spacing:-0.5px}
.stat-label{font-size:.78rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;margin-top:4px}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;margin-bottom:25px}
</style>
    <style>
      @media print {
        /* Hide everything by default */
        body * { visibility: hidden; }
        
        /* Show only the print-only statement */
        #print-statement, #print-statement * { visibility: visible; }
        #print-statement { 
            position: absolute; 
            left: 0; 
            top: 0; 
            width: 100%; 
            padding: 20px;
            color: #000 !important;
            background: #fff !important;
        }
        
        /* Professional Statement Layout */
        .print-header { border-bottom: 2px solid #011B33; padding-bottom: 15px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: flex-end; }
        .print-logo { height: 50px; }
        .print-title { font-size: 24px; font-weight: bold; color: #011B33; margin: 0; text-transform: uppercase; }
        
        .print-summary { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; background: #f9fafb; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb; }
        .summary-item { font-size: 14px; }
        .summary-label { color: #6b7280; font-weight: 600; margin-bottom: 4px; }
        .summary-value { font-size: 18px; font-weight: 700; }
        
        .print-table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        .print-table th { background: #f3f4f6; color: #374151; font-weight: 700; text-align: left; padding: 10px; border: 1px solid #d1d5db; text-transform: uppercase; }
        .print-table td { padding: 10px; border: 1px solid #d1d5db; vertical-align: top; }
        .print-table tr:nth-child(even) { background: #fafafa; }
        
        .print-footer { margin-top: 50px; border-top: 1px solid #eee; padding-top: 10px; font-size: 10px; color: #999; text-align: center; }
        
        /* Hide UI elements that might sneak in */
        .toolbar, .stats-grid, .sidebar, .header, .nav-item, .btn-primary, .filter-btn, .action-btns, .pagination, .card-hd { display: none !important; }
      }
      
      /* Hide the print statement on screen */
      #print-statement { display: none; }
      @media print { #print-statement { display: block; } }
    </style>
</head>
<body>
<div class="app">

<!-- OVERLAY -->
<div class="sidebar-overlay" id="overlay" onclick="closeMobile()"></div>

<!-- ══════════ SIDEBAR ══════════ -->
<aside class="sidebar" id="sidebar">
  <div class="brand">
<?php
      $logoPath = $adminBrandLogo;
      if ($logoPath && file_exists(__DIR__.'/../'.$logoPath)) {
          echo '<img src="../'.Helpers::e($logoPath).'" alt="'.Helpers::e($siteName).'" class="brand-logo-img">';
      } else {
          echo '<span class="brand-name">'.Helpers::e($siteName).'</span>';
      }
?>
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
      <span class="nav-badge"><?php echo Helpers::e((string)$pendingReview); ?></span>
    </div>
    <div class="nav-item" onclick="showPage('users',this)">
      <i class="fas fa-users nav-icon"></i>
      <span class="nav-text">Users</span>
      <span class="nav-badge green"><?php echo Helpers::e((string)$totalAdmins); ?></span>
    </div>
    <div class="nav-item" onclick="showPage('programmes',this)">
      <i class="fas fa-seedling nav-icon"></i>
      <span class="nav-text">Programmes</span>
      <span class="nav-badge amber"><?php echo Helpers::e((string)$publishedProgrammes); ?></span>
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
    <div class="nav-item" onclick="showPage('events',this)">
      <i class="fas fa-calendar-days nav-icon"></i>
      <span class="nav-text">Events</span>
    </div>
    <div class="nav-item" onclick="showPage('gallery',this)">
      <i class="fas fa-images nav-icon"></i>
      <span class="nav-text">Gallery</span>
    </div>

    <div class="nav-section">System</div>
    <div class="nav-item" onclick="showPage('security',this)">
      <i class="fas fa-shield-halved nav-icon"></i>
      <span class="nav-text">Security</span>
    </div>
    <div class="nav-item" onclick="showPage('settings',this)">
      <i class="fas fa-gear nav-icon"></i>
      <span class="nav-text">Settings</span>
    </div>
  </nav>

  <div class="footer-links">
    <a href="<?php echo Helpers::e(Helpers::siteUrl()); ?>" target="_blank">View Site</a>
    <a href="<?php echo Helpers::e(Helpers::adminUrl("logout.php")); ?>" class="danger">Logout</a>
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
      <!-- Notifications -->
      <div class="tb-dropdown-wrap">
        <button class="tb-btn" title="Notifications" onclick="toggleDropdown('dd-notifications', event)">
          <i class="fas fa-bell"></i>
          <?php if ($unreadNotifCount > 0): ?><span class="tb-dot"></span><?php endif; ?>
        </button>
        <div class="tb-dropdown" id="dd-notifications">
          <div class="dd-header">
            <h4>Notifications</h4>
            <span class="badge bg-soft-brand"><?php echo $unreadNotifCount; ?> New</span>
          </div>
          <div class="dd-body">
            <?php if (empty($recentNotifications)): ?>
              <div class="p-4 text-center text-muted">No notifications yet</div>
            <?php else: ?>
              <?php foreach ($recentNotifications as $n): ?>
                <a href="<?php echo Helpers::e($n['link'] ?: '#'); ?>" class="dd-item">
                  <div class="dd-icon"><i class="<?php echo Helpers::e($n['icon']); ?>"></i></div>
                  <div class="dd-content">
                    <div class="dd-title"><?php echo Helpers::e($n['title']); ?></div>
                    <div class="dd-text"><?php echo Helpers::e($n['message']); ?></div>
                    <div class="dd-time"><?php echo date('M d, H:i', strtotime($n['created_at'])); ?></div>
                  </div>
                </a>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
          <div class="dd-footer"><a href="#">View All Notifications</a></div>
        </div>
      </div>

      <!-- Messages -->
      <div class="tb-dropdown-wrap">
        <button class="tb-btn hide-sm" title="Messages" onclick="toggleDropdown('dd-messages', event)">
          <i class="fas fa-envelope"></i>
          <?php if ($unreadMsgCount > 0): ?><span class="tb-dot"></span><?php endif; ?>
        </button>
        <div class="tb-dropdown" id="dd-messages">
          <div class="dd-header">
            <h4>Messages</h4>
            <span class="badge bg-soft-brand"><?php echo $unreadMsgCount; ?> Unread</span>
          </div>
          <div class="dd-body">
            <?php if (empty($recentMessages)): ?>
              <div class="p-4 text-center text-muted">No messages yet</div>
            <?php else: ?>
              <?php foreach ($recentMessages as $m): ?>
                <a href="admin/index.php?page=messages&id=<?php echo $m['id']; ?>" class="dd-item">
                  <div class="dd-icon"><i class="fas fa-user"></i></div>
                  <div class="dd-content">
                    <div class="dd-title"><?php echo Helpers::e($m['name']); ?></div>
                    <div class="dd-text"><?php echo Helpers::e($m['message']); ?></div>
                    <div class="dd-time"><?php echo date('M d, H:i', strtotime($m['created_at'])); ?></div>
                  </div>
                </a>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
          <div class="dd-footer"><a href="admin/index.php?page=messages">View All Messages</a></div>
        </div>
      </div>

      <!-- Profile -->
      <div class="tb-dropdown-wrap">
        <div class="tb-avatar" onclick="toggleDropdown('dd-profile', event)">
          <?php if ($adminAvatar && file_exists(__DIR__.'/../'.$adminAvatar)): ?>
            <img src="../<?php echo Helpers::e($adminAvatar); ?>" style="width:100%;height:100%;border-radius:50%;object-fit:cover">
          <?php else: ?>
            <?php echo Helpers::e($adminInitials); ?>
          <?php endif; ?>
        </div>
        <div class="tb-dropdown dd-profile" id="dd-profile">
          <div class="dd-user-info">
            <div class="user-ava-lg">
              <?php if ($adminAvatar && file_exists(__DIR__.'/../'.$adminAvatar)): ?>
                <img src="../<?php echo Helpers::e($adminAvatar); ?>" style="width:100%;height:100%;border-radius:50%;object-fit:cover">
              <?php else: ?>
                <?php echo Helpers::e($adminInitials); ?>
              <?php endif; ?>
            </div>
            <div class="user-name"><?php echo Helpers::e($adminName); ?></div>
            <div class="user-role"><?php echo Helpers::e(strtoupper($adminRole)); ?></div>
          </div>
          <div class="dd-body p-2">
            <a href="#" class="dd-link" onclick="showPage('profile', this)"><i class="fas fa-user-circle"></i> My Profile</a>
            <a href="#" class="dd-link" onclick="showPage('settings', this)"><i class="fas fa-cog"></i> Account Settings</a>
            <div class="border-top my-1"></div>
            <a href="<?php echo Helpers::e(Helpers::adminUrl("logout.php")); ?>" class="dd-link danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- ════════════════════════════════════════════
       DASHBOARD
  ════════════════════════════════════════════ -->
  <div class="content active" id="page-dashboard">

    <div class="stats-grid">
      <div class="stat-card t1">
        <div class="stat-top">
          <div class="stat-icon-wrap"><i class="fas fa-dollar-sign"></i></div>
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
      </div>
    </div>

    <div class="charts-row">
      <div class="card">
        <div class="card-hd">
          <div class="card-hd-left">
            <div class="card-title">Donation Overview</div>
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
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-hd">
          <div class="card-hd-left">
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
        </div>
      </div>
    </div>

    <div class="two-col">
      <div class="card">
        <div class="card-hd">
          <div class="card-hd-left">
            <div class="card-title">Recent Donations</div>
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
      </div>

      <div class="card">
        <div class="card-hd">
          <div class="card-hd-left">
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
      </div>
    </div>

  </div>

  <!-- ════════════════════════════════════════════
       DONATIONS
  ════════════════════════════════════════════ -->
  <div class="content" id="page-donations">
    <div class="stats-grid">
      <div class="stat-card t1">
        <div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-money-bill-wave"></i></div><span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i>All time</span></div>
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
      </div>
    </div>

    <div class="card">
      <form class="toolbar" method="GET" action="index.php">
        <input type="hidden" name="page" value="donations">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input name="search" placeholder="Search donors or refs…" value="<?php echo Helpers::e($_GET['search'] ?? ''); ?>"/>
        </div>
        
        <select name="status" class="filter-select" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="successful" <?php echo ($_GET['status'] ?? '') === 'successful' ? 'selected' : ''; ?>>Successful</option>
            <option value="pending" <?php echo ($_GET['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="failed" <?php echo ($_GET['status'] ?? '') === 'failed' ? 'selected' : ''; ?>>Failed</option>
        </select>

        <select name="gateway" class="filter-select" onchange="this.form.submit()">
            <option value="">All Gateways</option>
            <option value="paystack" <?php echo ($_GET['gateway'] ?? '') === 'paystack' ? 'selected' : ''; ?>>Paystack</option>
            <option value="manual" <?php echo ($_GET['gateway'] ?? '') === 'manual' ? 'selected' : ''; ?>>Manual</option>
        </select>

        <div class="action-btns ml-auto" style="display: flex; gap: 8px;">
            <a href="export_donations.php?<?php echo http_build_query($_GET); ?>" class="btn-primary" style="background: #059669; padding: 10px 18px; border-radius: 10px; text-decoration: none; color: white; display: flex; align-items: center; gap: 7px; font-weight: 600; font-size: 0.85rem;">
                <i class="fas fa-file-csv"></i> Export CSV
            </a>
            <button type="button" onclick="window.print()" class="btn-primary" style="background: #4b5563; padding: 10px 18px; border-radius: 10px; color: white; border: none; display: flex; align-items: center; gap: 7px; font-weight: 600; font-size: 0.85rem;">
                <i class="fas fa-print"></i> Print PDF
            </button>
        </div>
      </form>
      <div style="overflow-x:auto">
        <table class="data-table">
          <thead><tr><th>Donor</th><th>Amount</th><th>Gateway</th><th>Reference</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
            <?php
            $fStatus = $_GET['status'] ?? '';
            $fGateway = $_GET['gateway'] ?? '';
            $fSearch = $_GET['search'] ?? '';
            
            $sql = "SELECT id, donor_name, donor_email, amount, currency, gateway, payment_reference, status, COALESCE(paid_at,created_at) AS dt FROM donations WHERE 1=1";
            $p = [];
            
            if ($fStatus) { $sql .= " AND status = :st"; $p['st'] = $fStatus; }
            if ($fGateway) { $sql .= " AND gateway = :gw"; $p['gw'] = $fGateway; }
            if ($fSearch) { 
                $sql .= " AND (donor_name LIKE :s OR payment_reference LIKE :s OR donor_email LIKE :s)"; 
                $p['s'] = "%$fSearch%"; 
            }
            
            $sql .= " ORDER BY dt DESC LIMIT 50";
            $allDonations = $dbAvail ? (Database::fetchAll($sql, $p) ?: []) : [];
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
                <td data-label="Actions"><div class="action-btns"><button class="action-btn view" onclick="openModal('donation',<?php echo (int)$d['id']; ?>)"><i class="fas fa-eye"></i></button><button class="action-btn edit" onclick="openModal('donation',<?php echo (int)$d['id']; ?>)"><i class="fas fa-pen"></i></button></div></td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="7"><div class="empty-state"><i class="fas fa-inbox"></i><p>No donation records</p></div></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      
      <!-- Professional Print-Only Statement -->
      <div id="print-statement">
          <div class="print-header">
              <div>
                  <h1 class="print-title">Donation Statement</h1>
                  <p style="margin:5px 0 0; color:#666;"><?php echo Helpers::e($siteName); ?></p>
              </div>
              <img src="../<?php echo Helpers::e($adminBrandLogo); ?>" class="print-logo" alt="Logo">
          </div>

          <div class="print-summary">
              <div class="summary-item">
                  <div class="summary-label">Report Period</div>
                  <div class="summary-value"><?php echo date('M j, Y'); ?></div>
              </div>
              <div class="summary-item">
                  <div class="summary-label">Total Transactions</div>
                  <div class="summary-value"><?php echo count($allDonations); ?> Record(s)</div>
              </div>
              <div class="summary-item">
                  <div class="summary-label">Total Amount</div>
                  <div class="summary-value">
                      <?php 
                        $sum = array_sum(array_column($allDonations, 'amount'));
                        echo Helpers::e(Helpers::fmt($sum)); 
                      ?>
                  </div>
              </div>
              <div class="summary-item">
                  <div class="summary-label">Generated By</div>
                  <div class="summary-value"><?php echo Helpers::e($adminName); ?></div>
              </div>
          </div>

          <table class="print-table">
              <thead>
                  <tr>
                      <th>Date</th>
                      <th>Donor Details</th>
                      <th>Reference</th>
                      <th>Gateway</th>
                      <th>Status</th>
                      <th style="text-align:right">Amount</th>
                  </tr>
              </thead>
              <tbody>
                  <?php foreach ($allDonations as $d): ?>
                  <tr>
                      <td><?php echo date('d M Y, H:i', strtotime($d['dt'])); ?></td>
                      <td>
                          <strong><?php echo Helpers::e($d['donor_name']); ?></strong><br>
                          <small><?php echo Helpers::e($d['donor_email'] ?? '—'); ?></small>
                      </td>
                      <td><?php echo Helpers::e($d['payment_reference']); ?></td>
                      <td><?php echo Helpers::e(ucfirst($d['gateway'])); ?></td>
                      <td><?php echo Helpers::e(ucfirst($d['status'])); ?></td>
                      <td style="text-align:right"><strong><?php echo Helpers::e(Helpers::fmt((float)$d['amount'])); ?></strong></td>
                  </tr>
                  <?php endforeach; ?>
              </tbody>
          </table>

          <div class="print-footer">
              This is a computer-generated donation statement. Generated on <?php echo date('Y-m-d H:i:s'); ?>.
          </div>
      </div>

      <div class="pagination">
        <span class="page-info">Showing <?php echo Helpers::e(min(count($allDonations), 20)); ?> of <?php echo Helpers::e($totalTxCount); ?> entries</span>
        <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
        <button class="page-btn on">1</button>
        <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
      </div>
    </div>
  </div>

  <!-- ════════════════════════════════════════════
       USERS
  ════════════════════════════════════════════ -->
  <div class="content" id="page-users">
    <div class="stats-grid">
      <div class="stat-card t1"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-users"></i></div><span class="stat-trend up"><i class="fas fa-arrow-trend-up"></i>Total</span></div><div class="stat-value"><?php echo Helpers::e($totalAdmins); ?></div><div class="stat-label">Total Admin Users</div></div>
      <div class="stat-card t4"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-user-check"></i></div><span class="stat-trend up"><?php echo Helpers::e($totalAdmins > 0 ? round(($activeAdmins/$totalAdmins)*100) : 0); ?>%</span></div><div class="stat-value"><?php echo Helpers::e($activeAdmins); ?></div><div class="stat-label">Active</div></div>
      <div class="stat-card t2"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-user-clock"></i></div><span class="stat-trend neutral">Inactive</span></div><div class="stat-value"><?php echo Helpers::e(max(0, $totalAdmins - $activeAdmins - $suspendedAdmins)); ?></div><div class="stat-label">Inactive</div></div>
      <div class="stat-card t3"><div class="stat-top"><div class="stat-icon-wrap"><i class="fas fa-user-slash"></i></div><span class="stat-trend down"><i class="fas fa-arrow-down"></i><?php echo Helpers::e($suspendedAdmins); ?></span></div><div class="stat-value"><?php echo Helpers::e($suspendedAdmins); ?></div><div class="stat-label">Suspended</div></div>
    </div>
    <div class="card">
      <div class="toolbar">
        <div class="search-box"><i class="fas fa-search"></i><input placeholder="Search users…"/></div>
        <button class="filter-btn"><i class="fas fa-tag"></i> Role</button>
        <button class="filter-btn"><i class="fas fa-circle-half-stroke"></i> Status</button>
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
  <div class="content" id="page-gallery">
    <div class="section-hd">
      <div><h2>Media Gallery</h2><p>Photos and videos from our programs</p></div>
      <div style="display:flex;gap:8px">
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
    </div>
    <div class="security-grid">
      <div class="card">
        <div class="card-hd">
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
      </div>

      <div class="card">
        <div class="card-hd">
          <div class="card-title"><i class="fas fa-display" style="color:var(--brand);margin-right:6px"></i>System Health</div>
          <span class="badge success"><i class="fas fa-circle-check"></i>All Systems Go</span>
        </div>
        <div class="sys-grid">
          <div class="sys-stat"><div class="sys-val"><?php echo Helpers::e(round(memory_get_usage(true) / 1024 / 1024, 1)); ?> MB</div><div class="sys-lbl">PHP Memory</div></div>
          <div class="sys-stat"><div class="sys-val"><?php echo Helpers::e(phpversion()); ?></div><div class="sys-lbl">PHP Version</div></div>
          <div class="sys-stat"><div class="sys-val"><?php echo Helpers::e($dbAvail ? "Connected" : "Offline"); ?></div><div class="sys-lbl">Database</div></div>
          <div class="sys-stat"><div class="sys-val">v3.5.0</div><div class="sys-lbl">App Version</div></div>
        </div>
        <div style="margin-top:16px;padding:13px;background:var(--brand-bg);border-radius:9px;border:1px solid var(--brand-dim)">
          <div style="font-size:.8rem;font-weight:700;color:var(--brand);margin-bottom:3px"><i class="fas fa-lock" style="margin-right:5px"></i>Security Status</div>
          <div style="font-size:.75rem;color:var(--mid)">All admin accounts are active. <?php echo Helpers::e($totalAdmins); ?> total administrators.</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ════════════════════════════════════════════
       SETTINGS
  ════════════════════════════════════════════ -->
  <div class="content" id="page-settings">
    <div class="two-col">
      <div style="display:flex;flex-direction:column;gap:18px">
        <div class="card">
          <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="_csrf_token" value="<?php echo Helpers::e($_SESSION["_csrf_token"] ?? ""); ?>">
            <input type="hidden" name="_action" value="save_branding_settings">
            <input type="hidden" name="_page" value="settings">
            <div class="card-hd">
              <div class="card-hd-left"><div class="card-title"><i class="fas fa-building" style="margin-right:6px;color:var(--muted)"></i>Organization Profile</div></div>
              <button class="btn-primary" type="submit"><i class="fas fa-floppy-disk"></i> Save</button>
            </div>
            <div class="form-field">
              <label class="form-label">Organization Name</label>
              <input class="form-input" name="site_name" value="<?php echo Helpers::e($settings["site_name"] ?? "Gracious Charity"); ?>"/>
            </div>
            <div class="form-field">
              <label class="form-label">Contact Email</label>
              <input class="form-input" name="contact_email" type="email" value="<?php echo Helpers::e($settings["contact_email"] ?? $adminEmail); ?>"/>
            </div>
            <div class="form-field">
              <label class="form-label">Phone</label>
              <input class="form-input" name="contact_phone" value="<?php echo Helpers::e($settings["contact_phone"] ?? "+234 800 000 0000"); ?>"/>
            </div>
            <div class="form-field">
              <label class="form-label">Current Logo</label>
              <div style="padding:14px;border:1px solid var(--border);border-radius:12px;background:#101726;display:flex;align-items:center;justify-content:center;min-height:100px">
                <?php 
                  // FIXED: Use realpath for proper path resolution
                  $logoFullPath = realpath(__DIR__ . '/../') . '/' . $adminBrandLogo;
                  $logoExists = $adminBrandLogo && file_exists($logoFullPath);
                  
                  if ($logoExists): 
                ?>
                  <img src="<?php echo Helpers::e($adminBrandLogo); ?>" alt="<?php echo Helpers::e($siteName); ?>" style="max-width:220px;max-height:72px;width:auto;height:auto;display:block">
                <?php else: ?>
                  <span style="color:var(--soft)">No logo uploaded</span>
                <?php endif; ?>
              </div>
            </div>
            <div class="form-field">
              <label class="form-label">Upload Logo (SVG, PNG, JPG, GIF, WebP)</label>
              <input class="form-input" name="site_logo" type="file" accept=".svg,.png,.jpg,.jpeg,.gif,.webp"/>
              <div style="font-size:.75rem;color:var(--soft);margin-top:6px">Recommended: Transparent SVG or PNG for the full platform header and admin sidebar. Max 2MB.</div>
            </div>
            <div class="form-field">
              <label class="form-label">Current Favicon</label>
              <div style="padding:14px;border:1px solid var(--border);border-radius:12px;background:var(--surface);display:flex;align-items:center;gap:12px">
                <?php 
                  // FIXED: Use realpath for proper path resolution
                  $faviconFullPath = realpath(__DIR__ . '/../') . '/' . $adminFavicon;
                  $faviconExists = $adminFavicon && file_exists($faviconFullPath);
                  
                  if ($faviconExists): 
                ?>
                  <img src="<?php echo Helpers::e($adminFavicon); ?>" alt="Favicon" style="width:32px;height:32px;object-fit:contain">
                  <span style="font-size:.8rem;color:var(--muted)">Current favicon in use</span>
                <?php else: ?>
                  <span style="font-size:.8rem;color:var(--soft)">No favicon uploaded</span>
                <?php endif; ?>
              </div>
            </div>
            <div class="form-field">
              <label class="form-label">Upload Favicon (ICO, PNG, SVG)</label>
              <input class="form-input" name="site_favicon" type="file" accept=".ico,.png,.svg"/>
              <div style="font-size:.75rem;color:var(--soft);margin-top:6px">Recommended: Square ICO or PNG, at least 64x64. Max 1MB.</div>
            </div>
          </form>
        </div>
        <div class="card">
          <div class="card-hd"><div class="card-title"><i class="fas fa-credit-card" style="margin-right:6px;color:var(--muted)"></i>Payment Gateways</div></div>
          <div class="gateway-row">
            <div class="gw-left"><div class="gw-icon"><i class="fas fa-bolt" style="color:var(--brand)"></i></div><div><div class="gw-name">Paystack</div><div class="gw-desc">West Africa payments</div></div></div>
            <span class="badge <?php echo Helpers::e(($settings["paystack_public_key"] ?? "") ? "success" : "warning"); ?>"><i class="fas fa-<?php echo Helpers::e(($settings["paystack_public_key"] ?? "") ? "plug" : "clock"); ?>"></i><?php echo Helpers::e(($settings["paystack_public_key"] ?? "") ? "Connected" : "Not configured"); ?></span>
          </div>
          <div class="gateway-row">
            <div class="gw-left"><div class="gw-icon"><i class="fab fa-stripe-s" style="color:#6772e5"></i></div><div><div class="gw-name">Stripe</div><div class="gw-desc">International cards</div></div></div>
            <span class="badge <?php echo Helpers::e(($settings["stripe_public_key"] ?? "") ? "success" : "warning"); ?>"><i class="fas fa-<?php echo Helpers::e(($settings["stripe_public_key"] ?? "") ? "plug" : "clock"); ?>"></i><?php echo Helpers::e(($settings["stripe_public_key"] ?? "") ? "Connected" : "Not configured"); ?></span>
          </div>
        </div>
      </div>

      <div style="display:flex;flex-direction:column;gap:18px">
        <div class="card">
          <div class="card-hd"><div class="card-title"><i class="fas fa-bell" style="margin-right:6px;color:var(--muted)"></i>Notification Preferences</div></div>
          <div class="notif-row">
            <div><div class="notif-label">New Donation Alerts</div><div class="notif-desc">Get notified for every donation</div></div>
            <div class="toggle-switch"></div>
          </div>
          <div class="notif-row">
            <div><div class="notif-label">Security Alerts</div><div class="notif-desc">Suspicious activity warnings</div></div>
            <div class="toggle-switch"></div>
          </div>
          <div class="notif-row">
            <div><div class="notif-label">Weekly Reports</div><div class="notif-desc">Email digest every Monday</div></div>
            <div class="toggle-switch off"></div>
          </div>
        </div>
        <div class="card">
          <div class="card-hd"><div class="card-title"><i class="fas fa-user-circle" style="margin-right:6px;color:var(--muted)"></i>Account</div></div>
          <div class="form-field"><label class="form-label">Your Name</label><input class="form-input" value="<?php echo Helpers::e($adminName); ?>"/></div>
          <div class="form-field"><label class="form-label">Email</label><input class="form-input" value="<?php echo Helpers::e($adminEmail); ?>"/></div>
          <div class="form-field"><label class="form-label">Role</label><input class="form-input" value="<?php echo Helpers::e(ucwords(str_replace("_", " ", $adminRole))); ?>" disabled style="opacity:.6"/></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ════════════════════════════════════════════
       PROFILE
  ════════════════════════════════════════════ -->
  <div class="content" id="page-profile">
    <div class="card profile-card" style="max-width:650px;margin:0 auto">
      <div class="profile-cover"></div>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="_csrf_token" value="<?php echo Helpers::e($_SESSION["_csrf_token"] ?? ""); ?>">
        <input type="hidden" name="_action" value="update_profile">
        <input type="hidden" name="_page" value="profile">
        
        <div class="profile-avatar-container">
          <div class="profile-avatar-wrap" onclick="document.getElementById('avatar-input').click()" title="Click to change avatar">
            <?php if ($adminAvatar && file_exists(__DIR__.'/../'.$adminAvatar)): ?>
              <img src="../<?php echo Helpers::e($adminAvatar); ?>" id="avatar-preview">
            <?php else: ?>
              <div class="initials" id="avatar-initials"><?php echo Helpers::e($adminInitials); ?></div>
              <img id="avatar-preview" style="display:none">
            <?php endif; ?>
            <div class="profile-avatar-overlay"><i class="fas fa-camera"></i></div>
            <input type="file" name="avatar" id="avatar-input" style="display:none" accept="image/*" onchange="previewAvatar(this)">
          </div>
          <div class="profile-info">
            <h3><?php echo Helpers::e($adminName); ?></h3>
            <p><?php echo Helpers::e(str_replace('_', ' ', $adminRole)); ?></p>
          </div>
        </div>

        <div class="profile-body">
          <div class="form-row">
            <div class="form-group">
              <label>Full Name</label>
              <input type="text" name="full_name" class="form-control" value="<?php echo Helpers::e($adminName); ?>" required>
            </div>
            <div class="form-group">
              <label>Email Address</label>
              <input type="email" name="email" class="form-control" value="<?php echo Helpers::e($adminEmail); ?>" required>
            </div>
          </div>

          <div class="form-group" style="margin-top:14px">
            <label>New Password (Leave blank to keep current)</label>
            <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters">
          </div>

          <div style="margin-top:28px;display:flex;justify-content:flex-end">
            <button type="submit" class="btn-primary" style="padding:10px 24px"><i class="fas fa-floppy-disk"></i> Save Changes</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- ════════════════════════════════════════════
       MESSAGES
  ════════════════════════════════════════════ -->
  <div class="content" id="page-messages">
    <div class="section-hd">
      <div><h2>Inbound Messages</h2><p>Contact form inquiries from the website</p></div>
    </div>
    <div class="card">
      <div class="toolbar">
        <div class="search-box"><i class="fas fa-search"></i><input placeholder="Search messages…"/></div>
        <button class="filter-btn"><i class="fas fa-circle-half-stroke"></i> Status</button>
      </div>
      <div style="overflow-x:auto">
        <table class="data-table">
          <thead><tr><th>Sender</th><th>Subject</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
            <?php if (!empty($recentMessages)): ?>
              <?php foreach ($recentMessages as $m): ?>
              <tr>
                <td data-label="Sender">
                  <div class="cell-user">
                    <div class="cell-ava" style="background:var(--brand-bg);color:var(--brand)"><?php echo Helpers::e(substr($m['name'] ?? 'U', 0, 1)); ?></div>
                    <div><span class="cell-name"><?php echo Helpers::e($m['name'] ?? 'Unknown'); ?></span><span class="cell-sub"><?php echo Helpers::e($m['email'] ?? ''); ?></span></div>
                  </div>
                </td>
                <td data-label="Subject"><strong><?php echo Helpers::e($m['subject'] ?? 'No Subject'); ?></strong><div class="cell-sub"><?php echo Helpers::e(substr($m['message'] ?? '', 0, 40)); ?>…</div></td>
                <td data-label="Date" class="mono"><?php echo Helpers::e(Helpers::ta($m['created_at'] ?? '')); ?></td>
                <td data-label="Status"><span class="badge <?php echo ($m['status'] ?? '') === 'unread' ? 'warning' : 'success'; ?>"><?php echo ucfirst($m['status'] ?? 'read'); ?></span></td>
                <td data-label="Actions">
                  <div class="action-btns">
                    <button class="action-btn view" title="Read Message"><i class="fas fa-eye"></i></button>
                    <button class="action-btn del" title="Delete"><i class="fas fa-trash"></i></button>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="5"><div class="empty-state"><i class="fas fa-envelope-open"></i><p>No messages yet</p></div></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div><!-- /main -->
</div><!-- /app -->

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
document.title = <?php echo json_encode($siteName . " - Admin Dashboard"); ?>;
(function () {
  const href = <?php echo json_encode($adminFavicon ?? 'assets/images/favicon.ico'); ?>;
  let icon = document.querySelector('link[rel="shortcut icon"]');
  if (!icon) {
    icon = document.createElement('link');
    icon.rel = 'shortcut icon';
    document.head.appendChild(icon);
  }
  icon.href = href;
})();
const CSRF_TOKEN = '<?php echo $_SESSION["_csrf_token"] ?? ""; ?>';
const PAGES = {
  dashboard:'Dashboard',donations:'Donations',users:'Users',
  programmes:'Programmes',partners:'Partners',blog:'Blog & News',
  events:'Events',gallery:'Gallery',security:'Security',settings:'Settings',
  profile:'My Profile',messages:'Messages'
};

function previewAvatar(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      const preview = document.getElementById('avatar-preview');
      const initials = document.getElementById('avatar-initials');
      if (preview) {
        preview.src = e.target.result;
        preview.style.display = 'block';
      }
      if (initials) initials.style.display = 'none';
    }
    reader.readAsDataURL(input.files[0]);
  }
}

function showPage(id, el) {
  const contentAreas = document.querySelectorAll('.content');
  const navItems = document.querySelectorAll('.nav-item');
  const pg = document.getElementById('page-' + id);
  
  if (!pg) return;

  // Clear all and deactivate
  contentAreas.forEach(c => {
    c.classList.remove('active');
    c.style.opacity = '';
    c.style.transform = '';
  });

  // Activate target
  pg.classList.add('active');

  // Nav highlights
  navItems.forEach(n => n.classList.remove('active'));
  if (el) el.classList.add('active');
  else {
    document.querySelectorAll('.nav-item').forEach(n => {
      if (n.getAttribute('onclick') && n.getAttribute('onclick').includes("'"+id+"'")) n.classList.add('active');
    });
  }

  // Update Page Title & Breadcrumbs
  const titleEl = document.getElementById('pageTitle');
  const breadEl = document.getElementById('breadSub');
  
  if (titleEl) titleEl.textContent = PAGES[id] || id;
  if (breadEl) breadEl.textContent = PAGES[id] || id;

  // Persist current page in URL
  const url = new URL(window.location);
  if (url.searchParams.get('page') !== id) {
    url.searchParams.set('page', id);
    window.history.replaceState({page: id}, '', url);
  }
  
  if (window.innerWidth < 1024) closeMobile();
}

let isCollapsed = false;
let mobileOpen = false;
let editId = 0;

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
  },
  donation: {
    title: 'Donation',
    action: 'update_donation',
    fields: [
      {name:'_action',type:'hidden'},
      {name:'id',type:'hidden'},
      {name:'donor_name',label:'Donor Name',type:'text',required:true},
      {name:'donor_email',label:'Email',type:'email',required:true},
      {name:'amount',label:'Amount',type:'number',required:true},
      {name:'currency',label:'Currency',type:'text',required:true},
      {name:'gateway',label:'Gateway',type:'text',required:true},
      {name:'payment_reference',label:'Reference',type:'text',required:true},
      {name:'status',label:'Status',type:'select',options:['pending','successful','failed','refunded']},
      {name:'paid_at',label:'Paid At',type:'datetime-local'},
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

// Tabs
document.querySelectorAll('.tabs').forEach(group => {
  group.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      group.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('on'));
      btn.classList.add('on');
    });
  });
});

// Toggle switches
document.querySelectorAll('.toggle-switch').forEach(sw => {
  sw.addEventListener('click', () => sw.classList.toggle('off'));
});

// Dropdown Toggle (Professional)
function toggleDropdown(id, event) {
  if (event) event.stopPropagation();
  const el = document.getElementById(id);
  const isOpen = el.classList.contains('active');
  
  // Close all other dropdowns
  document.querySelectorAll('.tb-dropdown').forEach(dd => dd.classList.remove('active'));
  
  // Toggle current
  if (!isOpen) el.classList.add('active');
}

// Close dropdowns on outside click
document.addEventListener('click', () => {
  document.querySelectorAll('.tb-dropdown').forEach(dd => dd.classList.remove('active'));
});

// Prevent dropdown close when clicking inside it
document.querySelectorAll('.tb-dropdown').forEach(dd => {
  dd.addEventListener('click', (e) => e.stopPropagation());
});

// Resize handler
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
