document.addEventListener('DOMContentLoaded', function () {

    var fileInputs = document.querySelectorAll('.file-input');
    fileInputs.forEach(function (input) {
        var preview = findPreviewForInput(input);
        input.addEventListener('change', function () {
            previewImage(input, preview);
        });
    });

    var els = document.querySelectorAll('[data-preview-target]');
    els.forEach(function (el) {
        var input = document.querySelector(el.getAttribute('data-preview-target'));
        if (input && input.classList.contains('file-input')) {
            var preview = el.querySelector('.preview-img') || document.getElementById('imagePreview');
            input.addEventListener('change', function () { previewImage(input, preview); });
        }
    });
});

function findPreviewForInput(input) {

    var group = input.closest('.form-group') || input.closest('.auth-card') || input.parentElement;
    if (group) {
        var img = group.querySelector('.preview-img');
        if (img) return img;
    }

    
    var globalPreview = document.getElementById('imagePreview');
    return globalPreview || null;
}

function previewImage(input, imgEl) {
    if (!imgEl) return;
    var file = input.files && input.files[0];
    if (!file) {
        imgEl.style.display = 'none';
        return;
    }
    var reader = new FileReader();
    reader.onload = function (e) {
        imgEl.src = e.target.result;
        imgEl.style.display = 'block';
    };
    reader.readAsDataURL(file);
}
