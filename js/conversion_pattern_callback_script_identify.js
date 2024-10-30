/* eslint-disable no-undef */

function conversionPatternGetNewURLString(href, name, value) {
  const url = new URL(href);
  const searchParams = url.searchParams;
  searchParams.delete(name);
  searchParams.set(name, value);
  return url.toString();
}

function conversionPatternPersistURLParams(lilocp) {
  return function persist() {
    window.removeEventListener('lilolocationchange', persist);
    const href = window.location.href;
    const newURLString = conversionPatternGetNewURLString(
      href,
      'lilocp',
      lilocp,
    );
    window.history.replaceState(null, '', newURLString);

    window.addEventListener('lilolocationchange', persist);
  };
}

function getBodyJson(value) {
  const href = window.location.href;
  const referer = window.document.referrer;

  const {height, width, pixelDepth, colorDepth} = window.screen;
  const llScreen = {
    height: height || 0,
    width: width || 0,
    pixelDepth: pixelDepth || 0,
    colorDepth: colorDepth || 0,
  };
  return {
    referer,
    screen_height: llScreen.height,
    screen_width: llScreen.width,
    screen_pixel_depth: llScreen.pixelDepth,
    screen_color_depth: llScreen.colorDepth,
    href,
    action: 'my_user_identify',
    value: value,
  };
}

jQuery(document).ready(async function () {
  jQuery.ajax({
    type: 'post',
    url: conversionPatternAjax.ajaxurl,
    headers: {'Content-typer': 'application/json'},
    xhrFields: {
      withCredentials: true,
    },
    data: getBodyJson('identify'),
    success: function (response) {
      if (response.lilocp) {
        conversionPatternPersistURLParams(response.lilocp)();
      } else {
        console.log('Failed to ping back php instance');
      }
    },
  });
});
