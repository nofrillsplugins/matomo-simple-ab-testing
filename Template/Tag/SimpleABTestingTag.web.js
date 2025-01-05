(function () {
  return function (parameters, TagManager) {
      this.fire = function () {
          const experiment = parameters.get("experiment");
          const parts = experiment.split(",");
          const name = "ab_" + parts[0];
          const start = parts[1] + "T00:00:00Z";
          const stop = parts[2] + "T23:59:00Z";
          const css = decodeURIComponent(parts[3].replace(/\+/g, "%20"));
          const js = decodeURIComponent(parts[4].replace(/\+/g, "%20"));
          const dimension = parts[5];
          const _paq = (window._paq = window._paq || []);
          const VARIANT_ORIGINAL = "0";
          const VARIANT_TEST = "1";

          initExp(_paq, name, start, stop, js, css, dimension);

          function initExp(_paq, testName, testStartDate, testEndDate, scriptText, cssText, customDimension) {
              let currentVariant = getCookie(testName);
              const currentDate = new Date();
              const startDate = new Date(testStartDate);
              const endDate = new Date(testEndDate);

              if (currentDate >= startDate && currentDate <= endDate) {
                  if (!currentVariant) {
                      // Randomly assign original or variant.
                      currentVariant = Math.random() < 0.5 ? VARIANT_ORIGINAL : VARIANT_TEST;
                      setCookie(testName, currentVariant, testEndDate);
                  }
                  if (currentVariant === VARIANT_TEST) {
                      try {
                          insertCSS(cssText);
                          insertJS(scriptText);
                          callDimension(customDimension, testName, currentVariant);
                      } catch (e) {
                          console.error("Error in script execution", e);
                      }
                  } else {
                      callDimension(customDimension, testName, currentVariant);
                  }
              }
          }

          /**
           * Function to insert CSS into the document head
           */
          function insertCSS(cssText) {
              const style = document.createElement("style");
              style.type = "text/css";
              style.textContent = cssText;
              document.head.appendChild(style);
          }

          /**
           * Function to insert JS script into the document head
           */
          function insertJS(scriptText) {
              const script = document.createElement("script");
              script.type = "text/javascript";
              script.text = scriptText;
              document.head.appendChild(script);
          }

          /**
           * Function to call a custom dimension
           */
          function callDimension(customDimension, testName, currentVariant) {
              const variantName = currentVariant === VARIANT_TEST ? "variant" : "original";
              window._paq.push([
                  "setCustomDimension",
                  customDimension,
                  testName + "-" + variantName,
              ]);
          }

          /**
           * Function to set a cookie
           */
          function setCookie(name, value, expires) {
              const date = new Date(expires);
              const cookie = name + "=" + encodeURIComponent(value) + ";expires=" + date.toUTCString() + ";path=/";
              document.cookie = cookie;
          }

          /**
           * Function to get a cookie by name
           */
          function getCookie(name) {
              const escapeRegExp = (string) => string.replace(/[.*+\-?^${}()|[\]\\]/g, "\\$&");
              const safeName = escapeRegExp(name);
              const match = document.cookie.match(new RegExp("(^| )" + safeName + "=([^;]+)"));
              return match ? decodeURIComponent(match[2]) : null;
          }
      };
  };
})();