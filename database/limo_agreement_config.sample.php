<?php
/**
 * Copy this file to SuiteCRM:
 *   custom/limo_agreement_config.php
 *
 * Never commit real secrets. Use getenv() in production if preferred.
 */
return [
    /** Shared secret with signing links — must match only on server (used to sign/verify ?t=) */
    'link_secret' => 'REPLACE_WITH_LONG_RANDOM_STRING',

    /** Stripe secret key (starts with sk_test_ or sk_live_) */
    'stripe_secret_key' => '',

    /** Base URL where public agreement.php is hosted (no trailing slash) */
    'agreement_public_base_url' => 'https://your-domain.example/limo_crm',
];
