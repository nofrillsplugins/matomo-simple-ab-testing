(function () {
  return function (parameters, TagManager) {
    this.fire = function () {
      let experiment = parameters.get("experiment");
      let parts = experiment.split(",");
      let name = "ab_" + parts[0];
      let start = parts[1] + "T00:00:00Z";
      let stop = parts[2] + "T23:59:00Z";
      let css = parts[3].replace(/\+/g, "%20");
      let cssInsert = decodeURIComponent(css);
      let js = parts[4].replace(/\+/g, "%20");
      let jsInsert = decodeURIComponent(js);
      let dimension = parts[5];
      var _paq = (window._paq = window._paq || []);
      initExp(_paq, name, start, stop, jsInsert, cssInsert, dimension);
      function initExp(
        _paq,
        testName,
        testStartDate,
        testEndDate,
        scriptText,
        cssText,
        customDimension
      ) {
        let currentVariant = getCookie(testName);
        let currentDate = new Date();
        let startDate = new Date(testStartDate);
        let endDate = new Date(testEndDate);

        if (currentDate >= startDate && currentDate <= endDate) {
          if (!currentVariant) {
            // Randomly assign variant 0 or 1
            currentVariant = Math.random() < 0.5 ? "0" : "1";
            setCookie(testName, currentVariant, testEndDate);
          }
          if (currentVariant === "1") {
            try {
              // Insert CSS
              let style = document.createElement("style");
              style.type = "text/css";
              style.textContent = cssText;
              document.head.appendChild(style);

              // Insert JS script
              let script = document.createElement("script");
              script.type = "text/javascript";
              script.text = scriptText;
              document.head.appendChild(script);

              callDimension(customDimension, testName, currentVariant);
            } catch (e) {
              console.error("Error in script execution", e);
            }
          } else {
            callDimension(customDimension, testName, currentVariant);
          }
        }
      }

      function callDimension(customDimension, testName, currentVariant) {
        let variantName = currentVariant === "1" ? "variant" : "control";
        window._paq.push([
          "setCustomDimension",
          customDimension,
          testName + "-" + variantName,
        ]);
      }

      // Function to set a cookie
      function setCookie(name, value, expires) {
        let date = new Date(expires);
        let cookie =
          name +
          "=" +
          encodeURIComponent(value) +
          ";expires=" +
          date.toUTCString() +
          ";path=/";
        document.cookie = cookie;
      }

      // Function to get a cookie by name
      function getCookie(name) {
        // Function to escape special characters
        function escapeRegExp(string) {
          return string.replace(/[.*+\-?^${}()|[\]\\]/g, "\\$&"); // $& means the whole matched string
        }

        // Escape the cookie name
        let safeName = escapeRegExp(name);

        // Create the regular expression
        let match = document.cookie.match(
          new RegExp("(^| )" + safeName + "=([^;]+)")
        );

        if (match) {
          return decodeURIComponent(match[2]);
        }
        return null;
      }
    };
  };
})();
