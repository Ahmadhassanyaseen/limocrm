(function () {
  "use strict";

  /* page loader */
  function hideLoader() {
    const loader = document.getElementById("loader");
    loader.classList.add("loader-disable")
  }

  window.addEventListener("load", hideLoader);
  /* page loader */


  /* popover  */
  const popoverTriggerList = document.querySelectorAll(
    '[data-bs-toggle="popover"]'
  );
  const popoverList = [...popoverTriggerList].map(
    (popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl)
  );

  /* breadcrumb date range picker */
  flatpickr("#daterange", {
    mode: "range",
    dateFormat: "Y-m-d",
    defaultDate: ["2024-05-01", "2024-05-30"]
  });
  /* breadcrumb date range picker */

  if (document.querySelector("#hs-overlay-switcher")) {
  
    //switcher color pickers
    const pickrContainerPrimary = document.querySelector(
      ".pickr-container-primary"
    );
    const themeContainerPrimary = document.querySelector(
      ".theme-container-primary"
    );
    const pickrContainerBackground = document.querySelector(
      ".pickr-container-background"
    );
    const themeContainerBackground = document.querySelector(
      ".theme-container-background"
    );

    /* for theme primary */
    const nanoThemes = [
      [
        "nano",
        {
          defaultRepresentation: "RGB",
          components: {
            preview: true,
            opacity: false,
            hue: true,

            interaction: {
              hex: false,
              rgba: true,
              hsva: false,
              input: true,
              clear: false,
              save: false,
            },
          },
        },
      ],
    ];
    const nanoButtons = [];
    let nanoPickr = null;
    for (const [theme, config] of nanoThemes) {
      const button = document.createElement("button");
      button.innerHTML = theme;
      nanoButtons.push(button);

      button.addEventListener("click", () => {
        const el = document.createElement("p");
        pickrContainerPrimary.appendChild(el);

        /* Delete previous instance */
        if (nanoPickr) {
          nanoPickr.destroyAndRemove();
        }

        /* Apply active class */
        for (const btn of nanoButtons) {
          btn.classList[btn === button ? "add" : "remove"]("active");
        }

        /* Create fresh instance */
        nanoPickr = new Pickr(
          Object.assign(
            {
              el,
              theme,
              default: "#5c67f7",
            },
            config
          )
        );

        /* Set events */
        nanoPickr.on("changestop", (source, instance) => {
          let color = instance.getColor().toRGBA();
          let html = document.querySelector("html");
          html.style.setProperty(
            "--primary",
            `${Math.floor(color[0])} ${Math.floor(color[1])} ${Math.floor(
              color[2]
            )}`
          );
          html.style.setProperty(
            "--primary-rgb",
            `${Math.floor(color[0])} ,${Math.floor(color[1])}, ${Math.floor(
              color[2]
            )}`
          );
          /* theme color picker */
          localStorage.setItem(
            "primaryRGB",
            `${Math.floor(color[0])}, ${Math.floor(color[1])}, ${Math.floor(
              color[2]
            )}`
          );
          localStorage.setItem(
            "primaryRGB1",
            `${Math.floor(color[0])} ${Math.floor(color[1])} ${Math.floor(
              color[2]
            )}`
          );
          updateColors();
        });
      });

      themeContainerPrimary.appendChild(button);
    }
    nanoButtons[0].click();
    /* for theme primary */

    /* for theme background */
    const nanoThemes1 = [
      [
        "nano",
        {
          defaultRepresentation: "RGB",
          components: {
            preview: true,
            opacity: false,
            hue: true,

            interaction: {
              hex: false,
              rgba: true,
              hsva: false,
              input: true,
              clear: false,
              save: false,
            },
          },
        },
      ],
    ];
    const nanoButtons1 = [];
    let nanoPickr1 = null;
    for (const [theme, config] of nanoThemes) {
      const button = document.createElement("button");
      button.innerHTML = theme;
      nanoButtons1.push(button);

      button.addEventListener("click", () => {
        const el = document.createElement("p");
        pickrContainerBackground.appendChild(el);

        /* Delete previous instance */
        if (nanoPickr1) {
          nanoPickr1.destroyAndRemove();
        }

        /* Apply active class */
        for (const btn of nanoButtons) {
          btn.classList[btn === button ? "add" : "remove"]("active");
        }

        /* Create fresh instance */
        nanoPickr1 = new Pickr(
          Object.assign(
            {
              el,
              theme,
              default: "#5c67f7",
            },
            config
          )
        );

        /* Set events */
        nanoPickr1.on("changestop", (source, instance) => {
          let color = instance.getColor().toRGBA();
          let html = document.querySelector("html");
          html.style.setProperty(
            "--body-bg",
            `${Math.floor(color[0] + 14)}
             ${Math.floor(color[1] + 14)}
              ${Math.floor(color[2] + 14)}`
          );
          html.style.setProperty(
            "--dark-bg",
            `
            ${Math.floor(color[0])}
            ${Math.floor(color[1])}
            ${Math.floor(color[2])}
            `
          );
          html.style.setProperty(
            "--light",
            `
            ${Math.floor(color[0] + 5)}
            ${Math.floor(color[1] + 5)}
            ${Math.floor(color[2] + 5)}
            `
          );
          localStorage.removeItem("bgtheme");
          updateColors();
          html.classList.add("dark");
          html.classList.remove("light");
          html.setAttribute("data-menu-styles", "dark");
          html.setAttribute("data-header-styles", "transparent");
          document.querySelector("#switcher-dark-theme").checked = true;
          localStorage.setItem(
            "bodyBgRGB",
            `${Math.floor(color[0] + 14)}
             ${Math.floor(color[1] + 14)}
              ${Math.floor(color[2] + 14)}`
          );
          localStorage.setItem(
            "--light",
            `${Math.floor(color[0] + 5)}
             ${Math.floor(color[1] + 5)}
              ${Math.floor(color[2] + 5)}`
          );
          localStorage.setItem(
            "darkBgRGB",
            `${Math.floor(color[0])} ${Math.floor(color[1])} ${Math.floor(
              color[2]
            )}`
          );
        });
      });
      themeContainerBackground.appendChild(button);
    }
    nanoButtons1[0].click();
    /* for theme background */
  }

  /* Choices JS */
  document.addEventListener("DOMContentLoaded", function () {
    var genericExamples = document.querySelectorAll("[data-trigger]");
    for (let i = 0; i < genericExamples.length; ++i) {
      var element = genericExamples[i];
      new Choices(element, {
        allowHTML: true,
        placeholderValue: "This is a placeholder set in the config",
        searchPlaceholderValue: "Search",
      });
    }
  });
  /* Choices JS */

  /* footer year */
  document.getElementById("year").innerHTML = new Date().getFullYear();
  /* footer year */

  /* node waves */
  Waves.attach(".btn-wave", ["waves-light"]);
  Waves.init();
  /* node waves */

  /* card with close button */
  let DIV_BOX = ".box";
  let boxRemoveBtn = document.querySelectorAll(
    '[data-bs-toggle="box-remove"]'
  );
  boxRemoveBtn.forEach((ele) => {
    ele.addEventListener("click", function (e) {
      e.preventDefault();
      let $this = this;
      let box = $this.closest(DIV_BOX);
      box.remove();
      return false;
    });
  });
  /* card with close button */

  /* card with fullscreen */
  let boxFullscreenBtn = document.querySelectorAll(
    '[data-bs-toggle="box-fullscreen"]'
  );
  boxFullscreenBtn.forEach((ele) => {
    ele.addEventListener("click", function (e) {
      let $this = this;
      let box = $this.closest(DIV_BOX);
      box.classList.toggle("box-fullscreen");
      box.classList.remove("box-collapsed");
      e.preventDefault();
      return false;
    });
  });
  /* card with fullscreen */

  /* count-up */
  var i = 1;
  setInterval(() => {
    document.querySelectorAll(".count-up").forEach((ele) => {
      if (ele.getAttribute("data-count") >= i) {
        i = i + 1;
        ele.innerText = i;
      }
    });
  }, 10);
  /* count-up */

  /* back to top */
  const scrollToTop = document.querySelector(".scrollToTop");
  const $rootElement = document.documentElement;
  const $body = document.body;
  window.onscroll = () => {
    const scrollTop = window.scrollY || window.pageYOffset;
    const clientHt = $rootElement.scrollHeight - $rootElement.clientHeight;
    if (window.scrollY > 100) {
      scrollToTop.style.display = "flex";
    } else {
      scrollToTop.style.display = "none";
    }
  };
  scrollToTop.onclick = () => {
    window.scrollTo(0, 0);
  };
  /* back to top */

  /* header dropdowns scroll */
  var myHeadernotification = document.getElementById("header-notification-scroll");
  new SimpleBar(myHeadernotification, { autoHide: true });

  var myHeaderCart = document.getElementById("header-cart-items-scroll");
  new SimpleBar(myHeaderCart, { autoHide: true });
  /* header dropdowns scroll */

  const autoCompleteJS = new autoComplete({
    selector: "#header-search",
    data: {
      src: [
        "What is the meaning of life?",
        "How does gravity work?",
        "Why is the sky blue?",
        "What is the capital of France?",
        "Who painted the Mona Lisa?",
        "What is the speed of light?",
        "Why do we dream?",
        "How do birds fly?",
        "What is the largest mammal?",
        "Why do leaves change color in the fall?"
      ],
      cache: true,
    },
    resultItem: {
      highlight: true
    },
    events: {
      input: {
        selection: (event) => {
          const selection = event.detail.selection.value;
          autoCompleteJS.input.value = selection;
        }
      }
    }
  });
})();

/* full screen */
var elem = document.documentElement;
function openFullscreen() {
  if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
    requestFullscreen();
  } else {
    exitFullscreen();
  }
}
function requestFullscreen() {
  if (elem.requestFullscreen) {
    elem.requestFullscreen();
  } else if (elem.webkitRequestFullscreen) {
    elem.webkitRequestFullscreen();
  } else if (elem.msRequestFullscreen) {
    elem.msRequestFullscreen();
  }
}
// function exitFullscreen() {
//   if (document.exitFullscreen) {
//     document.exitFullscreen();
//   } else if (document.webkitExitFullscreen) {
//     document.webkitExitFullscreen();
//   } else if (document.msExitFullscreen) {
//     document.msExitFullscreen();
//   }
// }
// Listen for fullscreen change event
document.addEventListener("fullscreenchange", handleFullscreenChange);
// function handleFullscreenChange() {
  
//   let open = document.querySelector(".full-screen-open");
//   let close = document.querySelector(".full-screen-close");

//   if (document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement) {
//     // Update icon for fullscreen mode
//     close.classList.add("block");
//     close.classList.remove("hidden");
//     open.classList.add("hidden");
//   } else {
//     // Update icon for non-fullscreen mode
//     close.classList.remove("block");
//     open.classList.remove("hidden");
//     close.classList.add("hidden");
//     open.classList.add("block");
//   }
// }
/* full screen */

/* toggle switches */
let customSwitch = document.querySelectorAll(".toggle");
customSwitch.forEach((e) =>
  e.addEventListener("click", () => {
    e.classList.toggle("on");
  })
);
/* toggle switches */

/* header dropdown close button */

/* for cart dropdown */
const headerbtn = document.querySelectorAll(".dropdown-item-close");
headerbtn.forEach((button) => {
  button.addEventListener("click", (e) => {
    e.preventDefault();
    e.stopPropagation();
    button.parentNode.parentNode.parentNode.parentNode.parentNode.remove();
    document.getElementById("cart-data").innerText = `${document.querySelectorAll(".dropdown-item-close").length
      } `;
    document.getElementById("cart-icon-badge").innerText = `${document.querySelectorAll(".dropdown-item-close").length
      }`;
    console.log(
      document.getElementById("header-cart-items-scroll").children.length
    );
    if (document.querySelectorAll(".dropdown-item-close").length == 0) {
      let elementHide = document.querySelector(".empty-header-item");
      let elementShow = document.querySelector(".empty-item");
      elementHide.classList.add("hidden");
      elementShow.classList.remove("hidden");
    }
  });
});
/* for cart dropdown */

/* for notifications dropdown */
const headerbtn1 = document.querySelectorAll(".dropdown-item-close1");
headerbtn1.forEach((button) => {
  button.addEventListener("click", (e) => {
    e.preventDefault();
    e.stopPropagation();
    button.parentNode.parentNode.parentNode.parentNode.remove();
    document.getElementById("notifiation-data").innerText = `${document.querySelectorAll(".dropdown-item-close1").length
      } Unread`;
    if (document.querySelectorAll(".dropdown-item-close1").length == 0) {
      let elementHide1 = document.querySelector(".empty-header-item1");
      let elementShow1 = document.querySelector(".empty-item1");
      elementHide1.classList.add("hidden");
      elementShow1.classList.remove("hidden");
    }
  });
});

// Preview profile image before upload
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Remove profile picture
function removeProfilePicture() {
    if (confirm('Are you sure you want to remove your profile picture?')) {
        document.getElementById('profilePreview').src = 'assets/images/faces/default-avatar.png';
        document.getElementById('profile_picture').value = '';
    }
}

// Toggle password visibility
function togglePassword(fieldId) {
    var field = document.getElementById(fieldId);
    var type = field.type === 'password' ? 'text' : 'password';
    field.type = type;
}

// Password strength checker
document.getElementById('new_password')?.addEventListener('input', function() {
    var password = this.value;
    var strength = 0;
    var progressBar = document.getElementById('passwordStrength');
    var strengthText = document.getElementById('passwordStrengthText');
    
    if (password.length >= 8) strength += 25;
    if (password.match(/[a-z]+/)) strength += 25;
    if (password.match(/[A-Z]+/)) strength += 25;
    if (password.match(/[0-9]+/)) strength += 15;
    if (password.match(/[$@#&!]+/)) strength += 10;
    
    progressBar.style.width = strength + '%';
    
    if (strength < 30) {
        progressBar.className = 'progress-bar bg-danger';
        strengthText.innerHTML = 'Weak password';
    } else if (strength < 60) {
        progressBar.className = 'progress-bar bg-warning';
        strengthText.innerHTML = 'Medium password';
    } else if (strength < 80) {
        progressBar.className = 'progress-bar bg-info';
        strengthText.innerHTML = 'Good password';
    } else {
        progressBar.className = 'progress-bar bg-success';
        strengthText.innerHTML = 'Strong password';
    }
});

// Password match checker
document.getElementById('confirm_password')?.addEventListener('input', function() {
    var password = document.getElementById('new_password').value;
    var confirm = this.value;
    var matchDiv = document.getElementById('passwordMatch');
    
    if (password === confirm) {
        matchDiv.innerHTML = '<span class="text-success"><i class="ri-check-line"></i> Passwords match</span>';
    } else {
        matchDiv.innerHTML = '<span class="text-danger"><i class="ri-close-line"></i> Passwords do not match</span>';
    }
});

// Validate password form
function validatePassword() {
    var password = document.getElementById('new_password').value;
    var confirm = document.getElementById('confirm_password').value;
    
    if (password !== confirm) {
        alert('Passwords do not match!');
        return false;
    }
    
    if (password.length < 8) {
        alert('Password must be at least 8 characters long!');
        return false;
    }
    
    return true;
}

// Setup 2FA
function setup2FA() {
    window.location.href = 'two-factor-setup.php';
}

// Logout all devices
function logoutAllDevices() {
    if (confirm('This will log you out from all other devices. Continue?')) {
        window.location.href = 'logout-all-devices.php';
    }
}

// Save active tab
function saveActiveTab() {
    var activeTab = document.querySelector('.tab-pane.active');
    if (activeTab) {
        var form = activeTab.querySelector('form');
        if (form) {
            form.submit();
        }
    }
}

// Tab persistence
document.addEventListener('DOMContentLoaded', function() {
    // Get tab from URL
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    
    if (tab) {
        // Remove active class from all tabs and panes
        document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('show', 'active'));
        
        // Activate the selected tab
        const activeLink = document.querySelector(`[data-tab="${tab}"]`);
        const activePane = document.getElementById(`${tab}-pane`);
        
        if (activeLink && activePane) {
            activeLink.classList.add('active');
            activePane.classList.add('show', 'active');
        }
    }
    
    // Update URL when tab changes
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
            const tab = this.getAttribute('data-tab');
            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.pushState({}, '', url);
        });
    });
});
