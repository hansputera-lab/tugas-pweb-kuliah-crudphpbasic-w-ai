document.addEventListener('DOMContentLoaded', function () {
    var forms = document.querySelectorAll('form');

    forms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            clearErrors(form);

            unformatCurrencyInputs(form);
            checkDataConstraints(form);

            if (!form.checkValidity()) {
                e.preventDefault();
                showErrors(form);
                focusFirstInvalid(form);
                reformatCurrencyInputs(form);
            }
        });
    });

    function unformatCurrencyInputs(form) {
        form.querySelectorAll('[data-currency]').forEach(function (input) {
            if (input.value) {
                var cleaned = input.value.replace(/Rp\s*/g, '').replace(/\s/g, '').replace(/\./g, '').replace(',', '.');
                var num = parseFloat(cleaned);
                input.value = isNaN(num) ? '' : num.toString();
            }
        });
    }

    function reformatCurrencyInputs(form) {
        form.querySelectorAll('[data-currency]').forEach(function (input) {
            if (input.value) {
                var num = parseFloat(input.value);
                if (!isNaN(num)) {
                    input.value = num.toLocaleString('id-ID', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 2,
                    });
                }
            }
        });
    }

    function showErrors(form) {
        var invalidFields = form.querySelectorAll(':invalid');
        invalidFields.forEach(function (field) {
            var container = field.closest('.space-y-6 > *, .grid > *, div') || field.parentElement;
            var existing = container.querySelector('.validation-error');
            if (existing) return;

            var msg = getCustomMessage(field) || field.validationMessage || 'This field is invalid.';

            var errorEl = document.createElement('p');
            errorEl.className = 'validation-error mt-1 text-sm text-red-600';
            errorEl.textContent = msg;
            container.appendChild(errorEl);

            field.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            field.classList.remove('focus:border-blue-500', 'focus:ring-blue-500');
        });
    }

    function checkDataConstraints(form) {
        form.querySelectorAll('[data-min]:not([type="number"]):not([type="date"]):not([type="range"])').forEach(function (field) {
            var min = parseFloat(field.getAttribute('data-min') || field.getAttribute('min'));
            if (!isNaN(min) && field.value) {
                var num = parseFloat(field.value);
                if (!isNaN(num) && num < min) {
                    field.setCustomValidity('Must be at least ' + min + '.');
                    return;
                }
            }
            field.setCustomValidity('');
        });
        form.querySelectorAll('[data-max]:not([type="number"]):not([type="date"]):not([type="range"])').forEach(function (field) {
            var max = parseFloat(field.getAttribute('data-max') || field.getAttribute('max'));
            if (!isNaN(max) && field.value) {
                var num = parseFloat(field.value);
                if (!isNaN(num) && num > max) {
                    field.setCustomValidity('Must be no more than ' + max + '.');
                    return;
                }
            }
            field.setCustomValidity('');
        });
    }

    function clearErrors(form) {
        form.querySelectorAll('.validation-error').forEach(function (el) {
            el.remove();
        });
        form.querySelectorAll('.border-red-500').forEach(function (el) {
            el.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            el.classList.add('focus:border-blue-500', 'focus:ring-blue-500');
        });
    }

    function focusFirstInvalid(form) {
        var first = form.querySelector(':invalid');
        if (first) first.focus();
    }

    function getCustomMessage(field) {
        if (field.validity && field.validity.valueMissing) {
            var label = findLabel(field);
            return label ? label + ' is required.' : 'This field is required.';
        }
        if (field.validity && field.validity.typeMismatch) {
            if (field.type === 'email') return 'Please enter a valid email address.';
            if (field.type === 'url') return 'Please enter a valid URL.';
        }
        if (field.validity && field.validity.rangeUnderflow) {
            var label = findLabel(field) || 'Value';
            return label + ' must be at least ' + field.min + '.';
        }
        if (field.validity && field.validity.rangeOverflow) {
            var label = findLabel(field) || 'Value';
            return label + ' must be no more than ' + field.max + '.';
        }
        if (field.validity && field.validity.tooLong) {
            return 'Maximum ' + field.maxLength + ' characters allowed.';
        }
        if (field.validity && field.validity.stepMismatch) {
            return 'Please enter a valid value.';
        }
        if (field.validity && field.validity.badInput) {
            return 'Please enter a valid number.';
        }
        if (field.validity && field.validity.patternMismatch) {
            return field.title || 'Please match the requested format.';
        }
        return null;
    }

    function findLabel(field) {
        var label = document.querySelector('label[for="' + field.id + '"]');
        if (label) {
            return cleanLabel(label.textContent);
        }
        var parent = field.closest('div');
        if (parent) {
            label = parent.querySelector('label');
            if (label) {
                return cleanLabel(label.textContent);
            }
        }
        return null;
    }

    function cleanLabel(text) {
        return text
            .replace(/\s*\(.*?\)\s*/g, ' ')
            .replace(/\s*–\s*.*$/, '')
            .replace(/\s*≈.*$/, '')
            .replace(/\s*\(%.*$/, '')
            .replace(/\s+/g, ' ')
            .trim();
    }
});
