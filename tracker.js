(function () {
  function getCookieWithPrefix(prefix) {
      // Get all cookies as a single string
      const cookies = document.cookie;

      // Split the cookies string into individual cookies
      const cookieArray = cookies.split(';');

      // Iterate through each cookie pair
      for (let cookie of cookieArray) {
          // Trim leading/trailing spaces from the cookie string
          cookie = cookie.trim();

          // Check if the cookie starts with the given prefix
          if (cookie.startsWith(prefix)) {
              // Extract the cookie value
              const value = cookie.substring(cookie.indexOf('=') + 1);

              // Extract the name after the prefix, e.g. `sabt_test` -> `test`
              const experimentName = cookie.substring(prefix.length, cookie.indexOf('='));

              // Return both the experimentName and the cookie value
              return {
                  value: value,
                  experimentName: experimentName,
              };
          }
      }

      // Return null if no cookie with the prefix is found
      return null;
  }

  function init() {
      const sabtCookie = getCookieWithPrefix('sabt_');

      Matomo.addPlugin('SimpleABTesting', {
          log: function () {
              // If no cookie with the prefix is found, return nothing
              if (!sabtCookie) {
                  return '';
              }

              // Extract the cookie's value and the portion of the name after "sabt_"
              const { value, experimentName } = sabtCookie;

              // Build the tracking parameter based on the value
              if (value === '1') {
                  return `&sabt=1&experiment=${experimentName}`;
              }

              if (value === '0') {
                  return `&sabt=0&experiment=${experimentName}`;
              }

              // Return nothing if the cookie has other unexpected values
              return '';
          },
      });
  }

  // If Matomo object exists, initialize the plugin
  if ('object' === typeof window.Matomo) {
      init();
  } else {
      // Tracker might not be loaded yet
      if ('object' !== typeof window.matomoPluginAsyncInit) {
          window.matomoPluginAsyncInit = [];
      }
      window.matomoPluginAsyncInit.push(init);
  }
})();