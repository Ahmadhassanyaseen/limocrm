<?php
include_once 'components/layout/header.php';
include_once 'components/layout/sidebar.php';
?>
<div class="main-content app-content">
  <div class="container-fluid">
    <h1 class="page-title font-bold text-xl mb-3">Agreements</h1>
    <p class="text-sm text-textmuted dark:text-textmuted/50 mb-6 max-w-2xl">
      Use <strong>Agreement link</strong> on any lead detail page to copy the public signing and payment URL.
      Clients open it in the browser without logging in—review terms, draw a signature, and pay with Stripe.
    </p>
    <div class="ti-card custom-card">
      <div class="ti-card-body">
        <p class="mb-2 text-sm font-semibold">Setup checklist</p>
        <ul class="list-disc ms-5 text-sm text-textmuted space-y-1">
          <li>Run <code class="text-xs">database/limo_stripe_agreement.sql</code> on the SuiteCRM database (and add <code class="text-xs">stripe_customer_id_c</code> to Leads/Contacts in Studio if not using the commented ALTER).</li>
          <li>Create <code class="text-xs">custom/limo_agreement_config.php</code> on the SuiteCRM server (see <code class="text-xs">database/limo_agreement_config.sample.php</code>).</li>
          <li>Create <code class="text-xs">config/agreement_config.php</code> here with your Stripe <strong>publishable</strong> key.</li>
        </ul>
      </div>
    </div>
  </div>
</div>
<?php include_once 'components/layout/footer.php'; ?>
