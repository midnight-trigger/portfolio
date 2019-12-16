
<footer id='footer'>
  <p>Copyright Junya Nishiwaki. All Rights Reserved.</p>
</footer>

<script src="js/jquery-3.4.1.min.js"></script>
<script>
$(function() {
'use strict';

// Footer最下部表示
let $ftr = $('#footer');
if (window.innerHeight > $ftr.offset().top + $ftr.outerHeight()) {
    $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
}

// 画像ライブプレビュー
let $imgBox = $('.img-box');
$imgBox.on('dragover', function(e) {
  e.stopPropagation();
  e.preventDefault();
  $(this).css('border', '3px dashed #ccc');
});
$imgBox.on('dragleave', function(e) {
  e.stopPropagation();
  e.preventDefault();
  $(this).css('border', 'none');
});
$('.live-preview').on('change', function(e) {
  $imgBox.css('border', 'none');
  let file = this.files[0],
      $img = $(this).siblings('.img-preview'),
      fileReader = new FileReader();

  fileReader.onload = function(event) {
    $img.attr('src', event.target.result).show();
  }

  fileReader.readAsDataURL(file);
});

// 画像切替
let array = [];
$('.js-changeImg-sub').on('click', function() {
  array[0] = $(this).attr('src');
  array[1] = $('#js-changeImg-main').attr('src');
  $(this).attr('src', array[1]);
  $('#js-changeImg-main').attr('src', array[0]);
})

// お気に入り機能
let likeProductId = $('.js-click-like').data('productid');
$('.js-click-like').on('click', function() {
  let $this = $(this);
  $.ajax({
    type: "POST",
    url: "ajaxLike.php",
    data: {productId: likeProductId}
  }).done(function (data) {
    $this.toggleClass('active');
  }).fail(function (msg) {
    console.log('Ajax error!');
  });
});
});
</script>
</body>
</html>
