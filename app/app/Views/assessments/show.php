<?php
$progress = (int) ($assessment['progress_percent'] ?? 0);
$answered = (int) ($assessment['answers_count'] ?? 0);
$totalQuestions = (int) ($assessment['questions_count'] ?? 0);
$totalClauses = is_array($clauses ?? null) ? count($clauses) : 0;
$currentClauseNumber = (int) ($currentClauseIndex ?? 0) + 1;
?>
<section class="hero">
    <span class="eyebrow">Assessment Workspace</span>
    <h1><?= e($assessment['title'] ?: 'Untitled assessment') ?></h1>
    <p class="muted"><?= e($assessment['code']) ?> · <?= e($assessment['name']) ?> · <?= e($assessment['status']) ?></p>
    <div class="grid section">
        <article class="metric-card">
            <p class="muted">Completion</p>
            <div class="metric-value"><?= e((string) $progress) ?>%</div>
            <div class="progress"><span style="width: <?= e((string) $progress) ?>%"></span></div>
        </article>
        <article class="metric-card">
            <p class="muted">Questions answered</p>
            <div class="metric-value"><?= e((string) $answered) ?>/<?= e((string) $totalQuestions) ?></div>
        </article>
        <article class="metric-card">
            <p class="muted">Compliance score</p>
            <div class="metric-value"><?= e($assessment['complianceScore'] !== null ? number_format((float) $assessment['complianceScore'], 2) . '%' : 'Pending') ?></div>
        </article>
        <article class="metric-card">
            <p class="muted">Created</p>
            <div class="metric-value" style="font-size:1.15rem;"><?= e((string) $assessment['createdAt']) ?></div>
        </article>
    </div>
</section>

<div class="actions" style="margin: 0 0 18px;">
    <a class="button secondary" href="<?= e(url('/assessments/export?id=' . urlencode((string) $assessment['id']))) ?>">
        <i class="bi bi-download"></i> Export Results (CSV)
    </a>
</div>

<section class="grid" style="margin-bottom:22px;">
    <article class="metric-card">
        <p class="muted">Required Answered</p>
        <div class="metric-value"><?= e((string) ($resultsSnapshot['required_answered'] ?? 0)) ?>/<?= e((string) ($resultsSnapshot['required_total'] ?? 0)) ?></div>
    </article>
    <article class="metric-card">
        <p class="muted">YES Responses</p>
        <div class="metric-value"><?= e((string) ($resultsSnapshot['yes_count'] ?? 0)) ?></div>
    </article>
    <article class="metric-card">
        <p class="muted">NO Responses</p>
        <div class="metric-value"><?= e((string) ($resultsSnapshot['no_count'] ?? 0)) ?></div>
    </article>
    <article class="metric-card">
        <p class="muted">Average Scale</p>
        <div class="metric-value"><?= ($resultsSnapshot['avg_scale'] ?? null) !== null ? e((string) $resultsSnapshot['avg_scale']) : '—' ?></div>
    </article>
</section>

<section class="card">
    <div class="toolbar">
        <div>
            <h2>Clause <?= e((string) $currentClauseNumber) ?> of <?= e((string) $totalClauses) ?></h2>
            <p class="muted">Each clause is handled on its own page. Required questions in the current clause must be completed before moving on for review.</p>
        </div>
        <div class="badge-custom info">Auto draft enabled</div>
    </div>

    <?php if (!empty($saved)): ?>
        <div class="notice section"><?= e($saved) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="notice section"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($clauses)): ?>
        <div class="actions section">
            <?php foreach ($clauses as $index => $clause): ?>
                <?php
                $requiredTotal = 0;
                $requiredDone = 0;
                foreach ($clause['questions'] as $clauseQuestion) {
                    if (!empty($clauseQuestion['question_required'])) {
                        $requiredTotal++;
                        $value = trim((string) ($clauseQuestion['answer_value'] ?? ''));
                        $text = trim((string) ($clauseQuestion['answer_text'] ?? ''));
                        if ($value !== '' || $text !== '') {
                            $requiredDone++;
                        }
                    }
                }
                ?>
                <a class="<?= $index === $currentClauseIndex ? 'button' : 'button secondary' ?>" href="<?= e(url('/assessments/show?id=' . urlencode((string) $assessment['id']) . '&clause=' . $index)) ?>">
                    <?= e((string) $clause['clause_number']) ?>
                    <?php if ($requiredTotal > 0): ?>
                        (<?= e((string) $requiredDone) ?>/<?= e((string) $requiredTotal) ?>)
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= e(url('/assessments/answers')) ?>" class="stack section" data-validate id="assessmentForm" data-assessment-id="<?= e((string) $assessment['id']) ?>">
        <input type="hidden" name="assessment_id" value="<?= e((string) $assessment['id']) ?>">
        <input type="hidden" name="clause_index" value="<?= e((string) $currentClauseIndex) ?>">
        <?php foreach (($currentClause['questions'] ?? []) as $index => $question): ?>
            <?php
            $options = [];
            if (!empty($question['question_options'])) {
                $decoded = json_decode((string) $question['question_options'], true);
                if (is_array($decoded)) {
                    $options = $decoded;
                }
            }
            $fieldName = 'answers[' . $question['question_id'] . ']';
            ?>
            <article class="card">
                <div class="toolbar">
                    <div>
                        <span class="badge-custom info">Question <?= e((string) ($index + 1)) ?></span>
                        <p class="muted" style="margin-top:10px;">Clause <?= e((string) $question['clause_number']) ?> · <?= e((string) $question['clause_title']) ?></p>
                    </div>
                    <?php if (!empty($question['question_required'])): ?>
                        <span class="badge-custom warning">Required</span>
                    <?php endif; ?>
                </div>
                <h3><?= e((string) $question['question_text']) ?></h3>
                <?php if (!empty($question['question_description'])): ?>
                    <p class="muted"><?= e((string) $question['question_description']) ?></p>
                <?php endif; ?>

                <?php if ($question['question_type'] === 'YES_NO'): ?>
                    <div class="form-row">
                        <label for="question-<?= e((string) $question['question_id']) ?>">Select response</label>
                        <select id="question-<?= e((string) $question['question_id']) ?>" name="<?= e($fieldName) ?>[value]" <?= !empty($question['question_required']) ? 'required' : '' ?>>
                            <option value="">Select</option>
                            <option value="YES" <?= ($question['answer_value'] ?? '') === 'YES' ? 'selected' : '' ?>>Yes</option>
                            <option value="NO" <?= ($question['answer_value'] ?? '') === 'NO' ? 'selected' : '' ?>>No</option>
                        </select>
                    </div>
                <?php elseif ($question['question_type'] === 'SCALE'): ?>
                    <div class="form-row">
                        <label for="question-<?= e((string) $question['question_id']) ?>">Select score</label>
                        <select id="question-<?= e((string) $question['question_id']) ?>" name="<?= e($fieldName) ?>[value]" <?= !empty($question['question_required']) ? 'required' : '' ?>>
                            <option value="">Select</option>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?= $i ?>" <?= (string) ($question['answer_value'] ?? '') === (string) $i ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                <?php elseif ($question['question_type'] === 'MULTIPLE_CHOICE' && $options): ?>
                    <div class="form-row">
                        <label for="question-<?= e((string) $question['question_id']) ?>">Choose one option</label>
                        <select id="question-<?= e((string) $question['question_id']) ?>" name="<?= e($fieldName) ?>[value]" <?= !empty($question['question_required']) ? 'required' : '' ?>>
                            <option value="">Select</option>
                            <?php foreach ($options as $option): ?>
                                <option value="<?= e((string) $option) ?>" <?= (string) ($question['answer_value'] ?? '') === (string) $option ? 'selected' : '' ?>><?= e((string) $option) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php else: ?>
                    <div class="form-row">
                        <label for="question-<?= e((string) $question['question_id']) ?>">Response</label>
                        <textarea id="question-<?= e((string) $question['question_id']) ?>" name="<?= e($fieldName) ?>[text]" rows="4" <?= !empty($question['question_required']) ? 'required' : '' ?>><?= e((string) ($question['answer_text'] ?? $question['answer_value'] ?? '')) ?></textarea>
                    </div>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>

        <?php if (!empty($currentClause['questions'])): ?>
            <div class="actions">
                <button type="submit" name="action" value="save" class="button secondary">Save This Clause</button>
                <?php if (($currentClauseIndex + 1) < $totalClauses): ?>
                    <button type="submit" name="action" value="next" class="button">Save and Continue</button>
                <?php else: ?>
                    <button type="submit" name="action" value="save" class="button">Save for Review</button>
                <?php endif; ?>
                <a class="button secondary" href="<?= e(url('/assessments')) ?>">Back to list</a>
            </div>
        <?php else: ?>
            <p class="muted">No questions found for this clause.</p>
        <?php endif; ?>
    </form>
</section>

<script>
    (() => {
        const form = document.getElementById('assessmentForm');
        if (!form) return;

        const storageKey = 'assessment-draft:' + form.dataset.assessmentId;
        const fields = Array.from(form.querySelectorAll('input, select, textarea')).filter((field) => field.name && field.type !== 'hidden');

        try {
            const saved = JSON.parse(localStorage.getItem(storageKey) || '{}');
            fields.forEach((field) => {
                if (field.type === 'checkbox') {
                    field.checked = Boolean(saved[field.name]);
                    return;
                }
                if ((field.value || '') === '' && typeof saved[field.name] === 'string') {
                    field.value = saved[field.name];
                }
            });
        } catch (error) {
            console.warn(error);
        }

        const persist = () => {
            const payload = {};
            fields.forEach((field) => {
                payload[field.name] = field.type === 'checkbox' ? field.checked : field.value;
            });
            localStorage.setItem(storageKey, JSON.stringify(payload));
        };

        fields.forEach((field) => field.addEventListener('input', persist));
        form.addEventListener('submit', () => localStorage.removeItem(storageKey));
    })();
</script>
