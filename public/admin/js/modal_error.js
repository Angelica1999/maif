function initializeModalAccessibility(modalId, options = {}) {
    let originalFocus = null;
    const modal = $(modalId);
    const { hasIframe = false, hasSelect2 = false } = options;

    modal.on('show.bs.modal', function () {
        modal.removeAttr('aria-hidden');
        originalFocus = document.activeElement;

        modal.find('[tabindex="-1"]').each(function () {
            const $el = $(this);
            const originalTabindex = $el.data('original-tabindex');
            if (originalTabindex !== undefined) {
                $el.attr('tabindex', originalTabindex);
                $el.removeData('original-tabindex');
            } else {
                $el.removeAttr('tabindex');
            }
        });
    });

    modal.on('shown.bs.modal', function () {
        const focusTarget = modal.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])').first();
        if (focusTarget.length) {
            focusTarget.focus();
        } else {
            modal.attr('tabindex', '-1').focus();
        }
    });

    modal.on('hide.bs.modal', function () {
        if (hasIframe) {
            modal.find('*').addBack().each(function () {
                $(this).blur();
            });
            modal.find('iframe').each(function () {
                try {
                    this.contentWindow.blur();
                } catch (e) {
                    console.warn(`Cannot blur iframe content: ${e.message}`);
                }
                this.blur();
            });
        }

        modal.find(':focus').blur();

        modal.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])').each(function () {
            const $el = $(this);
            const currentTabindex = $el.attr('tabindex');
            if (currentTabindex !== undefined && currentTabindex !== '-1') {
                $el.data('original-tabindex', currentTabindex);
            }
            $el.attr('tabindex', '-1');
        });
    });

    modal.on('hidden.bs.modal', function () {
        if (hasSelect2) {
            modal.find('.select2-hidden-accessible').each(function () {
                $(this).select2('destroy');
            });
            $('.select2-container').remove();
        }

        modal.removeAttr('tabindex');

        if (!modal.hasClass('show') && modal.find(':focus').length === 0) {
            modal.removeAttr('aria-hidden');
        }

        if (originalFocus && $(originalFocus).is(':visible')) {
            originalFocus.focus();
        }
    });

    modal.on('keydown', function (e) {
        if (e.key === 'Tab') {
            const focusableElements = modal.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            const firstFocusable = focusableElements.first();
            const lastFocusable = focusableElements.last();

            if (e.shiftKey && document.activeElement === firstFocusable[0]) {
                e.preventDefault();
                lastFocusable.focus();
            } else if (!e.shiftKey && document.activeElement === lastFocusable[0]) {
                e.preventDefault();
                firstFocusable.focus();
            }
        }

        if (e.key === 'Escape') {
            modal.modal('hide');
        }
    });
}

$(document).ready(function () {
    // Prevent aria-hidden on visible modals
    $('[role="dialog"], .modal').removeAttr('aria-hidden');

    const forceRemoveAriaHidden = setInterval(() => {
        $('[role="dialog"], .modal').each(function () {
            const $modal = $(this);
            if ($modal.hasClass('show') || $modal.find(':focus').length > 0) {
                $modal.removeAttr('aria-hidden');
            }
        });
    }, 100);

    setTimeout(() => clearInterval(forceRemoveAriaHidden), 2000);

    // Initialize select2 modals
    const select2Modals = ['#gl_tracking', '#modified_funds'];
    select2Modals.forEach(modalId => {
        initializeModalAccessibility(modalId, { hasSelect2: true });
    });

    // Initialize regular modals
    const regularModals = ['#create_fundsource', '#track_details', '#track_details2', '#transfer_fundsource', '#obligate', '#create_dv', '#view_v1', '#view_v2', '#update_predv'];
    regularModals.forEach(modalId => {
        initializeModalAccessibility(modalId);
    });

    // Initialize iframe modals
    const iframeModals = ['#version2', '#i_frame'];
    iframeModals.forEach(modalId => {
        initializeModalAccessibility(modalId, { hasIframe: true });
    });
});

function appModal() {
    $(document).ready(function () {
        const targets = [
            'app', 'supp_tracking', 'sub_tracking', 'budget_funds', 'budget_track2',
            'cost_tracking', 'logbook', 'updateProponent', 'get_mail', 'patient_history',
            'create_dv2', 'releaseTo', 'dv_history', 'summary_display', 'hold_pro', 'hold_facility',
            'facility_included', 'update_facility', 'update_note', 'update_patient', 
            'create_dv3', 'create_predv', 'create_patient', 'create_fundsource2', 'add_user', 'update_remarks'
        ];

        targets.forEach(function (id) {
            const el = document.getElementById(id);
            if (!el) return;

            el.removeAttribute('aria-hidden');

            const observer = new MutationObserver(() => {
                if (el.getAttribute('aria-hidden') === 'true') {
                    el.removeAttribute('aria-hidden');
                }
            });

            observer.observe(el, {
                attributes: true,
                attributeFilter: ['aria-hidden']
            });
        });
    });
}

function sample() {
    console.log('sample log for debugging');
}
