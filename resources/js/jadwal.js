function initScheduleTitle() {
    const form = document.querySelector('#jadwal-form');
    const type = form?.querySelector('#jenis');
    const title = form?.querySelector('#judul');

    if (!type || !title) {
        return;
    }

    const defaultTitle = (value) => (value === 'pupuk' ? 'Pemupukan' : 'Panen');
    let titleIsDefault = title.value === defaultTitle(type.value);

    type.addEventListener('change', () => {
        if (titleIsDefault || title.value.trim() === '') {
            title.value = defaultTitle(type.value);
            titleIsDefault = true;
        }
    });

    title.addEventListener('input', () => {
        titleIsDefault = title.value === defaultTitle(type.value);
    });
}

document.addEventListener('DOMContentLoaded', initScheduleTitle);
