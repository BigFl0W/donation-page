<?php

declare(strict_types=1);

$contentAreas = [
    ["name" => "Homepage", "owner" => "Landing content", "status" => "Ready"],
    ["name" => "Photo & Video Gallery", "owner" => "Explore media", "status" => "Ready"],
    ["name" => "Partners & Sponsors", "owner" => "Explore partners", "status" => "Ready"],
    ["name" => "Projects & Programmes", "owner" => "Explore programmes", "status" => "Ready"],
    ["name" => "FAQs", "owner" => "Explore FAQs", "status" => "Ready"],
    ["name" => "About & Contact", "owner" => "Core pages", "status" => "Planned"],
];
?>
<div class="admin-topbar">
    <div>
        <h2>Content Management</h2>
        <p>Keep all public-facing sections editable from one admin area.</p>
    </div>
    <div class="admin-actions">
        <a class="admin-btn primary" href="#">Create Section</a>
    </div>
</div>

<section class="admin-grid-2">
    <div class="admin-table-card">
        <div class="admin-section-title">
            <h3>Editable Content Areas</h3>
        </div>
        <table class="admin-table">
            <thead>
            <tr>
                <th>Section</th>
                <th>Purpose</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($contentAreas as $item): ?>
                <tr>
                    <td><?php echo e($item["name"]); ?></td>
                    <td><?php echo e($item["owner"]); ?></td>
                    <td><span class="admin-badge <?php echo $item["status"] === "Ready" ? "success" : "info"; ?>"><?php echo e($item["status"]); ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="admin-note-card">
        <h3>Recommended Content Tables</h3>
        <ul>
            <li>`pages` for high-level page metadata and status.</li>
            <li>`content_blocks` for hero text, CTA labels, and reusable sections.</li>
            <li>`gallery_items`, `partners`, `programmes`, and `faqs` for Explore content.</li>
            <li>`media_assets` if you later want uploads tracked outside inline paths.</li>
        </ul>
    </div>
</section>
