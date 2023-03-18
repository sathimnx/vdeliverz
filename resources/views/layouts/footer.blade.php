<!-- BEGIN: Footer-->
<footer class="footer footer-static footer-light">
    <p class="clearfix mb-0"><span class="float-left d-inline-block">2021 &copy; {{ config('app.name') }}</span>
        {{-- <span class="float-right d-sm-inline-block d-none">Crafted with<i class="bx bxs-heart pink mx-50 font-small-3"></i>by<a class="text-uppercase" href="https://1.envato.market/pixinvent_portfolio" target="_blank">Mindnotix</a></span> --}}
        <button class="btn btn-primary btn-icon scroll-top" type="button"><i class="bx bx-up-arrow-alt"></i></button>
    </p>
</footer>
<!-- END: Footer-->


<!-- BEGIN: Vendor JS-->
<script src="{{ asset('app-assets/vendors/js/vendors.min.js') }}"></script>
<script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.tools.min.js') }}"></script>
<script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.defaults.min.js') }}"></script>
<script src="{{ asset('app-assets/fonts/LivIconsEvo/js/LivIconsEvo.min.js') }}"></script>

<!-- BEGIN Vendor JS-->
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script> --}}
@stack('scripts')
<!-- BEGIN: Page Vendor JS-->
{{-- <script src="{{ asset('app-assets/vendors/js/charts/chartist.min.js') }}"></script> --}}
<script src="{{ asset('app-assets/vendors/js/forms/repeater/jquery.repeater.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
<!-- END: Page Vendor JS-->

<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.time.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/legacy.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/daterange/moment.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/daterange/daterangepicker.js') }}"></script>

<!-- BEGIN: Theme JS-->
<script src="{{ asset('app-assets/js/scripts/configs/vertical-menu-dark.min.js') }}"></script>
<script src="{{ asset('app-assets/js/core/app-menu.min.js') }}"></script>
<script src="{{ asset('app-assets/js/core/app.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/components.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/footer.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/customizer.min.js') }}"></script>
<!-- END: Theme JS-->

<!-- BEGIN: Page JS-->
{{-- <script src="{{ asset('assets/app-assets/js/scripts/charts/chart-chartist.min.js') }}"></script> --}}
<script src="{{ asset('app-assets/js/scripts/forms/form-repeater.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/datatables/datatable.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/spinner/jquery.bootstrap-touchspin.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/number-input.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/extensions/toastr.min.js') }}"></script>
<!-- END: Page JS-->

<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<!-- Validation-->
<script src="{{ asset('formvalidation/js/formValidation.min.js') }}"></script>
<script src="{{ asset('formvalidation/js/framework/bootstrap.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/validation/jqBootstrapValidation.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/validation/form-validation.js') }}"></script>
<script src="{{ asset('app-assets/js/pusher/push.min.js') }}"></script>
<!-- Validation-->
<script src="https://cdn.jsdelivr.net/timepicker.js/latest/timepicker.min.js"></script>


<script>
    // var timepicker = new TimePicker('fullTime', {
    //     lang: 'en',
    //     theme: 'dark'
    // });
    // timepicker.on('change', function(evt) {

    //     var value = (evt.hour || '00') + ':' + (evt.minute || '00');
    //     evt.element.value = value;
    //     console.log(value);

    // });
    function timeForm(id, value) {
        console.log(id, value);
        $(id).val(value);
    }

    $('table').DataTable({
        "bPaginate": false,
        "bInfo": false,
        "bFilter": false,
        "ordering": false
        // processing: true,
        // serverSide: true,
    });
    $(document).on('keyup', "#search", function() {
        searchPosts($(this).val());
    })

    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        $('.pagination li').removeClass('active');

        $(this).parent('li').addClass('active');
        var page = $(this).attr('href').split('page=')[1];
        getPosts(page);

    });

    function getPosts(page) {
        var search = $('#search').val();
        $.ajax({
            url: '?page=' + page + '&search=' + search,
            type: 'GET',
            data: {
                '_token': 'csrf_token()'
            },
        }).done(function(data) {
            $('.table-responsive').html(data);
            const url = new URL(window.location.href);
            url.searchParams.set('page', page);
            window.history.replaceState(null, null, url);
        }).fail(function() {
            // alert('Posts could not be loaded.');
        });
    }

    function searchPosts(search = null) {
        $.ajax({
            url: '?search=' + search,
            type: 'GET',
            data: {
                '_token': 'csrf_token()'
            },
        }).done(function(data) {
            $('.table-responsive').html(data);
            // location.hash = search;
        }).fail(function() {
            // alert('Posts could not be loaded.');
        });
    }
</script>
@include('scripts.global')

{{-- @include('layouts.validation') --}}
{{-- <script src="{{ asset('formvalidation/js/framework/bootstrap.min.js') }}"></script> --}}

</body>
<!-- END: Body-->

</html>
