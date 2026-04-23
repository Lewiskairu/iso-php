<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\AssessmentRepository;
use App\Repositories\ContentRepository;

final class AssessmentController extends Controller
{
    public function index(): void
    {
        $user = $this->requireAuth();
        $repository = new AssessmentRepository();
        $assessments = $repository->getUserAssessments($user['id']);

        $this->view('assessments/index', [
            'title' => 'Assessments',
            'assessments' => $assessments,
            'analytics' => $repository->analyticsSummary($user['id']),
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $content = new ContentRepository();
        $this->view('assessments/create', [
            'title' => 'New Assessment',
            'standards' => (new AssessmentRepository())->getStandards(),
            'terms' => $content->latestTerms(),
            'error' => $this->session->consumeFlash('error'),
        ]);
    }

    public function store(): void
    {
        $user = $this->requireAuth();
        $standardId = trim((string) ($_POST['iso_standard_id'] ?? ''));
        $title = trim((string) ($_POST['title'] ?? ''));
        $acceptedTerms = (bool) ($_POST['accept_terms'] ?? false);
        $termsId = (int) ($_POST['terms_id'] ?? 0);

        if ($standardId === '' || $title === '' || !$acceptedTerms || $termsId <= 0) {
            $message = 'Title and standard are required, and you must accept the current terms before starting.';
            $this->flashFormState(
                [
                    'title' => $title === '' ? 'Assessment title is required.' : null,
                    'iso_standard_id' => $standardId === '' ? 'Please select an ISO standard.' : null,
                    'accept_terms' => !$acceptedTerms ? 'You must accept the current terms and conditions.' : null,
                ],
                [
                    'title' => $title,
                    'iso_standard_id' => $standardId,
                    'accept_terms' => $acceptedTerms ? '1' : '',
                ]
            );
            $this->session->flash('error', $message);
            redirect('/assessments/create');
        }

        $id = bin2hex(random_bytes(16));
        $repository = new AssessmentRepository();
        $repository->createAssessment($id, $user['id'], $standardId, $title);
        $repository->acceptTerms(
            $user['id'],
            $termsId,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );
        redirect('/assessments/show?id=' . urlencode($id));
    }

    public function show(): void
    {
        $user = $this->requireAuth();
        $assessmentId = trim((string) ($_GET['id'] ?? ''));
        $repository = new AssessmentRepository();
        $assessment = $repository->findAssessmentSummary($assessmentId, $user['id']);

        if (!$assessment) {
            http_response_code(404);
            exit('Assessment not found');
        }

        $questions = $repository->getAssessmentQuestions($assessmentId, $user['id']);
        $clauses = $this->groupQuestionsByClause($questions);
        $currentClause = isset($_GET['clause']) ? max(0, (int) $_GET['clause']) : $this->firstIncompleteClauseIndex($clauses);
        if ($currentClause >= count($clauses)) {
            $currentClause = max(0, count($clauses) - 1);
        }

        $resultsSnapshot = $this->buildResultsSnapshot($questions);

        if (($assessment['status'] ?? '') === 'COMPLETED' && !isset($_GET['clause'])) {
            $this->view('assessments/results', [
                'title' => 'Assessment Results',
                'assessment' => $assessment,
                'questions' => $questions,
                'clauses' => $clauses,
                'resultsSnapshot' => $resultsSnapshot,
            ]);
            return;
        }

        $this->view('assessments/show', [
            'title' => 'Assessment Summary',
            'assessment' => $assessment,
            'questions' => $questions,
            'clauses' => $clauses,
            'currentClauseIndex' => $currentClause,
            'currentClause' => $clauses[$currentClause] ?? null,
            'resultsSnapshot' => $resultsSnapshot,
            'saved' => $this->session->consumeFlash('success'),
            'error' => $this->session->consumeFlash('error'),
        ]);
    }

    public function saveAnswers(): void
    {
        $user = $this->requireAuth();
        $assessmentId = trim((string) ($_POST['assessment_id'] ?? ''));
        $answers = $_POST['answers'] ?? [];
        $clauseIndex = max(0, (int) ($_POST['clause_index'] ?? 0));
        $action = (string) ($_POST['action'] ?? 'save');

        $repository = new AssessmentRepository();
        $assessment = $repository->findAssessmentSummary($assessmentId, $user['id']);
        if (!$assessment) {
            http_response_code(404);
            exit('Assessment not found');
        }

        $questions = $repository->getAssessmentQuestions($assessmentId, $user['id']);
        $clauses = $this->groupQuestionsByClause($questions);
        $currentClause = $clauses[$clauseIndex] ?? null;
        if ($currentClause === null) {
            $this->session->flash('error', 'Clause section not found.');
            redirect('/assessments/show?id=' . urlencode($assessmentId));
        }

        if (is_array($answers)) {
            $repository->saveAnswers($assessmentId, $answers);
        }

        $updatedQuestions = $repository->getAssessmentQuestions($assessmentId, $user['id']);
        $updatedClauses = $this->groupQuestionsByClause($updatedQuestions);
        $currentClause = $updatedClauses[$clauseIndex] ?? $currentClause;

        if (!$this->isClauseComplete($currentClause)) {
            $this->session->flash('error', 'Complete every required question in this clause before moving forward.');
            redirect('/assessments/show?id=' . urlencode($assessmentId) . '&clause=' . $clauseIndex);
        }

        $this->session->flash('success', 'Answers saved.');
        if ($action === 'next' && isset($updatedClauses[$clauseIndex + 1])) {
            redirect('/assessments/show?id=' . urlencode($assessmentId) . '&clause=' . ($clauseIndex + 1));
        }

        redirect('/assessments/show?id=' . urlencode($assessmentId) . '&clause=' . $clauseIndex);
    }

    public function export(): void
    {
        $user = $this->requireAuth();
        $assessmentId = trim((string) ($_GET['id'] ?? ''));
        $repository = new AssessmentRepository();
        $assessment = $repository->findAssessmentSummary($assessmentId, $user['id']);
        if (!$assessment) {
            http_response_code(404);
            exit('Assessment not found');
        }

        $questions = $repository->getAssessmentQuestions($assessmentId, $user['id']);
        $filename = 'assessment-results-' . preg_replace('/[^a-z0-9_-]+/i', '-', (string) $assessment['id']) . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $out = fopen('php://output', 'w');
        if ($out === false) {
            exit;
        }

        fputcsv($out, ['Assessment ID', $assessment['id']]);
        fputcsv($out, ['Title', $assessment['title'] ?: 'Untitled assessment']);
        fputcsv($out, ['Standard', ($assessment['code'] ?? '') . ' - ' . ($assessment['name'] ?? '')]);
        fputcsv($out, ['Status', $assessment['status'] ?? '']);
        fputcsv($out, ['Compliance Score', $assessment['complianceScore'] !== null ? number_format((float) $assessment['complianceScore'], 2) : 'Pending']);
        fputcsv($out, []);
        fputcsv($out, ['Clause', 'Question', 'Required', 'Answer Value', 'Answer Text']);

        foreach ($questions as $question) {
            fputcsv($out, [
                trim((string) ($question['clause_number'] ?? '')) . ' ' . trim((string) ($question['clause_title'] ?? '')),
                (string) ($question['question_text'] ?? ''),
                !empty($question['question_required']) ? 'Yes' : 'No',
                (string) ($question['answer_value'] ?? ''),
                (string) ($question['answer_text'] ?? ''),
            ]);
        }

        fclose($out);
        exit;
    }

    private function groupQuestionsByClause(array $questions): array
    {
        $clauses = [];
        foreach ($questions as $question) {
            $key = (string) $question['clause_id'];
            if (!isset($clauses[$key])) {
                $clauses[$key] = [
                    'clause_id' => $question['clause_id'],
                    'clause_number' => $question['clause_number'],
                    'clause_title' => $question['clause_title'],
                    'questions' => [],
                ];
            }

            $clauses[$key]['questions'][] = $question;
        }

        return array_values($clauses);
    }

    private function isClauseComplete(array $clause): bool
    {
        foreach ($clause['questions'] as $question) {
            if (empty($question['question_required'])) {
                continue;
            }

            $value = trim((string) ($question['answer_value'] ?? ''));
            $text = trim((string) ($question['answer_text'] ?? ''));
            if ($value === '' && $text === '') {
                return false;
            }
        }

        return true;
    }

    private function firstIncompleteClauseIndex(array $clauses): int
    {
        foreach ($clauses as $index => $clause) {
            if (!$this->isClauseComplete($clause)) {
                return $index;
            }
        }

        return 0;
    }

    private function buildResultsSnapshot(array $questions): array
    {
        $requiredTotal = 0;
        $requiredAnswered = 0;
        $yesCount = 0;
        $noCount = 0;
        $scoredCount = 0;
        $scoreTotal = 0.0;

        foreach ($questions as $q) {
            $value = strtoupper(trim((string) ($q['answer_value'] ?? '')));
            $text = trim((string) ($q['answer_text'] ?? ''));
            $hasAnswer = $value !== '' || $text !== '';

            if (!empty($q['question_required'])) {
                $requiredTotal++;
                if ($hasAnswer) {
                    $requiredAnswered++;
                }
            }

            if ($value === 'YES') {
                $yesCount++;
            } elseif ($value === 'NO') {
                $noCount++;
            }

            if (in_array($value, ['1', '2', '3', '4', '5'], true)) {
                $scoredCount++;
                $scoreTotal += (float) $value;
            }
        }

        return [
            'required_total' => $requiredTotal,
            'required_answered' => $requiredAnswered,
            'yes_count' => $yesCount,
            'no_count' => $noCount,
            'avg_scale' => $scoredCount > 0 ? round($scoreTotal / $scoredCount, 2) : null,
        ];
    }
}
