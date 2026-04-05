<?php

namespace App\ViewModels;

class FreelancerSubmissionsViewModel
{
    public function __construct(
        public readonly string $pageTitle,
        public readonly string $badgeLabel,
        public readonly string $heroTitle,
        public readonly string $heroDescription,
        public readonly array $stats,
        public readonly array $submissions,
    ) {
    }

    public static function createFromSubmissions(array $submissions): self
    {
        $pendingCount = count(array_filter($submissions, static fn(array $submission): bool => $submission['status'] === 'pending'));
        $acceptedCount = count(array_filter($submissions, static fn(array $submission): bool => $submission['status'] === 'accepted'));
        $rejectedCount = count(array_filter($submissions, static fn(array $submission): bool => $submission['status'] === 'rejected'));

        $normalizedSubmissions = array_map(static function (array $submission): array {
            return [
                'submissionId' => $submission['submissionId'],
                'gigId' => $submission['gigId'],
                'gigTitle' => $submission['gigTitle'],
                'gigCategory' => $submission['gigCategory'],
                'gigLocation' => $submission['gigLocation'],
                'status' => $submission['status'],
                'statusLabel' => ucfirst((string) $submission['status']),
                'submittedAt' => $submission['submittedAt'],
                'canWithdraw' => (bool) $submission['canWithdraw'],
                'detailUrl' => $submission['detailUrl'],
            ];
        }, $submissions);

        return new self(
            pageTitle: 'My Submissions - FilmGig',
            badgeLabel: 'My Submissions',
            heroTitle: 'Track Your Applications',
            heroDescription: 'See the status of every gig application and withdraw pending submissions when needed.',
            stats: [
                ['label' => 'Total Submissions', 'value' => (string) count($submissions), 'note' => 'All time'],
                ['label' => 'Pending', 'value' => (string) $pendingCount, 'note' => 'Can still be withdrawn'],
                ['label' => 'Accepted', 'value' => (string) $acceptedCount, 'note' => 'Confirmed applications'],
                ['label' => 'Rejected', 'value' => (string) $rejectedCount, 'note' => 'Closed applications'],
            ],
            submissions: $normalizedSubmissions,
        );
    }
}