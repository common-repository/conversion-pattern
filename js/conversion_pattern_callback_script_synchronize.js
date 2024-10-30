/* eslint-disable no-undef */

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
  // We dont want to invoke synchronize immediately, so we sleep for a bit
  await new Promise((r) => setTimeout(r, 1000));
  jQuery.ajax({
    type: 'post',
    url: conversionPatternAjax.ajaxurl,
    headers: {'Content-typer': 'application/json'},
    xhrFields: {
      withCredentials: true,
    },
    data: getBodyJson('synchronize'),
    success: function (response) {
      if (response.type == 'success') {
        console.log(response);
      } else {
        console.log('Failed to ping back php instance');
      }
    },
  });
});
