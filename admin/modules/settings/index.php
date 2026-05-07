<?php

declare(strict_types=1);
?>
<div class="admin-topbar">
    <div>
        <h2>Settings</h2>
        <p>Control platform identity, payment keys, contact details, and operational preferences.</p>
    </div>
</div>

<section class="admin-grid-2">
    <div class="admin-table-card">
        <div class="admin-section-title">
            <h3>Core Settings Areas</h3>
        </div>
        <table class="admin-table">
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
                <td>Access Control</td>
                <td>Password policy, session duration, admin role defaults</td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="admin-note-card">
        <h3>Scaffold Priorities</h3>
        <ul>
            <li>Move hardcoded phone, email, and social links into settings records.</li>
            <li>Load homepage hero content and CTA labels from content blocks.</li>
            <li>Store gateway credentials outside version control in environment variables.</li>
            <li>Log critical admin actions for traceability and rollback support.</li>
        </ul>
    </div>
</section>
