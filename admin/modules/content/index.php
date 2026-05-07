<?php

declare(strict_types=1);

$contentAreas = [
    ["name" => "Homepage", "owner" => "Landing content", "status" => "Ready"],
    ["name" => "Events", "owner" => "Featured event and upcoming events page", "status" => "Ready"],
    ["name" => "Photo & Video Gallery", "owner" => "Explore media", "status" => "Ready"],
    ["name" => "Partners & Sponsors", "owner" => "Explore partners", "status" => "Ready"],
    ["name" => "Projects & Programmes", "owner" => "Explore programmes", "status" => "Ready"],
    ["name" => "FAQs", "owner" => "Explore FAQs", "status" => "Ready"],
    ["name" => "About & Contact", "owner" => "Core pages", "status" => "Planned"],
];
?>
<div class="admin-topbar">
    <div>
        <h2>Content & Partners</h2>
        <p>Maintain public pages, structured content areas, and partner-facing sections from one workspace.</p>
    </div>
    <div class="admin-actions">
        <a class="admin-btn primary" href="#">New Section</a>
    </div>
</div>

<div class="admin-workspace-grid">
    <div class="admin-workspace-main">
        <section class="admin-panel">
            <div class="admin-panel-head">
                <div>
                    <h3>Content Overview</h3>
                    <p>Current editable sections across the NGO website</p>
                </div>
            </div>
            <div class="admin-summary-grid">
                <div class="admin-summary-metric">
                    <span>Managed Areas</span>
                    <strong><?php echo e((string) count($contentAreas)); ?></strong>
                    <small>Tracked sections</small>
                </div>
                <div class="admin-summary-metric">
                    <span>Ready</span>
                    <strong><?php echo e((string) count(array_filter($contentAreas, static fn(array $item): bool => $item["status"] === "Ready"))); ?></strong>
                    <small>Live and editable</small>
                </div>
                <div class="admin-summary-metric">
                    <span>Planned</span>
                    <strong><?php echo e((string) count(array_filter($contentAreas, static fn(array $item): bool => $item["status"] !== "Ready"))); ?></strong>
                    <small>Pending workflow</small>
                </div>
            </div>
        </section>

        <section class="admin-table-card">
            <div class="admin-section-title">
                <h3>Editable Content Areas</h3>
            </div>
            <table class="admin-table admin-table-clean">
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
        </section>
    </div>

    <aside class="admin-workspace-side">
        <section class="admin-panel">
            <div class="admin-panel-head">
                <div>
                    <h3>Structure Notes</h3>
                </div>
            </div>
            <ul class="admin-plain-list">
                <li><div><strong>Pages</strong><span>High-level page metadata and publishing status.</span></div></li>
                <li><div><strong>Content Blocks</strong><span>Hero text, CTA labels, and reusable sections.</span></div></li>
                <li><div><strong>Partners & Media</strong><span>Gallery items, partners, programmes, and FAQs.</span></div></li>
            </ul>
        </section>
    </aside>
</div>
