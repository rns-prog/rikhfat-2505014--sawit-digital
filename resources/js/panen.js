function initIncomePreview() {
    const form = document.querySelector('#panen-form');
    const weight = form?.querySelector('#berat_kg');
    const price = form?.querySelector('#harga_per_kg');
    const preview = document.querySelector('#pendapatan-preview');

    if (!weight || !price || !preview) {
        return;
    }

    const currency = new Intl.NumberFormat('id-ID', {
        maximumFractionDigits: 0,
    });

    const updatePreview = () => {
        const weightValue = Number.parseFloat(weight.value);
        const priceValue = Number.parseFloat(price.value);
        const income = Number.isFinite(weightValue) && Number.isFinite(priceValue)
            ? weightValue * priceValue
            : 0;

        preview.textContent = `Rp ${currency.format(income)}`;
    };

    weight.addEventListener('input', updatePreview);
    price.addEventListener('input', updatePreview);
    updatePreview();
}

document.addEventListener('DOMContentLoaded', initIncomePreview);
