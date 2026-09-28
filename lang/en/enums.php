<?php

return [
    'delegation_status' => [
        'draft' => 'Draft',
        'sent' => 'Sent',
        'accepted' => 'Accepted',
        'in_progress' => 'In Progress',
        'pending_verification' => 'Pending Verification',
        'done' => 'Done',
        'rejected' => 'Rejected',
    ],
    'task_status' => [
        'new' => 'New',
        'accepted' => 'Accepted',
        'in_progress' => 'In Progress',
        'pending_review' => 'Pending Review',
        'done' => 'Done',
        'rejected' => 'Rejected',
        'late' => 'Late',
    ],
    'agenda_status' => [
        'scheduled' => 'Scheduled',
        'done' => 'Done',
        'cancelled' => 'Cancelled',
    ],
    'agenda_type' => [
        'rapat' => 'Meeting',
        'undangan' => 'Invitation',
        'kunjungan' => 'Visit',
        'audiensi' => 'Audience',
        'pelatihan' => 'Training',
        'lainnya' => 'Other',
    ],
    'priority' => [
        'low' => 'Low',
        'normal' => 'Normal',
        'high' => 'High',
        'urgent' => 'Urgent',
    ],
    'letter_verification' => [
        'waiting' => 'Waiting for Verification',
        'verified' => 'Verified',
        'no_follow_up' => 'No Follow Up Needed',
        'needs_follow_up' => 'Needs Follow Up',
        'delegated' => 'Delegated',
        'completed' => 'Completed',
        'archived' => 'Archived',
    ],
];
