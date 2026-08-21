<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Firebase credentials
    |--------------------------------------------------------------------------
    |
    | Path to the service-account JSON downloaded from the Firebase console
    | (Project settings → Service accounts → Generate new private key).
    | Firestore/custom-token features simply no-op (logged, not thrown)
    | until this file exists.
    |
    */

    'credentials' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase/service-account.json')),

    'project_id' => env('FIREBASE_PROJECT_ID'),

];
