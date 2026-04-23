<section class="card">
    <h1>Partner leads</h1>
    <p class="muted">Lead assignment uses the existing `leads.assignedPartnerId` field.</p>

    <table class="table section">
        <thead>
        <tr>
            <th>Company</th>
            <th>Contact</th>
            <th>Status</th>
            <th>Created</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($leads as $lead): ?>
            <tr>
                <td><?= e($lead['companyName']) ?></td>
                <td><?= e($lead['contactEmail']) ?></td>
                <td><?= e($lead['status']) ?></td>
                <td><?= e((string) $lead['createdAt']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$leads): ?>
            <tr><td colspan="4" class="muted">No assigned leads found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</section>
