<?php

    return [
        'name' => 'Firebase',
        'slug' => 'firebase',
        'image' => 'modules/firebase/images/logo.svg',
        'configs' => [
            'firebase_sid' => env('FIREBASE_SID'),
            'firebase_auth_token' => env('FIREBASE_AUTH_TOKEN'),
            'firebase_number' => env('FIREBASE_NUMBER'),
        ],
        'fields' => [
            'firebase_sid' => [
                'type' => 'password',
                'label' => 'Firebase SID',
            ],
            'firebase_auth_token' => [
                'type' => 'password',
                'label' => 'Firebase Auth Token',
            ],
            'firebase_number' => [
                'type' => 'password',
                'label' => 'Firebase Number',
            ],
        ],
    ];
