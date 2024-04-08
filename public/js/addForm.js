document.addEventListener('DOMContentLoaded', function() {

    const addFormBtn = document.getElementById('addFormBtn');

    document.addEventListener('click', function(event) {
        if (event.target && event.target.id === 'addFormBtn') {
            console.log("bouton gris ajout")
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

document.addEventListener('DOMContentLoaded', function() {

    // Sélectionnez tous les boutons "Modifier" par leur nouvelle classe
    const modifyBtns = document.querySelectorAll('.modify-btn');

    modifyBtns.forEach(function(modifyBtn) {
        modifyBtn.addEventListener('click', function(event) {
            event.preventDefault();
            console.log("bouton gris")

            // Tous les formulaires form2
            const allForms = document.querySelectorAll('[id^="addSecondForm_"]');

            // Construisez l'ID du div du formulaire en utilisant l'ID du bouton
            const formDivId = 'addSecondForm_' + modifyBtn.id.split('_')[1];
            const addForm = document.getElementById(formDivId);

            if(addForm.style.display === 'inline-block'){
                // Masqer tous les formulaires
                allForms.forEach(function(form) {
                    form.style.display = 'none';
                });
            } else if (addForm.style.display === 'none' || addForm.style.display === '') {
                allForms.forEach(function(form) {
                    form.style.display = 'none';
                });
                addForm.style.display = 'inline-block';
            }
        });
    });
});
