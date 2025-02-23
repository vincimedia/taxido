<?php

return [
    'name' => 'Ride',
    'slug' => 'Taxido',
    'email-templates' => [
        'create-ride-driver' => [
            'name' => 'Create Ride (Driver)',
            'description' => 'Sent to the driver when ride is created.',
            'slug' => 'create-ride-driver',
            'shortcodes' => [
                ['type' => 'menuitem', 'text' => 'Driver Name', 'action' => '{{driver_name}}'],
                ['type' => 'menuitem', 'text' => 'Ride Number', 'action' => '{{ride_number}}'],
                ['type' => 'menuitem', 'text' => 'Services', 'action' => '{{services}}'],
                ['type' => 'menuitem', 'text' => 'Service Category', 'action' => '{{service_category}}'],
                ['type' => 'menuitem', 'text' => 'Rider Name', 'action' => '{{rider_name}}'],
                ['type' => 'menuitem', 'text' => 'Bid Status', 'action' => '{{bid_status}}'],
                ['type' => 'menuitem', 'text' => 'Rider Email', 'action' => '{{rider_email}}'],
                ['type' => 'menuitem', 'text' => 'Rider Phone', 'action' => '{{rider_phone}}'],
                ['type' => 'menuitem', 'text' => 'Vehicle Type', 'action' => '{{vehicle_type}}'],
                ['type' => 'menuitem', 'text' => 'Fare Amount', 'action' => '{{fare_amount}}'],
                ['type' => 'menuitem', 'text' => 'Distance', 'action' => '{{distance}}'],
                ['type' => 'menuitem', 'text' => 'Distance Unit', 'action' => '{{distance_unit}}'],
                ['type' => 'menuitem', 'text' => 'Your Company Name', 'action' => '{{company_name}}'],
            ]
        ],
        'create-ride-admin' => [
            'name' => 'Create Ride (Admin)',
            'description' => 'Notifies the admin when new ride is created.',
            'slug' => 'create-ride-admin',
            'shortcodes' => [
                ['type' => 'menuitem', 'text' => 'Driver Name', 'action' => '{{driver_name}}'],
                ['type' => 'menuitem', 'text' => 'Ride Number', 'action' => '{{ride_number}}'],
                ['type' => 'menuitem', 'text' => 'Services', 'action' => '{{services}}'],
                ['type' => 'menuitem', 'text' => 'Service Category', 'action' => '{{service_category}}'],
                ['type' => 'menuitem', 'text' => 'Rider Name', 'action' => '{{rider_name}}'],
                ['type' => 'menuitem', 'text' => 'Bid Status', 'action' => '{{bid_status}}'],
                ['type' => 'menuitem', 'text' => 'Rider Email', 'action' => '{{rider_email}}'],
                ['type' => 'menuitem', 'text' => 'Rider Phone', 'action' => '{{rider_phone}}'],
                ['type' => 'menuitem', 'text' => 'Vehicle Type', 'action' => '{{vehicle_type}}'],
                ['type' => 'menuitem', 'text' => 'Fare Amount', 'action' => '{{fare_amount}}'],
                ['type' => 'menuitem', 'text' => 'Distance', 'action' => '{{distance}}'],
                ['type' => 'menuitem', 'text' => 'Distance Unit', 'action' => '{{distance_unit}}'],
                ['type' => 'menuitem', 'text' => 'Your Company Name', 'action' => '{{company_name}}'],
            ]
        ],
        'ride-request-driver' => [
            'name' => 'Ride Request (Driver)',
            'description' => 'Alerts the driver of a new ride request.',
            'slug' => 'ride-request-driver',
            'shortcodes' => [
                ['type' => 'menuitem', 'text' => 'Locations', 'action' => '{{locations}}'],
                ['type' => 'menuitem', 'text' => 'Services', 'action' => '{{services}}'],
                ['type' => 'menuitem', 'text' => 'Service Category', 'action' => '{{service_category}}'],
                ['type' => 'menuitem', 'text' => 'Vehicle Type', 'action' => '{{vehicle_type}}'],
                ['type' => 'menuitem', 'text' => 'Driver Name', 'action' => '{{driver_name}}'],
                ['type' => 'menuitem', 'text' => 'Rider Name', 'action' => '{{rider_name}}'],
                ['type' => 'menuitem', 'text' => 'Rider Phone', 'action' => '{{rider_phone}}'],
                ['type' => 'menuitem', 'text' => 'Fare Amount', 'action' => '{{fare_amount}}'],
                ['type' => 'menuitem', 'text' => 'Distance', 'action' => '{{distance}}'],
                ['type' => 'menuitem', 'text' => 'Distance Unit', 'action' => '{{distance_unit}}'],
                ['type' => 'menuitem', 'text' => 'Zone', 'action' => '{{zone}}'],
            ]
        ],
        'create-withdraw-request-admin' => [
            'name' => 'Create Withdraw Request (Admin)',
            'description' => 'Alerts the Admin of a new withdraw request.',
            'slug' => 'create-withdraw-request-admin',
            'shortcodes' => [
                ['type' => 'menuitem', 'text' => 'Amount', 'action' => '{{amount}}'],
                ['type' => 'menuitem', 'text' => 'Driver Name', 'action' => '{{driver_name}}'],
            ]
        ],
        'update-withdraw-request-driver' => [
            'name' => 'Update Withdraw Request (Driver)',
            'description' => 'Update the Driver about the status of their Withdraw Request',
            'slug' => 'update-withdraw-request-driver',
            'shortcodes' => [
                ['type' => 'menuitem', 'text' => 'Amount', 'action' => '{{amount}}'],
                ['type' => 'menuitem', 'text' => 'Driver Name', 'action' => '{{driver_name}}'],
                ['type' => 'menuitem', 'text' => 'Status', 'action' => '{{status}}'],
            ]
        ],
        'bid-status-driver' => [
            'name' => 'Bid Status (Driver)',
            'description' => 'Sent to the driver when status of their bid is changed.',
            'slug' => 'bid-status-driver',
            'shortcodes' => [
                ['type' => 'menuitem', 'text' => 'Driver Name', 'action' => '{{driver_name}}'],
                ['type' => 'menuitem', 'text' => 'Rider Name', 'action' => '{{rider_name}}'],
                ['type' => 'menuitem', 'text' => 'Bid Status', 'action' => '{{bid_status}}'],
                ['type' => 'menuitem', 'text' => 'Your Company Name', 'action' => '{{company_name}}'],
            ]
        ],

    ],
    'sms-templates' => [
        'create-ride-driver' => [
            'name' => 'Create Ride (Driver)',
            'description' => 'Sent to the driver when ride is created.',
            'slug' => 'create-ride-driver',
            'shortcodes' => [
                ['type' => 'menuitem', 'text' => 'Driver Name', 'action' => '{{driver_name}}'],
                ['type' => 'menuitem', 'text' => 'Ride Number', 'action' => '{{ride_number}}'],
                ['type' => 'menuitem', 'text' => 'Services', 'action' => '{{services}}'],
                ['type' => 'menuitem', 'text' => 'Service Category', 'action' => '{{service_category}}'],
                ['type' => 'menuitem', 'text' => 'Rider Name', 'action' => '{{rider_name}}'],
                ['type' => 'menuitem', 'text' => 'Bid Status', 'action' => '{{bid_status}}'],
                ['type' => 'menuitem', 'text' => 'Rider Email', 'action' => '{{rider_email}}'],
                ['type' => 'menuitem', 'text' => 'Rider Phone', 'action' => '{{rider_phone}}'],
                ['type' => 'menuitem', 'text' => 'Vehicle Type', 'action' => '{{vehicle_type}}'],
                ['type' => 'menuitem', 'text' => 'Fare Amount', 'action' => '{{fare_amount}}'],
                ['type' => 'menuitem', 'text' => 'Distance', 'action' => '{{distance}}'],
                ['type' => 'menuitem', 'text' => 'Distance Unit', 'action' => '{{distance_unit}}'],
                ['type' => 'menuitem', 'text' => 'Your Company Name', 'action' => '{{company_name}}'],
            ]
        ],
        'create-ride-admin' => [
            'name' => 'Create Ride (Admin)',
            'description' => 'Notifies the admin when new ride is created.',
            'slug' => 'create-ride-admin',
            'shortcodes' => [
                ['type' => 'menuitem', 'text' => 'Driver Name', 'action' => '{{driver_name}}'],
                ['type' => 'menuitem', 'text' => 'Ride Number', 'action' => '{{ride_number}}'],
                ['type' => 'menuitem', 'text' => 'Services', 'action' => '{{services}}'],
                ['type' => 'menuitem', 'text' => 'Service Category', 'action' => '{{service_category}}'],
                ['type' => 'menuitem', 'text' => 'Rider Name', 'action' => '{{rider_name}}'],
                ['type' => 'menuitem', 'text' => 'Bid Status', 'action' => '{{bid_status}}'],
                ['type' => 'menuitem', 'text' => 'Rider Email', 'action' => '{{rider_email}}'],
                ['type' => 'menuitem', 'text' => 'Rider Phone', 'action' => '{{rider_phone}}'],
                ['type' => 'menuitem', 'text' => 'Vehicle Type', 'action' => '{{vehicle_type}}'],
                ['type' => 'menuitem', 'text' => 'Fare Amount', 'action' => '{{fare_amount}}'],
                ['type' => 'menuitem', 'text' => 'Distance', 'action' => '{{distance}}'],
                ['type' => 'menuitem', 'text' => 'Distance Unit', 'action' => '{{distance_unit}}'],
                ['type' => 'menuitem', 'text' => 'Your Company Name', 'action' => '{{company_name}}'],
            ]
        ],
        'ride-request-driver' => [
            'name' => 'Ride Request (Driver)',
            'description' => 'Alerts the driver of a new ride request.',
            'slug' => 'ride-request-driver',
            'shortcodes' => [
                ['type' => 'menuitem', 'text' => 'Locations', 'action' => '{{locations}}'],
                ['type' => 'menuitem', 'text' => 'Services', 'action' => '{{services}}'],
                ['type' => 'menuitem', 'text' => 'Service Category', 'action' => '{{service_category}}'],
                ['type' => 'menuitem', 'text' => 'Vehicle Type', 'action' => '{{vehicle_type}}'],
                ['type' => 'menuitem', 'text' => 'Driver Name', 'action' => '{{driver_name}}'],
                ['type' => 'menuitem', 'text' => 'Rider Name', 'action' => '{{rider_name}}'],
                ['type' => 'menuitem', 'text' => 'Rider Phone', 'action' => '{{rider_phone}}'],
                ['type' => 'menuitem', 'text' => 'Fare Amount', 'action' => '{{fare_amount}}'],
                ['type' => 'menuitem', 'text' => 'Distance', 'action' => '{{distance}}'],
                ['type' => 'menuitem', 'text' => 'Distance Unit', 'action' => '{{distance_unit}}'],
                ['type' => 'menuitem', 'text' => 'Zone', 'action' => '{{zone}}'],
            ]
        ],
        'create-withdraw-request-admin' => [
            'name' => 'Create Withdraw Request (Admin)',
            'description' => 'Alerts the Admin of a new withdraw request.',
            'slug' => 'create-withdraw-request-admin',
            'shortcodes' => [
                ['type' => 'menuitem', 'text' => 'Amount', 'action' => '{{amount}}'],
                ['type' => 'menuitem', 'text' => 'Driver Name', 'action' => '{{driver_name}}'],

            ]
        ],
        'update-withdraw-request-driver' => [
            'name' => 'Update Withdraw Request (Driver)',
            'description' => 'Update the Driver about the status of their Withdraw Request',
            'slug' => 'update-withdraw-request-driver',
            'shortcodes' => [
                ['type' => 'menuitem', 'text' => 'Amount', 'action' => '{{amount}}'],
                ['type' => 'menuitem', 'text' => 'Driver Name', 'action' => '{{driver_name}}'],
                ['type' => 'menuitem', 'text' => 'Status', 'action' => '{{status}}'],
            ]
        ],
        'bid-status-driver' => [
            'name' => 'Bid Status (Driver)',
            'description' => 'Sent to the driver when status of their bid is changed.',
            'slug' => 'bid-status-driver',
            'shortcodes' => [
                ['type' => 'menuitem', 'text' => 'Driver Name', 'action' => '{{driver_name}}'],
                ['type' => 'menuitem', 'text' => 'Ride Number', 'action' => '{{ride_number}}'],
                ['type' => 'menuitem', 'text' => 'Rider Name', 'action' => '{{rider_name}}'],
                ['type' => 'menuitem', 'text' => 'Bid Status', 'action' => '{{bid_status}}'],
                ['type' => 'menuitem', 'text' => 'Your Company Name', 'action' => '{{company_name}}'],
            ]
        ],

    ],
    'push-notification-templates' => [
        'create-ride-driver' => [
            'name' => 'Create Ride (Driver)',
            'description' => 'Sent to the driver when ride is created.',
            'slug' => 'create-ride-driver',
            'shortcodes' => [
                ['type' => 'menuitem', 'text' => 'Driver Name', 'action' => '{{driver_name}}'],
                ['type' => 'menuitem', 'text' => 'Ride Number', 'action' => '{{ride_number}}'],
                ['type' => 'menuitem', 'text' => 'Services', 'action' => '{{services}}'],
                ['type' => 'menuitem', 'text' => 'Service Category', 'action' => '{{service_category}}'],
                ['type' => 'menuitem', 'text' => 'Rider Name', 'action' => '{{rider_name}}'],
                ['type' => 'menuitem', 'text' => 'Bid Status', 'action' => '{{bid_status}}'],
                ['type' => 'menuitem', 'text' => 'Rider Email', 'action' => '{{rider_email}}'],
                ['type' => 'menuitem', 'text' => 'Rider Phone', 'action' => '{{rider_phone}}'],
                ['type' => 'menuitem', 'text' => 'Vehicle Type', 'action' => '{{vehicle_type}}'],
                ['type' => 'menuitem', 'text' => 'Fare Amount', 'action' => '{{fare_amount}}'],
                ['type' => 'menuitem', 'text' => 'Distance', 'action' => '{{distance}}'],
                ['type' => 'menuitem', 'text' => 'Distance Unit', 'action' => '{{distance_unit}}'],
                ['type' => 'menuitem', 'text' => 'Your Company Name', 'action' => '{{company_name}}'],
            ]
        ],
        'create-ride-admin' => [
            'name' => 'Create Ride (Admin)',
            'description' => 'Notifies the admin when new ride is created.',
            'slug' => 'create-ride-admin',
            'shortcodes' => [
                ['type' => 'menuitem', 'text' => 'Driver Name', 'action' => '{{driver_name}}'],
                ['type' => 'menuitem', 'text' => 'Ride Number', 'action' => '{{ride_number}}'],
                ['type' => 'menuitem', 'text' => 'Services', 'action' => '{{services}}'],
                ['type' => 'menuitem', 'text' => 'Service Category', 'action' => '{{service_category}}'],
                ['type' => 'menuitem', 'text' => 'Rider Name', 'action' => '{{rider_name}}'],
                ['type' => 'menuitem', 'text' => 'Bid Status', 'action' => '{{bid_status}}'],
                ['type' => 'menuitem', 'text' => 'Rider Email', 'action' => '{{rider_email}}'],
                ['type' => 'menuitem', 'text' => 'Rider Phone', 'action' => '{{rider_phone}}'],
                ['type' => 'menuitem', 'text' => 'Vehicle Type', 'action' => '{{vehicle_type}}'],
                ['type' => 'menuitem', 'text' => 'Fare Amount', 'action' => '{{fare_amount}}'],
                ['type' => 'menuitem', 'text' => 'Distance', 'action' => '{{distance}}'],
                ['type' => 'menuitem', 'text' => 'Distance Unit', 'action' => '{{distance_unit}}'],
                ['type' => 'menuitem', 'text' => 'Your Company Name', 'action' => '{{company_name}}'],
            ]
        ],
        'ride-request-driver' => [
            'name' => 'Ride Request (Driver)',
            'description' => 'Alerts the driver of a new ride request.',
            'slug' => 'ride-request-driver',
            'shortcodes' => [
                ['type' => 'menuitem', 'text' => 'Locations', 'action' => '{{locations}}'],
                ['type' => 'menuitem', 'text' => 'Services', 'action' => '{{services}}'],
                ['type' => 'menuitem', 'text' => 'Service Category', 'action' => '{{service_category}}'],
                ['type' => 'menuitem', 'text' => 'Vehicle Type', 'action' => '{{vehicle_type}}'],
                ['type' => 'menuitem', 'text' => 'Driver Name', 'action' => '{{driver_name}}'],
                ['type' => 'menuitem', 'text' => 'Rider Name', 'action' => '{{rider_name}}'],
                ['type' => 'menuitem', 'text' => 'Rider Phone', 'action' => '{{rider_phone}}'],
                ['type' => 'menuitem', 'text' => 'Fare Amount', 'action' => '{{fare_amount}}'],
                ['type' => 'menuitem', 'text' => 'Distance', 'action' => '{{distance}}'],
                ['type' => 'menuitem', 'text' => 'Distance Unit', 'action' => '{{distance_unit}}'],
                ['type' => 'menuitem', 'text' => 'Zone', 'action' => '{{zone}}'],
            ]
        ],
        'create-withdraw-request-admin' => [
            'name' => 'Create Withdraw Request (Admin)',
            'description' => 'Alerts the Admin of a new withdraw request.',
            'slug' => 'create-withdraw-request-admin',
            'shortcodes' => [
                ['type' => 'menuitem', 'text' => 'Amount', 'action' => '{{amount}}'],
                ['type' => 'menuitem', 'text' => 'Driver Name', 'action' => '{{driver_name}}'],

            ]
        ],
        'update-withdraw-request-driver' => [
            'name' => 'Update Withdraw Request (Driver)',
            'description' => 'Update the Driver about the status of their Withdraw Request',
            'slug' => 'update-withdraw-request-driver',
            'shortcodes' => [
                ['type' => 'menuitem', 'text' => 'Amount', 'action' => '{{amount}}'],
                ['type' => 'menuitem', 'text' => 'Driver Name', 'action' => '{{driver_name}}'],
                ['type' => 'menuitem', 'text' => 'Status', 'action' => '{{status}}'],
            ]
        ],
        'bid-status-driver' => [
            'name' => 'Bid Status (Driver)',
            'description' => 'Sent to the driver when status of their bid is changed.',
            'slug' => 'bid-status-driver',
            'shortcodes' => [
                ['type' => 'menuitem', 'text' => 'Driver Name', 'action' => '{{driver_name}}'],
                ['type' => 'menuitem', 'text' => 'Ride Number', 'action' => '{{ride_number}}'],
                ['type' => 'menuitem', 'text' => 'Rider Name', 'action' => '{{rider_name}}'],
                ['type' => 'menuitem', 'text' => 'Bid Status', 'action' => '{{bid_status}}'],
                ['type' => 'menuitem', 'text' => 'Your Company Name', 'action' => '{{company_name}}'],
            ]
        ],
    ],
];
