<section class="card" style="max-width: 980px;">
    <h1>Certification request</h1>
    <p class="muted">This page preserves the same workflow as the original certification request area and stores records in `certification_requests`.</p>

    <?php if (!empty($flash)): ?>
        <div class="notice section"><?= e($flash) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= e(url('/certification/request')) ?>" class="section">
        <div class="grid">
            <div class="form-row">
                <label for="companyName">Company name</label>
                <input id="companyName" name="companyName" required>
            </div>
            <div class="form-row">
                <label for="contactName">Contact name</label>
                <input id="contactName" name="contactName" required>
            </div>
            <div class="form-row">
                <label for="contactEmail">Contact email</label>
                <input id="contactEmail" name="contactEmail" required>
            </div>
            <div class="form-row">
                <label for="contactPhone">Phone</label>
                <input id="contactPhone" name="contactPhone">
            </div>
            <div class="form-row">
                <label for="companySize">Company size</label>
                <input id="companySize" name="companySize">
            </div>
            <div class="form-row">
                <label for="currentStatus">Current status</label>
                <input id="currentStatus" name="currentStatus">
            </div>
        </div>
        <div class="form-row">
            <label for="requirements">Requirements</label>
            <textarea id="requirements" name="requirements" rows="5"></textarea>
        </div>
        <button type="submit">Send request</button>
    </form>

    <div class="section">
        <h2>Your requests</h2>
        <table class="table">
            <thead><tr><th>Company</th><th>Status</th><th>Created</th></tr></thead>
            <tbody>
            <?php foreach ($requests as $request): ?>
                <tr>
                    <td><?= e($request['companyName']) ?></td>
                    <td><?= e($request['status']) ?></td>
                    <td><?= e((string) $request['createdAt']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$requests): ?>
                <tr><td colspan="3" class="muted">No requests found for this account yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
