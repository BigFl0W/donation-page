<?php

declare(strict_types=1);
?>
<div class="admin-topbar">
    <div>
        <h2>Donations</h2>
        <p>Track donor activity, payment channels, and reconciliation signals from one finance workspace.</p>
    </div>
    <div class="admin-actions">
        <a class="admin-btn primary" href="#">Export Report</a>
    </div>
</div>

<section class="admin-summary-grid mb-4">
    <div class="admin-summary-metric">
        <div class="label">Paystack Volume</div>
        <div class="value">NGN 3.2M</div>
        <p class="meta">38 successful transactions</p>
    </div>
    <div class="admin-summary-metric">
        <div class="label">Stripe Volume</div>
        <div class="value">USD 5.9K</div>
        <p class="meta">11 successful transactions</p>
    </div>
    <div class="admin-summary-metric">
        <div class="label">Pending Reviews</div>
        <div class="value">5</div>
        <p class="meta">Awaiting gateway confirmation or manual audit</p>
    </div>
</section>

<div class="admin-workspace-grid">
    <div class="admin-workspace-main">
        <div class="admin-table-card">
            <div class="admin-section-title">
                <h3>Latest Gateway Activity</h3>
            </div>
            <table class="admin-table admin-table-clean">
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
    </div>

    <aside class="admin-workspace-side">
        <div class="admin-panel">
            <div class="admin-panel-head">
                <div><h3>Gateway Notes</h3></div>
            </div>
            <ul class="admin-plain-list">
                <li><div><strong>Auditability</strong><span>Store payment reference, verification result, and webhook payload.</span></div></li>
                <li><div><strong>Donor records</strong><span>Keep donor profile, donation type, and communication history linked.</span></div></li>
                <li><div><strong>Exports</strong><span>Prepare CSV or finance-ready reporting for reconciliation.</span></div></li>
            </ul>
        </div>
    </aside>
 </div>
