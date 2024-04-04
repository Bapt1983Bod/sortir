document.addEventListener('DOMContentLoaded', () => {
    const addFormBtn = document.getElementById('addFormBtn');
    const addForm = document.getElementById('addForm');

    addFormBtn.addEventListener('click', (event) => {
        // event.preventDefault();

        if (addForm.style.display === 'none' || addForm.style.display === '') {
            addForm.style.display = 'block';
        } else {
            addForm.style.display = 'none';
        }
    });
});