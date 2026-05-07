<?php

declare(strict_types=1);
?>
<div class="admin-topbar">
    <div>
        <h2>Site Settings</h2>
        <p>Manage brand identity, SEO defaults, tracking, and platform-wide operational configuration.</p>
    </div>
</div>

<div class="admin-workspace-grid">
    <div class="admin-workspace-main">
        <section class="admin-table-card">
            <div class="admin-section-title">
                <h3>Core Settings Areas</h3>
            </div>
            <table class="admin-table admin-table-clean">
                <thead>
                <tr>
                    <th>Setting Group</th>
                    <th>Purpose</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Branding</td>
                    <td>Logo, site title, primary colors, footer identity</td>
                </tr>
                <tr>
                    <td>Contact Details</td>
                    <td>Phone, email, address, office hours, social profiles</td>
                </tr>
                <tr>
                    <td>Payment Gateways</td>
                    <td>Paystack keys, Stripe keys, webhook secrets, environment mode</td>
                </tr>
                <tr>
                    <td>SEO & Tracking</td>
                    <td>Meta defaults, analytics, and verification scripts</td>
                </tr>
                <tr>
                    <td>Access Control</td>
                    <td>Password policy, session duration, admin role defaults</td>
                </tr>
                </tbody>
            </table>
        </section>
    </div>
    <aside class="admin-workspace-side">
        <section class="admin-panel">
            <div class="admin-panel-head">
                <div><h3>Configuration Notes</h3></div>
            </div>
            <ul class="admin-plain-list">
                <li><div><strong>Environment values</strong><span>Keep gateway credentials and sensitive settings outside version control.</span></div></li>
                <li><div><strong>SEO defaults</strong><span>Store page-level titles and descriptions in a managed settings layer.</span></div></li>
                <li><div><strong>Audit trail</strong><span>Log critical admin actions for traceability and rollback support.</span></div></li>
            </ul>
        </section>
    </aside>
</div>
