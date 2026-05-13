<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class AssessmentRepository
{
    public function getStandards(): array
    {
        return Database::query(
            'SELECT id, code, name, description, year FROM iso_standards WHERE active = TRUE ORDER BY code ASC'
        )->fetchAll();
    }

    public function getDefaultStandardId(): ?string
    {
        $configured = Database::query(
            'SELECT value
             FROM site_settings
             WHERE `key` = :key
             LIMIT 1',
            ['key' => 'default_assessment_standard_id']
        )->fetchColumn();

        if ($configured !== false && $configured !== null && trim((string) $configured) !== '') {
            $exists = Database::query(
                'SELECT id
                 FROM iso_standards
                 WHERE id = :id AND active = TRUE
                 LIMIT 1',
                ['id' => trim((string) $configured)]
            )->fetchColumn();

            if ($exists !== false && $exists !== null) {
                return (string) $exists;
            }
        }

        $firstActive = Database::query(
            'SELECT id FROM iso_standards WHERE active = TRUE ORDER BY code ASC LIMIT 1'
        )->fetchColumn();

        return $firstActive !== false && $firstActive !== null ? (string) $firstActive : null;
    }

    public function findStandardById(string $standardId): ?array
    {
        $row = Database::query(
            'SELECT id, code, name
             FROM iso_standards
             WHERE id = :id AND active = TRUE
             LIMIT 1',
            ['id' => $standardId]
        )->fetch();

        return $row ?: null;
    }

    public function getUserAssessments(string $userId): array
    {
        return Database::query(
            'SELECT
                a.id,
                a.title,
                a.status,
                a."complianceScore",
                a."createdAt",
                a."updatedAt",
                a."completedAt",
                s.code,
                s.name,
                COALESCE(answer_stats.answers_count, 0) AS answers_count,
                COALESCE(question_stats.questions_count, 0) AS questions_count
             FROM assessments a
             INNER JOIN iso_standards s ON s.id = a."isoStandardId"
             LEFT JOIN (
                SELECT "assessmentId", COUNT(*) AS answers_count
                FROM answers
                GROUP BY "assessmentId"
             ) answer_stats ON answer_stats."assessmentId" = a.id
             LEFT JOIN (
                SELECT c."isoStandardId", COUNT(*) AS questions_count
                FROM clauses c
                INNER JOIN questions q ON q."clauseId" = c.id
                GROUP BY c."isoStandardId"
             ) question_stats ON question_stats."isoStandardId" = a."isoStandardId"
             WHERE a."userId" = :user_id
             ORDER BY a."createdAt" DESC',
            ['user_id' => $userId]
        )->fetchAll();
    }

    public function createAssessment(string $id, string $userId, string $standardId, string $title): void
    {
        Database::query(
            'INSERT INTO assessments (id, "userId", "isoStandardId", title, status, "createdAt", "updatedAt")
             VALUES (:id, :user_id, :standard_id, :title, :status, NOW(), NOW())',
            [
                'id' => $id,
                'user_id' => $userId,
                'standard_id' => $standardId,
                'title' => $title,
                'status' => 'IN_PROGRESS',
            ]
        );
    }

    public function acceptTerms(string $userId, int $termsId, ?string $ipAddress, ?string $userAgent): void
    {
        Database::query(
            'INSERT INTO user_terms_acceptances (user_id, terms_id, accepted_at, ip_address, user_agent)
             VALUES (:user_id, :terms_id, NOW(), :ip_address, :user_agent)',
            [
                'user_id' => $userId,
                'terms_id' => $termsId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]
        );
    }

    public function analyticsSummary(string $userId): array
    {
        $summary = Database::query(
            <<<'SQL'
SELECT
    COUNT(*) AS total,
    SUM(CASE WHEN status = 'COMPLETED' THEN 1 ELSE 0 END) AS completed,
    SUM(CASE WHEN status = 'IN_PROGRESS' THEN 1 ELSE 0 END) AS in_progress,
    ROUND(AVG("complianceScore"), 2) AS average_score
FROM assessments
WHERE "userId" = :user_id
SQL,
            ['user_id' => $userId]
        )->fetch() ?: [];

        return [
            'total' => (int) ($summary['total'] ?? 0),
            'completed' => (int) ($summary['completed'] ?? 0),
            'in_progress' => (int) ($summary['in_progress'] ?? 0),
            'average_score' => $summary['average_score'],
        ];
    }

    public function findAssessmentSummary(string $assessmentId, string $userId): ?array
    {
        $assessment = Database::query(
            'SELECT a.id, a.title, a.status, a."complianceScore", a."createdAt", s.code, s.name, s.description
             FROM assessments a
             INNER JOIN iso_standards s ON s.id = a."isoStandardId"
             WHERE a.id = :id AND a."userId" = :user_id
             LIMIT 1',
            ['id' => $assessmentId, 'user_id' => $userId]
        )->fetch();

        if (!$assessment) {
            return null;
        }

        $assessment['answers_count'] = (int) Database::query(
            'SELECT COUNT(*) FROM answers WHERE "assessmentId" = :assessment_id',
            ['assessment_id' => $assessmentId]
        )->fetchColumn();

        $assessment['questions_count'] = (int) Database::query(
            'SELECT COUNT(*)
             FROM questions q
             INNER JOIN clauses c ON c.id = q."clauseId"
             WHERE c."isoStandardId" = (
                SELECT "isoStandardId" FROM assessments WHERE id = :assessment_id
             )',
            ['assessment_id' => $assessmentId]
        )->fetchColumn();

        $assessment['progress_percent'] = $assessment['questions_count'] > 0
            ? (int) round(((int) $assessment['answers_count'] / (int) $assessment['questions_count']) * 100)
            : 0;

        return $assessment;
    }

    public function getAssessmentQuestions(string $assessmentId, string $userId): array
    {
        $assessment = Database::query(
            'SELECT a.id, a."isoStandardId"
             FROM assessments a
             WHERE a.id = :id AND a."userId" = :user_id
             LIMIT 1',
            ['id' => $assessmentId, 'user_id' => $userId]
        )->fetch();

        if (!$assessment) {
            return [];
        }

        return Database::query(
            'SELECT
                c.id AS clause_id,
                c.number AS clause_number,
                c.title AS clause_title,
                q.id AS question_id,
                q.text AS question_text,
                q.description AS question_description,
                q.type AS question_type,
                q.options AS question_options,
                q.required AS question_required,
                q."order" AS question_order,
                a.value AS answer_value,
                a."textValue" AS answer_text
             FROM clauses c
             INNER JOIN questions q ON q."clauseId" = c.id
             LEFT JOIN answers a ON a."questionId" = q.id AND a."assessmentId" = :assessment_id
             WHERE c."isoStandardId" = :standard_id
             ORDER BY c."order" ASC, q."order" ASC',
            [
                'assessment_id' => $assessmentId,
                'standard_id' => $assessment['isoStandardId'],
            ]
        )->fetchAll();
    }

    public function saveAnswers(string $assessmentId, array $answers): void
    {
        foreach ($answers as $questionId => $payload) {
            $value = trim((string) ($payload['value'] ?? ''));
            $textValue = trim((string) ($payload['text'] ?? ''));

            if ($value === '' && $textValue === '') {
                continue;
            }

            Database::query(
                'INSERT INTO answers (id, "assessmentId", "questionId", value, "textValue", score, "createdAt", "updatedAt")
                 VALUES (:id, :assessment_id, :question_id, :value, :text_value, :score, NOW(), NOW())
                 ON DUPLICATE KEY UPDATE value = VALUES(value), "textValue" = VALUES("textValue"), score = VALUES(score), "updatedAt" = NOW()',
                [
                    'id' => bin2hex(random_bytes(16)),
                    'assessment_id' => $assessmentId,
                    'question_id' => $questionId,
                    'value' => $value !== '' ? $value : $textValue,
                    'text_value' => $textValue !== '' ? $textValue : null,
                    'score' => $this->scoreAnswer($value),
                ]
            );
        }

        Database::query(
            <<<'SQL'
UPDATE assessments
SET status = CASE
        WHEN (
            SELECT COUNT(*)
            FROM answers
            WHERE "assessmentId" = :assessment_id
        ) >= (
            SELECT COUNT(*)
            FROM questions q
            INNER JOIN clauses c ON c.id = q."clauseId"
            WHERE c."isoStandardId" = (
                SELECT "isoStandardId" FROM assessments WHERE id = :assessment_id
            )
        ) AND (
            SELECT COUNT(*)
            FROM questions q
            INNER JOIN clauses c ON c.id = q."clauseId"
            WHERE c."isoStandardId" = (
                SELECT "isoStandardId" FROM assessments WHERE id = :assessment_id
            )
        ) > 0 THEN 'COMPLETED'
        ELSE 'IN_PROGRESS'
    END,
    "completedAt" = CASE
        WHEN (
            SELECT COUNT(*)
            FROM answers
            WHERE "assessmentId" = :assessment_id
        ) >= (
            SELECT COUNT(*)
            FROM questions q
            INNER JOIN clauses c ON c.id = q."clauseId"
            WHERE c."isoStandardId" = (
                SELECT "isoStandardId" FROM assessments WHERE id = :assessment_id
            )
        ) AND (
            SELECT COUNT(*)
            FROM questions q
            INNER JOIN clauses c ON c.id = q."clauseId"
            WHERE c."isoStandardId" = (
                SELECT "isoStandardId" FROM assessments WHERE id = :assessment_id
            )
        ) > 0 THEN NOW()
        ELSE NULL
    END,
    "complianceScore" = (
        SELECT AVG(score) * 20
        FROM answers
        WHERE "assessmentId" = :assessment_id AND score IS NOT NULL
    ),
    "updatedAt" = NOW()
WHERE id = :assessment_id
SQL,
            ['assessment_id' => $assessmentId]
        );
    }

    private function scoreAnswer(string $value): ?float
    {
        $normalized = strtoupper(trim($value));
        return match ($normalized) {
            'YES' => 5.0,
            'NO' => 1.0,
            '1' => 1.0,
            '2' => 2.0,
            '3' => 3.0,
            '4' => 4.0,
            '5' => 5.0,
            default => null,
        };
    }
}
