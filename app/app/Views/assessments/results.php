<?php
$progress = (int) ($assessment['progress_percent'] ?? 0);
$answered = (int) ($assessment['answers_count'] ?? 0);
$totalQuestions = (int) ($assessment['questions_count'] ?? 0);
$score = $assessment['complianceScore'] !== null ? number_format((float) $assessment['complianceScore'], 2) : '0.00';
?>
<section class="hero" style="margin-bottom: 24px;">
    <span class="eyebrow">Assessment Results</span>
    <h1><?= e($assessment['title'] ?: 'Untitled assessment') ?></h1>
    <p class="muted"><?= e($assessment['code']) ?> · <?= e($assessment['name']) ?> · Completed on <?= e((string) ($assessment['completedAt'] ?? $assessment['updatedAt'] ?? '')) ?></p>
    
    <div class="grid section" style="margin-top: 24px; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <article class="metric-card" style="border-color: rgba(20,184,166,.4); background: rgba(20,184,166,.04);">
            <p class="muted" style="color: var(--brand);">Compliance Score</p>
            <div class="metric-value" style="color: var(--brand); font-size: 2rem;"><?= e($score) ?>%</div>
        </article>
        <article class="metric-card">
            <p class="muted">Questions Answered</p>
            <div class="metric-value"><?= e((string) $answered) ?>/<?= e((string) $totalQuestions) ?></div>
        </article>
        <article class="metric-card">
            <p class="muted">Required Answered</p>
            <div class="metric-value"><?= e((string) ($resultsSnapshot['required_answered'] ?? 0)) ?>/<?= e((string) ($resultsSnapshot['required_total'] ?? 0)) ?></div>
        </article>
        <article class="metric-card">
            <p class="muted">Average Scale</p>
            <div class="metric-value"><?= ($resultsSnapshot['avg_scale'] ?? null) !== null ? e((string) $resultsSnapshot['avg_scale']) : '—' ?></div>
        </article>
    </div>
</section>

<div class="actions" style="margin-bottom: 24px;">
    <a class="button secondary" href="<?= e(url('/assessments/export?id=' . urlencode((string) $assessment['id']))) ?>">
        <i class="bi bi-download"></i> Export Results (CSV)
    </a>
    <a class="button secondary" href="<?= e(url('/assessments/show?id=' . urlencode((string) $assessment['id']) . '&clause=0')) ?>">
        <i class="bi bi-pencil"></i> Review/Edit Answers
    </a>
</div>

<section class="stack" style="gap: 24px;">
    <?php foreach ($clauses as $index => $clause): ?>
        <article class="card">
            <div class="toolbar" style="margin-bottom: 16px; border-bottom: 1px solid rgba(15,23,42,.08); padding-bottom: 12px;">
                <h2 style="font-size: 1.2rem; margin: 0;">Clause <?= e((string) $clause['clause_number']) ?>: <?= e((string) $clause['clause_title']) ?></h2>
            </div>
            
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 55%;">Question</th>
                            <th style="width: 30%;">Your Answer</th>
                            <th style="width: 15%; text-align: center;">Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($clause['questions'] ?? []) as $qIndex => $question): ?>
                            <?php 
                                $val = (string) ($question['answer_value'] ?? '');
                                $text = (string) ($question['answer_text'] ?? '');
                                $displayAnswer = $val !== '' ? $val : ($text !== '' ? $text : '-');
                                
                                $scoreVal = match (strtoupper($val)) {
                                    'YES' => 5,
                                    'NO' => 1,
                                    '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5,
                                    default => null
                                };
                            ?>
                            <tr>
                                <td>
                                    <strong><?= e((string) $question['question_text']) ?></strong>
                                    <?php if (!empty($question['question_required'])): ?>
                                        <span class="badge-custom warning" style="font-size: 0.65rem; padding: 2px 6px; margin-left: 6px;">Required</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($displayAnswer === 'YES'): ?>
                                        <span class="badge-custom success">YES</span>
                                    <?php elseif ($displayAnswer === 'NO'): ?>
                                        <span class="badge-custom danger">NO</span>
                                    <?php elseif (in_array($displayAnswer, ['1','2','3','4','5'])): ?>
                                        <span class="badge-custom info">Scale: <?= e($displayAnswer) ?></span>
                                    <?php else: ?>
                                        <span class="muted"><?= e($displayAnswer) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td align="center">
                                    <?php if ($scoreVal !== null): ?>
                                        <strong><?= $scoreVal ?></strong> <span class="muted">/ 5</span>
                                    <?php else: ?>
                                        <span class="muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($clause['questions'])): ?>
                            <tr><td colspan="3" class="muted">No questions in this clause.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>
    <?php endforeach; ?>
    <?php if (empty($clauses)): ?>
        <p class="muted card">No clauses found for this assessment.</p>
    <?php endif; ?>
</section>
