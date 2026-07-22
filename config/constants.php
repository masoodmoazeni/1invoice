<?php

return [
    'PAGINATION_LIMIT' => 10,
    'USER_ROLES' => [
        'ADMIN' => 'ADMIN',
        'BROKER' => 'BROKER',
        'USER' => 'USER',
    ],
    'LOWER_MID_MARKET_TEXT' => 'Lower Mid Market Sale',
    'MARKET_TYPE_THRESHOLD' => 4_500_000,

    /*
     * Admin notification email recipients (SendGrid). Comma-separated in .env.
     * Used instead of DB admin role lookup until restored in RoleService::getAdminNotificationRecipients().
     */
    'ADMIN_NOTIFICATION_EMAILS' => array_values(array_filter(array_map(
        'trim',
        explode(',', env('ADMIN_NOTIFICATION_EMAILS', 'susan@salonspaconnection.com'))
    ))),

    'ADMIN_SEND_FOR_APPROVAL_NOTIFICATION_EMAILS' => array_values(array_filter(array_map(
        'trim',
        explode(',', env('ADMIN_SEND_FOR_APPROVAL_NOTIFICATION_EMAILS', 'katelyn@salonspaconnection.com,customerservice@salonspaconnection.com,susan@salonspaconnection.com'))
    ))),
];
