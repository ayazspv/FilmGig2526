<?php

namespace App\ViewModels;

class AdminSubmissionReviewViewModel
{
    public function __construct(
        public readonly string $pageTitle,
        public readonly string $badgeLabel,
        public readonly string $heroTitle,
        public readonly string $heroDescription,
        public readonly string $tableTitle,
        public readonly string $emptyStateMessage,
        public readonly array $stats,
        public readonly array $submissions,
    ) {
    }

    public static function createFromSubmissions(array $submissions): self
    {
        return self::createWithContent(
            submissions: $submissions,
            pageTitle: 'Review Submissions - FilmGig',
            badgeLabel: 'Submission Review',
            heroTitle: 'Review Pending Applications',
            heroDescription: 'Accept or reject freelancer submissions and keep each gig status in sync with the latest decision.',
            tableTitle: 'Submission Queue',
            emptyStateMessage: 'No submissions are available for review yet.'
        );
    }

    public static function createForProductionHouseSubmissions(array $submissions): self
    {
        return self::createWithContent(
            submissions: $submissions,
            pageTitle: 'Gig Submissions - FilmGig',
            badgeLabel: 'My Gig Submissions',
            heroTitle: 'Submissions For Your Gigs',
            heroDescription: 'Track every application sent to your gigs from a single dashboard page.',
            tableTitle: 'Recent Applications',
            emptyStateMessage: 'No submissions have been received for your gigs yet.'
        );
    }

    private static function createWithContent(
        array $submissions,
        string $pageTitle,
        string $badgeLabel,
        string $heroTitle,
        string $heroDescription,
        string $tableTitle,
        string $emptyStateMessage,
    ): self
    {
        $pendingCount = count(array_filter($submissions, static fn(array $submission): bool => $submission['status'] === 'pending'));
        $acceptedCount = count(array_filter($submissions, static fn(array $submission): bool => $submission['status'] === 'accepted'));
        $rejectedCount = count(array_filter($submissions, static fn(array $submission): bool => $submission['status'] === 'rejected'));

        $normalizedSubmissions = array_map(static function (array $submission): array {
            return [
                'submissionId' => $submission['submissionId'],
                'gigId' => $submission['gigId'],
                'gigTitle' => $submission['gigTitle'],
                'gigStatus' => $submission['gigStatus'],
                'gigStatusLabel' => $submission['gigStatusLabel'],
                'freelancerName' => $submission['freelancerName'],
                'freelancerEmail' => $submission['freelancerEmail'],
                'status' => $submission['status'],
                'statusLabel' => $submission['statusLabel'],
                'submittedAt' => $submission['submittedAt'],
                'canReview' => (bool) $submission['canReview'],
                'detailUrl' => $submission['detailUrl'],
            ];
        }, $submissions);

        return new self(
            pageTitle: $pageTitle,
            badgeLabel: $badgeLabel,
            heroTitle: $heroTitle,
            heroDescription: $heroDescription,
            tableTitle: $tableTitle,
            emptyStateMessage: $emptyStateMessage,
            stats: [
                ['label' => 'Total Submissions', 'value' => (string) count($submissions), 'note' => 'All applications'],
                ['label' => 'Pending Reviews', 'value' => (string) $pendingCount, 'note' => 'Waiting for decision'],
                ['label' => 'Accepted', 'value' => (string) $acceptedCount, 'note' => 'Approved applications'],
                ['label' => 'Rejected', 'value' => (string) $rejectedCount, 'note' => 'Declined applications'],
            ],
            submissions: $normalizedSubmissions,
        );
    }
}