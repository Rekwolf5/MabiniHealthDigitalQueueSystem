<?php

/**
 * Mabini Rural Health Unit - Services Configuration
 * Based on actual services provided by the health center
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Service Queue Prefixes
    |--------------------------------------------------------------------------
    |
    | Unique prefixes for queue numbering per service type
    | Example: DENT-P001 (Dental - Priority Patient #1)
    |
    */
    
    'service_prefixes' => [
        'Consultation and Treatment' => 'CONS',
        'Dental Services' => 'DENT',
        'Prenatal Care' => 'PRE',
        'Normal Delivery' => 'DEL',
        'Post-natal Care' => 'POST',
        'Laboratory Services' => 'LAB',
        'Immunization Program' => 'IMM',
        'Family Planning' => 'FP',
        'Circumcision' => 'CIRC',
        'Incision and Drainage' => 'INC',
        'Newborn Screening' => 'NB',
        'Dengue Program' => 'DEN',
        'Non-Communicable Diseases' => 'NCD',
        'Sanitation Inspection' => 'SAN',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Health Center Services
    |--------------------------------------------------------------------------
    |
    | Complete list of services offered by Mabini Rural Health Unit
    | 
    */

    'services' => [
        // Primary Services
        'Consultation and Treatment' => [
            'category' => 'Primary Care',
            'description' => 'General medical consultation and treatment for common illnesses',
            'icon' => 'fa-stethoscope',
        ],
        
        'Circumcision' => [
            'category' => 'Minor Procedures',
            'description' => 'Safe circumcision procedure',
            'icon' => 'fa-procedures',
        ],
        
        'Incision and Drainage' => [
            'category' => 'Minor Procedures',
            'description' => 'Surgical drainage of abscesses',
            'icon' => 'fa-syringe',
        ],

        // Laboratory Services
        'Laboratory Services' => [
            'category' => 'Diagnostics',
            'description' => 'Urinalysis, blood tests, Dengue NS1, blood typing, etc.',
            'icon' => 'fa-flask',
            'sub_services' => [
                'Urinalysis',
                'Random Blood Sugar',
                'Complete Blood Count',
                'Dengue NS1',
                'Blood Typing',
            ],
        ],

        // Maternal Health
        'Prenatal Care' => [
            'category' => 'Maternal Health',
            'description' => 'Comprehensive prenatal checkups and monitoring',
            'icon' => 'fa-baby',
        ],
        
        'Normal Delivery' => [
            'category' => 'Maternal Health',
            'description' => 'Normal childbirth services at birthing facility',
            'icon' => 'fa-baby-carriage',
        ],
        
        'Post-natal Care' => [
            'category' => 'Maternal Health',
            'description' => 'Post-delivery checkup and care for mother and baby',
            'icon' => 'fa-heartbeat',
        ],
        
        'Newborn Screening' => [
            'category' => 'Maternal Health',
            'description' => 'Screening for newborns',
            'icon' => 'fa-baby',
        ],

        // Family Services
        'Family Planning' => [
            'category' => 'Family Health',
            'description' => 'Counseling and provision of contraceptives (Pills, IUD, Condom, Injectable)',
            'icon' => 'fa-users',
            'sub_services' => [
                'Family Counseling',
                'Pills',
                'IUD',
                'Condom',
                'Injectable',
                'Cycle Beads',
            ],
        ],

        // Immunization
        'Immunization Program' => [
            'category' => 'Preventive Care',
            'description' => 'Vaccination program as per DOH schedule',
            'icon' => 'fa-shield-virus',
            'sub_services' => [
                'BCG Vaccine',
                'Hepa B Vaccine',
                'Pentavalent Vaccine',
                'Pneumonia Conjugate Vaccine',
                'Oral Polio Vaccine',
                'IPV',
                'MMR',
            ],
        ],

        // Dental Services
        'Dental Services' => [
            'category' => 'Dental Care',
            'description' => 'Oral care and tooth extraction',
            'icon' => 'fa-tooth',
            'sub_services' => [
                'Oral Care',
                'Tooth Extraction',
            ],
        ],

        // Disease Programs
        'Dengue Program' => [
            'category' => 'Disease Control',
            'description' => 'NS1 screening and management (Stage 1)',
            'icon' => 'fa-bug',
            'sub_services' => [
                'NS1 Screening',
                'Management & Treatment Stage 1',
            ],
        ],

        'Non-Communicable Diseases' => [
            'category' => 'Disease Control',
            'description' => 'Assessment and provision of maintenance medicine',
            'icon' => 'fa-pills',
            'sub_services' => [
                'Assessment',
                'Provision of Maintenance Medicine',
            ],
        ],

        // Environmental Health
        'Sanitation Inspection' => [
            'category' => 'Environmental Health',
            'description' => 'Safe drinking water management and zero open defecation',
            'icon' => 'fa-water',
            'sub_services' => [
                'Safe Drinking Water Management',
                'Zero Open Defecation',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Service Categories
    |--------------------------------------------------------------------------
    */
    
    'categories' => [
        'Primary Care',
        'Minor Procedures',
        'Diagnostics',
        'Maternal Health',
        'Family Health',
        'Preventive Care',
        'Dental Care',
        'Disease Control',
        'Environmental Health',
    ],

    /*
    |--------------------------------------------------------------------------
    | Priority Groups
    |--------------------------------------------------------------------------
    */
    
    'priorities' => [
        'PWD' => [
            'label' => 'Person with Disability',
            'color' => 'primary',
            'prefix' => 'P',
        ],
        'Pregnant' => [
            'label' => 'Pregnant Women',
            'color' => 'warning',
            'prefix' => 'P',
        ],
        'Senior' => [
            'label' => 'Senior Citizen (60+)',
            'color' => 'info',
            'prefix' => 'P',
        ],
        'Regular' => [
            'label' => 'Regular',
            'color' => 'secondary',
            'prefix' => 'R',
        ],
    ],
];
