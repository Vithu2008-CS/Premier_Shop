{{--
    admin/partials/noble_footer.blade.php — Admin layout footer
    =============================================================
    Minimal footer bar: copyright year (dynamic) and shop name link.
    Included by layouts/admin_noble.blade.php.
--}}
<footer class="footer d-flex flex-column flex-md-row align-items-center justify-content-between">
  <p class="text-muted text-center text-md-left">Copyright © {{ date('Y') }} <a href="{{ url('/') }}" target="_blank">Premier Shop</a>. All rights reserved</p>
</footer>
