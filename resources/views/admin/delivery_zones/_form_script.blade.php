{{--
    admin/delivery_zones/_form_script.blade.php — Live rule preview and
    fully-free toggle behaviour shared by the create and edit forms.
--}}
<script nonce="{{ Vite::cspNonce() }}">
$(function() {
    'use strict';

    const isFree    = $('#is_free');
    const freeOver  = $('#free_over_amount');
    const fee       = $('#delivery_fee');
    const minMiles  = $('#min_miles');
    const maxMiles  = $('#max_miles');
    const preview   = $('#rule_preview');

    function updateForm() {
        const free = isFree.is(':checked');
        freeOver.prop('disabled', free);
        fee.prop('disabled', free);

        const from = parseFloat(minMiles.val()) || 0;
        const to   = parseFloat(maxMiles.val()) || 0;
        const band = from.toFixed(1) + '–' + to.toFixed(1) + ' miles';

        if (free) {
            preview.text(band + ': every order delivers FREE.');
            return;
        }

        const threshold = parseFloat(freeOver.val());
        const feeVal    = parseFloat(fee.val()) || 0;

        if (!isNaN(threshold) && threshold > 0) {
            preview.text(band + ': orders over £' + threshold.toFixed(2) + ' deliver FREE, under it pay £' + feeVal.toFixed(2) + '.');
        } else {
            preview.text(band + ': every order pays £' + feeVal.toFixed(2) + ' delivery.');
        }
    }

    isFree.on('change', updateForm);
    [freeOver, fee, minMiles, maxMiles].forEach(el => el.on('input', updateForm));
    updateForm();
});
</script>
