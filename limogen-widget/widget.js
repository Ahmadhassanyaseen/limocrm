(function () {
  function init() {
    var host = document.getElementById("limogen-widget");
    if (!host) return;

    var userId = (host.getAttribute("data-user-id") || "").trim();
    var theme = (host.getAttribute("data-theme") || "light").trim();
    var width = host.getAttribute("data-width") || "100%";
    var height = host.getAttribute("data-height") || "800px";
    var accentColor = (host.getAttribute("data-accent-color") || "").trim();
    var fontFamily = (host.getAttribute("data-font-family") || "").trim();

    var source = "";
    try {
      source = window.location.hostname || "";
    } catch (e) {
      source = document.referrer ? new URL(document.referrer).hostname : "";
    }

    var src =
      "https://zabrin.xyz/limogen-widget/widget-frame.php" +
      "?user_id=" + encodeURIComponent(userId) +
      "&theme=" + encodeURIComponent(theme) +
      "&source=" + encodeURIComponent(source);

    if (accentColor) {
      src += "&accent_color=" + encodeURIComponent(accentColor);
    }
    if (fontFamily) {
      src += "&font_family=" + encodeURIComponent(fontFamily);
    }

    var iframe = document.createElement("iframe");
    iframe.src = src;
    iframe.style.width = width;

    var heightStr = typeof height === "string" ? height : height + "px";
    if (/^\d+$/.test(String(height).trim())) {
      heightStr = String(height).trim() + "px";
    }
    iframe.style.height = heightStr;
    iframe.style.minHeight = heightStr;

    iframe.style.border = "0";
    iframe.style.borderRadius = "16px";
    iframe.style.overflow = "hidden";
    iframe.setAttribute("loading", "lazy");
    iframe.setAttribute("referrerpolicy", "no-referrer-when-downgrade");
    iframe.allow = "geolocation *";

    iframe.setAttribute("sandbox", "allow-forms allow-scripts allow-same-origin allow-popups");

    host.innerHTML = "";
    host.appendChild(iframe);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
