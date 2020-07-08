import '../css/posts.scss';

$(document).ready(function() {
    $(".remove-button").on('click', function(e) {
        var postId = e.target.getAttribute('data-id');
        $.ajax({
            url: '/post/delete/' + postId,
            success: function (data) {
                console.log(data);
                if(data.msg === 'success') {
                    $('#pRow' + data.postId).remove();
                    $('#pageNotice').html('Post was removed');
                    $('#pageNotice').show();
                }
            },
            dataType: 'json'
        });
    });
});
