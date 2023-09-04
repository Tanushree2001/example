(function ($, Drupal) {
  Drupal.behaviors.customLike = {
    attach: function (context, settings) {
      $(".like-button", context).click(function () {
        var nodeId =  $(this).data("nodeId");
        var button = $(this);
        $.ajax({
          // url: "/custom_like_button/like/" + nodeId,
          url: "/custom_like_button/" + nodeId,
          method: "POST",
          success: function (response) {
            button.text("Like (" + response.likes + ")");
          },
        });
      });
    },
  };    
})(jQuery, Drupal);