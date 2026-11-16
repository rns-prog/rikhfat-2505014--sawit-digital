function initWagePreview() {
    const form = document.querySelector('#biaya-form');

    if (!form) {
        return;
    }

    const category = form.querySelector('#kategori');
    const mode = form.querySelector('#mode_hitung');
    const modeGroup = form.querySelector('#cara-hitung-group');
    const automaticFields = form.querySelector('#upah-otomatis-fields');
    const nominal = form.querySelector('#nominal');
    const nominalNote = form.querySelector('#nominal-auto-note');
    const kebun = form.querySelector('#kebun_id');
    const rate = form.querySelector('#tarif_per_kg');
    const start = form.querySelector('#tanggal_mulai');
    const end = form.querySelector('#tanggal_selesai');
    const detail = form.querySelector('#upah-preview-detail');
    const message = form.querySelector('#upah-preview-message');
    const submit = form.querySelector('#biaya-submit');
    const numberFormat = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 });
    const currencyFormat = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 });
    let activeRequest;
    let debounceTimer;
    let previewReady = false;

    const isWage = () => category.value === 'Upah panen';
    const isAutomatic = () => isWage() && mode.value === 'otomatis';

    function renderMode() {
        modeGroup.hidden = !isWage();
        modeGroup.classList.toggle('hidden', !isWage());
        automaticFields.classList.toggle('hidden', !isAutomatic());
        nominal.readOnly = isAutomatic();
        nominal.required = !isAutomatic();
        nominalNote.classList.toggle('hidden', !isAutomatic());

        if (submit) {
            submit.disabled = isAutomatic() && !previewReady;
        }

        if (isAutomatic()) {
            updatePreview();
        } else {
            previewReady = false;
        }
    }

    function updatePreview() {
        window.clearTimeout(debounceTimer);
        activeRequest?.abort();
        previewReady = false;

        if (!isAutomatic()) {
            return;
        }

        submit.disabled = true;
        nominal.value = '';
        const values = {
            kebun_id: kebun.value,
            tarif_per_kg: rate.value,
            tanggal_mulai: start.value,
            tanggal_selesai: end.value,
        };

        if (Object.values(values).some((value) => value === '')) {
            detail.textContent = 'Pilih kebun, tarif, dan rentang tanggal untuk melihat perhitungan.';
            message.textContent = '';
            return;
        }

        detail.textContent = 'Menghitung berat panen…';
        message.textContent = '';
        debounceTimer = window.setTimeout(async () => {
            activeRequest = new AbortController();

            try {
                const url = new URL(form.dataset.previewUrl, window.location.origin);
                Object.entries(values).forEach(([key, value]) => url.searchParams.set(key, value));
                const response = await fetch(url, {
                    headers: { Accept: 'application/json' },
                    signal: activeRequest.signal,
                });

                if (!response.ok) {
                    const body = await response.json();
                    const firstError = Object.values(body.errors || {}).flat()[0];
                    throw new Error(firstError || 'Pratinjau upah tidak dapat dihitung.');
                }

                const result = await response.json();

                if (!result.has_harvest) {
                    detail.textContent = 'Belum ada panen dalam rentang tanggal ini.';
                    message.textContent = 'Pilih rentang tanggal yang memiliki catatan panen. Biaya tidak dapat disimpan tanpa panen.';
                    return;
                }

                nominal.value = result.nominal;
                detail.textContent = `${numberFormat.format(result.total_berat_kg)} kg × Rp ${currencyFormat.format(result.tarif_per_kg)} = Rp ${currencyFormat.format(result.nominal)}`;
                message.textContent = 'Nominal akan dihitung ulang dari data panen saat disimpan.';
                previewReady = true;
                submit.disabled = false;
            } catch (error) {
                if (error.name !== 'AbortError') {
                    detail.textContent = 'Perhitungan belum tersedia.';
                    message.textContent = error.message || 'Gagal menghitung pratinjau upah.';
                }
            }
        }, 250);
    }

    category.addEventListener('change', () => {
        if (!isWage()) {
            mode.value = 'manual';
        }

        renderMode();
    });
    mode.addEventListener('change', renderMode);
    [kebun, rate, start, end].forEach((input) => input.addEventListener('input', updatePreview));
    [kebun, start, end].forEach((input) => input.addEventListener('change', updatePreview));
    renderMode();
}

document.addEventListener('DOMContentLoaded', initWagePreview);
