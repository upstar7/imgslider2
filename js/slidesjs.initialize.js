jQuery(function() {
      jQuery('#slides').slidesjs({
        navigation: false,
        play: {
          active: setting.playBtn,
          effect: setting.effect,
          auto: setting.autoplay,
          interval: setting.interval
        }
      });
    });

