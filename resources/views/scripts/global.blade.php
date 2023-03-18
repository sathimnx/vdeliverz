<script>
    $('#readAllNotifications').on('click', function() {
        $.ajax({
            url: "{{ route('notifications-read-all') }}",
            type: 'get',
            data: {
                "_token": '{{ csrf_token() }}'

            },
            success: function(response) {
                if (response.status == true) {
                    toastr.success(response.message);
                    $('#loadNotifications').html(response.notify_view);
                    $('#notifyCount').text(response.count);
                }
            },
            error: function(response) {
                toastr.error("Bad Network!", "Please Refresh page");
            }
        })
    });

    $('#checkNotificationPerm').on('click', function() {
        // var url = "{{ asset('app-assets/notify/notify.wav') }}";
        // const audio = new Audio(url);
        // audio.play();
        Push.create('Notification Check');
    })
    $(function() {
        // flash auto hide
        $('#flash-msg .alert').not('.alert-danger, .alert-important').delay(6000).slideUp(500);
    })

    function change_status(id, name, check, column) {
        var status = 0;
        if ($(check).is(':checked')) {
            var status = 1;
        }
        $.ajax({
            url: "{{ route('change_status.index') }}",
            type: 'POST',
            data: {
                "_token": '{{ csrf_token() }}',
                status: status,
                id: id,
                name: name,
                column: column
            },
            success: function(response) {
                if (response.status == 1) {
                    toastr.success("Status Changed Successfully!", response.message);
                } else {
                    toastr.error("Error While changing Status", response.message);
                }

            },
            error: function(response) {
                toastr.error("Error While changing Status", "Please Refresh and Try!");
            }
        })
    }


    function checkUniqueName(table, field, id, value) {
        $.ajax({
            url: "{{ route('check-unique.index') }}",
            type: 'POST',
            data: {
                "_token": '{{ csrf_token() }}',
                table: table,
                id: id,
                field: field,
                value: value
            },
            success: function(response) {
                if (response.status == false) {
                    toastr.error(response.message);
                    $(id + ' .unique').remove();
                    $(id).append('<div class="help-block text-danger unique"><ul role="alert"><li>' +
                        response.message + '</li></ul></div>');
                }
                if (response.status == true) {
                    // console.log($(id+' .unique').addClass('d-none'));
                    $(id + ' .unique').remove();
                }
            }
        })
    }


    function showReviewOrder(prefix, id) {
        $.ajax({
            url: "{{ route('show-review.index') }}",
            type: 'POST',
            data: {
                "_token": '{{ csrf_token() }}',
                id: id,
                prefix: prefix
            },
            success: function(response) {
                $('#modal_order_id').val(id);
                $('#reviewOrder h4').text('Review Order ' + prefix + id);
                $('#dynamicReviewOrder').html(response);
                $('#reviewOrder').modal('show');
            }
        })
    }
</script>
