 <!-- End::app-content -->
      <footer
          class="mt-auto py-4 bg-white dark:bg-bodybg text-center border-t border-defaultborder dark:border-defaultborder/10"
        >
        <div class="container">
          <span class="text-textmuted dark:text-textmuted/50">
            Copyright © <span id="year">2026</span>
            <a href="j#" class="text-dark font-medium"
              >LimoGen</a
            >. Designed with <span class="text-danger">❤</span> by
            <a href="https://www.shmai.com/" target="_blank">
              <span class="font-medium text-primary">SHMAI</span>
            </a>
            All rights reserved
          </span>
        </div>
      </footer>
      <div
        class="hs-overlay ti-modal hidden"
        id="header-responsive-search"
        tabindex="-1"
        aria-labelledby="header-responsive-search"
        >
        <div class="ti-modal-box">
          <div class="ti-modal-dialog">
            <div class="ti-modal-content">
              <div class="ti-modal-body">
                <div class="input-group">
                  <input
                    type="text"
                    class="form-control border-end-0 !border-s"
                    placeholder="Search Anything ..."
                    aria-label="Search Anything ..."
                    aria-describedby="button-addon2"
                  />
                  <button
                    aria-label="button"
                    class="ti-btn ti-btn-primary !m-0"
                    type="button"
                    id="button-addon2"
                  >
                    <i class="bi bi-search"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Scroll To Top -->
    <div class="scrollToTop">
      <span class="arrow"><i class="ri-arrow-up-line text-xl"></i></span>
    </div>
    <div id="responsive-overlay"></div>
    <!-- Scroll To Top -->
    <script src="assets/js/switch.js"></script>
    <script src="assets/libs/@popperjs/core/umd/popper.min.js"></script>
    <script src="assets/libs/preline/preline.js"></script>
    <script src="assets/js/defaultmenu.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/js/sticky.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/js/simplebar.js"></script>
    <script src="assets/libs/@tarekraafat/autocomplete.js/autoComplete.min.js"></script>
    <script src="assets/libs/@simonwep/pickr/pickr.es5.min.js"></script>
    <script src="assets/libs/flatpickr/flatpickr.min.js"></script>
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
    <script src="assets/js/crm-dashboard.js"></script>
    <svg
      id="SvgjsSvg1006"
      width="2"
      height="0"
      xmlns="http://www.w3.org/2000/svg"
      version="1.1"
      xmlns:xlink="http://www.w3.org/1999/xlink"
      xmlns:svgjs="http://svgjs.dev"
      style="
        overflow: hidden;
        top: -100%;
        left: -100%;
        position: absolute;
        opacity: 0;
      "
    >
      <defs id="SvgjsDefs1007"></defs>
      <polyline id="SvgjsPolyline1008" points="0,0"></polyline>
      <path id="SvgjsPath1009" d="M0 0 "></path>
    </svg>
    <script src="assets/js/custom.js"></script>
    <div
      class="pcr-app"
      data-theme="nano"
      aria-label="color picker dialog"
      role="window"
      style="left: 0px; top: 8px"
    >
      <div class="pcr-selection">
        <div class="pcr-color-preview">
          <button
            type="button"
            class="pcr-last-color"
            aria-label="use previous color"
            style="--pcr-color: rgba(92, 103, 247, 1)"
          ></button>
          <div
            class="pcr-current-color"
            style="--pcr-color: rgba(92, 103, 247, 1)"
          ></div>
        </div>

        <div class="pcr-color-palette">
          <div
            class="pcr-picker"
            style="
              left: calc(62.753% - 9px);
              top: calc(3.13725% - 9px);
              background: rgb(92, 103, 247);
            "
          ></div>
          <div
            class="pcr-palette"
            tabindex="0"
            aria-label="color selection area"
            role="listbox"
            style="
              background: linear-gradient(to top, rgb(0, 0, 0), transparent),
                linear-gradient(to left, rgb(0, 18, 255), rgb(255, 255, 255));
            "
          ></div>
        </div>

        <div class="pcr-color-chooser">
          <div
            class="pcr-picker"
            style="
              left: calc(65.4839% - 9px);
              background-color: rgb(0, 18, 255);
            "
          ></div>
          <div
            class="pcr-hue pcr-slider"
            tabindex="0"
            aria-label="hue selection slider"
            role="slider"
          ></div>
        </div>

        <div class="pcr-color-opacity" style="display: none" hidden="">
          <div class="pcr-picker"></div>
          <div
            class="pcr-opacity pcr-slider"
            tabindex="0"
            aria-label="selection slider"
            role="slider"
          ></div>
        </div>
      </div>

      <div class="pcr-swatches"></div>

      <div class="pcr-interaction">
        <input
          class="pcr-result"
          type="text"
          spellcheck="false"
          aria-label="color input field"
        />

        <input
          class="pcr-type"
          data-type="HEXA"
          value="HEXA"
          type="button"
          style="display: none"
          hidden=""
        />
        <input
          class="pcr-type active"
          data-type="RGBA"
          value="RGBA"
          type="button"
        />
        <input
          class="pcr-type"
          data-type="HSLA"
          value="HSLA"
          type="button"
          style="display: none"
          hidden=""
        />
        <input
          class="pcr-type"
          data-type="HSVA"
          value="HSVA"
          type="button"
          style="display: none"
          hidden=""
        />
        <input
          class="pcr-type"
          data-type="CMYK"
          value="CMYK"
          type="button"
          style="display: none"
          hidden=""
        />

        <input
          class="pcr-save"
          value="Save"
          type="button"
          style="display: none"
          hidden=""
          aria-label="save and close"
        />
        <input
          class="pcr-cancel"
          value="Cancel"
          type="button"
          style="display: none"
          hidden=""
          aria-label="cancel and close"
        />
        <input
          class="pcr-clear"
          value="Clear"
          type="button"
          style="display: none"
          hidden=""
          aria-label="clear and close"
        />
      </div>
    </div>
    <div
      class="pcr-app"
      data-theme="nano"
      aria-label="color picker dialog"
      role="window"
      style="left: 0px; top: 8px"
    >
      <div class="pcr-selection">
        <div class="pcr-color-preview">
          <button
            type="button"
            class="pcr-last-color"
            aria-label="use previous color"
            style="--pcr-color: rgba(92, 103, 247, 1)"
          ></button>
          <div
            class="pcr-current-color"
            style="--pcr-color: rgba(92, 103, 247, 1)"
          ></div>
        </div>

        <div class="pcr-color-palette">
          <div
            class="pcr-picker"
            style="
              left: calc(62.753% - 9px);
              top: calc(3.13725% - 9px);
              background: rgb(92, 103, 247);
            "
          ></div>
          <div
            class="pcr-palette"
            tabindex="0"
            aria-label="color selection area"
            role="listbox"
            style="
              background: linear-gradient(to top, rgb(0, 0, 0), transparent),
                linear-gradient(to left, rgb(0, 18, 255), rgb(255, 255, 255));
            "
          ></div>
        </div>

        <div class="pcr-color-chooser">
          <div
            class="pcr-picker"
            style="
              left: calc(65.4839% - 9px);
              background-color: rgb(0, 18, 255);
            "
          ></div>
          <div
            class="pcr-hue pcr-slider"
            tabindex="0"
            aria-label="hue selection slider"
            role="slider"
          ></div>
        </div>

        <div class="pcr-color-opacity" style="display: none" hidden="">
          <div class="pcr-picker"></div>
          <div
            class="pcr-opacity pcr-slider"
            tabindex="0"
            aria-label="selection slider"
            role="slider"
          ></div>
        </div>
      </div>

      <div class="pcr-swatches"></div>

      <div class="pcr-interaction">
        <input
          class="pcr-result"
          type="text"
          spellcheck="false"
          aria-label="color input field"
        />

        <input
          class="pcr-type"
          data-type="HEXA"
          value="HEXA"
          type="button"
          style="display: none"
          hidden=""
        />
        <input
          class="pcr-type active"
          data-type="RGBA"
          value="RGBA"
          type="button"
        />
        <input
          class="pcr-type"
          data-type="HSLA"
          value="HSLA"
          type="button"
          style="display: none"
          hidden=""
        />
        <input
          class="pcr-type"
          data-type="HSVA"
          value="HSVA"
          type="button"
          style="display: none"
          hidden=""
        />
        <input
          class="pcr-type"
          data-type="CMYK"
          value="CMYK"
          type="button"
          style="display: none"
          hidden=""
        />

        <input
          class="pcr-save"
          value="Save"
          type="button"
          style="display: none"
          hidden=""
          aria-label="save and close"
        />
        <input
          class="pcr-cancel"
          value="Cancel"
          type="button"
          style="display: none"
          hidden=""
          aria-label="cancel and close"
        />
        <input
          class="pcr-clear"
          value="Clear"
          type="button"
          style="display: none"
          hidden=""
          aria-label="clear and close"
        />
      </div>
    </div>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="assets/css/limo-driver-intro.css" />
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.js.iife.js" crossorigin="anonymous"></script>
    <script src="assets/js/custom-switcher.min.js"></script>
    <script src="assets/js/limo-intro-wizard.js"></script>
  </body>
</html>
