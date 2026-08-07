    </div>
</div>
<script>
function previewImage(input) {
    var previewArea = input.closest('.form-group').querySelector('.image-upload-area');
    var previewImg = previewArea ? previewArea.querySelector('img') : null;
    if (input.files && input.files[0] && previewImg) {
        var reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewArea.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Close admin sidebar on mobile when clicking outside
document.addEventListener('click', function(e) {
    var sidebar = document.getElementById('adminSidebar');
    var toggle = document.querySelector('.admin-menu-toggle');
    if (sidebar && sidebar.classList.contains('open') && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
        sidebar.classList.remove('open');
    }
});

// Auto-dismiss flash messages
setTimeout(function() {
    document.querySelectorAll('.alert-success, .alert-error').forEach(function(el) {
        el.style.opacity = '0';
        el.style.transition = 'opacity 0.3s';
        setTimeout(function() { el.style.display = 'none'; }, 300);
    });
}, 4000);
</script>
</body>
</html>
