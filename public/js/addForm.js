document.addEventListener('DOMContentLoaded', function() {

    const addFormBtn = document.getElementById('addFormBtn');

    document.addEventListener('click', function(event) {
        if (event.target && event.target.id === 'addFormBtn') {

            event.preventDefault();

            const addForm = document.getElementById('addForm');

            if (addForm.style.display === 'none' || addForm.style.display === '') {
                addForm.style.display = 'block';
            } else {
                addForm.style.display = 'none';
            }
        }
    });
});
