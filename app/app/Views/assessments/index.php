<section class="hero">
    <span class="eyebrow">Assessments</span>
    <h1>Assessment analytics and active test history.</h1>
    <p class="muted">Track all attempts, see completion progress, and resume in-progress assessments from a cleaner workspace.</p>
    <div class="actions">
        <a class="button" href="<?= e(url('/assessments/create')) ?>">Start New Assessment</a>
        <a class="button secondary" href="<?= e(url('/terms')) ?>">Review Terms</a>
    </div>
</section>

<section class="grid">
    <article class="metric-card">
        <p class="muted">Total assessments</p>
        <div class="metric-value"><?= e((string) ($analytics['total'] ?? 0)) ?></div>
    </article>
    <article class="metric-card">
        <p class="muted">In progress</p>
        <div class="metric-value"><?= e((string) ($analytics['in_progress'] ?? 0)) ?></div>
    </article>
    <article class="metric-card">
        <p class="muted">Completed</p>
        <div class="metric-value"><?= e((string) ($analytics['completed'] ?? 0)) ?></div>
    </article>
    <article class="metric-card">
        <p class="muted">Average score</p>
        <div class="metric-value"><?= e($analytics['average_score'] !== null ? number_format((float) $analytics['average_score'], 2) . '%' : 'Pending') ?></div>
    </article>
</section>

<section class="card">
    <div class="toolbar">
        <div>
            <h2>Tests taken</h2>
            <p class="muted">Each row includes the current standard, completion progress, and the latest activity time.</p>
        </div>
    </div>

    <div class="table-wrap section">
        <table class="table">
            <thead>
                <tr>
                    <th>Assessment</th>
                    <th>Standard</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Score</th>
                    <th>Last updated</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assessments as $assessment): ?>
                    <?php
                    $questions = (int) ($assessment['questions_count'] ?? 0);
                    $answers = (int) ($assessment['answers_count'] ?? 0);
                    $progress = $questions > 0 ? (int) round(($answers / $questions) * 100) : 0;
                    $statusClass = ($assessment['status'] ?? '') === 'COMPLETED' ? 'success' : 'warning';
                    ?>
                    <tr>
                        <td>
                            <strong><?= e($assessment['title'] ?: 'Untitled assessment') ?></strong><br>
                            <span class="muted"><?= e((string) $assessment['code']) ?></span>
                        </td>
                        <td><?= e((string) $assessment['name']) ?></td>
                        <td><span class="badge-custom <?= e($statusClass) ?>"><?= e((string) $assessment['status']) ?></span></td>
                        <td>
                            <div class="progress"><span style="width: <?= e((string) $progress) ?>%"></span></div>
                            <div class="muted" style="margin-top:8px;"><?= e((string) $answers) ?> of <?= e((string) $questions) ?> answered</div>
                        </td>
                        <td><?= e($assessment['complianceScore'] !== null ? number_format((float) $assessment['complianceScore'], 2) . '%' : 'Pending') ?></td>
                        <td><?= e((string) ($assessment['updatedAt'] ?? $assessment['createdAt'] ?? '')) ?></td>
                        <td><a class="button secondary" href="<?= e(url('/assessments/show?id=' . urlencode((string) $assessment['id']))) ?>"><?= ($assessment['status'] ?? '') === 'COMPLETED' ? 'Review' : 'Resume' ?></a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$assessments): ?>
                    <tr><td colspan="7" class="muted">No assessments found yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
