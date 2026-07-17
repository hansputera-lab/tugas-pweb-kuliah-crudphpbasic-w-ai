document.addEventListener('DOMContentLoaded', function () {
    function formatCurrency(value) {
        var num = parseFloat(value);
        if (isNaN(num)) return '';
        return num.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2,
        });
    }

    function parseCurrency(str) {
        if (!str) return '';
        var cleaned = str.replace(/Rp\s*/g, '').replace(/\s/g, '').replace(/\./g, '').replace(',', '.');
        var num = parseFloat(cleaned);
        return isNaN(num) ? '' : num.toString();
    }

    var inputs = document.querySelectorAll('[data-currency]');

    inputs.forEach(function (input) {
        if (input.value) {
            var formatted = formatCurrency(input.value);
            if (formatted) input.value = formatted;
        }

        input.addEventListener('blur', function () {
            if (this.value) {
                var formatted = formatCurrency(parseCurrency(this.value));
                if (formatted !== false) this.value = formatted;
            }
        });

        input.addEventListener('focus', function () {
            if (this.value) {
                var raw = parseCurrency(this.value);
                if (raw) this.value = raw;
            }
        });
    });
});
