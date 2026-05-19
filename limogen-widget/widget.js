(function () {
  function init() {
    var host = document.getElementById("limogen-widget");
    if (!host) return;

    var userId = (host.getAttribute("data-user-id") || "").trim();
    var theme = (host.getAttribute("data-theme") || "light").trim();
    var width = host.getAttribute("data-width") || "100%";
    /** "auto" (default) = height follows iframe content via postMessage; fixed e.g. 600px = static height */
    var heightAttr = (host.getAttribute("data-height") || "auto").trim();
    var minHeightAttr = (host.getAttribute("data-min-height") || "320px").trim();
    var accentColor = (host.getAttribute("data-accent-color") || "").trim();
    var fontFamily = (host.getAttribute("data-font-family") || "").trim();
    /** Passed from embed host page (instantQuoteForm, etc.) into widget-frame.php */
    var embedWebsiteUrl = (host.getAttribute("data-embed-website-url") || "").trim();
    var embedName = (host.getAttribute("data-embed-name") || "").trim();
    var embedEmail = (host.getAttribute("data-embed-email") || "").trim();

    var source = "";
    try {
      source = window.location.hostname || "";
    } catch (e) {
      source = document.referrer ? new URL(document.referrer).hostname : "";
    }

    var src =
      "https://zabrin.xyz/limogen-widget/widget-frame.php" +
      "?user_id=" +
      encodeURIComponent(userId) +
      "&theme=" +
      encodeURIComponent(theme) +
      "&source=" +
      encodeURIComponent(source);

    if (accentColor) {
      src += "&accent_color=" + encodeURIComponent(accentColor);
    }
    if (fontFamily) {
      src += "&font_family=" + encodeURIComponent(fontFamily);
    }
    if (embedWebsiteUrl) {
      src +=
        "&embed_website_url=" + encodeURIComponent(embedWebsiteUrl);
    }
    if (embedName) {
      src += "&embed_name=" + encodeURIComponent(embedName);
    }
    if (embedEmail) {
      src += "&embed_email=" + encodeURIComponent(embedEmail);
    }

    var iframe = document.createElement("iframe");
    iframe.src = src;
    iframe.style.width = width;
    iframe.style.border = "0";
    iframe.style.display = "block";

    var autoHeight = !heightAttr || /^auto$/i.test(heightAttr);
    var frameOrigin = "";
    try {
      frameOrigin = new URL(src).origin;
    } catch (err) {
      frameOrigin = "";
    }

    if (autoHeight) {
      iframe.style.overflow = "hidden";
      iframe.style.minHeight = /^\d+$/.test(minHeightAttr)
        ? minHeightAttr + "px"
        : minHeightAttr;
      iframe.style.height = iframe.style.minHeight;

      window.addEventListener("message", function onFrameMessage(e) {
        if (!e || !e.data || e.data.type !== "limogen:resize") return;
        if (frameOrigin && e.origin !== frameOrigin) return;
        var h = parseInt(e.data.height, 10);
        if (!h || h < 1) return;
        var floor = 0;
        try {
          var raw = String(iframe.style.minHeight || minHeightAttr).trim();
          floor = parseInt(raw, 10) || 0;
        } catch (er) {
          floor = 0;
        }
        iframe.style.height = Math.max(h, floor) + "px";
      });
    } else {
      var heightStr = heightAttr;
      if (/^\d+$/.test(heightStr)) {
        heightStr = heightStr + "px";
      }
      iframe.style.height = heightStr;
      var minH = host.getAttribute("data-min-height");
      if (minH && String(minH).trim() !== "") {
        iframe.style.minHeight = /^\d+$/.test(String(minH).trim())
          ? String(minH).trim() + "px"
          : String(minH).trim();
      }
    }

    iframe.style.borderRadius = "16px";
    iframe.style.overflow = "hidden";
    iframe.setAttribute("loading", "lazy");
    iframe.setAttribute("referrerpolicy", "no-referrer-when-downgrade");
    iframe.allow = "geolocation *";

    iframe.setAttribute(
      "sandbox",
      "allow-forms allow-scripts allow-same-origin allow-popups",
    );

    host.innerHTML = "";
    host.appendChild(iframe);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
