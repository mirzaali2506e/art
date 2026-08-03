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
</script>
</body>
</html>
