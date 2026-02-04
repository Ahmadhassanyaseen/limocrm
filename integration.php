<?php include_once "components/layout/header.php"; ?>
<?php include_once "components/layout/sidebar.php"; ?>
<?php include_once "config/api.php"; ?>
 
<div id="limogen-widget"
     data-user-id="109a9a69-0209-5bfa-4079-696ba7b1cc44"
     data-width="100%"
     data-height="620"
     
     >
</div>

<script src="https://zabrin.xyz/limogen-widget/widget.js" async></script>

<div class="shortcodeparent">
<div class="crm-code-widget"  style=" display:block !important; visibility:visible !important;">

  <div class="crm-code-header">
    <span class="crm-code-title"> Website form Widget Embed Code</span>

    <button class="crm-copy-btn" data-copy-target="widgetCode">
      Copy Code
    </button>
  </div>

  <pre class="crm-code-box" id="widgetCode"><code>&lt;div id="limogen-widget"
     data-user-id="109a9a69-0209-5bfa-4079-696ba7b1cc44"
     data-width="100%"
     data-height="820"&gt;
&lt;/div&gt;

&lt;script src="https://zabrin.xyz/limogen-widget/widget.js" async&gt;&lt;/script&gt;
</code></pre>

  <small class="crm-copy-status"></small>

</div>

</div>




<script>
document.addEventListener("click", function (e) {

  const btn = e.target.closest(".crm-copy-btn");
  if (!btn) return;

  const target = btn.getAttribute("data-copy-target");
  const codeBox = document.getElementById(target);

  if (!codeBox) return;

  const text = codeBox.innerText;

  navigator.clipboard.writeText(text).then(() => {

    btn.innerText = "Copied";

    const status = btn.closest(".crm-code-widget")
                      .querySelector(".crm-copy-status");

    if (status) status.innerText = "Code copied successfully.";

    setTimeout(() => {
      btn.innerText = "Copy Code";
      if (status) status.innerText = "";
    }, 1500);

  });

});
</script>


<?php include_once "components/layout/footer.php"; ?>