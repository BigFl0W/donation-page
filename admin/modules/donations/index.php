<?php

declare(strict_types=1);
?>
<div class="admin-topbar">
    <div>
        <h2>Donations & Gateways</h2>
        <p>Centralize donations from Paystack and Stripe with statuses, donor data, and reconciliation notes.</p>
    </div>
    <div class="admin-actions">
        <a class="admin-btn primary" href="#">Export Report</a>
    </div>
</div>

<section class="admin-grid-3 mb-4">
    <div class="admin-card">
        <div class="label">Paystack Volume</div>
        <div class="value">NGN 3.2M</div>
        <p class="meta">38 successful transactions</p>
    </div>
    <div class="admin-card">
        <div class="label">Stripe Volume</div>
        <div class="value">USD 5.9K</div>
        <p class="meta">11 successful transactions</p>
    </div>
    <div class="admin-card">
        <div class="label">Pending Reviews</div>
        <div class="value">5</div>
        <p class="meta">Awaiting gateway confirmation or manual audit</p>
    </div>
</section>

<section class="admin-grid-2">
    <div class="admin-table-card">
        <div class="admin-section-title">
            <h3>Latest Gateway Activity</h3>
        </div>
        <table class="admin-table">
            <thead>
            <tr>
                <th>Reference</th>
                <th>Gateway</th>
                <th>Donor</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>PSTK-240517</td>
                <td>Paystack</td>
                <td>Mary A.</td>
                <td>NGN 50,000</td>
                <td><span class="admin-badge success">Successful</span></td>
            </tr>
            <tr>
                <td>STRP-90931</td>
                <td>Stripe</td>
                <td>Helping Hands Org</td>
                <td>USD 1,200</td>
                <td><span class="admin-badge success">Successful</span></td>
            </tr>
            <tr>
                <td>PSTK-240518</td>
                <td>Paystack</td>
                <td>Anonymous</td>
                <td>NGN 15,000</td>
                <td><span class="admin-badge warning">Pending</span></td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="admin-note-card">
        <h3>Gateway Integration Notes</h3>
        <ul>
            <li>Store each payment reference, webhook payload, and verification status.</li>
            <li>Keep donor, campaign, gateway fee, and currency data in dedicated columns.</li>
            <li>Use one donations table plus one payment_transactions table for auditability.</li>
            <li>Add webhook handlers later under a clean `public/webhooks/` or `api/webhooks/` folder.</li>
        </ul>
    </div>
</section>
